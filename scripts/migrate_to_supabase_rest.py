import os
import time
import json
import base64

import pymysql
import requests

# Source MySQL (from .env)
SOURCE_CONNECTION = os.environ.get('DB_CONNECTION', 'mysql')
SOURCE_HOST = os.environ.get('DB_HOST', '127.0.0.1')
SOURCE_PORT = int(os.environ.get('DB_PORT', '3306'))
SOURCE_DB = os.environ.get('DB_DATABASE', 'mcims')
SOURCE_USER = os.environ.get('DB_USERNAME', 'root')
SOURCE_PASSWORD = os.environ.get('DB_PASSWORD', '')

# Supabase REST
SUPABASE_REST_URL = os.environ.get(
    'SUPABASE_URL',
    'https://wxdtjnshqfvesqhabpzx.supabase.co/rest/v1/'
).rstrip('/') + '/'

# Use the Supabase API key you provided as the Authorization bearer token.
# NOTE: This script embeds the key to avoid relying on env vars.
# IMPORTANT: MUST be Supabase *service_role* key to bypass RLS for inserts/updates.
# Paste your service role key here.
SUPABASE_API_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Ind4ZHRqbnNocWZ2ZXNxaGFicHp4Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3OTc3NDc4OCwiZXhwIjoyMDk1MzUwNzg4fQ.tSjQcQ7c1Ru51MzKLZU9Y1ILHQg3eArO0IlQoZbjKH4'.strip()







TABLES = [
    'users',
    'barangays',
    'categories',
    'complaints',
    'barangay_admin_requests',
]

SCHEMA = os.environ.get('SUPABASE_SCHEMA', '').strip() or 'public'


HEADERS_BASE = {
    'apikey': SUPABASE_API_KEY,
    'Authorization': f'Bearer {SUPABASE_API_KEY}',
    'Content-Type': 'application/json',
}


def get_mysql_connection():


    if SOURCE_CONNECTION != 'mysql':
        raise RuntimeError('Only mysql source supported by this script')

    return pymysql.connect(
        host=SOURCE_HOST,
        port=SOURCE_PORT,
        user=SOURCE_USER,
        password=SOURCE_PASSWORD,
        database=SOURCE_DB,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def ensure_api_key():
    if not SUPABASE_API_KEY:
        raise RuntimeError('Missing SUPABASE_API_KEY env var (your Supabase API key)')


def fetch_table(mysql_conn, table):
    with mysql_conn.cursor() as cur:
        cur.execute(f"SELECT * FROM `{table}`")
        return cur.fetchall()


def upsert_table(table, rows, pk='id'):
    if not rows:
        return 0

    # Supabase REST upsert supports conflict target via Prefer: resolution.
    # Use a chunk size to avoid request limits.
    chunk_size = int(os.environ.get('CHUNK_SIZE', '200'))
    url = f"{SUPABASE_REST_URL}{table}"

    def sanitize_row(r):
        # Handle numeric overflow: Supabase schema uses numeric(3,2) for categories.weight.
        # Laravel sends decimals as floats/strings; clamp to 0..99.99 to avoid 22003.
        if isinstance(r, dict):
            if table == 'categories' and 'weight' in r and r['weight'] is not None:
                try:
                    w = float(r['weight'])
                    # numeric(3,2) => max 10^(3-2)=10, but Postgres stores as 3 digits total.
                    # The safest clamp to avoid overflow is < 10^1 = 10.
                    if w >= 10:
                        w = 9.99
                    if w < 0:
                        w = 0.00
                    r['weight'] = round(w, 2)
                except Exception:
                    # leave as-is if parsing fails
                    pass
        return r

    total = 0
    for i in range(0, len(rows), chunk_size):
        chunk = [sanitize_row(x) for x in rows[i:i+chunk_size]]

        prefer = (
            f'resolution=merge-duplicates,return=representation;'
            f"on_conflict=({pk})"
        )
        headers = dict(HEADERS_BASE)
        headers['Prefer'] = prefer

        # Use Prefer: return=minimal to reduce response size; keep upsert Prefer.
        resp = requests.post(
            url,
            headers=headers,
            data=json.dumps(chunk, default=str),
            timeout=120,
        )


        if resp.status_code not in (200, 201, 204):
            raise RuntimeError(f"Upsert failed for {table}. status={resp.status_code} body={resp.text[:1000]}")

        total += len(chunk)
        print(f"  - {table}: chunk {i//chunk_size + 1} ({len(chunk)} rows) ok")

        # small sleep to be polite
        time.sleep(0.1)

    return total



def main():
    ensure_api_key()
    mysql_conn = get_mysql_connection()

    for table in TABLES:
        print(f"Fetching {table} from MySQL...")
        rows = fetch_table(mysql_conn, table)
        print(f"Inserting/upserting {len(rows)} rows into Supabase {SCHEMA}.{table}...")
        inserted = upsert_table(table, rows)
        print(f"Done {table}: processed {inserted} rows")

    mysql_conn.close()
    print('REST migration complete')


if __name__ == '__main__':
    main()


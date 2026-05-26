import os
import time
import json

import pymysql
import psycopg2
from psycopg2.extras import execute_values

# ---- Source MySQL (from .env) ----
SOURCE_CONNECTION = os.environ.get('DB_CONNECTION', 'mysql')
SOURCE_HOST = os.environ.get('DB_HOST', '127.0.0.1')
SOURCE_PORT = int(os.environ.get('DB_PORT', '3306'))
SOURCE_DB = os.environ.get('DB_DATABASE', 'mcims')
SOURCE_USER = os.environ.get('DB_USERNAME', 'root')
SOURCE_PASSWORD = os.environ.get('DB_PASSWORD', '')

# ---- Supabase Postgres ----
# Best practice: use service_role key only for REST; for Postgres use DB connection.
# Get these from Supabase Project Settings -> Database -> Connection string (or Pooler).
# You can set SUPABASE_DATABASE_URL env var, otherwise the script expects it.
SUPABASE_DATABASE_URL = os.environ.get(
    'SUPABASE_DATABASE_URL',
    ''
).strip()

if not SUPABASE_DATABASE_URL:
    raise RuntimeError('Missing SUPABASE_DATABASE_URL env var (Supabase Postgres connection string).')

SCHEMA = os.environ.get('SUPABASE_SCHEMA', 'public').strip() or 'public'

TABLES = [
    'users',
    'barangays',
    'categories',
    'complaints',
    'barangay_admin_requests',
]


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


def get_pg_conn():
    return psycopg2.connect(SUPABASE_DATABASE_URL, connect_timeout=30, application_name='mcims-migrate')


def ensure_tables(pg_conn):
    # Create tables matching Laravel migrations (best-effort types).
    ddl = f"""
    CREATE TABLE IF NOT EXISTS {SCHEMA}.users (
        id bigint PRIMARY KEY,
        name text,
        email text UNIQUE,
        email_verified_at timestamp NULL,
        password text,
        role text DEFAULT 'resident',
        barangay_id bigint NULL,
        contact_number text NULL,
        remember_token text NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS {SCHEMA}.barangays (
        id bigint PRIMARY KEY,
        name text,
        captain_name text NULL,
        contact_number text NULL,
        population integer NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS {SCHEMA}.categories (
        id bigint PRIMARY KEY,
        name text,
        description text NULL,
        weight numeric(3,2) DEFAULT 1.00,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS {SCHEMA}.complaints (
        id bigint PRIMARY KEY,
        complaint_id text UNIQUE,
        user_id bigint NOT NULL,
        barangay_id bigint NOT NULL,
        category_id bigint NOT NULL,
        description text,
        image_path text NULL,
        vulnerability_flag text NULL,
        priority_score numeric(5,2) DEFAULT 0,
        priority_level text DEFAULT 'low',
        status text DEFAULT 'pending',
        resolution_notes text NULL,
        resolved_at timestamp NULL,
        escalated_to_municipal boolean DEFAULT false,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS {SCHEMA}.barangay_admin_requests (
        id bigint PRIMARY KEY,
        full_name text,
        position text,
        barangay_id bigint NOT NULL,
        official_email text UNIQUE,
        contact_number text,
        status text DEFAULT 'pending',
        rejection_reason text NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );
    """

    with pg_conn.cursor() as cur:
        cur.execute(ddl)
    pg_conn.commit()


def sanitize_row(table, row):
    # categories.weight numeric(3,2) in Supabase; clamp to avoid overflow.
    if table == 'categories' and 'weight' in row and row['weight'] is not None:
        try:
            w = float(row['weight'])
            if w >= 10:
                w = 9.99
            if w < 0:
                w = 0.00
            row['weight'] = round(w, 2)
        except Exception:
            pass
    return row


def fetch_table(mysql_conn, table):
    with mysql_conn.cursor() as cur:
        cur.execute(f"SELECT * FROM `{table}`")
        return cur.fetchall()


def insert_rows_upsert(pg_conn, table, rows, pk='id', chunk_size=1000):
    if not rows:
        return 0

    # Use first row columns ordering.
    cols = list(rows[0].keys())

    # Quote identifiers
    col_list = ','.join([f'"{c}"' for c in cols])

    # Build update assignments for all columns except pk
    update_cols = [c for c in cols if c != pk]
    update_set = ', '.join([f'"{c}"=EXCLUDED."{c}"' for c in update_cols])

    values = []
    inserted = 0

    with pg_conn.cursor() as cur:
        for start in range(0, len(rows), chunk_size):
            chunk = rows[start:start+chunk_size]
            chunk = [sanitize_row(table, r) for r in chunk]
            values = [tuple(r.get(c) for c in cols) for r in chunk]

            execute_values(
                cur,
                f"""
                INSERT INTO {SCHEMA}."{table}" ({col_list})
                VALUES %s
                ON CONFLICT ("{pk}") DO UPDATE SET {update_set}
                """,
                values,
                page_size=chunk_size,
            )
            inserted += len(chunk)
            pg_conn.commit()
            print(f"  - {table}: chunk rows {start+1}-{start+len(chunk)} ok")

    return inserted


def main():
    mysql_conn = get_mysql_connection()
    pg_conn = get_pg_conn()

    ensure_tables(pg_conn)

    for t in TABLES:
        print(f"Fetching {t} from MySQL...")
        rows = fetch_table(mysql_conn, t)
        print(f"Upserting {len(rows)} rows into Supabase public.{t}...")
        inserted = insert_rows_upsert(pg_conn, t, rows)
        print(f"Done {t}: processed {inserted} rows")

    mysql_conn.close()
    pg_conn.close()
    print('Postgres migration complete')


if __name__ == '__main__':
    main()


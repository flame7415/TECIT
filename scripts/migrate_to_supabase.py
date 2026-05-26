import os
import sys
import json
from urllib.parse import urlparse

import requests
import pymysql
import psycopg2
from psycopg2.extras import execute_values

SOURCE = {
    "connection": os.environ.get("SOURCE_CONNECTION", "mysql"),
    "host": os.environ.get("DB_HOST", "127.0.0.1"),
    "port": int(os.environ.get("DB_PORT", "3306")),
    "database": os.environ.get("DB_DATABASE", "mcims"),
    "user": os.environ.get("DB_USERNAME", "root"),
    "password": os.environ.get("DB_PASSWORD", ""),
}

# Supabase REST auth (required if you prefer REST). This script uses direct Postgres inserts.
SUPABASE_URL = os.environ.get(
    "SUPABASE_URL",
    "https://wxdtjnshqfvesqhabpzx.supabase.co/rest/v1/",
)
SUPABASE_SERVICE_ROLE_KEY = os.environ.get("SUPABASE_SERVICE_ROLE_KEY", "")

# Supabase Postgres connection
SUPABASE_PG_URL = os.environ.get(
    "SUPABASE_DATABASE_URL",
    "postgresql://postgres:[flame09216729811]@aws-1-ap-southeast-1.pooler.supabase.com:6543/postgres",
)




TABLES = [
    "users",
    "barangays",
    "categories",
    "complaints",
    "barangay_admin_requests",
]


def get_mysql_connection():
    if SOURCE["connection"] != "mysql":
        raise RuntimeError("Only mysql supported in this script right now")

    return pymysql.connect(
        host=SOURCE["host"],
        port=SOURCE["port"],
        user=SOURCE["user"],
        password=SOURCE["password"],
        database=SOURCE["database"],
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def get_pg_conn():
    # Supabase Pooler often requires SNI/hostname.
    # We'll connect using the hostname in SUPABASE_PG_URL (not the resolved IP).
    return psycopg2.connect(SUPABASE_PG_URL, connect_timeout=30, application_name="mcims-migrate")



def fetch_table(mysql_conn, table):
    with mysql_conn.cursor() as cur:
        cur.execute(f"SELECT * FROM `{table}`")
        rows = cur.fetchall()
    return rows


def ensure_tables(pg_conn):
    # Create tables matching Laravel migrations (minimal schema; types chosen to be compatible with existing columns).
    # If tables already exist, creation is skipped.
    ddl = """
    CREATE TABLE IF NOT EXISTS public.users (
        id bigserial PRIMARY KEY,
        name text NOT NULL,
        email text UNIQUE,
        email_verified_at timestamp NULL,
        password text NOT NULL,
        role text DEFAULT 'resident',
        barangay_id bigint NULL,
        contact_number text NULL,
        remember_token text NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS public.barangays (
        id bigserial PRIMARY KEY,
        name text NOT NULL,
        captain_name text NULL,
        contact_number text NULL,
        population integer NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS public.categories (
        id bigserial PRIMARY KEY,
        name text NOT NULL,
        description text NULL,
        weight numeric(3,2) DEFAULT 1.00,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );

    CREATE TABLE IF NOT EXISTS public.complaints (
        id bigserial PRIMARY KEY,
        complaint_id text UNIQUE,
        user_id bigint NOT NULL,
        barangay_id bigint NOT NULL,
        category_id bigint NOT NULL,
        description text NOT NULL,
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

    CREATE TABLE IF NOT EXISTS public.barangay_admin_requests (
        id bigserial PRIMARY KEY,
        full_name text NOT NULL,
        position text NOT NULL,
        barangay_id bigint NOT NULL,
        official_email text UNIQUE,
        contact_number text NOT NULL,
        status text DEFAULT 'pending',
        rejection_reason text NULL,
        created_at timestamp NULL,
        updated_at timestamp NULL
    );
    """

    with pg_conn.cursor() as cur:
        cur.execute(ddl)
    pg_conn.commit()


def insert_rows(pg_conn, table, rows):
    if not rows:
        return 0

    cols = list(rows[0].keys())

    # Postgres: quote identifiers
    col_list = ",".join([f'"{c}"' for c in cols])
    placeholders = ",".join(["%s"] * len(cols))

    # For safety, always insert explicit IDs.
    values = []
    for r in rows:
        values.append(tuple(r[c] for c in cols))

    with pg_conn.cursor() as cur:
        execute_values(
            cur,
            f"INSERT INTO public.{table} ({col_list}) VALUES %s "
            f"ON CONFLICT (id) DO UPDATE SET {', '.join([f'"{c}"=EXCLUDED."{c}"' for c in cols if c != 'id'])}",
            values,
            page_size=500,
        )

    pg_conn.commit()
    return len(rows)


def main():
    mysql_conn = get_mysql_connection()
    pg_conn = get_pg_conn()

    ensure_tables(pg_conn)

    print("Starting migration...")

    # Import order to reduce FK issues (even though we didn't enforce FKs in DDL)
    for t in TABLES:
        print(f"Fetching {t} from MySQL...")
        rows = fetch_table(mysql_conn, t)
        print(f"Inserting {len(rows)} rows into Supabase ({t})...")
        inserted = insert_rows(pg_conn, t, rows)
        print(f"Done {t}: inserted/upserted {inserted}")

    mysql_conn.close()
    pg_conn.close()
    print("All done.")


if __name__ == "__main__":
    main()


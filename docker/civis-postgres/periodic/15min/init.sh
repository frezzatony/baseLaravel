#! /bin/sh
echo "DROP DATABASE IF EXISTS test_civis;" | psql --host=localhost --port=5432 --username=udb_civis -d civis
echo "SELECT 'CREATE DATABASE test_civis' WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'test_civis')\gexec" | psql --host=localhost --port=5432 --username=udb_civis -d civis
pg_dump --verbose --host=localhost --port=5432 --username=udb_civis --format=t --encoding=UTF-8 --inserts --create --if-exists -c --file /home/dump.tar civis
pg_restore --verbose --host=localhost --port=5432 --username=udb_civis --clean --if-exists --format=t --dbname=test_civis /home/dump.tar

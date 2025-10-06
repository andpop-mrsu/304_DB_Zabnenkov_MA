#!/bin/bash
powershell -ExecutionPolicy Bypass -File make_db_init.ps1
sqlite3 db_init.db < db_init.sql

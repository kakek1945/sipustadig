#!/bin/sh
set -e

# Buat directory storage di /tmp untuk Vercel read-only filesystem
mkdir -p /tmp/storage/framework/cache/data
mkdir -p /tmp/storage/framework/sessions
mkdir -p /tmp/storage/framework/views
mkdir -p /tmp/storage/logs

# Set permission
chmod -R 777 /tmp/storage

# Jalankan FrankenPHP melalui konfigurasi Caddy bawaan image
exec frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile

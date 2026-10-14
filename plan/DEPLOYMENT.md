# Deployment Guide - Sirkulib MVP

**Project:** Sirkulib - Sistem Informasi Perpustakaan Sekolah
**Version:** 1.0
**Last Updated:** 2026-05-22

---

## 1. Overview

### 1.1 Deployment Options

| Option | Cost | Complexity | Best For | Recommendation |
|--------|------|------------|----------|----------------|
| Vercel (Community PHP) | Free tier | Low | MVP/POC | ✅ Initial MVP |
| Railway | $5-20/mo | Medium | Growing apps | ⭐ After MVP |
| Render | Free-$25/mo | Medium | Production | ⭐ After MVP |
| VPS (DigitalOcean/Linode) | $6-20/mo | High | Full control | For advanced users |
| Laravel Cloud | $29+/mo | Low | Laravel-native | ⭐ Production |
| Fly.io | $5-20/mo | High | Containerized | Advanced |

### 1.2 Current Architecture (MVP)

```
Internet → Vercel → Neon Postgres + R2 Storage
```

### 1.3 Recommended Production Architecture

```
Internet → Cloudflare → Laravel Cloud/Railway → Neon Postgres + R2 Storage
```

---

## 2. Pre-Deployment Checklist

### 2.1 Code Checklist

- [ ] All migrations tested locally
- [ ] All seeders run successfully
- [ ] Environment variables configured
- [ ] No hardcoded credentials
- [ ] `.env` not committed to git
- [ ] `storage/logs` in .gitignore
- [ ] `bootstrap/cache` in .gitignore
- [ ] APP_ENV=production in production
- [ ] APP_DEBUG=false in production
- [ ] APP_KEY=base64:... set

### 2.2 Database Checklist

- [ ] Neon project created
- [ ] Database created
- [ ] SSL connection verified
- [ ] Connection string ready
- [ ] Pooled connection string ready (for serverless)

### 2.3 Storage Checklist

- [ ] R2 bucket created (or S3)
- [ ] API keys generated
- [ ] Bucket policy set (public-read for assets)
- [ ] CORS configured
- [ ] Storage tested locally

### 2.4 Security Checklist

- [ ] Strong APP_KEY generated
- [ ] Session driver set (database/redis)
- [ ] Cache driver set (database/redis)
- [ ] CSRF enabled
- [ ] Rate limiting configured
- [ ] CORS configured for API
- [ ] File upload validation in place

---

## 3. Environment Configuration

### 3.1 Local Development (.env)

```env
APP_NAME=Sirkulib
APP_ENV=local
APP_KEY=base64:YOUR_LOCAL_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sirkulib
DB_USERNAME=postgres
DB_PASSWORD=your_password

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

### 3.2 Production (.env on server)

```env
APP_NAME=Sirkulib
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-neon-host.neon.tech
DB_PORT=5432
DB_DATABASE=sirkulib_main
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_SSLMODE=require

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password

FILESYSTEM_DISK=r2
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your-bucket
AWS_ENDPOINT=https://your-account.r2.cloudflarestorage.com

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=library@your-school.sch.id
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.3 Vercel Environment Variables

In Vercel dashboard → Settings → Environment Variables:

| Name | Value | Environments |
|------|-------|--------------|
| APP_NAME | Sirkulib | All |
| APP_ENV | production | All |
| APP_KEY | (generated) | All |
| APP_DEBUG | false | Production |
| APP_URL | (your-vercel-url) | All |
| DB_CONNECTION | pgsql | All |
| DB_HOST | (from Neon) | All |
| DB_PORT | 5432 | All |
| DB_DATABASE | (from Neon) | All |
| DB_USERNAME | (from Neon) | All |
| DB_PASSWORD | (from Neon) | All |
| DB_SSLMODE | require | All |
| SESSION_DRIVER | database | All |
| CACHE_DRIVER | database | All |
| QUEUE_CONNECTION | database | All |

---

## 4. Deployment to Vercel (MVP)

### 4.1 Prerequisites

1. GitHub repository with Laravel project
2. Vercel account (free tier)
3. Vercel CLI installed: `npm i -g vercel`

### 4.2 Step-by-Step

#### Step 1: Create vercel.json

```json
{
  "framework": null,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.9.0"
    }
  },
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ],
  "build": {
    "env": {
      "PHP_VERSION": "8.3"
    }
  }
}
```

#### Step 2: Create api/index.php (entry point)

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
```

#### Step 3: Deploy

```bash
# Login to Vercel
vercel login

# Deploy
vercel --prod

# Or via GitHub integration (recommended)
# Connect repo in Vercel dashboard → Settings → Git
# Enable "Deploy automatically"
```

#### Step 4: Run Migrations

```bash
# SSH into Vercel (if enabled)
vercel ssh

# Or use Vercel CLI
vercel run php artisan migrate --force

# Or use database migration job
```

### 4.3 Vercel Limitations & Workarounds

| Limitation | Workaround |
|------------|------------|
| No persistent filesystem | Use R2/S3 for uploads |
| No background workers | Use database queue, manual jobs |
| No cron scheduler | Use external cron (cron-job.org) |
| Cold starts | Use connection pooling |
| Max execution time | Break large tasks into smaller chunks |
| No PHP-FPM | Community runtime may have quirks |

---

## 5. Deployment to Laravel Cloud

### 5.1 Prerequisites

1. Laravel Cloud subscription (starts at $29/mo)
2. Project on GitHub

### 5.2 Step-by-Step

#### Step 1: Connect to Laravel Cloud

1. Go to https://cloud.laravel.com
2. Click "Add Project"
3. Connect GitHub repository
4. Select branch (main)

#### Step 2: Configure Project

1. **Region:** Choose nearest region
2. **Build Command:** `composer install --no-interaction`
3. **Start Command:** Leave default (Laravel handles this)
4. **Environment Variables:** Add from .env

#### Step 3: Add Databases

1. Click "Add Database"
2. Choose PostgreSQL
3. Note connection string

#### Step 4: Add Storage

1. Click "Add Disk"
2. Choose provider (S3/R2)
3. Configure credentials

#### Step 5: Deploy

```bash
# Push to main branch triggers deployment
git push origin main
```

Laravel Cloud handles:
- ✅ Auto-scaling
- ✅ Background workers
- ✅ Cron scheduling
- ✅ Database migrations
- ✅ SSL certificates
- ✅ CDN

---

## 6. Deployment to Railway

### 6.1 Prerequisites

1. Railway account
2. GitHub repository

### 6.2 Step-by-Step

#### Step 1: Create Railway Project

1. Go to https://railway.app
2. Click "New Project"
3. Choose "Deploy from GitHub repo"
4. Select repository

#### Step 2: Add PostgreSQL

1. Click "Add Database"
2. Choose "PostgreSQL"
3. Note connection string

#### Step 3: Configure Environment

Add these environment variables in Railway dashboard:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=(generate with: php artisan key:generate --show)
APP_URL=(your-railway-url)

DB_CONNECTION=pgsql
DB_HOST=(from Railway database)
DB_PORT=5432
DB_DATABASE=(from Railway database)
DB_USERNAME=(from Railway database)
DB_PASSWORD=(from Railway database)

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
```

#### Step 4: Deploy

Railway auto-deploys on git push. Monitor deployment in Railway dashboard.

#### Step 5: Configure Startup

In Railway project settings → Start Command:
```
sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"
```

---

## 7. Database Migration

### 7.1 Pre-Migration Checklist

- [ ] Backup production database
- [ ] Test migration on staging
- [ ] Downtime notification (if needed)
- [ ] Rollback plan ready

### 7.2 Migration Commands

```bash
# Local
php artisan migrate

# On server (after SSH/CLI access)
php artisan migrate --force

# Vercel (via Build Step)
# Add to vercel.json:
{
  "builds": [
    {
      "src": "*.php",
      "use": "vercel-php@0.9.0"
    }
  ],
  "setup": {
    "commands": [
      "php artisan migrate --force"
    ]
  }
}
```

### 7.3 Rollback

```bash
php artisan migrate:rollback --step=1
```

---

## 8. Post-Deployment Verification

### 8.1 Smoke Tests

Run these after deployment:

| Test | URL | Expected | Status |
|------|-----|----------|--------|
| Homepage loads | / | Redirects to login or dashboard | [ ] |
| Login works | /login | Form displays, login succeeds | [ ] |
| Dashboard loads | /dashboard | Stats display | [ ] |
| Create member | /members/create | Form works, member saved | [ ] |
| Create book | /books/create | Form works, book saved | [ ] |
| Cover upload | (book create) | Cover appears in list | [ ] |
| Loan transaction | /loans/create | Full flow works | [ ] |
| Return transaction | /loans/{id}/return | Fine calculated if late | [ ] |
| Export Excel | /members/export | File downloads | [ ] |

### 8.2 Performance Check

| Check | Tool | Target |
|-------|------|--------|
| Page load time | Lighthouse | < 3s |
| API response | Browser DevTools | < 500ms |
| Database queries | Laravel Debugbar | < 100ms |

### 8.3 Security Check

| Check | Status |
|-------|--------|
| SSL certificate valid | [ ] |
| HTTPS enforced | [ ] |
| No sensitive data in response | [ ] |
| CSRF works on forms | [ ] |
| Rate limiting active | [ ] |

---

## 9. Monitoring & Maintenance

### 9.1 Logging

Configure log channels:

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'stderr'],
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],
    'stderr' => [
        'driver' => 'monolog',
        'handler' => StreamHandler::class,
        'with' => [
            'stream' => 'php://stderr',
        ],
    ],
],
```

### 9.2 Error Tracking (Sentry)

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish
```

### 9.3 Uptime Monitoring

Use free services:
- https://uptimerobot.com
- https://betterstack.com
- https://healthchecks.io

---

## 10. Backup Strategy

### 10.1 Neon Backups

Neon provides automatic backups:
- Point-in-time recovery
- Daily snapshots
- Configurable retention

### 10.2 Manual Backup Script

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="sirkulib_backup_$DATE.sql"

# Export database
PGPASSWORD=$DB_PASSWORD pg_dump \
  -h $DB_HOST \
  -U $DB_USERNAME \
  -d $DB_DATABASE \
  --no-owner \
  --no-acl \
  -f "backups/$FILENAME"

# Upload to R2/S3
aws s3 cp "backups/$FILENAME" "s3://your-bucket/backups/"

# Keep only last 7 backups locally
find backups -type f -mtime +7 -delete

echo "Backup $FILENAME complete"
```

### 10.3 Restore Procedure

```bash
# From backup file
PGPASSWORD=$DB_PASSWORD psql \
  -h $DB_HOST \
  -U $DB_USERNAME \
  -d $DB_DATABASE \
  -f backups/sirkulib_backup_YYYYMMDD_HHMMSS.sql

# From Neon point-in-time
# Use Neon dashboard → Branching → Create branch from time
```

---

## 11. Troubleshooting

### 11.1 Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| "No application key" | APP_KEY not set | `php artisan key:generate` |
| "Connection refused" | DB host wrong | Check DB_HOST in .env |
| "SSL required" | SSL mode wrong | Set `DB_SSLMODE=require` |
| Blank page | APP_DEBUG=false | Enable temporarily for error |
| 500 after deploy | Permissions | `chmod -R 755 storage bootstrap/cache` |
| Images not loading | R2 config wrong | Check FILESYSTEM_DISK and credentials |
| Slow response | Cold start | Enable connection pooling |
| Login redirect loop | Session config | Check SESSION_DRIVER |

### 11.2 Useful Commands

```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check routes
php artisan route:list

# Check config
php artisan about

# Test database connection
php artisan db

# Rollback & re-migrate
php artisan migrate:fresh --seed
```

---

## 12. Rollback Procedure

### 12.1 Code Rollback

```bash
# Find the last working commit
git log --oneline

# Revert to previous commit
git revert HEAD

# Or reset to specific commit
git reset --hard <commit-hash>

# Force push (use carefully!)
git push --force origin main
```

### 12.2 Database Rollback

```bash
# Rollback last migration
php artisan migrate:rollback

# Restore from backup
# See 10.3 Restore Procedure
```

### 12.3 Vercel Rollback

In Vercel Dashboard:
1. Go to Deployments
2. Find the working deployment
3. Click "..." → "Promote to Production"

---

## 13. Contacts & Escalation

| Role | Contact | For |
|------|---------|------|
| Dev Lead | (Your Name) | Code issues, deployment |
| DevOps | (Your Name) | Infrastructure, scaling |
| Neon Support | support@neon.tech | Database issues |
| Vercel Support | (Vercel dashboard) | Hosting issues |

---

## 14. Appendix

### A. Useful Links

- Laravel Docs: https://laravel.com/docs
- Vercel PHP: https://github.com/vercel-community/php
- Neon Docs: https://neon.tech/docs
- Laravel Cloud: https://cloud.laravel.com

### B. Version History

| Date | Version | Changes |
|------|---------|---------|
| 2026-05-22 | 1.0 | Initial deployment guide |
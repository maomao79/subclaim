# SubClaim v0.1

Mobile-first subcontractor claims tracker.

## Stack
- PHP 8.x
- MySQL / MariaDB
- Bootstrap 5
- jQuery
- PDO
- No framework
- No Composer package required in v0.1

## Current working scope
- Login/session authentication
- Company/tenant boundary
- Customer
- Project
- Contract
- Basic claim entry
- Dashboard totals
- CSRF tokens
- PDO prepared statements

## Local test

1. Create empty MySQL database:

```sql
CREATE DATABASE subclaim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. In PowerShell:

```powershell
Copy-Item .\config.example.php .\config.php
```

Edit `config.php` for your local MySQL.

3. Run:

```powershell
php -S localhost:8080 -t public
```

4. Open:

`http://localhost:8080/setup.php`

Create first company/admin, then log in.

## GitHub versioning

First time:

```powershell
git init
git add .
git commit -m "Initial SubClaim v0.1"
git branch -M main
git remote add origin YOUR_GITHUB_REPO_URL
git push -u origin main
```

Normal work:

```powershell
git status
git add .
git commit -m "Add whatever we changed"
git push
```

## Next version
v0.2 should add:
1. BOQ / contract items UI
2. claim line items
3. cumulative vs current-period claim values
4. certification record
5. variations
6. proper CIS labour/material treatment
7. retention release
8. PWA manifest/service worker

## Before public deployment
- remove/block setup.php
- HTTPS only
- secure session cookie settings
- login throttling
- audit log
- database backups
- tenant security review

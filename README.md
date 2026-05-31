# SiteWorx (local workspace)

This repository contains a PHP website and an admin area under `Admin_SiteWorx`.

Quick setup (development)

1. Create/import the database

   - Import the provided migration SQL to create the schema and seed data. From shell:

```bash
mysql -u <dbuser> -p < database/migration.sql
```

   - Or open `database/migration.sql` in your DB admin and run it.

2. Configure DB credentials

   - Edit `Admin_SiteWorx/connection.php` and set `$databaseHost`, `$databaseName`, `$databaseUsername`, and `$databasePassword` to your MySQL values.

3. Create an admin user

   - Use the admin register form at `/Admin_SiteWorx/register.php` to create an account (it uses secure `password_hash`).
   - Alternatively insert a user manually (ensure the password is a bcrypt hash created with PHP `password_hash()`).

4. Test the admin

   - Visit `/Admin_SiteWorx/login.php` to sign in. After login, open `/Admin_SiteWorx/dashboard.php`.

Notes

- The admin area was refactored to use PDO and prepared statements.
- For production: disable `display_errors` in `php.ini` and secure the DB credentials.
- This repo contains a migration file at `database/migration.sql` that creates a more feature-rich schema; the admin area uses a simpler `login` and `products` table.

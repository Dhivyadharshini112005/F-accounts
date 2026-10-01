# F-Taxi Accounts — Core PHP Version

This package runs the existing F-Taxi Accounts UI without the Laravel framework.

## What was kept

- Existing `resources/views` Blade page HTML/CSS/JS and public logo/assets.
- Existing page layout and styling.
- Income, Expense and Salary Advance screens.
- Owner/Manager role behaviour.
- Dashboard totals and Cash/A/C breakdown.
- Driver ledger and payment recording.
- Reports and CSV export.
- Search.
- CSRF/session login handling.
- Existing URL paths such as `/dashboard`, `/income`, `/expenses`, `/reports`, `/search`.

A small `core/BladeLite.php` compatibility renderer is included so the existing Blade pages can be reused without installing Laravel.

## Requirements

- PHP 8.0+
- MySQL 5.7+/8.x or MariaDB
- Apache/XAMPP or PHP built-in server
- PDO MySQL extension enabled

## Setup with XAMPP

1. Copy the project folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Create a database named `f_taxi_accounts`.
4. Import `database_core.sql` into phpMyAdmin.
5. Check the database settings in `.env`.
6. Open:

   `http://localhost/<project-folder>/public/`

If you use the PHP built-in server instead:

```text
php -S 127.0.0.1:8000 -t public
```

Then open:

`http://127.0.0.1:8000/`

## Demo login

The SQL file creates:

- Username: `owner`
- Password: `password`
- Role: OWNER

and

- Username: `manager`
- Password: `password`
- Role: MANAGER

Change these credentials before production use.

## Important

The original Laravel files are retained in this package for reference/rollback, but the new `public/index.php` uses only the `core/` PHP layer. Laravel's `vendor/` directory is not required by the Core PHP runtime.

Do not expose `.env` publicly. It may contain database or external-service credentials.

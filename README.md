# Sun Son Solar

PHP and MariaDB registration/login project for XAMPP.

## Folder guide
- `config/database.php` - database connection settings
- `models/User.php` - database queries for user accounts
- `controllers/AuthController.php` - registration, login, and logout logic
- `views/` - website pages
- `assets/css/style.css` - page styling
- `database.sql` - creates the database and users table

## Run locally
1. Put this folder in `C:\\xampp\\htdocs\\sunson_solar_mvc`.
2. Start Apache and MySQL/MariaDB in XAMPP.
3. Import `database.sql` in phpMyAdmin.
4. Check `config/database.php`. It currently uses port `3307`, based on the port shown in the local MariaDB terminal. Change it if your database server uses another port.
5. Visit `http://localhost/sunson_solar_mvc/`.

Passwords are stored as hashes. SQL queries use prepared statements. Public registration creates customer accounts only.

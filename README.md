# Sun Son Solar — Registration and Login

A beginner-friendly PHP + MySQL project for XAMPP, organized into config, models, controllers, views, and assets.

## Folder structure

- `config/database.php` — MySQL connection settings
- `models/User.php` — database queries for user accounts
- `controllers/AuthController.php` — registration, login, and logout logic
- `views/` — page templates
- `assets/css/style.css` — styling
- `index.php` — page routing
- `database.sql` — database and users table

## Run locally with XAMPP

1. Put this folder inside `C:\\xampp\\htdocs\\sunson_solar_mvc`.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`.
4. Choose **Import**, select `database.sql`, and import it.
5. Open `config/database.php`. The default settings are `root` with a blank password. Change `$dbPassword` if your local MySQL root account uses a password.
6. Visit `http://localhost/sunson_solar_mvc/`.

## GitHub setup

1. Create a **private** repository on GitHub named `sunson-solar-login-registration`.
2. Do not upload real customer data, passwords, or database credentials.
3. In VS Code, open this project folder.
4. Open **Terminal → New Terminal**, then run:

```bash
git init
git add .
git commit -m "Initial Sun Son Solar registration and login"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/sunson-solar-login-registration.git
git push -u origin main
```

Replace `YOUR-USERNAME` with your GitHub username. If Git asks you to sign in, follow the browser sign-in prompt. Never put your GitHub password or access token directly into source files.

If the GitHub repository already has a README or other files, creating the repo without those starter files avoids an initial push conflict.

## Security notes

- Public registration creates **customer** accounts only.
- Passwords are saved using PHP `password_hash()` and checked using `password_verify()`.
- Database access uses prepared statements.
- Do not commit real customer data or credentials. For a deployed app, use environment variables and production-grade configuration.

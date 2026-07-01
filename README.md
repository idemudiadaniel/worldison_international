# Worldison International

## Local Startup

This project is a PHP app served from `public/`.

### Requirements

- PHP 8.3 CLI
- PHP MySQL extension (`php-mysql` or `php8.3-mysql`)
- MariaDB/MySQL server

### Recommended startup steps

1. Ensure MySQL is running:

```bash
sudo service mysql start
```

2. Create the app database (optional if already created):

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS worldison CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'worldison_user'@'localhost' IDENTIFIED BY 'worldison_pass'; GRANT ALL PRIVILEGES ON worldison.* TO 'worldison_user'@'localhost'; FLUSH PRIVILEGES;"
```

3. Start MySQL before launching the PHP server:

```bash
sudo service mysql start
sudo service mysql status --no-pager
```

4. Start the PHP built-in server from the project root:

```bash
cd /workspaces/worldison_international
php8.3 -S 0.0.0.0:8001 -t public
```

If `php8.3` is not installed, use `php -v` to confirm the default CLI version and install PHP 8.3 if needed.

If `php8.3` is not installed, use `php -v` to confirm the default CLI version and install PHP 8.3 if needed.

### Database config

The app uses `inc/db.php` for connection settings. It defaults to:

- host: `127.0.0.1`
- user: `worldison_user`
- password: `worldison_pass`
- database: `worldison`
- port: `3306`

You can override these values with environment variables before launching the server:

```bash
export DB_HOST=127.0.0.1
export DB_USER=worldison_user
export DB_PASS=worldison_pass
export DB_NAME=worldison
export DB_PORT=3306
```

### Access

Open the app in your browser at:

```text
http://127.0.0.1:8001
```

### Notes

- The app entrypoint is `public/index.php`.
- If port `8001` is already in use, choose a different port and update the startup command.
- `inc/db.php` must exist for the app to connect to the database correctly.

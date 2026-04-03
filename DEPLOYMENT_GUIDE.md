# Alumni Management System - Deployment Guide

## Quick Start (Development)

### Prerequisites

- PHP 8.0+ installed
- MySQL 8.0+ installed and running
- Composer installed (or use the bundled `composer.phar`)

### Step 1: Setup Database

1. Open MySQL client (e.g., MySQL Workbench, phpMyAdmin, or command line):

   ```bash
   mysql -u root -p
   ```

2. Run the schema file to create database and tables:

   ```sql
   SOURCE C:/Users/Denver/OneDrive/Desktop/Alumni_system/database/schema.sql;
   ```

   Or import via command line:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

### Step 2: Configure Backend

1. Navigate to the server directory:

   ```bash
   cd server
   ```

2. Edit `.env` file with your database credentials:

   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=alumni_system
   DB_USERNAME=root
   DB_PASSWORD=your_password_here
   ```

3. Install PHP dependencies:

   ```bash
   # If composer is installed globally:
   composer install

   # Or use bundled composer.phar:
   php ../composer.phar install
   ```

### Step 3: Start the Servers

**Open TWO terminal windows:**

**Terminal 1 - Backend Server (Port 8000):**

```bash
cd C:\Users\Denver\OneDrive\Desktop\Alumni_system
php -S localhost:8000 -t server server/index.php
```

**Terminal 2 - Frontend Server (Port 3000):**

```bash
cd C:\Users\Denver\OneDrive\Desktop\Alumni_system
php -S localhost:3000 -t client
```

### Step 4: Access the System

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api/v1/

### Default Admin Credentials

After importing the database:
- **Email**: `admin@minsu.edu.ph`
- **Password**: `password`

⚠️ **Change this password immediately after first login!**

---

## Production Deployment

### Using Apache (XAMPP/WAMP/LAMP)

1. Copy the entire project to your web server's document root (e.g., `htdocs/alumni`)

2. Create virtual hosts or use `.htaccess` for routing

3. Update `client/assets/js/api.js` baseUrl:
   ```javascript
   baseUrl: "https://your-domain.com/api/v1";
   ```

### Using Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/alumni/client;
    index index.html;

    # Frontend SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API routing
    location /api/ {
        alias /var/www/alumni/server/;
        fastcgi_pass unix:/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME /var/www/alumni/server/index.php;
        include fastcgi_params;
    }

    # File uploads
    location /uploads/ {
        alias /var/www/alumni/server/uploads/;
    }

    client_max_body_size 10M;
}
```

---

## Troubleshooting

### "Network Error" on login

1. Ensure both servers are running (ports 8000 and 3000)
2. Check browser console (F12) for CORS errors
3. Verify `api.js` baseUrl matches backend server

### Database connection failed

1. Check MySQL is running: `net start mysql` (Windows)
2. Verify credentials in `server/.env`
3. Ensure database `alumni_system` exists

### 404 errors on pages

1. Make sure you're accessing via `http://localhost:3000`
2. Routes use hash-based routing (e.g., `/#/login`)

### PHP errors

1. Enable error display in `server/index.php`:
   ```php
   ini_set('display_errors', 1);
   ```
2. Check PHP error log

---

## Security Checklist

| Feature                        | Status |
| ------------------------------ | ------ |
| JWT authentication             | ✅     |
| Password hashing (bcrypt)      | ✅     |
| CORS headers                   | ✅     |
| Rate limiting                  | ✅     |
| Input validation               | ✅     |
| SQL injection prevention (PDO) | ✅     |
| XSS prevention                 | ✅     |
| CSRF protection                | ✅     |
| File upload validation         | ✅     |

---

## Backup Strategy

```bash
# Database backup
mysqldump -u root -p alumni_system > backup_$(date +%Y%m%d).sql

# Uploads backup
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz server/uploads/
```

---

## Gmail App Password Setup (for email features)

1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Go to App passwords → Select "Mail" → Generate
4. Copy the 16-character password into `MAIL_PASSWORD` in `server/.env`

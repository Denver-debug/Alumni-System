# Deployment Guide

## Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Copy `server/.env.example` to `server/.env`
- [ ] Configure database credentials in `server/.env`
- [ ] Set appropriate `APP_ENV` (production)
- [ ] Configure email settings (SMTP)
- [ ] Set secure session secrets

### 2. Database Setup

#### For Hostinger (phpMyAdmin):

**Step 1: Pre-Import Setup**
1. Login to phpMyAdmin on Hostinger
2. Select your database (or create a new one)
3. Go to the **SQL** tab
4. Copy and paste the content of `database/00_pre_import.sql`
5. Click **Go** to execute

**Step 2: Import Main Schema**
1. Go to the **Import** tab
2. Click **Choose File** and select `database/schema.sql`
3. Scroll down and click **Go**
4. Wait for import to complete (may take 30-60 seconds)

**Step 3: Post-Import Verification**
1. Go back to the **SQL** tab
2. Copy and paste the content of `database/01_post_import.sql`
3. Click **Go** to execute
4. Verify you see 35+ tables listed

**Step 4: Run Migrations**
1. Still in the **SQL** tab, import each migration file in order:
2. Copy content from `database/migrations/001_add_multi_campus_support_SAFE.sql` → Execute
3. Copy content from `database/migrations/002_verification_system.sql` → Execute
4. Copy content from `database/migrations/003_analytics_views.sql` → Execute
5. Copy content from `database/migrations/004_security_tables.sql` → Execute
6. Copy content from `database/migrations/005_messaging_enhancements.sql` → Execute
7. Copy content from `database/migrations/006_add_verification_email_templates.sql` → Execute

#### For Command Line (Local/VPS):

```bash
# Pre-import setup
mysql -u username -p database_name < database/00_pre_import.sql

# Import the main schema
mysql -u username -p database_name < database/schema.sql

# Post-import verification
mysql -u username -p database_name < database/01_post_import.sql

# Run migrations in order
mysql -u username -p database_name < database/migrations/001_add_multi_campus_support_SAFE.sql
mysql -u username -p database_name < database/migrations/002_verification_system.sql
mysql -u username -p database_name < database/migrations/003_analytics_views.sql
mysql -u username -p database_name < database/migrations/004_security_tables.sql
mysql -u username -p database_name < database/migrations/005_messaging_enhancements.sql
mysql -u username -p database_name < database/migrations/006_add_verification_email_templates.sql
```

### 3. Dependencies
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# If using Node.js for build tools
npm install --production
```

### 4. File Permissions
```bash
# Make uploads directory writable
chmod 755 server/uploads
chown www-data:www-data server/uploads

# Secure sensitive files
chmod 600 server/.env
```

### 5. Web Server Configuration

#### Apache (.htaccess already configured)
- Ensure `mod_rewrite` is enabled
- Verify `.htaccess` files are being read
- Check `AllowOverride All` in Apache config

#### Nginx (example configuration)
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/alumni_system/client;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location /server {
        alias /path/to/alumni_system/server;
    }
}
```

## Security Hardening

### 1. Remove Development Files
All test files, development configs, and IDE settings have been removed.

### 2. Secure Sensitive Directories
```bash
# Prevent direct access to server directory
# (Already configured in .htaccess)
```

### 3. Enable HTTPS
- Obtain SSL certificate (Let's Encrypt recommended)
- Force HTTPS redirects
- Update `APP_URL` in `.env` to use https://

### 4. Database Security
- Use strong passwords
- Limit database user privileges
- Enable MySQL SSL connections if possible

## Post-Deployment

### 1. Verify Installation
- [ ] Access the application homepage
- [ ] Test user registration
- [ ] Test admin login
- [ ] Verify email sending
- [ ] Check file uploads
- [ ] Test database connections

### 2. Create Admin User
```sql
-- Run this SQL to create initial admin user
INSERT INTO users (email, password, role, status, created_at)
VALUES ('admin@yourdomain.com', '$2y$10$...', 'admin', 'active', NOW());
```

### 3. Configure Backups
- Set up automated database backups
- Configure file backup for uploads directory
- Test restore procedures

### 4. Monitoring
- Set up error logging
- Configure uptime monitoring
- Enable performance monitoring

## Troubleshooting

### Database Connection Issues
- Verify credentials in `server/.env`
- Check database server is running
- Ensure database user has proper permissions

### File Upload Issues
- Check directory permissions (755 for directories, 644 for files)
- Verify `upload_max_filesize` in php.ini
- Check `post_max_size` in php.ini

### Email Not Sending
- Verify SMTP credentials in `.env`
- Check firewall allows outbound SMTP connections
- Test with a simple mail script

## Maintenance

### Regular Tasks
- Monitor disk space (especially uploads directory)
- Review error logs weekly
- Update dependencies monthly
- Backup database daily

### Updates
```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run any new migrations
# Check database/migrations/ for new files
```

## Support

For issues or questions:
- Check logs in `logs/` directory
- Review error messages in browser console
- Check PHP error logs
- Verify database connection and queries

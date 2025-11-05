# TripEase Installation Guide

Complete step-by-step guide to install and configure TripEase on your local or production server.

## 📋 System Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: 2.4 or higher (with mod_rewrite enabled)
- **RAM**: 512 MB minimum
- **Disk Space**: 500 MB minimum

### Recommended Requirements
- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher
- **RAM**: 2 GB or more
- **Disk Space**: 2 GB or more

### PHP Extensions Required
- PDO
- PDO_MySQL
- mbstring
- openssl
- fileinfo
- GD or Imagick (for image processing)
- JSON
- cURL

## 🚀 Installation Steps

### Step 1: Download/Clone the Project

#### Option A: Using Git
```bash
cd C:\xampp\htdocs
git clone https://github.com/yourusername/tripease.git TripEase
```

#### Option B: Manual Download
1. Download the ZIP file
2. Extract to `C:\xampp\htdocs\TripEase`

### Step 2: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service
4. Verify both services are running (green indicators)

### Step 3: Create Database

#### Option A: Using phpMyAdmin (Recommended for Beginners)
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar
3. Database name: `tripease`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Select the `tripease` database
7. Click on "Import" tab
8. Choose file: `C:\xampp\htdocs\TripEase\database\schema.sql`
9. Click "Go" at the bottom
10. Wait for success message

#### Option B: Using MySQL Command Line
```bash
# Open Command Prompt
cd C:\xampp\mysql\bin

# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE tripease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Use the database
USE tripease;

# Import schema
SOURCE C:/xampp/htdocs/TripEase/database/schema.sql;

# Exit
EXIT;
```

### Step 4: Configure Application

1. Open `C:\xampp\htdocs\TripEase\config\config.php`
2. Update database credentials (if different from defaults):

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'tripease');
```

3. Update application URL (if needed):

```php
define('APP_URL', 'http://localhost/TripEase');
```

### Step 5: Set Directory Permissions

Ensure the following directories are writable:

**Windows (XAMPP):**
- Right-click on `uploads` folder → Properties → Security
- Give "Full Control" to "Users" group

**Linux/Mac:**
```bash
chmod -R 755 uploads/
chmod -R 755 uploads/users/
chmod -R 755 uploads/providers/
chmod -R 755 uploads/listings/
```

### Step 6: Verify Installation

1. Open browser and navigate to: `http://localhost/TripEase`
2. You should see the TripEase homepage
3. If you see errors, check the troubleshooting section below

### Step 7: Test Admin Access

1. Go to: `http://localhost/TripEase/login.php`
2. Select "Admin" as login type
3. Use default credentials:
   - **Email**: `admin@tripease.com`
   - **Password**: `password`
4. **IMPORTANT**: Change the admin password immediately!

## 🔧 Configuration Options

### Email Configuration (Optional)

For email notifications, update in `config/config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@tripease.com');
define('SMTP_FROM_NAME', 'TripEase');
```

### Upload Limits

To increase file upload size, edit `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

Restart Apache after changes.

### URL Rewriting

If clean URLs are not working:

1. Enable mod_rewrite in Apache:
   - Open `C:\xampp\apache\conf\httpd.conf`
   - Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Find `AllowOverride None` and change to `AllowOverride All`
   - Restart Apache

## 🧪 Testing the Installation

### Test User Registration
1. Go to: `http://localhost/TripEase/register.php`
2. Create a test user account
3. Login with the new credentials

### Test Provider Registration
1. Go to: `http://localhost/TripEase/provider/register.php`
2. Create a test provider account
3. Login and verify dashboard access

### Test Search Functionality
1. Go to: `http://localhost/TripEase/search.php`
2. Try different filters
3. Verify listings display correctly

## 🐛 Troubleshooting

### Database Connection Error

**Error**: "Database connection failed"

**Solutions**:
1. Verify MySQL is running in XAMPP
2. Check database credentials in `config/config.php`
3. Ensure database `tripease` exists
4. Test connection:
   ```php
   <?php
   $conn = new mysqli('localhost', 'root', '', 'tripease');
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   echo "Connected successfully";
   ?>
   ```

### Page Not Found (404)

**Solutions**:
1. Check Apache is running
2. Verify URL: `http://localhost/TripEase` (case-sensitive)
3. Check `.htaccess` file exists
4. Enable mod_rewrite (see URL Rewriting section)

### Blank White Page

**Solutions**:
1. Enable error display in `config/config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check PHP error log: `C:\xampp\php\logs\php_error_log`
3. Verify PHP version: `php -v` (must be 7.4+)

### Upload Directory Error

**Error**: "Failed to create upload directory"

**Solutions**:
1. Manually create directories:
   - `uploads/`
   - `uploads/users/`
   - `uploads/providers/`
   - `uploads/listings/`
2. Set proper permissions (see Step 5)
3. Check Apache has write access

### CSS/JS Not Loading

**Solutions**:
1. Clear browser cache (Ctrl + F5)
2. Check file paths in `config/config.php`
3. Verify `assets/` folder exists
4. Check browser console (F12) for errors

### Session Issues

**Solutions**:
1. Check PHP session directory is writable
2. Clear browser cookies
3. Verify `session_start()` is called in `config.php`

## 🔒 Security Recommendations

### For Production Deployment

1. **Change Default Credentials**
   - Change admin password immediately
   - Use strong passwords (12+ characters)

2. **Disable Error Display**
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **Enable HTTPS**
   - Get SSL certificate
   - Uncomment HTTPS redirect in `.htaccess`

4. **Update Database Password**
   - Set strong MySQL root password
   - Create dedicated database user

5. **File Permissions**
   - Set restrictive permissions on config files
   - Protect sensitive directories

6. **Regular Backups**
   - Backup database daily
   - Backup uploaded files weekly

7. **Update Dependencies**
   - Keep PHP updated
   - Update Bootstrap/jQuery if needed

## 📊 Database Management

### Backup Database

**Using phpMyAdmin**:
1. Go to phpMyAdmin
2. Select `tripease` database
3. Click "Export"
4. Choose "Quick" method
5. Click "Go"

**Using Command Line**:
```bash
cd C:\xampp\mysql\bin
mysqldump -u root -p tripease > backup.sql
```

### Restore Database

**Using phpMyAdmin**:
1. Go to phpMyAdmin
2. Select `tripease` database
3. Click "Import"
4. Choose backup file
5. Click "Go"

**Using Command Line**:
```bash
cd C:\xampp\mysql\bin
mysql -u root -p tripease < backup.sql
```

## 🚀 Performance Optimization

### Enable Caching
1. Enable OPcache in `php.ini`
2. Use browser caching (already in `.htaccess`)
3. Optimize images before upload

### Database Optimization
```sql
-- Optimize all tables
OPTIMIZE TABLE users, providers, listings, bookings, reviews;

-- Add indexes if needed
CREATE INDEX idx_listing_location ON listings(location);
```

### Apache Optimization
1. Enable compression (already in `.htaccess`)
2. Enable KeepAlive in `httpd.conf`
3. Adjust MaxClients based on RAM

## 📞 Support

If you encounter issues not covered here:

1. Check error logs:
   - Apache: `C:\xampp\apache\logs\error.log`
   - PHP: `C:\xampp\php\logs\php_error_log`

2. Enable debugging in `config/config.php`

3. Contact support:
   - Email: support@tripease.com
   - GitHub Issues: [Create Issue](https://github.com/yourusername/tripease/issues)

## ✅ Post-Installation Checklist

- [ ] Database created and imported successfully
- [ ] Application accessible at localhost
- [ ] Admin login working
- [ ] User registration working
- [ ] Provider registration working
- [ ] Search functionality working
- [ ] File uploads working
- [ ] Admin password changed
- [ ] Error logging configured
- [ ] Backup system in place

## 🎉 Next Steps

1. **Customize Settings**
   - Update site name and logo
   - Configure email settings
   - Set platform policies

2. **Add Content**
   - Create test listings
   - Add sample images
   - Configure categories

3. **Test Features**
   - Test booking flow
   - Test review system
   - Test admin functions

4. **Deploy to Production**
   - Follow security recommendations
   - Set up SSL certificate
   - Configure domain name

---

**Congratulations!** TripEase is now installed and ready to use. 🎊

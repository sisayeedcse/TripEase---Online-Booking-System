# TripEase Quick Start Guide

Get TripEase up and running in 5 minutes!

## ⚡ Quick Installation

### 1. Prerequisites
- XAMPP installed and running
- Web browser

### 2. Setup (5 steps)

```bash
# Step 1: Extract files to XAMPP
Extract TripEase folder to: C:\xampp\htdocs\

# Step 2: Start services
Open XAMPP Control Panel
Start Apache ✓
Start MySQL ✓

# Step 3: Create database
Open: http://localhost/phpmyadmin
Create database: tripease
Import: database/schema.sql

# Step 4: Access application
Open: http://localhost/TripEase

# Step 5: Login as admin
URL: http://localhost/TripEase/login.php
Email: admin@tripease.com
Password: password
```

## 🎯 Quick Test

### Test User Flow
1. **Register**: `/register.php` → Create account
2. **Login**: `/login.php` → Login as user
3. **Search**: `/search.php` → Browse listings
4. **Book**: Click listing → Book now

### Test Provider Flow
1. **Register**: `/provider/register.php` → Create provider account
2. **Login**: `/login.php` → Login as provider
3. **Add Listing**: Dashboard → Add new listing
4. **Manage**: View bookings and availability

### Test Admin Flow
1. **Login**: `/login.php` → Login as admin
2. **Dashboard**: View statistics
3. **Manage**: Users, providers, listings
4. **Approve**: Verify providers and listings

## 🔑 Default Accounts

### Admin
- **Email**: admin@tripease.com
- **Password**: password
- **Access**: Full system control

### Test User (Create your own)
- Register at `/register.php`
- Use for booking tests

### Test Provider (Create your own)
- Register at `/provider/register.php`
- Requires admin verification

## 📁 Important Files

```
TripEase/
├── index.php           # Homepage
├── login.php           # Login page
├── register.php        # User registration
├── search.php          # Search listings
├── config/
│   ├── config.php      # Main configuration
│   └── database.php    # Database connection
├── database/
│   └── schema.sql      # Database structure
└── uploads/            # User uploads
```

## ⚙️ Quick Configuration

### Update Base URL
Edit `config/config.php`:
```php
define('APP_URL', 'http://localhost/TripEase');
```

### Database Settings
Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tripease');
```

## 🐛 Quick Fixes

### Can't access site?
- Check Apache is running
- Try: `http://localhost/TripEase/index.php`

### Database error?
- Verify MySQL is running
- Check database `tripease` exists
- Verify credentials in `config.php`

### Blank page?
- Enable errors in `config.php`:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```

### Upload errors?
- Create `uploads/` folder
- Set folder permissions to writable

## 📱 Access Points

### Public Pages
- **Home**: `/index.php`
- **Search**: `/search.php`
- **About**: `/about.php`
- **Contact**: `/contact.php`

### User Pages
- **Login**: `/login.php`
- **Register**: `/register.php`
- **Dashboard**: `/user/dashboard.php`
- **Bookings**: `/user/bookings.php`
- **Profile**: `/user/profile.php`

### Provider Pages
- **Register**: `/provider/register.php`
- **Dashboard**: `/provider/dashboard.php`
- **Listings**: `/provider/listings.php`
- **Bookings**: `/provider/bookings.php`

### Admin Pages
- **Login**: `/login.php` (select Admin)
- **Dashboard**: `/admin/dashboard.php`
- **Users**: `/admin/users.php`
- **Providers**: `/admin/providers.php`
- **Listings**: `/admin/listings.php`
- **Bookings**: `/admin/bookings.php`

## 🎨 Customization

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
    --primary-color: #2196F3;  /* Blue */
    --secondary-color: #4CAF50; /* Green */
}
```

### Change Logo
Replace in `assets/images/`:
- `logo.png` (200x50px recommended)
- `favicon.png` (32x32px)

### Update Site Name
Edit `config/config.php`:
```php
define('APP_NAME', 'Your Site Name');
```

## 📊 Sample Data

### Create Test Listings
1. Login as provider
2. Go to "Add Listing"
3. Fill in details:
   - Title: "Luxury Boat Tour"
   - Category: Boat
   - Location: Cox's Bazar
   - Price: 5000
   - Capacity: 10

### Add Sample Images
Place images in:
- `uploads/listings/` for listing images
- `uploads/users/` for user avatars
- `uploads/providers/` for provider logos

## 🚀 Next Steps

1. **Change admin password** (Security!)
2. **Create test accounts** (User & Provider)
3. **Add sample listings** (For testing)
4. **Test booking flow** (End-to-end)
5. **Customize design** (Colors, logo)
6. **Configure email** (For notifications)

## 💡 Tips

- Use Chrome DevTools (F12) for debugging
- Check browser console for JavaScript errors
- Monitor Apache/PHP error logs
- Clear browser cache if CSS doesn't update
- Use phpMyAdmin to view database

## 📞 Need Help?

- **Full Guide**: See `INSTALLATION.md`
- **README**: See `README.md`
- **Support**: support@tripease.com

---

**Ready to go!** Start exploring TripEase! 🎉

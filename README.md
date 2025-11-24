# TripEase - Local Travel Booking Platform

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive web-based platform connecting travelers with local service providers for boat rentals and room accommodations. Built with modern PHP, MySQL, and responsive design principles.

## 📋 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [User Roles & Access](#user-roles--access)
- [Security Features](#security-features)
- [Development Guide](#development-guide)
- [Testing](#testing)
- [Deployment](#deployment)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

---

## Overview

**TripEase** is a full-stack web application designed to connect travelers with local service providers offering boat rentals and room accommodations. The platform provides a seamless booking experience with role-based access control, secure authentication, and an intuitive user interface.

### Project Goals

- Simplify the process of discovering and booking local travel services
- Provide a secure, transparent platform for service providers to list their offerings
- Enable efficient management through comprehensive admin controls
- Deliver a modern, responsive user experience across all devices

### Current Status

**Version:** 1.0.0  
**Status:** Core infrastructure complete - Dashboard implementations in progress

**Completed Components:**

- ✅ Database architecture with 11 tables and 3 analytical views
- ✅ Authentication system with multi-role support (User, Provider, Admin)
- ✅ Responsive frontend with modern UI/UX design
- ✅ Search and filtering functionality
- ✅ Security implementation (SQL injection prevention, XSS protection, password hashing)
- ✅ Query builder for database operations
- ✅ File upload system with validation

**In Progress:**

- 🚧 User dashboard and booking management
- 🚧 Provider dashboard and listing management
- 🚧 Admin panel with comprehensive controls
- 🚧 Review and rating system
- 🚧 Email notification system

---

## Key Features

### Multi-Role Architecture

The platform supports three distinct user roles, each with dedicated dashboards and functionality:

#### 👤 Travelers (Users)

- **Authentication:** Secure registration and login with session management
- **Search & Discovery:** Advanced filtering by location, category, price, and date availability
- **Booking System:** Real-time availability checking, instant booking confirmation, and cancellation management
- **Reviews:** 5-star rating system with detailed review submission and management
- **Profile Management:** Personal information updates, profile image upload, and password management
- **Notifications:** Real-time updates on bookings, confirmations, and special offers

#### 🏪 Service Providers

- **Business Profile:** Comprehensive profile management with verification system
- **Listing Management:** Create, edit, and manage multiple boat or room listings
- **Availability Control:** Calendar-based availability management with bulk operations
- **Booking Dashboard:** View and manage incoming bookings, accept/decline requests
- **Analytics:** Revenue tracking, performance metrics, and booking statistics
- **Communication:** Direct interaction with travelers and review response system

#### 👨‍💼 Administrators

- **User Management:** Complete oversight of user and provider accounts
- **Content Moderation:** Approve/reject listings, moderate reviews, enforce policies
- **Platform Analytics:** Comprehensive reports on bookings, revenue, and user engagement
- **System Configuration:** Manage site settings, email templates, and platform policies
- **Activity Monitoring:** Track system activity, view logs, and manage security
- **Financial Oversight:** Commission tracking, revenue reports, and payout management

### Technical Features

- **Responsive Design:** Mobile-first approach with adaptive layouts for all screen sizes
- **Security:** SQL injection prevention, XSS protection, bcrypt password hashing, CSRF tokens
- **Performance:** Optimized database queries with indexing, lazy loading, and browser caching
- **Scalability:** Modular architecture with query builder for easy database operations
- **File Management:** Secure file upload system with type validation and size limits
- **Activity Logging:** Comprehensive tracking of user actions for audit trails

---

## Technology Stack

### Backend

- **PHP 7.4+** - Server-side scripting and business logic
- **MySQL 5.7+** - Relational database management
- **PDO (PHP Data Objects)** - Database abstraction layer with prepared statements
- **Apache 2.4+** - Web server (XAMPP/LAMP stack)

### Frontend

- **HTML5** - Semantic markup and structure
- **CSS3** - Modern styling with custom properties and animations
- **Bootstrap 5.3** - Responsive UI framework
- **JavaScript (ES6+)** - Client-side interactivity and validation
- **Font Awesome 6** - Comprehensive icon library

### Design & Architecture

- **MVC-Inspired Pattern** - Separation of concerns between views, logic, and data
- **Query Builder Pattern** - Fluent interface for database operations
- **Singleton Pattern** - Database connection management
- **Repository Pattern** - Data access abstraction through the Auth class

### Development Tools

- **XAMPP** - Local development environment
- **phpMyAdmin** - Database administration
- **Git** - Version control
- **VS Code** - Recommended IDE

### Security Implementation

- **Password Hashing** - bcrypt with cost factor 10
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - Input sanitization and HTML entity encoding
- **CSRF Protection** - Token-based validation (structure ready)
- **Session Management** - Secure session configuration with timeouts

---

## Project Structure

```
TripEase/
├── admin/                      # Administrative panel
│   ├── bookings.php           # Booking management
│   ├── dashboard.php          # Admin dashboard
│   ├── listings.php           # Listing approval/moderation
│   ├── providers.php          # Provider verification
│   ├── reports.php            # Analytics and reports
│   ├── reviews.php            # Review moderation
│   ├── settings.php           # Platform configuration
│   ├── sidebar.php            # Admin navigation
│   └── users.php              # User management
│
├── assets/                     # Static resources
│   ├── css/
│   │   ├── modern-ui.css      # Modern UI components
│   │   ├── responsive.css     # Responsive breakpoints
│   │   └── style.css          # Base styles
│   └── js/
│       ├── main.js            # Main JavaScript
│       └── modern-ui.js       # UI interactions
│
├── config/                     # Configuration files
│   ├── config.php             # Application constants and helpers
│   └── database.php           # Database connection and query builder
│
├── database/                   # Database management
│   ├── demo-data.sql          # Sample data for testing
│   └── schema.sql             # Complete database schema
│
├── includes/                   # Shared components
│   ├── Auth.php               # Authentication class
│   ├── footer.php             # Common footer template
│   └── header.php             # Common header template
│
├── provider/                   # Service provider dashboard
│   ├── add-listing.php        # Create new listing
│   ├── bookings.php           # Manage bookings
│   ├── dashboard.php          # Provider overview
│   ├── edit-listing.php       # Edit existing listing
│   ├── listings.php           # View all listings
│   ├── profile.php            # Profile management
│   ├── register.php           # Provider registration
│   └── sidebar.php            # Provider navigation
│
├── uploads/                    # User-uploaded content
│   ├── listings/              # Listing images
│   ├── providers/             # Provider profile images
│   ├── screenshots/           # System screenshots
│   └── users/                 # User profile images
│
├── user/                       # Traveler dashboard
│   ├── add-review.php         # Submit review
│   ├── booking-details.php    # View booking details
│   ├── bookings.php           # Booking history
│   ├── cancel-booking.php     # Cancel booking
│   ├── dashboard.php          # User overview
│   ├── delete-review.php      # Remove review
│   ├── edit-review.php        # Modify review
│   ├── notifications.php      # Notification center
│   ├── profile.php            # Profile management
│   ├── reviews.php            # Review management
│   └── sidebar.php            # User navigation
│
├── all mds/                    # Documentation
│   └── [Comprehensive project documentation]
│
├── .htaccess                   # Apache configuration
├── about.php                   # About page
├── booking-confirmation.php    # Booking confirmation handler
├── contact.php                 # Contact form
├── index.php                   # Homepage
├── listing-details.php         # Listing detail view
├── login.php                   # Login page
├── logout.php                  # Logout handler
├── process-booking.php         # Booking processor
├── README.md                   # This file
├── register.php                # User registration
└── search.php                  # Search and filter page
```

### Architecture Overview

The application follows a modular, role-based architecture with clear separation of concerns:

- **Public Layer:** Homepage, search, listing details, authentication pages
- **User Layer:** Traveler dashboard, bookings, reviews, profile management
- **Provider Layer:** Business dashboard, listing management, booking handling
- **Admin Layer:** Platform management, user moderation, analytics
- **Core Layer:** Configuration, authentication, database operations, shared components

---

## Prerequisites

Before installing TripEase, ensure you have the following:

### Required Software

- **XAMPP** (or equivalent LAMP/WAMP stack)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher
- **Modern Web Browser** (Chrome, Firefox, Safari, Edge - latest versions)

### Recommended

- **Git** - For version control
- **Composer** - For future PHP dependency management (optional)
- **Code Editor** - VS Code, PhpStorm, or Sublime Text

### System Requirements

- **RAM:** Minimum 2GB (4GB+ recommended)
- **Disk Space:** At least 500MB free space
- **Operating System:** Windows 10/11, macOS 10.14+, or Linux (Ubuntu 18.04+)

---

## Installation

Follow these steps to set up TripEase on your local development environment:

### Step 1: Clone the Repository

```bash
# Using Git
git clone https://github.com/sisayeedcse/TripEase---Online-Booking-System.git

# Or download and extract the ZIP file from GitHub
```

### Step 2: Move to Web Server Directory

**Windows (XAMPP):**

```powershell
# Copy the project to XAMPP's htdocs directory
Copy-Item -Path "TripEase---Online-Booking-System" -Destination "C:\xampp\htdocs\TripEase" -Recurse
```

**macOS (XAMPP):**

```bash
sudo cp -r TripEase---Online-Booking-System /Applications/XAMPP/htdocs/TripEase
```

**Linux (LAMP):**

```bash
sudo cp -r TripEase---Online-Booking-System /opt/lampp/htdocs/TripEase
# Or for standalone Apache
sudo cp -r TripEase---Online-Booking-System /var/www/html/TripEase
```

### Step 3: Start Web Server Services

1. Open **XAMPP Control Panel**
2. Start **Apache** service
3. Start **MySQL** service
4. Ensure both services show "Running" status

### Step 4: Create Database

**Option A: Using phpMyAdmin (Recommended)**

1. Open your browser and navigate to `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar to create a new database
3. Enter database name: `tripease`
4. Select collation: `utf8mb4_unicode_ci`
5. Click **"Create"**
6. Select the newly created `tripease` database
7. Click **"Import"** tab
8. Click **"Choose File"** and select `database/schema.sql` from the project directory
9. Scroll down and click **"Go"**
10. Wait for the success message

**Option B: Using Command Line**

```bash
# Navigate to project directory
cd C:\xampp\htdocs\TripEase  # Windows
cd /Applications/XAMPP/htdocs/TripEase  # macOS
cd /opt/lampp/htdocs/TripEase  # Linux

# Import database
mysql -u root -p < database/schema.sql
# When prompted, press Enter (default XAMPP has no password)
```

### Step 5: Configure Application

1. Open `config/config.php` in your code editor
2. Update database credentials if necessary:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Default XAMPP password is empty
define('DB_NAME', 'tripease');
```

3. Update the base URL to match your setup:

```php
// Application Configuration
define('APP_URL', 'http://localhost/TripEase');
```

### Step 6: Set File Permissions (Linux/macOS only)

```bash
# Navigate to project directory
cd /opt/lampp/htdocs/TripEase  # or appropriate path

# Set directory permissions
chmod -R 755 .

# Set upload directory permissions
chmod -R 777 uploads/
```

### Step 7: Verify Installation

1. Open your web browser
2. Navigate to `http://localhost/TripEase`
3. You should see the TripEase homepage
4. Test the login functionality with default admin credentials (see below)

### Default Credentials

**Administrator Account:**

- **Email:** `admin@tripease.com`
- **Password:** `password`

> ⚠️ **IMPORTANT:** Change the default admin password immediately after first login for security purposes!

### Troubleshooting Common Issues

**Issue: Database connection error**

- Verify MySQL service is running in XAMPP
- Check database credentials in `config/config.php`
- Ensure `tripease` database exists

**Issue: 404 Not Found errors**

- Check that `.htaccess` file exists in the root directory
- Verify Apache `mod_rewrite` is enabled
- Ensure project is in the correct directory (`htdocs/TripEase`)

**Issue: Upload permission denied**

- Check that `uploads/` directory exists
- Verify write permissions on uploads directory
- On Windows, check folder isn't set to read-only

**Issue: Blank page or PHP errors**

- Check PHP version is 7.4 or higher
- Enable error display in `php.ini` for debugging
- Check Apache error logs for details

---

## Configuration

### Application Settings

Edit `config/config.php` to customize the platform:

#### General Settings

```php
define('APP_NAME', 'TripEase');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/TripEase');
```

#### Timezone & Locale

```php
date_default_timezone_set('Asia/Dhaka');  // Change to your timezone
define('CURRENCY', 'BDT');
define('CURRENCY_SYMBOL', '৳');
```

#### File Upload Settings

```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB max file size
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
```

#### Booking Settings

```php
define('CANCELLATION_HOURS', 24);    // Hours before booking for cancellation
define('MAX_BOOKING_DAYS', 30);      // Maximum advance booking days
define('MIN_BOOKING_DAYS', 1);       // Minimum booking duration
```

#### Pagination

```php
define('ITEMS_PER_PAGE', 12);        // Items per page in search results
define('ADMIN_ITEMS_PER_PAGE', 20);  // Items per page in admin panel
```

#### Security Settings

```php
define('PASSWORD_HASH_COST', 10);    // bcrypt cost factor (higher = more secure but slower)
define('SESSION_LIFETIME', 3600 * 24);  // Session lifetime in seconds (24 hours)
define('RESET_TOKEN_EXPIRY', 3600);  // Password reset token expiry (1 hour)
```

### Email Configuration (Future Implementation)

SMTP settings are defined but not yet implemented:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@tripease.com');
define('SMTP_FROM_NAME', 'TripEase');
```

### Apache Configuration

The `.htaccess` file contains important security and routing rules:

```apache
# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Disable directory browsing
Options -Indexes

# URL rewriting (if implementing clean URLs)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /TripEase/
</IfModule>
```

---

## Database Schema

TripEase uses a well-structured relational database with 11 tables and 3 analytical views.

### Core Tables

#### 1. Users Table

Stores traveler account information.

```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    status ENUM('active', 'blocked') DEFAULT 'active',
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 2. Providers Table

Stores service provider business accounts.

```sql
CREATE TABLE providers (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(150) NOT NULL,
    owner_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    description TEXT,
    profile_image VARCHAR(255) DEFAULT 'default-provider.png',
    address TEXT,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    status ENUM('active', 'blocked') DEFAULT 'active',
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3. Admins Table

Stores administrator accounts with role-based access.

```sql
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'moderator') DEFAULT 'moderator',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 4. Listings Table

Stores boat and room listings with details.

```sql
CREATE TABLE listings (
    listing_id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('boat', 'room') NOT NULL,
    location VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    price_unit ENUM('hour', 'night', 'day') DEFAULT 'hour',
    capacity INT NOT NULL,
    amenities TEXT,
    images TEXT,
    main_image VARCHAR(255),
    status ENUM('active', 'inactive', 'pending', 'rejected') DEFAULT 'pending',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);
```

#### 5. Bookings Table

Stores all booking transactions.

```sql
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    provider_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'declined') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    booking_reference VARCHAR(50) UNIQUE NOT NULL,
    special_requests TEXT,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);
```

#### 6. Reviews Table

Stores user reviews and ratings.

```sql
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    provider_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_review (booking_id)
);
```

### Supporting Tables

#### 7. Availability Table

Manages listing availability calendar.

#### 8. Notifications Table

Stores user, provider, and admin notifications.

#### 9. Contact Messages Table

Stores contact form submissions.

#### 10. Settings Table

Stores platform-wide configuration settings.

#### 11. Activity Logs Table

Tracks all user actions for security and audit purposes.

### Database Views

Three analytical views provide aggregated data:

1. **booking_stats** - Daily booking and revenue statistics
2. **listing_stats** - Per-listing performance metrics
3. **provider_stats** - Provider-level aggregated data

### Database Indexes

Strategically placed indexes optimize query performance:

- Email columns (unique indexes)
- Foreign key columns
- Status columns
- Date columns for bookings and availability
- Full-text index on listing title, description, and location

---

## User Roles & Access

TripEase implements a comprehensive role-based access control system with three distinct user types:

### 1. Travelers (Users)

**Access Level:** Standard  
**Registration:** Open to public  
**Authentication:** Email and password

**Permissions:**

- Browse and search listings
- View listing details
- Make and manage bookings
- Submit and manage reviews
- Update personal profile
- View notifications

**Restricted From:**

- Creating listings
- Accessing provider dashboard
- Accessing admin panel

### 2. Service Providers

**Access Level:** Business  
**Registration:** Requires admin verification  
**Authentication:** Email and password

**Permissions:**

- All user permissions
- Create and manage listings
- Set availability calendars
- View and manage bookings
- Access business analytics
- Respond to reviews
- Update business profile

**Restricted From:**

- Accessing other providers' data
- Accessing admin panel
- Modifying platform settings

### 3. Administrators

**Access Level:** Full platform control  
**Registration:** Manual creation only  
**Authentication:** Email and password with role assignment

**Admin Roles:**

**Super Admin:**

- Complete system access
- User and provider management
- Listing approval/rejection
- Platform configuration
- Financial reporting
- System maintenance

**Moderator:**

- Content moderation
- User support
- Review moderation
- Basic reporting

**Restricted From:**

- Deleting admin accounts (super admin only)
- Modifying critical system settings (super admin only)

### Session Management

- **Session Lifetime:** 24 hours (configurable)
- **Session Security:** HttpOnly and Secure flags enabled
- **Auto-logout:** After inactivity period
- **Concurrent Sessions:** Single session per user enforced

### Authentication Flow

```
1. User submits credentials →
2. System validates against database →
3. Password verified using bcrypt →
4. Session created with role-specific data →
5. User redirected to appropriate dashboard
```

---

## Security Features

TripEase implements multiple layers of security to protect user data and prevent common web vulnerabilities.

### Authentication & Authorization

**Password Security:**

- **Hashing:** bcrypt algorithm with cost factor 10
- **Salt:** Automatically generated unique salt per password
- **Requirements:** Minimum length enforced (configurable)
- **Reset:** Secure token-based password reset system with expiration

**Session Security:**

- **Session Hijacking Prevention:** Session ID regeneration on login
- **Session Fixation Prevention:** New session ID after privilege escalation
- **Timeout:** Automatic logout after inactivity
- **Secure Cookies:** HttpOnly and Secure flags enabled in production

### Input Validation & Sanitization

**Server-Side Validation:**

- All user inputs validated before processing
- Type checking and format validation
- Length restrictions enforced
- Whitelist-based validation for critical fields

**Sanitization Functions:**

```php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
```

### SQL Injection Prevention

**PDO Prepared Statements:**

- All database queries use parameterized statements
- User input never directly concatenated into SQL
- Automatic escaping of special characters
- Type hinting for bound parameters

**Query Builder:**

```php
// Safe query example
$user = db('users')
    ->where('email', $email)  // Automatically parameterized
    ->first();
```

### Cross-Site Scripting (XSS) Prevention

**Output Encoding:**

- All dynamic content HTML-encoded before display
- JavaScript context escaping where needed
- URL encoding for URL contexts
- CSS encoding for style attributes

**Content Security Policy (Ready for implementation):**

```apache
Header set Content-Security-Policy "default-src 'self';"
```

### Cross-Site Request Forgery (CSRF) Protection

**Token System (Structure Ready):**

- Unique token generated per session
- Tokens validated on state-changing operations
- Token regeneration after use
- Automatic token injection in forms

### File Upload Security

**Validation Layers:**

1. **File Type Validation:** MIME type checking using `finfo_file()`
2. **Extension Whitelist:** Only allowed extensions accepted
3. **File Size Limits:** Maximum 5MB (configurable)
4. **Filename Sanitization:** Random unique filenames generated
5. **Storage Location:** Uploads stored outside web root (recommended)

**Upload Function:**

```php
function upload_image($file, $directory, $prefix = '') {
    // Multiple validation checks
    // Secure filename generation
    // Safe file storage
}
```

### Database Security

**Connection Security:**

- Singleton pattern prevents connection leaks
- Connection details in separate config file
- Error messages don't expose database structure

**Data Protection:**

- Foreign key constraints maintain referential integrity
- Transactions for critical operations
- Regular automated backups recommended

### Apache Security Headers

**.htaccess Configuration:**

```apache
# Prevent directory browsing
Options -Indexes

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Prevent access to sensitive files
<FilesMatch "\.(env|ini|log|sql)$">
    Require all denied
</FilesMatch>
```

### Activity Logging

**Audit Trail:**

- All authentication attempts logged
- State-changing operations tracked
- IP addresses recorded
- Timestamp for all activities

**Log Table:**

```sql
CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('user', 'provider', 'admin'),
    user_id INT,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP
);
```

### Security Best Practices Checklist

- [ ] Change default admin password immediately
- [ ] Use HTTPS in production (SSL/TLS certificate)
- [ ] Keep PHP and MySQL updated to latest stable versions
- [ ] Implement rate limiting for login attempts
- [ ] Regular security audits and code reviews
- [ ] Monitor activity logs for suspicious behavior
- [ ] Implement automated database backups
- [ ] Use environment variables for sensitive data in production
- [ ] Enable PHP error logging (disable display_errors in production)
- [ ] Implement Content Security Policy headers

---

## Development Guide

### Setting Up Development Environment

1. **Install XAMPP:**

   - Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install with PHP, MySQL, and Apache modules

2. **Configure PHP:**

   - Edit `php.ini` file
   - Enable necessary extensions: `pdo_mysql`, `gd`, `mbstring`, `openssl`
   - Set appropriate `upload_max_filesize` and `post_max_size`

3. **IDE Setup (VS Code Recommended):**
   ```json
   // Recommended extensions
   {
     "recommendations": [
       "bmewburn.vscode-intelephense-client",
       "mrmlnc.vscode-apache",
       "xdebug.php-debug",
       "dbaeumer.vscode-eslint",
       "esbenp.prettier-vscode"
     ]
   }
   ```

### Code Standards

**PHP Coding Standards:**

- Follow PSR-12 coding style guide
- Use meaningful variable and function names
- Add PHPDoc comments for functions and classes
- Keep functions focused (single responsibility principle)
- Maximum line length: 120 characters

**Example:**

```php
/**
 * Retrieve user by email address
 *
 * @param string $email User's email address
 * @return array|null User data or null if not found
 */
public function getUserByEmail($email) {
    return db('users')
        ->where('email', $email)
        ->first();
}
```

**JavaScript Standards:**

- Use ES6+ features
- Consistent naming conventions (camelCase)
- Add JSDoc comments for complex functions
- Use `const` and `let`, avoid `var`

**CSS Standards:**

- Use BEM methodology for class naming
- Group related properties
- Use CSS variables for theming
- Mobile-first approach

### Database Operations

**Using the Query Builder:**

```php
// SELECT
$users = db('users')
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// INSERT
$userId = db('users')->insert([
    'name' => $name,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_BCRYPT)
]);

// UPDATE
db('users')
    ->where('user_id', $userId)
    ->update(['status' => 'active']);

// DELETE
db('users')
    ->where('user_id', $userId)
    ->delete();

// JOIN
$listings = db('listings')
    ->select('listings.*, providers.business_name')
    ->leftJoin('providers', 'listings.provider_id', '=', 'providers.provider_id')
    ->where('listings.status', 'active')
    ->get();

// COUNT
$totalUsers = db('users')->count();

// FIND ONE
$user = db('users')->where('email', $email)->first();
```

### Authentication Usage

**User Registration:**

```php
$result = Auth::registerUser($name, $email, $password, $phone);
if ($result['success']) {
    // Handle successful registration
} else {
    // Handle error: $result['message']
}
```

**User Login:**

```php
$result = Auth::loginUser($email, $password);
if ($result['success']) {
    redirect(base_url('user/dashboard.php'));
} else {
    // Display error message
}
```

**Checking Authentication:**

```php
// Check if any user is logged in
if (is_logged_in()) {
    // User is authenticated
}

// Check specific role
if (is_logged_in('user')) {
    // Traveler is logged in
}

if (is_logged_in('provider')) {
    // Provider is logged in
}

if (is_logged_in('admin')) {
    // Admin is logged in
}
```

### Helper Functions

**URL Helpers:**

```php
base_url('path/to/page.php');      // Full URL
assets_url('css/style.css');        // Assets URL
uploads_url('users/profile.jpg');   // Uploads URL
```

**Formatting Helpers:**

```php
format_price(1500.50);              // Returns: ৳ 1,500.50
format_date('2024-01-15');          // Returns: 15 Jan 2024
time_ago('2024-01-15 10:30:00');    // Returns: 2 hours ago
```

**Utility Helpers:**

```php
sanitize_input($userInput);         // Sanitize user input
generate_booking_reference();        // Generate unique booking reference
flash_message('success', 'Booking confirmed!');  // Set flash message
```

### Creating New Pages

**Template Structure:**

```php
<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check authentication if required
if (!is_logged_in('user')) {
    redirect(base_url('login.php'));
}

// Your page logic here
$pageTitle = 'Page Title';
$userId = get_user_id('user');

// Fetch data
$data = db('table')->where('user_id', $userId)->get();

// Include header
include 'includes/header.php';
?>

<!-- Your HTML content here -->
<div class="container">
    <h1><?php echo $pageTitle; ?></h1>
    <!-- Content -->
</div>

<?php include 'includes/footer.php'; ?>
```

### Adding New Features

1. **Plan the feature:**

   - Define requirements and user stories
   - Design database schema changes if needed
   - Sketch UI/UX flow

2. **Database changes:**

   - Update `database/schema.sql`
   - Create migration script for existing databases
   - Test on development database

3. **Implement backend logic:**

   - Create necessary functions/classes
   - Implement business logic
   - Add validation and security checks

4. **Create frontend:**

   - Design responsive UI
   - Implement client-side validation
   - Add user feedback mechanisms

5. **Test thoroughly:**

   - Unit test individual functions
   - Integration testing
   - User acceptance testing

6. **Document:**
   - Update relevant documentation
   - Add code comments
   - Update README if needed

### Debugging

**Enable Error Display (Development Only):**

```php
// In config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Database Query Debugging:**

```php
// The query builder doesn't have built-in debug output
// Use var_dump() to inspect results
$result = db('users')->where('email', $email)->first();
var_dump($result);
```

**Common Issues:**

- **Blank page:** Check PHP error logs
- **Database error:** Verify connection credentials
- **404 errors:** Check .htaccess and file paths
- **Upload issues:** Check folder permissions

---

## Testing

### Manual Testing Checklist

#### Authentication Testing

- [ ] User registration with valid data
- [ ] Registration with duplicate email (should fail)
- [ ] User login with correct credentials
- [ ] Login with incorrect credentials (should fail)
- [ ] Provider registration
- [ ] Admin login
- [ ] Logout functionality
- [ ] Session persistence
- [ ] Password reset flow (when implemented)

#### User Features Testing

- [ ] Browse homepage
- [ ] Search with various filters
- [ ] View listing details
- [ ] Create booking
- [ ] View booking history
- [ ] Cancel booking
- [ ] Submit review
- [ ] Edit/delete review
- [ ] Update profile
- [ ] Upload profile image

#### Provider Features Testing

- [ ] Provider dashboard access
- [ ] Create new listing
- [ ] Upload listing images
- [ ] Edit existing listing
- [ ] Delete listing
- [ ] View bookings
- [ ] Accept/decline booking
- [ ] Update business profile
- [ ] View analytics

#### Admin Features Testing

- [ ] Admin dashboard access
- [ ] View all users
- [ ] Block/unblock user
- [ ] Verify provider
- [ ] Approve/reject listing
- [ ] View all bookings
- [ ] Moderate reviews
- [ ] Generate reports
- [ ] Update platform settings

#### Responsive Design Testing

- [ ] Mobile view (320px - 480px)
- [ ] Tablet view (768px - 1024px)
- [ ] Desktop view (1280px+)
- [ ] Touch interactions on mobile
- [ ] Navigation menu on different devices

#### Browser Compatibility Testing

- [ ] Google Chrome (latest)
- [ ] Mozilla Firefox (latest)
- [ ] Safari (latest)
- [ ] Microsoft Edge (latest)

#### Security Testing

- [ ] SQL injection attempts
- [ ] XSS attempts
- [ ] CSRF testing (when tokens implemented)
- [ ] File upload validation
- [ ] Session hijacking prevention
- [ ] Unauthorized access attempts

### Performance Testing

**Page Load Times:**

- Homepage: Target < 2 seconds
- Search results: Target < 3 seconds
- Dashboard pages: Target < 2 seconds

**Database Query Optimization:**

- Use EXPLAIN for slow queries
- Ensure proper indexing
- Avoid N+1 query problems

**Load Testing (Production):**

- Use tools like Apache JMeter or LoadRunner
- Test with concurrent users
- Monitor server resource usage

### Testing Tools

**Recommended Tools:**

- **phpUnit** - For unit testing PHP code
- **Selenium** - For automated browser testing
- **Postman** - For API testing (future)
- **Chrome DevTools** - For frontend debugging
- **GTmetrix** - For performance analysis

---

## Deployment

### Pre-Deployment Checklist

#### Security Hardening

- [ ] Change all default passwords
- [ ] Remove or disable test accounts
- [ ] Update `error_reporting` to 0 in production
- [ ] Set `display_errors` to Off
- [ ] Enable HTTPS (SSL/TLS certificate)
- [ ] Update `.htaccess` with production security headers
- [ ] Set secure session cookies (`session.cookie_secure = 1`)
- [ ] Remove development/debug code
- [ ] Implement rate limiting for login attempts
- [ ] Configure firewall rules

#### Configuration Updates

- [ ] Update `APP_URL` to production domain
- [ ] Configure SMTP settings for email
- [ ] Set appropriate timezone
- [ ] Update currency settings if needed
- [ ] Configure error logging path
- [ ] Set appropriate file upload limits
- [ ] Update database credentials (use strong passwords)

#### File & Folder Permissions

```bash
# Set appropriate permissions
chmod 755 -R /var/www/html/TripEase
chmod 777 -R /var/www/html/TripEase/uploads
chmod 600 /var/www/html/TripEase/config/config.php
```

#### Database

- [ ] Create production database
- [ ] Import schema
- [ ] Set up automated backups
- [ ] Create database user with limited privileges
- [ ] Test database connection

### Deployment Steps

#### Option 1: Shared Hosting (cPanel)

1. **Prepare Files:**

   - Create a zip of the entire project
   - Exclude unnecessary files (.git, documentation, etc.)

2. **Upload Files:**

   - Login to cPanel
   - Navigate to File Manager
   - Upload zip to public_html or appropriate directory
   - Extract files

3. **Create Database:**

   - Open MySQL Database Wizard in cPanel
   - Create new database
   - Create database user with strong password
   - Grant all privileges
   - Import schema.sql via phpMyAdmin

4. **Configure Application:**

   - Edit config/config.php with production settings
   - Update database credentials
   - Set production APP_URL

5. **Test:**
   - Visit your domain
   - Test all major functionality
   - Monitor error logs

#### Option 2: VPS/Dedicated Server (Ubuntu/Linux)

1. **Server Setup:**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install LAMP stack
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql php-gd php-mbstring -y

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2
```

2. **Deploy Application:**

```bash
# Clone repository
cd /var/www/html
sudo git clone https://github.com/yourusername/TripEase.git

# Set permissions
sudo chown -R www-data:www-data TripEase/
sudo chmod -R 755 TripEase/
sudo chmod -R 777 TripEase/uploads/
```

3. **Configure Apache:**

```apache
# /etc/apache2/sites-available/tripease.conf
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/TripEase

    <Directory /var/www/html/TripEase>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/tripease_error.log
    CustomLog ${APACHE_LOG_DIR}/tripease_access.log combined
</VirtualHost>
```

```bash
# Enable site
sudo a2ensite tripease.conf
sudo systemctl reload apache2
```

4. **Set Up MySQL:**

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE tripease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tripease_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON tripease.* TO 'tripease_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import schema
mysql -u tripease_user -p tripease < /var/www/html/TripEase/database/schema.sql
```

5. **Configure SSL (Let's Encrypt):**

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtain certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is configured automatically
# Test renewal
sudo certbot renew --dry-run
```

### Post-Deployment

#### Monitoring

- Set up server monitoring (Uptime Robot, Pingdom)
- Configure log monitoring and alerts
- Monitor disk space and resource usage
- Track application errors

#### Backups

```bash
# Database backup script (save as backup_db.sh)
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/database"
DB_NAME="tripease"
DB_USER="tripease_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/tripease_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

```bash
# Schedule daily backups
sudo crontab -e
# Add: 0 2 * * * /path/to/backup_db.sh
```

#### Maintenance

- Regular security updates
- Monitor and optimize database
- Review and archive old logs
- Update dependencies
- Performance optimization

### Troubleshooting Production Issues

**Issue: 500 Internal Server Error**

- Check Apache error logs: `sudo tail -f /var/log/apache2/error.log`
- Verify file permissions
- Check .htaccess syntax

**Issue: Database Connection Errors**

- Verify database credentials in config.php
- Check if MySQL service is running
- Verify user privileges

**Issue: Slow Performance**

- Enable opcache in PHP
- Optimize database queries
- Implement caching
- Use CDN for static assets

---

## Roadmap

### Version 1.1 (Q2 2024)

**Core Functionality Completion**

- [ ] Complete user dashboard implementation
- [ ] Complete provider dashboard implementation
- [ ] Complete admin panel implementation
- [ ] Implement full booking system workflow
- [ ] Implement review and rating system
- [ ] Add email notification system
- [ ] Implement password reset functionality
- [ ] Add advanced search filters
- [ ] Implement booking calendar view
- [ ] Add image gallery for listings

### Version 1.2 (Q3 2024)

**Enhanced Features**

- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] SMS notifications
- [ ] Advanced analytics dashboard
- [ ] Automated email campaigns
- [ ] Social media login (Google, Facebook)
- [ ] Map integration for listings
- [ ] Favorites/wishlist functionality
- [ ] Provider verification documents upload
- [ ] Multi-language support (Bengali, English)
- [ ] Currency conversion

### Version 1.3 (Q4 2024)

**Advanced Capabilities**

- [ ] Mobile application (React Native)
- [ ] Real-time chat system
- [ ] Push notifications
- [ ] AI-powered recommendations
- [ ] Dynamic pricing system
- [ ] Seasonal demand analytics
- [ ] Loyalty program
- [ ] Referral system
- [ ] API for third-party integrations
- [ ] Advanced reporting and exports

### Version 2.0 (2025)

**Platform Expansion**

- [ ] Multi-vendor marketplace features
- [ ] Tour package bookings
- [ ] Transportation booking integration
- [ ] Travel insurance integration
- [ ] Guided tours and activities
- [ ] Group booking management
- [ ] Corporate account features
- [ ] Franchise/white-label solution
- [ ] Advanced fraud detection
- [ ] Blockchain-based verification (exploratory)

### Continuous Improvements

**Ongoing Priorities**

- Security audits and updates
- Performance optimization
- UI/UX refinements based on user feedback
- Bug fixes and stability improvements
- Documentation updates
- Community engagement
- Accessibility improvements
- SEO optimization

---

## Contributing

We welcome contributions from the community! Whether you're fixing bugs, adding features, or improving documentation, your help is appreciated.

### How to Contribute

1. **Fork the Repository**

   ```bash
   # Click the "Fork" button on GitHub
   # Clone your fork
   git clone https://github.com/YOUR-USERNAME/TripEase.git
   cd TripEase
   ```

2. **Create a Feature Branch**

   ```bash
   git checkout -b feature/your-feature-name
   # or for bug fixes
   git checkout -b bugfix/issue-description
   ```

3. **Make Your Changes**

   - Write clean, well-documented code
   - Follow the project's coding standards
   - Test your changes thoroughly
   - Update documentation if needed

4. **Commit Your Changes**

   ```bash
   git add .
   git commit -m "Add: clear description of your changes"
   ```

   **Commit Message Guidelines:**

   - Use present tense ("Add feature" not "Added feature")
   - Use imperative mood ("Move cursor to..." not "Moves cursor to...")
   - Limit first line to 72 characters
   - Reference issues and pull requests when relevant

5. **Push to Your Fork**

   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request**
   - Go to the original repository on GitHub
   - Click "New Pull Request"
   - Select your fork and branch
   - Provide a clear description of your changes
   - Reference any related issues

### Contribution Guidelines

**Code Quality:**

- Follow PSR-12 coding standards for PHP
- Write meaningful comments for complex logic
- Include PHPDoc blocks for functions and classes
- Keep functions focused and small
- Use meaningful variable and function names

**Testing:**

- Test your changes thoroughly
- Ensure existing features still work
- Test on multiple browsers if UI changes
- Test responsive design on various devices

**Documentation:**

- Update README if adding features
- Document new functions and classes
- Update configuration examples if needed
- Add comments for complex algorithms

**Security:**

- Never commit sensitive data (passwords, API keys)
- Follow security best practices
- Validate and sanitize all inputs
- Use prepared statements for database queries

### Types of Contributions

**Bug Reports:**

- Use GitHub Issues
- Include detailed description
- Provide steps to reproduce
- Include screenshots if applicable
- Specify environment details (PHP version, browser, OS)

**Feature Requests:**

- Open a GitHub Issue first to discuss
- Explain the use case and benefits
- Provide examples or mockups if possible

**Code Contributions:**

- Bug fixes
- New features
- Performance improvements
- Code refactoring
- UI/UX enhancements

**Documentation:**

- Fix typos and errors
- Improve existing documentation
- Add examples and tutorials
- Translate documentation

### Code Review Process

1. All pull requests will be reviewed by maintainers
2. Feedback may be provided for changes
3. Once approved, changes will be merged
4. Contributors will be credited in release notes

### Community Guidelines

- Be respectful and inclusive
- Help others in discussions
- Provide constructive feedback
- Follow the code of conduct

### Recognition

Contributors will be acknowledged in:

- Release notes
- CONTRIBUTORS.md file
- Project documentation

---

## License

TripEase is open-source software licensed under the **MIT License**.

### MIT License

```
MIT License

Copyright (c) 2024 TripEase

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

### What This Means

**You are free to:**

- ✅ Use the software for commercial purposes
- ✅ Modify the software
- ✅ Distribute copies
- ✅ Sublicense the software
- ✅ Use privately

**Under the conditions:**

- 📋 Include the original license and copyright notice
- 🚫 No liability or warranty provided

### Third-Party Licenses

This project uses the following open-source libraries:

- **Bootstrap 5.3** - MIT License
- **Font Awesome 6** - Font Awesome Free License
- **PHP** - PHP License 3.01
- **MySQL** - GPL v2

---

## Support

### Getting Help

**Documentation:**

- README.md (this file)
- Installation guide: `all mds/INSTALLATION.md`
- Quick start guide: `all mds/QUICKSTART.md`
- Features documentation: `all mds/FEATURES.md`

**Community Support:**

- GitHub Discussions: [GitHub Discussions](https://github.com/sisayeedcse/TripEase---Online-Booking-System/discussions)
- GitHub Issues: [Report Issues](https://github.com/sisayeedcse/TripEase---Online-Booking-System/issues)

**Contact:**

- Email: support@tripease.com
- Project Maintainer: [Sisay Eed](https://github.com/sisayeedcse)

### Frequently Asked Questions

**Q: How do I reset the admin password?**  
A: You can reset it directly in the database using phpMyAdmin:

```sql
UPDATE admins
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@tripease.com';
-- This sets the password to: password
```

**Q: Can I change the currency?**  
A: Yes, edit `config/config.php`:

```php
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');
```

**Q: How do I enable email notifications?**  
A: Email notification system is currently in development. SMTP settings are available in `config/config.php` for future implementation.

**Q: Is this production-ready?**  
A: The core infrastructure is complete and secure. However, user/provider/admin dashboards and the booking system are still under development. It's recommended to wait for version 1.1 for full production deployment.

**Q: Can I use this for commercial purposes?**  
A: Yes! The MIT License allows commercial use. You can modify and sell the software.

**Q: How do I upgrade to future versions?**  
A: Upgrade instructions will be provided with each release. Generally:

1. Backup your database and files
2. Replace application files
3. Run any database migrations
4. Update configuration if needed

**Q: Where are uploaded files stored?**  
A: In the `uploads/` directory, organized by type:

- `uploads/users/` - User profile images
- `uploads/providers/` - Provider profile images
- `uploads/listings/` - Listing images

**Q: Can I customize the design?**  
A: Absolutely! All CSS is in the `assets/css/` directory. The application uses Bootstrap 5, so you can easily customize by:

- Modifying `assets/css/style.css`
- Overriding Bootstrap variables
- Creating custom CSS classes

**Q: How do I report a security vulnerability?**  
A: Please email security issues directly to support@tripease.com rather than posting publicly. We take security seriously and will respond promptly.

---

## Acknowledgments

### Built With

- [PHP](https://www.php.net/) - Server-side scripting language
- [MySQL](https://www.mysql.com/) - Database management system
- [Bootstrap](https://getbootstrap.com/) - CSS framework
- [Font Awesome](https://fontawesome.com/) - Icon library
- [Google Fonts](https://fonts.google.com/) - Typography (Poppins)

### Inspiration

- Modern booking platforms and marketplace designs
- Best practices in web application security
- Community feedback and user experience research

### Special Thanks

- XAMPP team for the development environment
- Bootstrap team for the excellent framework
- PHP and MySQL communities for extensive documentation
- All contributors and testers

---

## Project Statistics

- **Total Lines of Code:** ~15,000+
- **Database Tables:** 11
- **Database Views:** 3
- **PHP Files:** 45+
- **CSS Files:** 3
- **JavaScript Files:** 2
- **Development Time:** 3+ months
- **Contributors:** 1 (open for contributions!)

---

## Changelog

### Version 1.0.0 (Current)

**Released:** 2024

**Added:**

- Complete database schema with 11 tables
- Multi-role authentication system
- Responsive frontend design
- Search and filtering functionality
- Security implementations
- Query builder for database operations
- File upload system
- Activity logging
- Comprehensive documentation

**In Progress:**

- User dashboard
- Provider dashboard
- Admin panel
- Booking system
- Review system
- Email notifications

---

<div align="center">

**Made with ❤️ for the travel community**

[⭐ Star on GitHub](https://github.com/sisayeedcse/TripEase---Online-Booking-System) • [🐛 Report Bug](https://github.com/sisayeedcse/TripEase---Online-Booking-System/issues) • [✨ Request Feature](https://github.com/sisayeedcse/TripEase---Online-Booking-System/issues)

</div>

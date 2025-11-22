# 🚢 TripEase - Local Travel Booking Platform

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> A comprehensive web-based platform connecting travelers with local service providers for boat rentals and room accommodations. Built with PHP, MySQL, and modern web technologies.

<<<<<<< HEAD
![TripEase Banner](https://via.placeholder.com/1200x400/4A90E2/FFFFFF?text=TripEase+-+Your+Local+Travel+Companion)
=======
![TripEase Banner](uploads/screenshots/TripEaseBanner.png)
>>>>>>> e3dd9e9 (Initial import of TripEase)

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [System Architecture](#-system-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Database Schema](#-database-schema)
- [Security](#-security)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Support](#-support)

---

## 🌟 Overview

**TripEase** is a full-featured local travel booking system designed to streamline the process of discovering and booking boats and accommodations from trusted local providers. The platform features a modern, responsive interface with comprehensive admin controls, real-time availability management, and secure payment processing.

### Key Highlights

- 🎯 **Multi-Role System**: Separate dashboards for Travelers, Providers, and Administrators
- 🔒 **Secure Authentication**: Industry-standard password hashing and session management
- 📱 **Responsive Design**: Mobile-first approach with adaptive layouts
- 🔍 **Advanced Search**: Filter by location, category, price, and availability
- ⭐ **Review System**: Verified reviews and ratings from real travelers
- 📊 **Analytics Dashboard**: Comprehensive reporting and insights
- 🌐 **Modern UI/UX**: Clean, intuitive interface with smooth animations

---

## ✨ Features

### For Travelers (Users)

#### 🔐 Authentication & Account Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- User registration with email validation
- Secure login/logout system
- Password reset functionality
- Profile management with image upload
- Session management with "Remember Me"

#### 🔍 Search & Discovery
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- **Advanced Search Engine**
  - Location-based filtering
  - Category selection (Boats/Rooms)
  - Price range filters
  - Date availability checking
- **Smart Sorting**
  - By newest listings
  - By price (low to high / high to low)
  - By rating
  - By popularity
- Featured listings showcase
- Top-rated services display
- Pagination for easy browsing

#### 📅 Booking Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Real-time availability calendar
- Instant booking confirmation
- Booking reference tracking
- Booking history and status
- Cancellation management (24-hour policy)
- Special requests handling

#### ⭐ Reviews & Ratings
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Leave detailed reviews after trips
- 5-star rating system
- Edit/delete own reviews
- View aggregated ratings
- Filter reviews by rating

#### 🔔 Notifications
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Booking confirmations
- Booking reminders
- Cancellation alerts
- Review prompts
- Special offers

### For Service Providers

#### 🏢 Business Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Provider registration with verification
- Business profile management
- Multi-listing support
- Performance analytics

#### 📝 Listing Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Create and edit listings
- Upload multiple images per listing
- Set pricing and availability
- Manage amenities and features
- Real-time status updates

#### 📊 Dashboard & Analytics
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Booking overview
- Revenue tracking
- Performance metrics
- Customer reviews
- Occupancy rates

#### 💬 Communication
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Direct customer messaging
- Booking notifications
- Review responses
- Support ticket system

### For Administrators

#### 👥 User Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- View all users and providers
- Account activation/deactivation
- User verification
- Activity monitoring

#### 📋 Listing Management
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Approve/reject listings
- Monitor listing quality
- Enforce platform policies
- Featured listing selection

#### 📊 System Analytics
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Platform-wide statistics
- Revenue reports
- User engagement metrics
- Booking trends
- Performance dashboards

#### ⚙️ Platform Settings
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Site configuration
- Email templates
- Payment settings
- Policy management
- System maintenance

---

## 🛠 Technology Stack

### Backend
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Relational database
- **PDO** - Database abstraction layer
- **Apache** - Web server (XAMPP)

### Frontend
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with custom properties
- **Bootstrap 5.3** - Responsive framework
- **JavaScript (ES6+)** - Client-side interactivity
- **Font Awesome 6** - Icon library

### Architecture Patterns
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- **MVC-inspired** - Separation of concerns
- **Query Builder** - Fluent database interface
- **Singleton Pattern** - Database connection
- **Helper Functions** - Reusable utilities

### Security
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- **bcrypt** - Password hashing
- **Prepared Statements** - SQL injection prevention
- **Input Sanitization** - XSS protection
- **CSRF Tokens** - Cross-site request forgery protection
- **Session Security** - Secure session handling

---

## 🏗 System Architecture

```
TripEase/
├── admin/                  # Admin panel pages
│   ├── dashboard.php
│   ├── users.php
│   ├── providers.php
│   ├── listings.php
│   ├── bookings.php
│   ├── reviews.php
│   ├── reports.php
│   ├── settings.php
│   └── sidebar.php
├── assets/                 # Static assets
│   ├── css/
│   │   ├── style.css
│   │   ├── admin.css
│   │   └── responsive.css
│   └── js/
│       ├── main.js
│       └── admin.js
├── config/                 # Configuration files
│   ├── config.php         # Application settings
│   └── database.php       # Database connection & query builder
├── database/              # Database files
│   └── schema.sql         # Database schema
├── includes/              # Shared components
│   ├── Auth.php          # Authentication class
│   ├── header.php        # Common header
│   └── footer.php        # Common footer
├── provider/              # Provider dashboard
│   ├── dashboard.php
│   ├── listings.php
│   ├── add-listing.php
│   ├── edit-listing.php
│   ├── bookings.php
│   ├── profile.php
│   ├── register.php
│   └── sidebar.php
├── uploads/               # User-uploaded files
│   ├── users/
│   ├── providers/
│   └── listings/
├── user/                  # User dashboard
│   ├── dashboard.php
│   ├── bookings.php
│   ├── booking-details.php
│   ├── reviews.php
│   ├── profile.php
│   ├── notifications.php
│   └── sidebar.php
├── .htaccess             # Apache configuration
├── index.php             # Homepage
├── search.php            # Search & filter page
├── listing-details.php   # Listing details page
├── login.php             # Login page
├── register.php          # User registration
├── about.php             # About page
├── contact.php           # Contact page
├── booking-confirmation.php
├── process-booking.php
└── logout.php
```

---

## 📦 Installation

### Prerequisites

- **XAMPP** (or similar LAMP/WAMP stack)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher
- **Web Browser** (Chrome, Firefox, Safari, Edge)
- **Text Editor** (VS Code, Sublime Text, etc.)

### Step-by-Step Installation

1. **Clone the Repository**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   ```bash
   git clone https://github.com/yourusername/tripease.git
   cd tripease
   ```

2. **Move to XAMPP Directory**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   ```bash
   # Windows
   move tripease C:\xampp\htdocs\

   # Linux/Mac
   sudo mv tripease /opt/lampp/htdocs/
   ```

3. **Start XAMPP Services**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

4. **Create Database**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "New" to create a database
   - Import the schema:
     - Click "Import" tab
     - Choose file: `database/schema.sql`
     - Click "Go"

5. **Configure Application**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Open `config/config.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'tripease');
     ```
   - Update base URL:
     ```php
     define('APP_URL', 'http://localhost/TripEase');
     ```

6. **Set Permissions** (Linux/Mac only)
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   ```bash
   chmod -R 755 /opt/lampp/htdocs/tripease
   chmod -R 777 /opt/lampp/htdocs/tripease/uploads
   ```

7. **Access the Application**
   - Homepage: `http://localhost/TripEase`
   - Admin Panel: `http://localhost/TripEase/admin`
   - Provider Panel: `http://localhost/TripEase/provider`

### Default Credentials

**Admin Account**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- Email: `admin@tripease.com`
- Password: `password`

> ⚠️ **Important**: Change the default admin password immediately after first login!

---

## ⚙️ Configuration

### Application Settings

Edit `config/config.php` to customize:

```php
// Application
define('APP_NAME', 'TripEase');
define('APP_VERSION', '1.0.0');

// Timezone
date_default_timezone_set('Asia/Dhaka');

// Currency
define('CURRENCY', 'BDT');
define('CURRENCY_SYMBOL', '৳');

// File Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);

// Booking
define('CANCELLATION_HOURS', 24);
define('MAX_BOOKING_DAYS', 30);

// Pagination
define('ITEMS_PER_PAGE', 12);
```

### Email Configuration (Optional)

For email notifications, configure SMTP settings:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Security Settings

Update `.htaccess` for additional security:

```apache
# Disable directory browsing
Options -Indexes

# Prevent access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 🚀 Usage

### For Travelers

1. **Register an Account**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Click "Sign Up" on the homepage
   - Fill in your details
   - Verify your email (if enabled)

2. **Search for Listings**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Use the search bar on homepage
   - Apply filters (location, category, price, date)
   - Browse featured and top-rated listings

3. **Make a Booking**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Click on a listing to view details
   - Select your dates
   - Review pricing and amenities
   - Confirm booking
   - Receive booking reference

4. **Manage Bookings**
   - Access your dashboard
   - View upcoming and past bookings
   - Cancel bookings (within policy)
   - Leave reviews after trip

### For Service Providers

1. **Register as Provider**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Navigate to Provider Registration
   - Fill in business details
   - Wait for admin verification

2. **Create Listings**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Access provider dashboard
   - Click "Add New Listing"
   - Upload images and details
   - Set pricing and availability
   - Submit for approval

3. **Manage Bookings**
   - View incoming bookings
   - Confirm or decline requests
   - Communicate with customers
   - Track revenue

### For Administrators

1. **Access Admin Panel**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Login with admin credentials
   - Navigate to `/admin`

2. **Manage Platform**
   - Verify new providers
   - Approve/reject listings
   - Monitor bookings
   - Handle disputes
   - Generate reports

---

## 🗄 Database Schema

### Core Tables

#### Users Table
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    status ENUM('active', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Providers Table
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
```sql
CREATE TABLE providers (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(150) NOT NULL,
    owner_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    status ENUM('active', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Listings Table
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
```sql
CREATE TABLE listings (
    listing_id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('boat', 'room') NOT NULL,
    location VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id)
);
```

#### Bookings Table
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
```sql
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    provider_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    booking_reference VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Additional Tables

- `admins` - Administrator accounts
- `reviews` - User reviews and ratings
- `notifications` - System notifications
- `availability` - Listing availability calendar
- `contact_messages` - Contact form submissions
- `settings` - Platform settings
- `activity_logs` - User activity tracking

### Database Views

- `booking_stats` - Booking analytics
- `listing_stats` - Listing performance
- `provider_stats` - Provider earnings and ratings

---

## 🔒 Security

### Implemented Security Measures

1. **Authentication & Authorization**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Password hashing using bcrypt (cost factor: 10)
   - Session-based authentication
   - Role-based access control (RBAC)
   - Session timeout and regeneration

2. **Input Validation & Sanitization**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Server-side validation for all inputs
   - HTML entity encoding (XSS prevention)
   - SQL injection prevention via PDO prepared statements
   - File upload validation (type, size, extension)

3. **Database Security**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Prepared statements for all queries
   - Parameterized queries
   - Foreign key constraints
   - Indexed columns for performance

4. **File Security**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Upload directory outside web root (recommended)
   - File type validation
   - File size limits
   - Unique filename generation

5. **Session Security**
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
   - Secure session configuration
   - HttpOnly cookies
   - Session regeneration on login
   - Session timeout

6. **Additional Measures**
   - CSRF token implementation (structure ready)
   - Security headers in .htaccess
   - Error message sanitization
   - Activity logging

### Security Best Practices

- Change default admin password immediately
- Use HTTPS in production
- Regular security updates
- Database backups
- Monitor activity logs
- Implement rate limiting
- Enable email verification

---

## 📚 API Documentation

### Query Builder Usage

The application includes a custom query builder for database operations:

```php
// SELECT queries
$users = db('users')
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// INSERT
$userId = db('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_BCRYPT)
]);

// UPDATE
db('users')
    ->where('user_id', $userId)
    ->update(['status' => 'active']);

// DELETE
db('users')
    ->where('user_id', $userId)
    ->delete();

// Complex queries with joins
$listings = db('listings')
    ->select('listings.*, providers.business_name')
    ->leftJoin('providers', 'listings.provider_id', '=', 'providers.provider_id')
    ->where('listings.status', 'active')
    ->get();
```

### Authentication Class

```php
// User registration
$result = Auth::registerUser($name, $email, $password, $phone);

// User login
$result = Auth::loginUser($email, $password);

// Provider registration
$result = Auth::registerProvider($businessName, $ownerName, $email, $password, $phone);

// Password reset request
$result = Auth::requestPasswordReset($email, 'user');

// Change password
$result = Auth::changePassword($userId, $currentPassword, $newPassword, 'user');

// Logout
Auth::logout('user');
```

---

## 🤝 Contributing

We welcome contributions to TripEase! Here's how you can help:

### Getting Started

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Contribution Guidelines

- Follow PSR-12 coding standards for PHP
- Write clear, descriptive commit messages
- Add comments for complex logic
- Test your changes thoroughly
- Update documentation as needed
- Ensure backward compatibility

### Code Style

```php
// Good
public function getUserBookings($userId) {
    return db('bookings')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'DESC')
        ->get();
}

// Use meaningful variable names
$activeListings = db('listings')
    ->where('status', 'active')
    ->get();
```

### Reporting Bugs

- Use GitHub Issues
- Include detailed description
- Provide steps to reproduce
- Include screenshots if applicable
- Specify environment details

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

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
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
```

---

## 📞 Support

### Get Help

- **Documentation**: Check the `/all mds` folder for detailed guides
- **Issues**: [GitHub Issues](https://github.com/yourusername/tripease/issues)
- **Email**: support@tripease.com
- **Community**: [Discord Server](https://discord.gg/tripease)

### Frequently Asked Questions

**Q: How do I reset the admin password?**
A: Access the database directly and update the password hash in the `admins` table.

**Q: Can I customize the currency?**
A: Yes, edit the `CURRENCY` and `CURRENCY_SYMBOL` constants in `config/config.php`.

**Q: How do I enable email notifications?**
A: Configure SMTP settings in `config/config.php` and implement email sending functionality.

**Q: Is this production-ready?**
A: The core features are complete. Additional security hardening and email integration are recommended for production.

---

## 🎯 Roadmap

### Version 1.1 (Planned)
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- [ ] Email notification system
- [ ] Payment gateway integration
- [ ] Advanced analytics dashboard
- [ ] Mobile app (React Native)
- [ ] Multi-language support

### Version 1.2 (Future)
<<<<<<< HEAD
=======

>>>>>>> e3dd9e9 (Initial import of TripEase)
- [ ] AI-powered recommendations
- [ ] Chat system (real-time)
- [ ] Calendar synchronization
- [ ] Social media integration
- [ ] Advanced reporting

---

## 🙏 Acknowledgments

- **Bootstrap Team** - For the amazing CSS framework
- **Font Awesome** - For the comprehensive icon library
- **PHP Community** - For excellent documentation and support
- **Contributors** - For making this project better

---

## 📊 Project Stats

- **Total Lines of Code**: ~15,000+
- **Database Tables**: 11
- **PHP Files**: 45+
- **CSS Files**: 3
- **JavaScript Files**: 2
- **Development Time**: 3+ months

---

<div align="center">

**Made with ❤️ by the TripEase Team**

[Website](https://tripease.com) • [Documentation](https://docs.tripease.com) • [Blog](https://blog.tripease.com)

⭐ Star us on GitHub — it helps!

</div>

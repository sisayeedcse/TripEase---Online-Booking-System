# TripEase - Local Travel Booking System

A comprehensive web-based platform connecting travelers with local service providers (boat & room owners) for easy, secure, and transparent bookings.

## 🚀 Features

### Traveler Features
- User authentication (signup, login, password reset)
- Search and explore boats & rooms with filters
- Real-time availability checking
- Instant booking with confirmation
- Booking history management
- Review and rating system
- Responsive mobile-first design

### Service Provider Features
- Provider registration and verification
- Listing management (add, edit, delete)
- Availability calendar
- Booking management
- Dashboard with analytics
- Profile management

### Admin Features
- User and provider management
- Booking oversight
- Listing moderation
- Reports and analytics
- Platform settings configuration
- Activity logs

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Version Control**: Git

## 📋 Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser
- Git (optional)

## 🔧 Installation

### 1. Clone or Download the Project

```bash
# If using Git
git clone https://github.com/yourusername/tripease.git

# Or download and extract the ZIP file to:
C:\xampp\htdocs\TripEase
```

### 2. Database Setup

1. Start XAMPP and ensure Apache and MySQL are running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import the database:
   - Click on "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go"

**Or** run the SQL file directly:
```bash
mysql -u root -p < database/schema.sql
```

### 3. Configuration

1. Open `config/config.php`
2. Update database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tripease');
```

3. Update the base URL if needed:
```php
define('APP_URL', 'http://localhost/TripEase');
```

### 4. File Permissions

Ensure the following directories are writable:
- `uploads/`
- `uploads/users/`
- `uploads/providers/`
- `uploads/listings/`

### 5. Access the Application

Open your browser and navigate to:
```
http://localhost/TripEase
```

## 👤 Default Admin Credentials

```
Email: admin@tripease.com
Password: password
```

**⚠️ IMPORTANT**: Change the admin password immediately after first login!

## 📱 Responsive Design

TripEase is built with a mobile-first approach:
- **Mobile**: Bottom navigation, touch-friendly UI
- **Tablet**: Adaptive grid layouts
- **Desktop**: Full-featured professional interface

## 🎨 Design Features

- Modern, clean interface
- Sky blue and soft green color palette
- Smooth animations and transitions
- Card-based layouts
- Intuitive navigation
- Accessible design

## 📂 Project Structure

```
TripEase/
├── admin/              # Admin panel files
├── assets/             # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
├── config/             # Configuration files
│   ├── config.php
│   └── database.php
├── database/           # Database schema
│   └── schema.sql
├── includes/           # Reusable PHP components
│   ├── header.php
│   ├── footer.php
│   └── Auth.php
├── provider/           # Provider dashboard
├── uploads/            # User uploaded files
├── user/               # User dashboard
├── index.php           # Landing page
├── login.php           # Login page
├── register.php        # Registration page
└── README.md           # This file
```

## 🔐 Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF protection (session tokens)
- Secure file uploads
- Activity logging

## 🧪 Testing

### Test Accounts

**Traveler Account:**
- Register a new account at `/register.php`

**Provider Account:**
- Register at `/provider/register.php`
- Requires admin verification

**Admin Account:**
- Use default credentials above

## 📊 Database Schema

The application uses 11 main tables:
- `users` - Traveler accounts
- `providers` - Service provider accounts
- `admins` - Admin accounts
- `listings` - Boats and rooms
- `bookings` - Booking records
- `reviews` - User reviews
- `availability` - Listing availability
- `notifications` - User notifications
- `contact_messages` - Contact form submissions
- `settings` - Platform settings
- `activity_logs` - System activity logs

## 🚀 Future Enhancements

- [ ] Online payment integration (SSLCommerz/Stripe)
- [ ] Email/SMS notifications
- [ ] Map view for listings
- [ ] Multi-language support
- [ ] Mobile app (React Native)
- [ ] AI-based recommendations
- [ ] Tour guide booking
- [ ] Advanced analytics dashboard

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config/config.php`
- Ensure database `tripease` exists

### Upload Directory Error
- Check folder permissions
- Ensure `uploads/` directory exists
- Verify Apache has write permissions

### Page Not Found (404)
- Check Apache is running
- Verify the URL matches your installation path
- Check `.htaccess` file if using mod_rewrite

### Styling Issues
- Clear browser cache
- Check if CSS files are loading (F12 Developer Tools)
- Verify `assets/` path in `config.php`

## 📞 Support

For issues or questions:
- Email: support@tripease.com
- GitHub Issues: [Create an issue](https://github.com/yourusername/tripease/issues)

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Contributors

- Your Name - Initial work

## 🙏 Acknowledgments

- Bootstrap team for the amazing framework
- Font Awesome for icons
- Google Fonts for Poppins font
- XAMPP for the development environment

---

**Built with ❤️ for local travel experiences**

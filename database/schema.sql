-- TripEase Database Schema
-- Drop existing database if exists
DROP DATABASE IF EXISTS tripease;
CREATE DATABASE tripease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tripease;

-- Users Table (Travelers)
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Service Providers Table
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_verification (verification_status),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Admins Table
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'moderator') DEFAULT 'moderator',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Listings Table (Boats & Rooms)
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
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE,
    INDEX idx_provider (provider_id),
    INDEX idx_category (category),
    INDEX idx_location (location),
    INDEX idx_status (status),
    INDEX idx_price (price),
    FULLTEXT idx_search (title, description, location)
) ENGINE=InnoDB;

-- Availability Table
CREATE TABLE availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    date DATE NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    UNIQUE KEY unique_listing_date (listing_id, date),
    INDEX idx_listing_date (listing_id, date)
) ENGINE=InnoDB;

-- Bookings Table
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
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_listing (listing_id),
    INDEX idx_provider (provider_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_reference (booking_reference)
) ENGINE=InnoDB;

-- Reviews Table
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_review (booking_id),
    INDEX idx_listing (listing_id),
    INDEX idx_provider (provider_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- Notifications Table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('user', 'provider', 'admin') NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_type, user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Contact Messages Table
CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Platform Settings Table
CREATE TABLE settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    setting_type VARCHAR(50) DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Activity Logs Table
CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('user', 'provider', 'admin') NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_type, user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert Default Admin
INSERT INTO admins (name, email, password, role, status) VALUES
('Super Admin', 'admin@tripease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active');
-- Default password: password (should be changed after first login)

-- Insert Default Settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'TripEase', 'text'),
('site_tagline', 'Your Local Travel Companion', 'text'),
('site_email', 'info@tripease.com', 'text'),
('site_phone', '+880 1234-567890', 'text'),
('currency', 'BDT', 'text'),
('currency_symbol', '৳', 'text'),
('booking_cancellation_hours', '24', 'number'),
('provider_verification_required', 'true', 'boolean'),
('listing_approval_required', 'true', 'boolean'),
('enable_reviews', 'true', 'boolean'),
('max_booking_days', '30', 'number');

-- Create Views for Analytics
CREATE VIEW booking_stats AS
SELECT 
    DATE(created_at) as booking_date,
    COUNT(*) as total_bookings,
    SUM(total_price) as total_revenue,
    AVG(total_price) as avg_booking_value
FROM bookings
WHERE status IN ('confirmed', 'completed')
GROUP BY DATE(created_at);

CREATE VIEW listing_stats AS
SELECT 
    l.listing_id,
    l.title,
    l.category,
    l.location,
    COUNT(DISTINCT b.booking_id) as total_bookings,
    AVG(r.rating) as avg_rating,
    COUNT(DISTINCT r.review_id) as total_reviews,
    l.views
FROM listings l
LEFT JOIN bookings b ON l.listing_id = b.listing_id
LEFT JOIN reviews r ON l.listing_id = r.listing_id
GROUP BY l.listing_id;

CREATE VIEW provider_stats AS
SELECT 
    p.provider_id,
    p.business_name,
    COUNT(DISTINCT l.listing_id) as total_listings,
    COUNT(DISTINCT b.booking_id) as total_bookings,
    SUM(b.total_price) as total_earnings,
    AVG(r.rating) as avg_rating
FROM providers p
LEFT JOIN listings l ON p.provider_id = l.provider_id
LEFT JOIN bookings b ON p.provider_id = b.provider_id
LEFT JOIN reviews r ON p.provider_id = r.provider_id
GROUP BY p.provider_id;

-- ============================================
-- TripEase Demo Data
-- Bangladeshi Locations: Cox's Bazar, Bandarban, Tanguar Haor
-- ============================================

-- Demo Users (Travelers)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `profile_image`, `status`, `created_at`) VALUES
('Farhan Ahmed', 'farhan.ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1712-345678', 'default-avatar.png', 'active', '2024-10-15 10:30:00'),
('Nusrat Jahan', 'nusrat.jahan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1823-456789', 'default-avatar.png', 'active', '2024-10-20 14:20:00'),
('Raihan Kabir', 'raihan.kabir@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1934-567890', 'default-avatar.png', 'active', '2024-11-01 09:15:00'),
('Tasnia Rahman', 'tasnia.rahman@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1645-678901', 'default-avatar.png', 'active', '2024-11-05 16:45:00'),
('Mahbub Alam', 'mahbub.alam@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1756-789012', 'default-avatar.png', 'active', '2024-11-10 11:30:00');

-- Demo Providers (Service Providers)
INSERT INTO `providers` (`business_name`, `owner_name`, `email`, `password`, `phone`, `address`, `description`, `profile_image`, `verification_status`, `status`, `created_at`) VALUES
-- Verified Providers
('Sea Pearl Beach Resort', 'Mohammad Rahman', 'seapearl@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1711-111111', 'Beach Road, Cox\'s Bazar 4700', 'Luxury beachfront resort offering stunning ocean views and world-class amenities. Experience the longest natural sea beach of the world.', 'default-provider.png', 'verified', 'active', '2024-09-01 08:00:00'),

('Sunset Paradise Hotel', 'Ayesha Siddique', 'sunsetparadise@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1722-222222', 'Marine Drive, Cox\'s Bazar 4700', 'Premium hotel with breathtaking sunset views. Located on the pristine shores of Cox\'s Bazar beach.', 'default-provider.png', 'verified', 'active', '2024-09-05 10:30:00'),

('Nilgiri Hill Resort', 'Kamal Hossain', 'nilgiri@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1733-333333', 'Nilgiri, Bandarban 4600', 'Mountain resort nestled in the clouds. Experience the beauty of Bandarban hills with panoramic valley views.', 'default-provider.png', 'verified', 'active', '2024-09-10 12:00:00'),

('Tanguar Haor Eco Resort', 'Shamim Ahmed', 'tanguarhaor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1744-444444', 'Tanguar Haor, Sunamganj 3000', 'Eco-friendly resort on the largest wetland of Bangladesh. Perfect for nature lovers and bird watchers.', 'default-provider.png', 'verified', 'active', '2024-09-15 09:30:00'),

('Cox\'s Bazar Boat Tours', 'Abdul Jabbar', 'coxboat@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1755-555555', 'Kolatoli Beach, Cox\'s Bazar 4700', 'Professional boat tours and fishing trips. Explore the Bay of Bengal with experienced captains.', 'default-provider.png', 'verified', 'active', '2024-09-20 14:15:00'),

('Sangu River Cruises', 'Rahim Uddin', 'sanguriver@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1766-666666', 'Sangu River, Bandarban 4600', 'Scenic river cruises through the hills of Bandarban. Experience the crystal clear waters of Sangu River.', 'default-provider.png', 'verified', 'active', '2024-09-25 11:00:00'),

-- Pending Providers
('Marine View Inn', 'Fatema Begum', 'marineview@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1777-777777', 'Sugandha Beach, Cox\'s Bazar 4700', 'Cozy beachside inn with authentic local hospitality. Just steps away from the beach.', 'default-provider.png', 'pending', 'active', '2024-11-15 16:30:00'),

('Golden Sands Resort', 'Imran Khan', 'goldensands@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1788-888888', 'Inani Beach, Cox\'s Bazar 4700', 'Boutique resort on the golden sands of Inani Beach. Peaceful retreat away from the crowds.', 'default-provider.png', 'pending', 'active', '2024-11-18 10:45:00');

-- Demo Listings for Sea Pearl Beach Resort (Provider ID: 1)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
-- Approved Listings
(1, 'Deluxe Ocean View Room', 'Spacious deluxe room with panoramic ocean views. Wake up to the sound of waves and enjoy stunning sunrises from your private balcony. Features king-size bed, modern bathroom, minibar, and complimentary breakfast.', 'room', 'Cox\'s Bazar, Chittagong', 8500.00, 'night', 2, 'WiFi,Air Conditioning,TV,Mini Bar,Ocean View,Balcony,Room Service,Complimentary Breakfast', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 245, '2024-10-01 09:00:00'),

(1, 'Premium Beach Suite', 'Luxury beachfront suite with direct beach access. Perfect for honeymooners and couples seeking privacy. Includes separate living area, jacuzzi, premium amenities, and exclusive beach cabana.', 'room', 'Cox\'s Bazar, Chittagong', 15000.00, 'night', 2, 'WiFi,Air Conditioning,TV,Mini Bar,Ocean View,Balcony,Room Service,Jacuzzi,Beach Access,Premium Toiletries', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 189, '2024-10-05 10:30:00'),

(1, 'Family Beach Villa', 'Spacious 3-bedroom villa perfect for families. Offers complete privacy with your own garden and direct beach access. Fully equipped kitchen, living room, and outdoor dining area.', 'room', 'Cox\'s Bazar, Chittagong', 25000.00, 'night', 6, 'WiFi,Air Conditioning,TV,Kitchen,Ocean View,Garden,Beach Access,BBQ Area,Parking,3 Bedrooms', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 156, '2024-10-10 14:00:00');

-- Demo Listings for Sunset Paradise Hotel (Provider ID: 2)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(2, 'Sunset View Superior Room', 'Beautifully appointed room with the best sunset views in Cox\'s Bazar. Watch the sun dip into the Bay of Bengal from your room. Includes queen bed, work desk, and modern amenities.', 'room', 'Cox\'s Bazar, Chittagong', 7500.00, 'night', 2, 'WiFi,Air Conditioning,TV,Mini Bar,Sunset View,Balcony,Work Desk,Daily Housekeeping', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 198, '2024-10-08 11:00:00'),

(2, 'Honeymoon Paradise Suite', 'Romantic suite designed for couples with private terrace overlooking the ocean. Features rose petal decoration, champagne on arrival, and candlelight dinner arrangement.', 'room', 'Cox\'s Bazar, Chittagong', 12000.00, 'night', 2, 'WiFi,Air Conditioning,TV,Mini Bar,Ocean View,Private Terrace,Romantic Setup,Complimentary Dinner', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 167, '2024-10-12 15:30:00');

-- Demo Listings for Nilgiri Hill Resort (Provider ID: 3)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(3, 'Cloud Nine Hilltop Cottage', 'Experience living among the clouds in this charming hilltop cottage. Panoramic views of Bandarban valleys and mountains. Perfect for nature lovers seeking tranquility.', 'room', 'Bandarban, Chittagong Hill Tracts', 6500.00, 'night', 3, 'WiFi,Heater,Mountain View,Fireplace,Balcony,Trekking Guides,Complimentary Tea', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 223, '2024-10-03 08:30:00'),

(3, 'Valley View Family Bungalow', 'Spacious bungalow with breathtaking valley views. Ideal for families and groups. Features 2 bedrooms, common area, and outdoor seating with mountain panorama.', 'room', 'Bandarban, Chittagong Hill Tracts', 10000.00, 'night', 5, 'WiFi,Heater,Mountain View,2 Bedrooms,Kitchen,BBQ Area,Parking,Garden', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 178, '2024-10-07 12:00:00'),

(3, 'Adventure Base Camp Room', 'Budget-friendly room for adventure seekers and trekkers. Basic amenities with comfortable beds. Starting point for various trekking routes in Nilgiri.', 'room', 'Bandarban, Chittagong Hill Tracts', 3500.00, 'night', 4, 'WiFi,Heater,Mountain View,Shared Bathroom,Trekking Guides,Storage Lockers', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 201, '2024-10-15 09:45:00');

-- Demo Listings for Tanguar Haor Eco Resort (Provider ID: 4)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(4, 'Wetland Heritage Cottage', 'Eco-friendly cottage built with traditional materials. Wake up to the songs of migratory birds. Experience the serene beauty of Bangladesh\'s largest wetland ecosystem.', 'room', 'Tanguar Haor, Sunamganj', 5500.00, 'night', 2, 'WiFi,Fan,Wetland View,Eco-Friendly,Bird Watching,Local Cuisine,Boat Tours', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 134, '2024-10-18 10:00:00'),

(4, 'Fisherman\'s Paradise Houseboat', 'Unique floating accommodation on the haor. Experience traditional fishing life while enjoying modern comforts. Includes guided fishing tours and local food.', 'room', 'Tanguar Haor, Sunamganj', 7000.00, 'night', 4, 'Fan,Wetland View,Boat,Fishing Equipment,Local Cuisine,Bird Watching,Traditional Experience', 'default-room.jpg', 'default-room.jpg', 'active', 'approved', 112, '2024-10-22 14:30:00');

-- Demo Boat Listings for Cox's Bazar Boat Tours (Provider ID: 5)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(5, 'Luxury Yacht Bay Cruise', 'Premium yacht experience on the Bay of Bengal. Perfect for parties, celebrations, or romantic cruises. Includes professional crew, sound system, and catering services.', 'boat', 'Cox\'s Bazar, Bay of Bengal', 25000.00, 'hour', 20, 'Life Jackets,Captain,Crew,Sound System,Catering,Bar,Toilet,Sun Deck', 'default-boat.jpg', 'default-boat.jpg', 'active', 'approved', 167, '2024-10-02 11:00:00'),

(5, 'Sunset Cruise Speedboat', 'Fast speedboat for sunset viewing and coastal exploration. Thrilling ride with safety equipment and experienced captain. Perfect for adventure seekers.', 'boat', 'Cox\'s Bazar, Bay of Bengal', 8000.00, 'hour', 8, 'Life Jackets,Captain,Safety Equipment,Snorkeling Gear,First Aid Kit', 'default-boat.jpg', 'default-boat.jpg', 'active', 'approved', 145, '2024-10-06 13:30:00'),

(5, 'Traditional Fishing Boat Experience', 'Authentic fishing boat experience with local fishermen. Learn traditional fishing techniques and enjoy fresh catch. Early morning and evening trips available.', 'boat', 'Cox\'s Bazar, Bay of Bengal', 5000.00, 'hour', 6, 'Life Jackets,Fishing Equipment,Experienced Fisherman,Traditional Boat,Fresh Fish Cooking', 'default-boat.jpg', 'default-boat.jpg', 'active', 'approved', 189, '2024-10-11 08:00:00');

-- Demo Boat Listings for Sangu River Cruises (Provider ID: 6)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(6, 'Sangu River Adventure Boat', 'Navigate the crystal clear waters of Sangu River through hills and valleys. Scenic journey with stops at picturesque locations. Includes local guide and lunch.', 'boat', 'Sangu River, Bandarban', 12000.00, 'day', 12, 'Life Jackets,Captain,Local Guide,Lunch,Photography Stops,Swimming Areas', 'default-boat.jpg', 'default-boat.jpg', 'active', 'approved', 156, '2024-10-09 10:00:00'),

(6, 'Family River Cruise', 'Gentle river cruise perfect for families with children. Safe and comfortable journey through scenic landscapes. Includes snacks and beverages.', 'boat', 'Sangu River, Bandarban', 8000.00, 'day', 10, 'Life Jackets,Captain,Crew,Snacks,Beverages,Toilet,Shade,Child Safety', 'default-boat.jpg', 'default-boat.jpg', 'active', 'approved', 134, '2024-10-14 12:30:00');

-- Pending Listings (Awaiting Approval)
INSERT INTO `listings` (`provider_id`, `title`, `description`, `category`, `location`, `price`, `price_unit`, `capacity`, `amenities`, `main_image`, `images`, `status`, `approval_status`, `views`, `created_at`) VALUES
(7, 'Cozy Beach Cabin', 'Affordable beach cabin just 50 meters from the sea. Simple, clean, and comfortable accommodation for budget travelers. Fan-cooled with attached bathroom.', 'room', 'Cox\'s Bazar, Chittagong', 2500.00, 'night', 2, 'Fan,Attached Bathroom,Beach Access,Parking,WiFi', 'default-room.jpg', 'default-room.jpg', 'active', 'pending', 23, '2024-11-16 09:00:00'),

(7, 'Beachfront Budget Room', 'Simple room with sea view at affordable price. Perfect for backpackers and budget-conscious travelers. Basic amenities with clean facilities.', 'room', 'Cox\'s Bazar, Chittagong', 3000.00, 'night', 3, 'Fan,Sea View,Shared Balcony,WiFi,Common Kitchen', 'default-room.jpg', 'default-room.jpg', 'active', 'pending', 18, '2024-11-17 11:30:00'),

(8, 'Inani Paradise Deluxe Suite', 'Luxury suite on the pristine Inani Beach. Away from the crowds, enjoy peace and tranquility. Modern amenities with traditional hospitality.', 'room', 'Inani Beach, Cox\'s Bazar', 9500.00, 'night', 2, 'WiFi,Air Conditioning,TV,Mini Bar,Beach View,Balcony,Room Service', 'default-room.jpg', 'default-room.jpg', 'active', 'pending', 31, '2024-11-18 14:00:00'),

(8, 'Golden Beach Family Room', 'Spacious family room with separate sleeping areas. Close to the beach with easy access to local restaurants and shops. Great value for families.', 'room', 'Inani Beach, Cox\'s Bazar', 6500.00, 'night', 4, 'WiFi,Air Conditioning,TV,Beach Access,Parking,Refrigerator', 'default-room.jpg', 'default-room.jpg', 'active', 'pending', 27, '2024-11-19 10:15:00');

-- Demo Bookings (Sample booking history)
INSERT INTO `bookings` (`user_id`, `listing_id`, `provider_id`, `booking_date`, `start_date`, `end_date`, `duration`, `total_price`, `status`, `payment_status`, `booking_reference`, `special_requests`, `created_at`) VALUES
-- Completed bookings
(1, 1, 1, '2024-10-20', '2024-10-25', '2024-10-27', 2, 17000.00, 'completed', 'paid', 'TRP-2024-001', 'Late check-in requested', '2024-10-20 14:30:00'),
(2, 3, 1, '2024-10-22', '2024-11-01', '2024-11-03', 2, 50000.00, 'completed', 'paid', 'TRP-2024-002', 'Anniversary celebration, need cake arrangement', '2024-10-22 10:15:00'),
(3, 6, 3, '2024-10-25', '2024-11-05', '2024-11-07', 2, 13000.00, 'completed', 'paid', 'TRP-2024-003', NULL, '2024-10-25 16:45:00'),

-- Confirmed upcoming bookings
(4, 2, 1, '2024-11-15', '2024-11-25', '2024-11-27', 2, 30000.00, 'confirmed', 'paid', 'TRP-2024-004', 'Honeymoon trip, need room decoration', '2024-11-15 09:20:00'),
(5, 7, 3, '2024-11-16', '2024-11-28', '2024-11-30', 2, 20000.00, 'confirmed', 'paid', 'TRP-2024-005', 'Vegetarian meals preferred', '2024-11-16 11:30:00'),
(1, 11, 5, '2024-11-17', '2024-12-01', '2024-12-01', 3, 75000.00, 'confirmed', 'paid', 'TRP-2024-006', 'Corporate event, need catering for 15 people', '2024-11-17 14:00:00'),

-- Pending bookings
(2, 4, 2, '2024-11-19', '2024-12-05', '2024-12-07', 2, 15000.00, 'pending', 'pending', 'TRP-2024-007', NULL, '2024-11-19 10:45:00'),
(3, 13, 6, '2024-11-20', '2024-12-10', '2024-12-10', 1, 12000.00, 'pending', 'pending', 'TRP-2024-008', 'Birthday celebration for 10 people', '2024-11-20 15:30:00');

-- Demo Reviews
INSERT INTO `reviews` (`user_id`, `listing_id`, `provider_id`, `booking_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 1, 1, 1, 5, 'Absolutely amazing experience! The ocean view was breathtaking and the staff was incredibly helpful. The room was spotless and exceeded our expectations. Will definitely come back!', 'approved', '2024-10-28 09:00:00'),
(2, 3, 1, 2, 5, 'Perfect for our anniversary! The villa was spacious, private, and beautifully maintained. Direct beach access was a huge plus. The staff went above and beyond to make our stay special.', 'approved', '2024-11-04 11:30:00'),
(3, 6, 3, 3, 4, 'Great hilltop location with stunning valley views. The cottage was cozy and comfortable. Only downside was the WiFi could be better, but that\'s expected in the hills. Loved the mountain air!', 'approved', '2024-11-08 14:15:00');

-- Demo Notifications
INSERT INTO `notifications` (`user_type`, `user_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
-- User notifications
('user', 4, 'Booking Confirmed', 'Your booking for Premium Beach Suite has been confirmed. Reference: TRP-2024-004', 'booking', '/user/booking-details.php?id=4', false, '2024-11-15 09:25:00'),
('user', 5, 'Booking Confirmed', 'Your booking for Valley View Family Bungalow has been confirmed. Reference: TRP-2024-005', 'booking', '/user/booking-details.php?id=5', false, '2024-11-16 11:35:00'),
('user', 2, 'Booking Pending', 'Your booking request for Sunset View Superior Room is pending provider confirmation. Reference: TRP-2024-007', 'booking', '/user/booking-details.php?id=7', true, '2024-11-19 10:50:00'),

-- Provider notifications
('provider', 1, 'New Booking Request', 'You have a new booking request for Premium Beach Suite from Nusrat Jahan', 'booking', '/provider/bookings.php?id=4', false, '2024-11-15 09:25:00'),
('provider', 2, 'New Booking Request', 'You have a new booking request for Sunset View Superior Room from Nusrat Jahan', 'booking', '/provider/bookings.php?id=7', true, '2024-11-19 10:50:00'),
('provider', 5, 'New Booking Request', 'You have a new booking request for Luxury Yacht Bay Cruise from Farhan Ahmed', 'booking', '/provider/bookings.php?id=6', false, '2024-11-17 14:05:00'),

-- Admin notifications
('admin', 1, 'New Provider Registration', 'New provider Marine View Inn has registered and awaits verification', 'provider_registration', '/admin/providers.php', false, '2024-11-15 16:35:00'),
('admin', 1, 'New Provider Registration', 'New provider Golden Sands Resort has registered and awaits verification', 'provider_registration', '/admin/providers.php', false, '2024-11-18 10:50:00'),
('admin', 1, 'New Listing Pending', 'New listing Cozy Beach Cabin is pending approval', 'listing_approval', '/admin/listings.php', false, '2024-11-16 09:05:00');

-- Demo Activity Logs
INSERT INTO `activity_logs` (`user_type`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
('user', 1, 'login', 'User logged in', '192.168.1.100', '2024-11-20 08:30:00'),
('user', 1, 'booking_created', 'Created booking #TRP-2024-001', '192.168.1.100', '2024-10-20 14:30:00'),
('user', 2, 'registration', 'User registered successfully', '192.168.1.101', '2024-10-20 14:20:00'),
('user', 2, 'login', 'User logged in', '192.168.1.101', '2024-11-19 10:00:00'),
('provider', 1, 'login', 'Provider logged in', '192.168.1.200', '2024-11-20 09:00:00'),
('provider', 7, 'registration', 'Provider registered successfully', '192.168.1.201', '2024-11-15 16:30:00'),
('provider', 5, 'listing_created', 'Created new listing: Traditional Fishing Boat Experience', '192.168.1.200', '2024-10-11 08:00:00'),
('admin', 1, 'login', 'Admin logged in', '192.168.1.1', '2024-11-20 08:00:00'),
('admin', 1, 'provider_verified', 'Verified provider: Sea Pearl Beach Resort', '192.168.1.1', '2024-09-02 10:00:00');

-- ============================================
-- Summary of Demo Data
-- ============================================
-- Users: 5 travelers
-- Providers: 8 total (6 verified, 2 pending)
-- Listings: 22 total (18 approved, 4 pending)
--   - Rooms: 17 (14 approved, 3 pending)
--   - Boats: 5 (4 approved, 1 pending)
-- Bookings: 8 (3 completed, 3 confirmed, 2 pending)
-- Reviews: 3 approved reviews
-- Notifications: 9 notifications
-- Activity Logs: 9 log entries

-- Locations covered:
-- - Cox's Bazar (Beach resorts, hotels, boat tours)
-- - Bandarban (Hill resorts, river cruises)
-- - Tanguar Haor (Eco resort, wetland experience)
-- - Inani Beach (Boutique resorts)
-- - Sangu River (River cruises)

-- Password for all demo accounts: password
-- (Hashed with bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)

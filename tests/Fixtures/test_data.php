<?php
/**
 * Test Data Fixtures
 * Sample data for testing purposes
 */

return [
    'users' => [
        [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'phone' => '01712345678',
            'status' => 'active'
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => 'password123',
            'phone' => '01823456789',
            'status' => 'active'
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob.johnson@example.com',
            'password' => 'password123',
            'phone' => '01934567890',
            'status' => 'active'
        ]
    ],

    'providers' => [
        [
            'business_name' => 'Sea Adventure Tours',
            'owner_name' => 'Michael Chen',
            'email' => 'michael@seatours.com',
            'password' => 'provider123',
            'phone' => '01712345001',
            'description' => 'Premier boat tour operator in Cox\'s Bazar',
            'address' => 'Beach Road, Cox\'s Bazar',
            'verification_status' => 'verified',
            'status' => 'active'
        ],
        [
            'business_name' => 'Sundarbans Eco Resort',
            'owner_name' => 'Sarah Rahman',
            'email' => 'sarah@sundarbansresort.com',
            'password' => 'provider123',
            'phone' => '01812345002',
            'description' => 'Eco-friendly resort in Sundarbans',
            'address' => 'Sundarbans, Khulna',
            'verification_status' => 'verified',
            'status' => 'active'
        ]
    ],

    'listings' => [
        [
            'title' => 'Luxury Speed Boat - Cox\'s Bazar',
            'description' => 'Experience the thrill of speed boating in Cox\'s Bazar. Perfect for groups and families.',
            'category' => 'boat',
            'location' => 'Cox\'s Bazar',
            'price' => 2500.00,
            'price_unit' => 'hour',
            'capacity' => 8,
            'amenities' => 'Life jackets, Safety equipment, Professional guide',
            'main_image' => 'boat1.jpg',
            'status' => 'active',
            'approval_status' => 'approved'
        ],
        [
            'title' => 'Deluxe Room with Sea View',
            'description' => 'Beautiful room with stunning sea views. Includes all modern amenities.',
            'category' => 'room',
            'location' => 'Cox\'s Bazar',
            'price' => 3500.00,
            'price_unit' => 'night',
            'capacity' => 2,
            'amenities' => 'AC, WiFi, TV, Mini-bar, Sea view balcony',
            'main_image' => 'room1.jpg',
            'status' => 'active',
            'approval_status' => 'approved'
        ],
        [
            'title' => 'Mangrove Safari Boat',
            'description' => 'Explore the Sundarbans mangrove forest with our experienced guides.',
            'category' => 'boat',
            'location' => 'Sundarbans',
            'price' => 5000.00,
            'price_unit' => 'day',
            'capacity' => 12,
            'amenities' => 'Guide, Meals, Safety equipment, Binoculars',
            'main_image' => 'boat2.jpg',
            'status' => 'active',
            'approval_status' => 'approved'
        ],
        [
            'title' => 'Eco Cottage in Forest',
            'description' => 'Stay in harmony with nature in our eco-friendly cottages.',
            'category' => 'room',
            'location' => 'Sundarbans',
            'price' => 2000.00,
            'price_unit' => 'night',
            'capacity' => 4,
            'amenities' => 'Fan, Mosquito net, Private bathroom, Nature view',
            'main_image' => 'cottage1.jpg',
            'status' => 'active',
            'approval_status' => 'approved'
        ]
    ],

    'bookings' => [
        [
            'booking_date' => '2024-01-15',
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-02',
            'duration' => 24,
            'total_price' => 60000.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'special_requests' => 'Please arrange pickup from hotel'
        ],
        [
            'booking_date' => '2024-01-20',
            'start_date' => '2024-02-10',
            'end_date' => '2024-02-12',
            'duration' => 2,
            'total_price' => 7000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'special_requests' => null
        ]
    ],

    'reviews' => [
        [
            'rating' => 5,
            'comment' => 'Excellent service! The boat was clean and the guide was very knowledgeable.',
            'status' => 'approved'
        ],
        [
            'rating' => 4,
            'comment' => 'Great experience overall. Would recommend to friends.',
            'status' => 'approved'
        ],
        [
            'rating' => 5,
            'comment' => 'Beautiful location and very comfortable room. Staff was friendly.',
            'status' => 'approved'
        ]
    ]
];

<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if user is logged in
if (!is_logged_in(ROLE_USER)) {
    flash_message('error', 'Please login to make a booking');
    redirect(base_url('login.php'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('search.php'));
}

$userId = get_user_id(ROLE_USER);
$listingId = (int)($_POST['listing_id'] ?? 0);
$startDate = sanitize_input($_POST['start_date'] ?? '');
$endDate = sanitize_input($_POST['end_date'] ?? '');
$guests = (int)($_POST['guests'] ?? 1);
$specialRequests = sanitize_input($_POST['special_requests'] ?? '');

// Validation
if (!$listingId || !$startDate || !$endDate) {
    flash_message('error', 'Please fill in all required fields');
    redirect(base_url('listing-details.php?id=' . $listingId));
}

// Get listing details
$listing = db('listings')->where('listing_id', $listingId)->first();

if (!$listing || $listing['status'] !== 'active') {
    flash_message('error', 'Listing not available');
    redirect(base_url('search.php'));
}

// Validate dates
$start = new DateTime($startDate);
$end = new DateTime($endDate);
$today = new DateTime();
$today->setTime(0, 0, 0);

if ($start < $today) {
    flash_message('error', 'Start date cannot be in the past');
    redirect(base_url('listing-details.php?id=' . $listingId));
}

if ($end <= $start) {
    flash_message('error', 'End date must be after start date');
    redirect(base_url('listing-details.php?id=' . $listingId));
}

// Calculate duration and price
$interval = $start->diff($end);
$days = $interval->days;

$duration = $days;
if ($listing['price_unit'] === 'hour') {
    $duration = $days * 24;
}

$totalPrice = $listing['price'] * $duration;

// Check if dates are available
$conflictingBookings = db('bookings')->raw(
    "SELECT COUNT(*) as count FROM bookings 
     WHERE listing_id = ? 
     AND status IN ('pending', 'confirmed')
     AND (
         (start_date <= ? AND end_date >= ?) OR
         (start_date <= ? AND end_date >= ?) OR
         (start_date >= ? AND end_date <= ?)
     )",
    [$listingId, $startDate, $startDate, $endDate, $endDate, $startDate, $endDate]
);

if ($conflictingBookings[0]['count'] > 0) {
    flash_message('error', 'Selected dates are not available. Please choose different dates.');
    redirect(base_url('listing-details.php?id=' . $listingId));
}

// Generate booking reference
$bookingReference = generate_booking_reference();

// Create booking
try {
    $bookingId = db('bookings')->insert([
        'user_id' => $userId,
        'listing_id' => $listingId,
        'provider_id' => $listing['provider_id'],
        'booking_date' => date('Y-m-d'),
        'start_date' => $startDate,
        'end_date' => $endDate,
        'duration' => $duration,
        'total_price' => $totalPrice,
        'status' => 'pending',
        'payment_status' => 'pending',
        'booking_reference' => $bookingReference,
        'special_requests' => $specialRequests
    ]);
    
    if ($bookingId) {
        // Create notification for provider
        db('notifications')->insert([
            'user_type' => 'provider',
            'user_id' => $listing['provider_id'],
            'title' => 'New Booking Request',
            'message' => "You have a new booking request for {$listing['title']}",
            'type' => 'booking',
            'link' => '/provider/bookings.php?id=' . $bookingId
        ]);
        
        // Create notification for user
        db('notifications')->insert([
            'user_type' => 'user',
            'user_id' => $userId,
            'title' => 'Booking Submitted',
            'message' => "Your booking request for {$listing['title']} has been submitted",
            'type' => 'booking',
            'link' => '/user/bookings.php?id=' . $bookingId
        ]);
        
        // Log activity
        db('activity_logs')->insert([
            'user_type' => 'user',
            'user_id' => $userId,
            'action' => 'booking_created',
            'description' => "Created booking #{$bookingReference}",
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        flash_message('success', 'Booking request submitted successfully! Reference: ' . $bookingReference);
        redirect(base_url('booking-confirmation.php?ref=' . $bookingReference));
    } else {
        throw new Exception('Failed to create booking');
    }
} catch (Exception $e) {
    flash_message('error', 'Failed to process booking. Please try again.');
    redirect(base_url('listing-details.php?id=' . $listingId));
}

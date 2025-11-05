<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$bookingId) {
    flash_message('error', 'Invalid booking');
    redirect(base_url('user/bookings.php'));
}

// Get booking details
$booking = db('bookings')
    ->where('booking_id', $bookingId)
    ->where('user_id', $userId)
    ->first();

if (!$booking) {
    flash_message('error', 'Booking not found');
    redirect(base_url('user/bookings.php'));
}

// Check if booking can be cancelled
if ($booking['status'] !== 'pending' && $booking['status'] !== 'confirmed') {
    flash_message('error', 'This booking cannot be cancelled');
    redirect(base_url('user/booking-details.php?id=' . $bookingId));
}

// Check cancellation deadline (e.g., 24 hours before start date)
$startDate = new DateTime($booking['start_date']);
$now = new DateTime();
$hoursUntilStart = ($startDate->getTimestamp() - $now->getTimestamp()) / 3600;

if ($hoursUntilStart < 24) {
    flash_message('error', 'Cannot cancel booking less than 24 hours before check-in');
    redirect(base_url('user/booking-details.php?id=' . $bookingId));
}

// Update booking status
$updated = db('bookings')->where('booking_id', $bookingId)->update([
    'status' => 'cancelled'
]);

if ($updated) {
    // Create notification for provider
    db('notifications')->insert([
        'user_type' => 'provider',
        'user_id' => $booking['provider_id'],
        'title' => 'Booking Cancelled',
        'message' => "Booking #{$booking['booking_reference']} has been cancelled by the user",
        'type' => 'booking',
        'link' => '/provider/bookings.php?id=' . $bookingId
    ]);
    
    // Create notification for user
    db('notifications')->insert([
        'user_type' => 'user',
        'user_id' => $userId,
        'title' => 'Booking Cancelled',
        'message' => "Your booking #{$booking['booking_reference']} has been cancelled successfully",
        'type' => 'booking',
        'link' => '/user/booking-details.php?id=' . $bookingId
    ]);
    
    // Log activity
    db('activity_logs')->insert([
        'user_type' => 'user',
        'user_id' => $userId,
        'action' => 'booking_cancelled',
        'description' => "Cancelled booking #{$booking['booking_reference']}",
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    flash_message('success', 'Booking cancelled successfully. Reference: ' . $booking['booking_reference']);
} else {
    flash_message('error', 'Failed to cancel booking. Please try again.');
}

redirect(base_url('user/bookings.php'));

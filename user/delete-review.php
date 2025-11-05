<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$reviewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$reviewId) {
    flash_message('error', 'Invalid review');
    redirect(base_url('user/reviews.php'));
}

// Verify review belongs to user
$review = db('reviews')
    ->where('review_id', $reviewId)
    ->where('user_id', $userId)
    ->first();

if (!$review) {
    flash_message('error', 'Review not found');
    redirect(base_url('user/reviews.php'));
}

// Delete review
$deleted = db('reviews')->raw(
    "DELETE FROM reviews WHERE review_id = ? AND user_id = ?",
    [$reviewId, $userId]
);

if ($deleted) {
    // Log activity
    db('activity_logs')->insert([
        'user_type' => 'user',
        'user_id' => $userId,
        'action' => 'review_deleted',
        'description' => "Deleted review #$reviewId",
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    flash_message('success', 'Review deleted successfully');
} else {
    flash_message('error', 'Failed to delete review');
}

redirect(base_url('user/reviews.php'));

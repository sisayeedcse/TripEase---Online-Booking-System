<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();
$bookingId = isset($_GET['booking']) ? (int)$_GET['booking'] : 0;

if (!$bookingId) {
    flash_message('error', 'Invalid booking');
    redirect(base_url('user/bookings.php'));
}

// Get booking details
$booking = db('bookings')->raw(
    "SELECT bookings.*, listings.title, listings.listing_id, listings.main_image,
     providers.business_name, providers.provider_id
     FROM bookings
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     WHERE bookings.booking_id = ? AND bookings.user_id = ? AND bookings.status = 'completed'",
    [$bookingId, $userId]
);

if (empty($booking)) {
    flash_message('error', 'Booking not found or not completed');
    redirect(base_url('user/bookings.php'));
}

$booking = $booking[0];

// Check if review already exists
$existingReview = db('reviews')
    ->where('user_id', $userId)
    ->where('listing_id', $booking['listing_id'])
    ->where('booking_id', $bookingId)
    ->first();

if ($existingReview) {
    flash_message('info', 'You have already reviewed this booking');
    redirect(base_url('user/reviews.php'));
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = sanitize_input($_POST['comment'] ?? '');
    
    if ($rating < 1 || $rating > 5) {
        $error = 'Please select a rating between 1 and 5 stars';
    } elseif (empty($comment)) {
        $error = 'Please write a review comment';
    } elseif (strlen($comment) < 10) {
        $error = 'Review comment must be at least 10 characters';
    } else {
        // Insert review
        $reviewId = db('reviews')->insert([
            'user_id' => $userId,
            'listing_id' => $booking['listing_id'],
            'provider_id' => $booking['provider_id'],
            'booking_id' => $bookingId,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending'
        ]);
        
        if ($reviewId) {
            // Create notification for provider
            db('notifications')->insert([
                'user_type' => 'provider',
                'user_id' => $booking['provider_id'],
                'title' => 'New Review Received',
                'message' => "You received a new review for {$booking['title']}",
                'type' => 'review',
                'link' => '/provider/reviews.php'
            ]);
            
            // Log activity
            db('activity_logs')->insert([
                'user_type' => 'user',
                'user_id' => $userId,
                'action' => 'review_created',
                'description' => "Created review for {$booking['title']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            
            flash_message('success', 'Review submitted successfully! It will be visible after admin approval.');
            redirect(base_url('user/reviews.php'));
        } else {
            $error = 'Failed to submit review. Please try again.';
        }
    }
}

$pageTitle = 'Write Review - TripEase';
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-4 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="dashboard-header">
                    <h2><i class="fas fa-star"></i> Write a Review</h2>
                    <p class="text-muted">Share your experience with other travelers</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Booking Info -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle"></i> Booking Details</h4>
                            </div>
                            <div class="card-body">
                                <div class="booking-review-info">
                                    <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                                         class="booking-review-image">
                                    <div>
                                        <h5><?php echo htmlspecialchars($booking['title']); ?></h5>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($booking['business_name']); ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo format_date($booking['start_date'], 'd M Y'); ?> - 
                                            <?php echo format_date($booking['end_date'], 'd M Y'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Form -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-edit"></i> Your Review</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <!-- Rating -->
                                    <div class="mb-4">
                                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                                        <div class="star-rating">
                                            <input type="radio" id="star5" name="rating" value="5" required>
                                            <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" id="star4" name="rating" value="4">
                                            <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" id="star3" name="rating" value="3">
                                            <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" id="star2" name="rating" value="2">
                                            <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" id="star1" name="rating" value="1">
                                            <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                        </div>
                                        <small class="text-muted">Click on the stars to rate your experience</small>
                                    </div>

                                    <!-- Comment -->
                                    <div class="mb-4">
                                        <label class="form-label">Your Review <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="comment" rows="6" 
                                                  placeholder="Tell us about your experience... (minimum 10 characters)" 
                                                  required minlength="10"></textarea>
                                        <small class="text-muted">Share details about the service, cleanliness, value, and overall experience</small>
                                    </div>

                                    <!-- Submit -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Submit Review
                                        </button>
                                        <a href="<?php echo base_url('user/bookings.php'); ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Sidebar -->
                    <div class="col-lg-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-lightbulb"></i> Review Tips</h4>
                            </div>
                            <div class="card-body">
                                <ul class="tips-list">
                                    <li><i class="fas fa-check-circle text-success"></i> Be honest and specific</li>
                                    <li><i class="fas fa-check-circle text-success"></i> Mention what you liked</li>
                                    <li><i class="fas fa-check-circle text-success"></i> Share constructive feedback</li>
                                    <li><i class="fas fa-check-circle text-success"></i> Keep it respectful</li>
                                    <li><i class="fas fa-check-circle text-success"></i> Minimum 10 characters</li>
                                </ul>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> Your review will be reviewed by our team before being published.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.booking-review-info {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.booking-review-image {
    width: 120px;
    height: 100px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 0.5rem;
    font-size: 2.5rem;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    color: var(--gray-300);
    transition: all var(--transition-fast);
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #FFD700;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tips-list li:last-child {
    border-bottom: none;
}

@media (max-width: 767px) {
    .booking-review-info {
        flex-direction: column;
        text-align: center;
    }
    
    .booking-review-image {
        width: 100%;
        height: 200px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>

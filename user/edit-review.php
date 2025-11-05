<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();
$reviewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$reviewId) {
    flash_message('error', 'Invalid review');
    redirect(base_url('user/reviews.php'));
}

// Get review details
$review = db('reviews')->raw(
    "SELECT reviews.*, listings.title, listings.main_image, providers.business_name
     FROM reviews
     LEFT JOIN listings ON reviews.listing_id = listings.listing_id
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     WHERE reviews.review_id = ? AND reviews.user_id = ?",
    [$reviewId, $userId]
);

if (empty($review)) {
    flash_message('error', 'Review not found');
    redirect(base_url('user/reviews.php'));
}

$review = $review[0];

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
        // Update review
        $updated = db('reviews')->where('review_id', $reviewId)->update([
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending' // Reset to pending after edit
        ]);
        
        if ($updated) {
            flash_message('success', 'Review updated successfully! It will be reviewed again by admin.');
            redirect(base_url('user/reviews.php'));
        } else {
            $error = 'Failed to update review. Please try again.';
        }
    }
}

$pageTitle = 'Edit Review - TripEase';
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
                    <h2><i class="fas fa-edit"></i> Edit Review</h2>
                    <p class="text-muted">Update your review</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Listing Info -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle"></i> Listing Details</h4>
                            </div>
                            <div class="card-body">
                                <div class="booking-review-info">
                                    <img src="<?php echo uploads_url('listings/' . ($review['main_image'] ?? 'default-listing.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         class="booking-review-image">
                                    <div>
                                        <h5><?php echo htmlspecialchars($review['title']); ?></h5>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($review['business_name']); ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar"></i> Reviewed <?php echo time_ago($review['created_at']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-edit"></i> Update Your Review</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <!-- Rating -->
                                    <div class="mb-4">
                                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                                        <div class="star-rating">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                                   <?php echo $review['rating'] == $i ? 'checked' : ''; ?> required>
                                            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">
                                                <i class="fas fa-star"></i>
                                            </label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- Comment -->
                                    <div class="mb-4">
                                        <label class="form-label">Your Review <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="comment" rows="6" 
                                                  required minlength="10"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                                    </div>

                                    <!-- Submit -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Review
                                        </button>
                                        <a href="<?php echo base_url('user/reviews.php'); ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> After updating, your review will be sent for admin approval again.
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
</style>

<?php include '../includes/footer.php'; ?>

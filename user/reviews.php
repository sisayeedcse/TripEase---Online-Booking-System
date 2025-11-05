<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();

// Get user's reviews
$reviews = db('reviews')->raw(
    "SELECT reviews.*, listings.title as listing_title, listings.main_image,
     listings.category, providers.business_name
     FROM reviews
     LEFT JOIN listings ON reviews.listing_id = listings.listing_id
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     WHERE reviews.user_id = ?
     ORDER BY reviews.created_at DESC",
    [$userId]
);

$pageTitle = 'My Reviews - TripEase';
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
                    <h2><i class="fas fa-star"></i> My Reviews</h2>
                    <p class="text-muted">Manage your reviews and ratings</p>
                </div>

                <?php if (!empty($reviews)): ?>
                    <div class="row">
                        <?php foreach ($reviews as $review): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="review-card-full">
                                <!-- Listing Info -->
                                <div class="review-listing-info">
                                    <img src="<?php echo uploads_url('listings/' . ($review['main_image'] ?? 'default-listing.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($review['listing_title']); ?>" 
                                         class="review-listing-image">
                                    <div class="review-listing-details">
                                        <h5><?php echo htmlspecialchars($review['listing_title']); ?></h5>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($review['business_name']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="review-rating-display">
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-text"><?php echo $review['rating']; ?>/5</span>
                                </div>

                                <!-- Comment -->
                                <div class="review-comment">
                                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>

                                <!-- Meta Info -->
                                <div class="review-meta">
                                    <span class="review-date">
                                        <i class="fas fa-calendar"></i> <?php echo time_ago($review['created_at']); ?>
                                    </span>
                                    <span class="review-status status-<?php echo $review['status']; ?>">
                                        <i class="fas fa-circle"></i> <?php echo ucfirst($review['status']); ?>
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="review-actions">
                                    <?php if ($review['status'] === 'pending'): ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Pending Approval
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?php echo base_url('user/edit-review.php?id=' . $review['review_id']); ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $review['review_id']; ?>)" 
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-star fa-4x text-muted mb-3"></i>
                        <h4>No Reviews Yet</h4>
                        <p class="text-muted">You haven't written any reviews. Complete a booking to leave a review!</p>
                        <a href="<?php echo base_url('user/bookings.php?filter=completed'); ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> View Completed Bookings
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.review-card-full {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
}

.review-card-full:hover {
    box-shadow: var(--shadow-lg);
}

.review-listing-info {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.review-listing-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.review-listing-details h5 {
    margin-bottom: 0.5rem;
    color: var(--gray-900);
}

.review-rating-display {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.review-rating-display .stars {
    display: flex;
    gap: 0.25rem;
}

.review-rating-display .stars i {
    color: var(--gray-300);
    font-size: 1.25rem;
}

.review-rating-display .stars i.active {
    color: #FFD700;
}

.rating-text {
    font-weight: 700;
    color: var(--gray-900);
}

.review-comment {
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.review-comment p {
    margin: 0;
    color: var(--gray-700);
    line-height: 1.6;
}

.review-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.review-date {
    color: var(--gray-600);
    font-size: 0.9rem;
}

.review-status {
    font-size: 0.85rem;
    font-weight: 600;
}

.status-approved {
    color: var(--success-color);
}

.status-pending {
    color: #f39c12;
}

.status-rejected {
    color: var(--danger-color);
}

.review-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.empty-state {
    background: white;
    padding: 4rem 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
}
</style>

<script>
function confirmDelete(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        window.location.href = 'delete-review.php?id=' + reviewId;
    }
}
</script>

<?php include '../includes/footer.php'; ?>

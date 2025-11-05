<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reviewId = (int)$_POST['review_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'approve':
            db('reviews')->where('review_id', $reviewId)->update(['status' => 'approved']);
            flash_message('success', 'Review approved successfully');
            break;
        case 'reject':
            db('reviews')->where('review_id', $reviewId)->update(['status' => 'rejected']);
            flash_message('success', 'Review rejected');
            break;
        case 'delete':
            db('reviews')->raw("DELETE FROM reviews WHERE review_id = ?", [$reviewId]);
            flash_message('success', 'Review deleted successfully');
            break;
    }
    redirect(base_url('admin/reviews.php'));
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT reviews.*, users.name as user_name, users.email as user_email,
          listings.title as listing_title, providers.business_name
          FROM reviews
          LEFT JOIN users ON reviews.user_id = users.user_id
          LEFT JOIN listings ON reviews.listing_id = listings.listing_id
          LEFT JOIN providers ON reviews.provider_id = providers.provider_id
          WHERE 1=1";
$params = [];

if ($filter === 'pending') {
    $query .= " AND reviews.status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND reviews.status = 'approved'";
} elseif ($filter === 'rejected') {
    $query .= " AND reviews.status = 'rejected'";
}

if ($search) {
    $query .= " AND (users.name LIKE ? OR listings.title LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm];
}

$query .= " ORDER BY reviews.created_at DESC";

$reviews = db('reviews')->raw($query, $params);

// Get statistics
$totalReviews = db('reviews')->count();
$pendingReviews = db('reviews')->where('status', 'pending')->count();
$approvedReviews = db('reviews')->where('status', 'approved')->count();
$rejectedReviews = db('reviews')->where('status', 'rejected')->count();
$avgRating = db('reviews')->raw("SELECT AVG(rating) as avg FROM reviews WHERE status = 'approved'")[0]['avg'];

$pageTitle = 'Review Management - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-10 col-md-9">
                <div class="admin-header">
                    <div>
                        <h2><i class="fas fa-star"></i> Review Management</h2>
                        <p class="text-muted">Moderate user reviews and ratings</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-star text-warning"></i>
                            <div>
                                <h4><?php echo $totalReviews; ?></h4>
                                <p>Total Reviews</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingReviews; ?></h4>
                                <p>Pending Approval</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $approvedReviews; ?></h4>
                                <p>Approved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-chart-line text-info"></i>
                            <div>
                                <h4><?php echo number_format($avgRating, 1); ?></h4>
                                <p>Average Rating</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="admin-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="filter-tabs-inline">
                                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                                        All (<?php echo $totalReviews; ?>)
                                    </a>
                                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                                        Pending (<?php echo $pendingReviews; ?>)
                                    </a>
                                    <a href="?filter=approved" class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">
                                        Approved
                                    </a>
                                    <a href="?filter=rejected" class="filter-tab <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
                                        Rejected
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search reviews..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <?php if (!empty($reviews)): ?>
                <div class="row">
                    <?php foreach ($reviews as $review): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="review-card-admin">
                            <div class="review-header">
                                <div class="review-user">
                                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                    <small class="text-muted"><?php echo time_ago($review['created_at']); ?></small>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="rating-value"><?php echo $review['rating']; ?>/5</span>
                                </div>
                            </div>

                            <div class="review-listing">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong><?php echo htmlspecialchars($review['listing_title']); ?></strong>
                                <span class="text-muted">by <?php echo htmlspecialchars($review['business_name']); ?></span>
                            </div>

                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>

                            <div class="review-footer">
                                <span class="badge bg-<?php 
                                    echo $review['status'] === 'approved' ? 'success' : 
                                        ($review['status'] === 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($review['status']); ?>
                                </span>

                                <div class="review-actions">
                                    <?php if ($review['status'] === 'pending'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-warning" title="Reject">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Delete this review permanently?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-star fa-4x text-muted mb-3"></i>
                    <h4>No Reviews Found</h4>
                    <p class="text-muted">No reviews match your current filter.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.review-card-admin {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
}

.review-card-admin:hover {
    box-shadow: var(--shadow-lg);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.review-user strong {
    display: block;
    color: var(--gray-900);
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.review-rating .fa-star {
    color: var(--gray-300);
    font-size: 1rem;
}

.review-rating .fa-star.active {
    color: #FFD700;
}

.rating-value {
    margin-left: 0.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.review-listing {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.review-comment {
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    color: var(--gray-700);
    line-height: 1.6;
}

.review-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.review-actions {
    display: flex;
    gap: 0.5rem;
}
</style>

<?php include '../includes/footer.php'; ?>

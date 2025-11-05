<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('login.php'));
}

$providerId = get_user_id(ROLE_PROVIDER);
$provider = db('providers')->where('provider_id', $providerId)->first();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $listingId = (int)$_POST['listing_id'];
    $action = $_POST['action'];
    
    // Verify ownership
    $listing = db('listings')->where('listing_id', $listingId)->where('provider_id', $providerId)->first();
    
    if ($listing) {
        switch ($action) {
            case 'delete':
                db('listings')->raw("DELETE FROM listings WHERE listing_id = ? AND provider_id = ?", [$listingId, $providerId]);
                flash_message('success', 'Listing deleted successfully');
                break;
            case 'activate':
                if ($listing['approval_status'] === 'approved') {
                    db('listings')->where('listing_id', $listingId)->update(['status' => 'active']);
                    flash_message('success', 'Listing activated');
                } else {
                    flash_message('error', 'Only approved listings can be activated');
                }
                break;
            case 'deactivate':
                db('listings')->where('listing_id', $listingId)->update(['status' => 'inactive']);
                flash_message('success', 'Listing deactivated');
                break;
        }
    }
    redirect(base_url('provider/listings.php'));
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$category = $_GET['category'] ?? '';

// Build query
$query = "SELECT listings.*, COUNT(DISTINCT bookings.booking_id) as booking_count
          FROM listings
          LEFT JOIN bookings ON listings.listing_id = bookings.listing_id
          WHERE listings.provider_id = ?";
$params = [$providerId];

if ($filter === 'active') {
    $query .= " AND listings.status = 'active'";
} elseif ($filter === 'inactive') {
    $query .= " AND listings.status = 'inactive'";
} elseif ($filter === 'pending') {
    $query .= " AND listings.approval_status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND listings.approval_status = 'approved'";
}

if ($category) {
    $query .= " AND listings.category = ?";
    $params[] = $category;
}

$query .= " GROUP BY listings.listing_id ORDER BY listings.created_at DESC";

$listings = db('listings')->raw($query, $params);

// Get statistics
$totalListings = db('listings')->where('provider_id', $providerId)->count();
$activeListings = db('listings')->where('provider_id', $providerId)->where('status', 'active')->count();
$pendingListings = db('listings')->where('provider_id', $providerId)->where('approval_status', 'pending')->count();

$pageTitle = 'My Listings - Provider';
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
                    <div>
                        <h2><i class="fas fa-list"></i> My Listings</h2>
                        <p class="text-muted">Manage your boats and rooms</p>
                    </div>
                    <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Listing
                    </a>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-list text-primary"></i>
                            <div>
                                <h4><?php echo $totalListings; ?></h4>
                                <p>Total Listings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $activeListings; ?></h4>
                                <p>Active Listings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingListings; ?></h4>
                                <p>Pending Approval</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="admin-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <div class="filter-tabs-inline">
                                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                                        All (<?php echo $totalListings; ?>)
                                    </a>
                                    <a href="?filter=active" class="filter-tab <?php echo $filter === 'active' ? 'active' : ''; ?>">
                                        Active
                                    </a>
                                    <a href="?filter=inactive" class="filter-tab <?php echo $filter === 'inactive' ? 'active' : ''; ?>">
                                        Inactive
                                    </a>
                                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                                        Pending (<?php echo $pendingListings; ?>)
                                    </a>
                                    <a href="?filter=approved" class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">
                                        Approved
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" onchange="window.location.href='?filter=<?php echo $filter; ?>&category=' + this.value">
                                    <option value="">All Categories</option>
                                    <option value="boat" <?php echo $category === 'boat' ? 'selected' : ''; ?>>Boats</option>
                                    <option value="room" <?php echo $category === 'room' ? 'selected' : ''; ?>>Rooms</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listings Grid -->
                <?php if (!empty($listings)): ?>
                <div class="row">
                    <?php foreach ($listings as $listing): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="listing-card-provider">
                            <div class="listing-image-provider">
                                <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" 
                                     alt="<?php echo htmlspecialchars($listing['title']); ?>">
                                <div class="listing-badges">
                                    <span class="badge bg-<?php echo $listing['category'] === 'boat' ? 'primary' : 'success'; ?>">
                                        <i class="fas fa-<?php echo $listing['category'] === 'boat' ? 'ship' : 'bed'; ?>"></i>
                                        <?php echo ucfirst($listing['category']); ?>
                                    </span>
                                </div>
                                <div class="listing-status-badges">
                                    <span class="badge bg-<?php echo $listing['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($listing['status']); ?>
                                    </span>
                                    <span class="badge bg-<?php 
                                        echo $listing['approval_status'] === 'approved' ? 'success' : 
                                            ($listing['approval_status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($listing['approval_status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="listing-body-provider">
                                <h5><?php echo htmlspecialchars($listing['title']); ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?>
                                </p>
                                <div class="listing-meta-provider">
                                    <div>
                                        <span class="price"><?php echo format_price($listing['price']); ?></span>
                                        <small class="text-muted">/ <?php echo $listing['price_unit']; ?></small>
                                    </div>
                                    <div>
                                        <span class="capacity"><i class="fas fa-users"></i> <?php echo $listing['capacity']; ?></span>
                                    </div>
                                </div>
                                <div class="listing-stats">
                                    <span><i class="fas fa-eye"></i> <?php echo $listing['views']; ?> views</span>
                                    <span><i class="fas fa-calendar-check"></i> <?php echo $listing['booking_count']; ?> bookings</span>
                                </div>
                                <div class="listing-actions-provider">
                                    <a href="<?php echo base_url('listing-details.php?id=' . $listing['listing_id']); ?>" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?php echo base_url('provider/edit-listing.php?id=' . $listing['listing_id']); ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <?php if ($listing['status'] === 'inactive' && $listing['approval_status'] === 'approved'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    <?php elseif ($listing['status'] === 'active'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="deactivate">
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Delete this listing? This action cannot be undone.')">
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
                    <i class="fas fa-list fa-4x text-muted mb-3"></i>
                    <h4>No Listings Yet</h4>
                    <p class="text-muted">Start by adding your first boat or room listing</p>
                    <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Add Your First Listing
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.stat-card-small {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card-small i {
    font-size: 2rem;
}

.stat-card-small h4 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
}

.stat-card-small p {
    margin: 0;
    color: var(--gray-600);
}

.listing-card-provider {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: all var(--transition-base);
}

.listing-card-provider:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
}

.listing-image-provider {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.listing-image-provider img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.listing-badges {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
}

.listing-status-badges {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.listing-body-provider {
    padding: 1rem;
}

.listing-body-provider h5 {
    margin-bottom: 0.75rem;
    color: var(--gray-900);
}

.listing-meta-provider {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 1rem 0;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.listing-meta-provider .price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.listing-stats {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    font-size: 0.9rem;
}

.listing-actions-provider {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.empty-state {
    background: white;
    padding: 4rem 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
}
</style>

<?php include '../includes/footer.php'; ?>

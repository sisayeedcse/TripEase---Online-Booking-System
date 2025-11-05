<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $listingId = (int)$_POST['listing_id'];
        $action = $_POST['action'];
        
        switch ($action) {
            case 'approve':
                db('listings')->where('listing_id', $listingId)->update([
                    'approval_status' => 'approved',
                    'status' => 'active'
                ]);
                $listing = db('listings')->where('listing_id', $listingId)->first();
                db('notifications')->insert([
                    'user_type' => 'provider',
                    'user_id' => $listing['provider_id'],
                    'title' => 'Listing Approved',
                    'message' => 'Your listing has been approved and is now live!',
                    'type' => 'system'
                ]);
                flash_message('success', 'Listing approved successfully');
                break;
            case 'reject':
                db('listings')->where('listing_id', $listingId)->update([
                    'approval_status' => 'rejected'
                ]);
                flash_message('success', 'Listing rejected');
                break;
            case 'deactivate':
                db('listings')->where('listing_id', $listingId)->update(['status' => 'inactive']);
                flash_message('success', 'Listing deactivated');
                break;
            case 'activate':
                db('listings')->where('listing_id', $listingId)->update(['status' => 'active']);
                flash_message('success', 'Listing activated');
                break;
            case 'delete':
                db('listings')->raw("DELETE FROM listings WHERE listing_id = ?", [$listingId]);
                flash_message('success', 'Listing deleted successfully');
                break;
        }
        redirect(base_url('admin/listings.php'));
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT listings.*, providers.business_name 
          FROM listings 
          LEFT JOIN providers ON listings.provider_id = providers.provider_id 
          WHERE 1=1";
$params = [];

if ($filter === 'pending') {
    $query .= " AND listings.approval_status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND listings.approval_status = 'approved'";
} elseif ($filter === 'rejected') {
    $query .= " AND listings.approval_status = 'rejected'";
} elseif ($filter === 'active') {
    $query .= " AND listings.status = 'active'";
} elseif ($filter === 'inactive') {
    $query .= " AND listings.status = 'inactive'";
}

if ($category) {
    $query .= " AND listings.category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (listings.title LIKE ? OR listings.location LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$query .= " ORDER BY listings.created_at DESC";

$listings = db('listings')->raw($query, $params);

$totalListings = db('listings')->count();
$pendingListings = db('listings')->where('approval_status', 'pending')->count();
$activeListings = db('listings')->where('status', 'active')->count();

$pageTitle = 'Listing Management - Admin';
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
                        <h2><i class="fas fa-list"></i> Listing Management</h2>
                        <p class="text-muted">Moderate and manage all listings</p>
                    </div>
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
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingListings; ?></h4>
                                <p>Pending Approval</p>
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
                </div>

                <!-- Filters & Search -->
                <div class="admin-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <div class="filter-tabs-inline">
                                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                                        Pending (<?php echo $pendingListings; ?>)
                                    </a>
                                    <a href="?filter=approved" class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">Approved</a>
                                    <a href="?filter=active" class="filter-tab <?php echo $filter === 'active' ? 'active' : ''; ?>">Active</a>
                                    <a href="?filter=inactive" class="filter-tab <?php echo $filter === 'inactive' ? 'active' : ''; ?>">Inactive</a>
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
                        <div class="row">
                            <div class="col-md-12">
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <input type="hidden" name="category" value="<?php echo $category; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search listings..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listings Grid -->
                <div class="row">
                    <?php foreach ($listings as $listing): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="listing-card-admin">
                            <div class="listing-image-admin">
                                <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" 
                                     alt="<?php echo htmlspecialchars($listing['title']); ?>">
                                <div class="listing-badges">
                                    <span class="badge bg-<?php echo $listing['category'] === 'boat' ? 'primary' : 'success'; ?>">
                                        <?php echo ucfirst($listing['category']); ?>
                                    </span>
                                    <span class="badge bg-<?php 
                                        echo $listing['approval_status'] === 'approved' ? 'success' : 
                                            ($listing['approval_status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($listing['approval_status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="listing-body-admin">
                                <h5><?php echo htmlspecialchars($listing['title']); ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-store"></i> <?php echo htmlspecialchars($listing['business_name']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?>
                                </p>
                                <div class="listing-meta-admin">
                                    <span class="price"><?php echo format_price($listing['price']); ?></span>
                                    <span class="capacity"><i class="fas fa-users"></i> <?php echo $listing['capacity']; ?></span>
                                </div>
                                <div class="listing-actions-admin">
                                    <a href="<?php echo base_url('listing-details.php?id=' . $listing['listing_id']); ?>" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <?php if ($listing['approval_status'] === 'pending'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($listing['status'] === 'active'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="deactivate">
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Delete this listing?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.listing-card-admin {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: all var(--transition-base);
}

.listing-card-admin:hover {
    box-shadow: var(--shadow-lg);
}

.listing-image-admin {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.listing-image-admin img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.listing-badges {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    display: flex;
    gap: 0.5rem;
}

.listing-body-admin {
    padding: 1rem;
}

.listing-body-admin h5 {
    margin-bottom: 0.75rem;
    color: var(--gray-900);
}

.listing-meta-admin {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 1rem 0;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.listing-meta-admin .price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.listing-actions-admin {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
</style>

<?php include '../includes/footer.php'; ?>

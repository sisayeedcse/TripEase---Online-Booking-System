<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('login.php'));
}

$providerId = get_user_id(ROLE_PROVIDER);
$provider = db('providers')->where('provider_id', $providerId)->first();

// Check verification status
if ($provider['verification_status'] !== 'verified') {
    $pageTitle = 'Verification Pending - TripEase';
    include '../includes/header.php';
    ?>
    <section class="verification-pending">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="verification-card">
                        <div class="verification-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h2>Account Verification Pending</h2>
                        <p class="lead">Thank you for registering as a service provider!</p>
                        <p>Your account is currently under review by our admin team. You will be able to add listings and manage bookings once your account is verified.</p>
                        <div class="verification-status">
                            <strong>Status:</strong> 
                            <span class="badge bg-<?php echo $provider['verification_status'] === 'pending' ? 'warning' : 'danger'; ?>">
                                <?php echo ucfirst($provider['verification_status']); ?>
                            </span>
                        </div>
                        <p class="mt-4">We typically review applications within 24-48 hours. You will receive a notification once your account is verified.</p>
                        <a href="<?php echo base_url('logout.php'); ?>" class="btn btn-outline-primary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
    .verification-pending {
        padding: 4rem 0;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
    }
    .verification-card {
        background: white;
        padding: 3rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        text-align: center;
    }
    .verification-icon {
        width: 100px;
        height: 100px;
        background: #FFF3CD;
        color: #856404;
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 2rem;
    }
    .verification-status {
        background: var(--gray-50);
        padding: 1rem;
        border-radius: var(--radius-md);
        margin: 2rem 0;
    }
    </style>
    <?php
    include '../includes/footer.php';
    exit;
}

// Get statistics
$totalListings = db('listings')->where('provider_id', $providerId)->count();
$activeListings = db('listings')->where('provider_id', $providerId)->where('status', 'active')->count();
$pendingListings = db('listings')->where('provider_id', $providerId)->where('approval_status', 'pending')->count();
$totalBookings = db('bookings')->where('provider_id', $providerId)->count();
$pendingBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'pending')->count();
$confirmedBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'confirmed')->count();
$completedBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'completed')->count();
$totalRevenue = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total 
     FROM bookings 
     WHERE provider_id = ? AND status IN ('confirmed', 'completed')",
    [$providerId]
)[0]['total'];

// Get recent bookings
$recentBookings = db('bookings')->raw(
    "SELECT bookings.*, users.name as user_name, users.phone as user_phone,
     listings.title as listing_title
     FROM bookings
     LEFT JOIN users ON bookings.user_id = users.user_id
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     WHERE bookings.provider_id = ?
     ORDER BY bookings.created_at DESC
     LIMIT 5",
    [$providerId]
);

// Get top listings
$topListings = db('listings')->raw(
    "SELECT listings.*, COUNT(bookings.booking_id) as booking_count
     FROM listings
     LEFT JOIN bookings ON listings.listing_id = bookings.listing_id
     WHERE listings.provider_id = ?
     GROUP BY listings.listing_id
     ORDER BY booking_count DESC
     LIMIT 3",
    [$providerId]
);

$pageTitle = 'Provider Dashboard - TripEase';
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
                        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($provider['business_name']); ?>!</p>
                    </div>
                    <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Listing
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalListings; ?></h3>
                                <p>Total Listings</p>
                                <small class="text-success"><?php echo $activeListings; ?> active</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalBookings; ?></h3>
                                <p>Total Bookings</p>
                                <small class="text-warning"><?php echo $pendingBookings; ?> pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $confirmedBookings; ?></h3>
                                <p>Confirmed</p>
                                <small class="text-info"><?php echo $completedBookings; ?> completed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo format_price($totalRevenue); ?></h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Bookings -->
                    <div class="col-lg-8 mb-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-calendar-alt"></i> Recent Bookings</h4>
                                <a href="<?php echo base_url('provider/bookings.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recentBookings)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Customer</th>
                                                <th>Listing</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['listing_title']); ?></td>
                                                <td><?php echo format_date($booking['start_date'], 'd M'); ?></td>
                                                <td><?php echo format_price($booking['total_price']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $booking['status'] === 'confirmed' ? 'success' : 
                                                            ($booking['status'] === 'pending' ? 'warning' : 
                                                            ($booking['status'] === 'completed' ? 'info' : 'danger')); 
                                                    ?>">
                                                        <?php echo ucfirst($booking['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No bookings yet</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Top Listings -->
                    <div class="col-lg-4 mb-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-star"></i> Top Listings</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($topListings)): ?>
                                    <?php foreach ($topListings as $listing): ?>
                                    <div class="top-listing-item">
                                        <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" 
                                             alt="" class="top-listing-image">
                                        <div class="top-listing-info">
                                            <h6><?php echo htmlspecialchars($listing['title']); ?></h6>
                                            <small class="text-muted"><?php echo $listing['booking_count']; ?> bookings</small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-list fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No listings yet</p>
                                    <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="btn btn-sm btn-primary mt-2">
                                        Add Your First Listing
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4><i class="fas fa-bolt"></i> Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-plus"></i>
                                <span>Add Listing</span>
                            </a>
                            <a href="<?php echo base_url('provider/listings.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-list"></i>
                                <span>My Listings</span>
                            </a>
                            <a href="<?php echo base_url('provider/bookings.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-calendar-check"></i>
                                <span>Bookings</span>
                            </a>
                            <a href="<?php echo base_url('provider/profile.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Dashboard Section */
.dashboard-section {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Dashboard Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.dashboard-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.75rem;
    font-weight: 700;
}

.dashboard-header p {
    margin: 0.5rem 0 0 0;
    color: #6c757d;
}

/* Stat Cards */
.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #3498db;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-icon.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.bg-success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-icon.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stat-icon.bg-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.stat-details {
    flex: 1;
}

.stat-details h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.stat-details p {
    margin: 0.5rem 0 0 0;
    color: #6c757d;
    font-size: 0.95rem;
}

.stat-details small {
    font-size: 0.85rem;
}

/* Dashboard Card */
.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h4 {
    margin: 0;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
}

.card-header h4 i {
    margin-right: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

/* Top Listing Item */
.top-listing-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.top-listing-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.top-listing-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.top-listing-image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.top-listing-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.top-listing-info h6 {
    margin: 0 0 0.25rem 0;
    color: #2c3e50;
    font-weight: 600;
    font-size: 0.95rem;
}

.top-listing-info small {
    color: #6c757d;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.quick-action-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.quick-action-btn i {
    font-size: 2rem;
    margin-bottom: 0.75rem;
}

.quick-action-btn span {
    font-weight: 600;
    font-size: 0.95rem;
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 1rem;
    font-size: 0.9rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    color: #495057;
}

.table-hover tbody tr:hover {
    background: #f8f9fa;
}

/* Badge Styling */
.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    font-size: 0.8rem;
    border-radius: 6px;
}

/* Empty State */
.text-center.py-4 {
    padding: 3rem 1rem !important;
}

.text-center.py-4 i {
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 991px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 767px) {
    .dashboard-section {
        padding: 1rem 0;
    }
    
    .dashboard-header {
        padding: 1rem;
    }
    
    .dashboard-header h2 {
        font-size: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .stat-details h3 {
        font-size: 1.5rem;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
}

/* Additional Professional Touches */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

/* Smooth Animations */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}
</style>

<?php include '../includes/footer.php'; ?>

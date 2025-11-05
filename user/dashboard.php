<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);

// Get user info
$user = db('users')->where('user_id', $userId)->first();

// Get statistics
$totalBookings = db('bookings')->where('user_id', $userId)->count();
$upcomingBookings = db('bookings')
    ->where('user_id', $userId)
    ->where('status', 'confirmed')
    ->where('start_date', '>=', date('Y-m-d'))
    ->count();
$completedBookings = db('bookings')
    ->where('user_id', $userId)
    ->where('status', 'completed')
    ->count();
$totalSpent = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total 
     FROM bookings 
     WHERE user_id = ? AND status IN ('confirmed', 'completed')",
    [$userId]
)[0]['total'];

// Get recent bookings
$recentBookings = db('bookings')->raw(
    "SELECT bookings.*, listings.title, listings.main_image, listings.location, listings.category
     FROM bookings
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     WHERE bookings.user_id = ?
     ORDER BY bookings.created_at DESC
     LIMIT 5",
    [$userId]
);

// Get upcoming bookings
$upcomingBookingsList = db('bookings')->raw(
    "SELECT bookings.*, listings.title, listings.main_image, listings.location
     FROM bookings
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     WHERE bookings.user_id = ? 
     AND bookings.status = 'confirmed'
     AND bookings.start_date >= CURDATE()
     ORDER BY bookings.start_date ASC
     LIMIT 3",
    [$userId]
);

// Get unread notifications
$notifications = db('notifications')
    ->where('user_type', 'user')
    ->where('user_id', $userId)
    ->where('is_read', false)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get();

$pageTitle = 'My Dashboard - TripEase';
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <!-- Welcome Header -->
                <div class="dashboard-header mb-4 fade-in">
                    <div class="card-gradient p-4 rounded-modern-lg">
                        <h2 class="text-white mb-2">Welcome back, <?php echo htmlspecialchars($user['name']); ?>! 👋</h2>
                        <p class="text-white mb-0" style="opacity: 0.9;">Here's what's happening with your bookings today</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon primary">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3 class="mb-1"><?php echo $totalBookings; ?></h3>
                                <p class="text-muted mb-0">Total Bookings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left" style="animation-delay: 0.1s;">
                        <div class="stat-card hover-lift">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $upcomingBookings; ?></h3>
                                <p>Upcoming</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $completedBookings; ?></h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo format_price($totalSpent); ?></h3>
                                <p>Total Spent</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <?php if (!empty($upcomingBookingsList)): ?>
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h4><i class="fas fa-calendar-alt"></i> Upcoming Bookings</h4>
                        <a href="<?php echo base_url('user/bookings.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php foreach ($upcomingBookingsList as $booking): ?>
                        <div class="booking-item">
                            <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                 alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                                 class="booking-image">
                            <div class="booking-info">
                                <h5><?php echo htmlspecialchars($booking['title']); ?></h5>
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?>
                                </p>
                                <p class="booking-dates">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo format_date($booking['start_date'], 'd M'); ?> - 
                                    <?php echo format_date($booking['end_date'], 'd M Y'); ?>
                                </p>
                            </div>
                            <div class="booking-actions">
                                <span class="badge bg-success">Confirmed</span>
                                <a href="<?php echo base_url('user/booking-details.php?id=' . $booking['booking_id']); ?>" 
                                   class="btn btn-sm btn-primary mt-2">View Details</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Recent Bookings -->
                    <div class="col-lg-8 mb-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Recent Bookings</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recentBookings)): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Listing</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                                             alt="" class="table-img me-2">
                                                        <span><?php echo htmlspecialchars($booking['title']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo format_date($booking['start_date'], 'd M Y'); ?></td>
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
                                                <td>
                                                    <a href="<?php echo base_url('user/booking-details.php?id=' . $booking['booking_id']); ?>" 
                                                       class="btn btn-sm btn-outline-primary">View</a>
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
                                    <a href="<?php echo base_url('search.php'); ?>" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Browse Listings
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="col-lg-4 mb-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-bell"></i> Notifications</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($notifications)): ?>
                                    <?php foreach ($notifications as $notification): ?>
                                    <div class="notification-item">
                                        <div class="notification-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <h6><?php echo htmlspecialchars($notification['title']); ?></h6>
                                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted"><?php echo time_ago($notification['created_at']); ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <a href="<?php echo base_url('user/notifications.php'); ?>" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                        View All Notifications
                                    </a>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No new notifications</p>
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
                            <a href="<?php echo base_url('search.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-search"></i>
                                <span>Browse Listings</span>
                            </a>
                            <a href="<?php echo base_url('user/bookings.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-calendar-check"></i>
                                <span>My Bookings</span>
                            </a>
                            <a href="<?php echo base_url('user/profile.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-user"></i>
                                <span>Edit Profile</span>
                            </a>
                            <a href="<?php echo base_url('contact.php'); ?>" class="quick-action-btn">
                                <i class="fas fa-headset"></i>
                                <span>Get Support</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.dashboard-section {
    padding: 2rem 0 4rem;
    background: var(--gray-50);
    min-height: calc(100vh - 200px);
}

.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h2 {
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all var(--transition-base);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-details h3 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stat-details p {
    margin: 0;
    color: var(--gray-600);
    font-size: 0.9rem;
}

.dashboard-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h4 {
    margin: 0;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

.booking-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    transition: all var(--transition-fast);
}

.booking-item:hover {
    box-shadow: var(--shadow-md);
}

.booking-image {
    width: 100px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.booking-info {
    flex: 1;
}

.booking-info h5 {
    margin-bottom: 0.5rem;
    color: var(--gray-900);
}

.booking-dates {
    color: var(--primary-color);
    font-weight: 600;
    margin: 0;
}

.booking-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: center;
}

.table-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: var(--radius-sm);
}

.notification-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-light);
    color: var(--primary-color);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-content h6 {
    margin-bottom: 0.25rem;
    color: var(--gray-900);
}

.notification-content p {
    margin-bottom: 0.25rem;
    color: var(--gray-700);
    font-size: 0.9rem;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--gray-700);
    transition: all var(--transition-fast);
}

.quick-action-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.quick-action-btn i {
    font-size: 2rem;
}

@media (max-width: 767px) {
    .booking-item {
        flex-direction: column;
    }
    
    .booking-image {
        width: 100%;
        height: 150px;
    }
    
    .booking-actions {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>

<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

// Get statistics
$totalUsers = db('users')->count();
$activeUsers = db('users')->where('status', 'active')->count();
$totalProviders = db('providers')->count();
$pendingProviders = db('providers')->where('verification_status', 'pending')->count();
$verifiedProviders = db('providers')->where('verification_status', 'verified')->count();
$totalListings = db('listings')->count();
$activeListings = db('listings')->where('status', 'active')->where('approval_status', 'approved')->count();
$pendingListings = db('listings')->where('approval_status', 'pending')->count();
$totalBookings = db('bookings')->count();
$pendingBookings = db('bookings')->where('status', 'pending')->count();
$confirmedBookings = db('bookings')->where('status', 'confirmed')->count();
$completedBookings = db('bookings')->where('status', 'completed')->count();
$totalRevenue = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total 
     FROM bookings 
     WHERE status IN ('confirmed', 'completed')"
)[0]['total'];

// Get recent activities
$recentBookings = db('bookings')->raw(
    "SELECT bookings.*, users.name as user_name, listings.title as listing_title
     FROM bookings
     LEFT JOIN users ON bookings.user_id = users.user_id
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     ORDER BY bookings.created_at DESC
     LIMIT 5"
);

$recentUsers = db('users')->orderBy('created_at', 'DESC')->limit(5)->get();
$recentProviders = db('providers')->orderBy('created_at', 'DESC')->limit(5)->get();

// Get monthly booking stats
$monthlyBookings = db('bookings')->raw(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, SUM(total_price) as revenue
     FROM bookings
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY DATE_FORMAT(created_at, '%Y-%m')
     ORDER BY month ASC"
);

$pageTitle = 'Admin Dashboard - TripEase';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">
                <!-- Header -->
                <div class="admin-header">
                    <div>
                        <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($admin['name']); ?></p>
                    </div>
                    <div class="header-actions">
                        <span class="badge bg-primary"><?php echo ucfirst($admin['role']); ?></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card stat-users">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalUsers; ?></h3>
                                <p>Total Users</p>
                                <small class="text-success"><i class="fas fa-check"></i> <?php echo $activeUsers; ?> active</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card stat-providers">
                            <div class="stat-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalProviders; ?></h3>
                                <p>Providers</p>
                                <small class="text-warning"><i class="fas fa-clock"></i> <?php echo $pendingProviders; ?> pending</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card stat-listings">
                            <div class="stat-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalListings; ?></h3>
                                <p>Listings</p>
                                <small class="text-info"><i class="fas fa-check-circle"></i> <?php echo $activeListings; ?> active</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card stat-bookings">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $totalBookings; ?></h3>
                                <p>Bookings</p>
                                <small class="text-success"><i class="fas fa-check"></i> <?php echo $completedBookings; ?> completed</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue & Pending Items -->
                <div class="row mb-4">
                    <div class="col-lg-4 col-sm-6 mb-3">
                        <div class="info-card bg-gradient-success">
                            <div class="info-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="info-details">
                                <h4><?php echo format_price($totalRevenue); ?></h4>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 mb-3">
                        <div class="info-card bg-gradient-warning">
                            <div class="info-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="info-details">
                                <h4><?php echo $pendingProviders; ?></h4>
                                <p>Pending Verifications</p>
                                <a href="<?php echo base_url('admin/providers.php?filter=pending'); ?>" class="btn btn-sm btn-light mt-2">Review Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 mb-3">
                        <div class="info-card bg-gradient-info">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-details">
                                <h4><?php echo $pendingListings; ?></h4>
                                <p>Pending Listings</p>
                                <a href="<?php echo base_url('admin/listings.php?filter=pending'); ?>" class="btn btn-sm btn-light mt-2">Review Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Bookings -->
                    <div class="col-lg-8 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-calendar-check"></i> Recent Bookings</h4>
                                <a href="<?php echo base_url('admin/bookings.php'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>User</th>
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
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="col-lg-4 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-pie"></i> Quick Stats</h4>
                            </div>
                            <div class="card-body">
                                <div class="quick-stat-item">
                                    <span>Pending Bookings</span>
                                    <strong class="text-warning"><?php echo $pendingBookings; ?></strong>
                                </div>
                                <div class="quick-stat-item">
                                    <span>Confirmed Bookings</span>
                                    <strong class="text-success"><?php echo $confirmedBookings; ?></strong>
                                </div>
                                <div class="quick-stat-item">
                                    <span>Verified Providers</span>
                                    <strong class="text-info"><?php echo $verifiedProviders; ?></strong>
                                </div>
                                <div class="quick-stat-item">
                                    <span>Active Listings</span>
                                    <strong class="text-primary"><?php echo $activeListings; ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="admin-card mt-4">
                            <div class="card-header">
                                <h4><i class="fas fa-history"></i> Recent Users</h4>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recentUsers as $user): ?>
                                <div class="activity-item">
                                    <img src="<?php echo uploads_url('users/' . ($user['profile_image'] ?? 'default-avatar.png')); ?>" 
                                         alt="" class="activity-avatar">
                                    <div class="activity-info">
                                        <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                        <small><?php echo time_ago($user['created_at']); ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Chart -->
                <div class="admin-card">
                    <div class="card-header">
                        <h4><i class="fas fa-chart-line"></i> Booking Trends (Last 6 Months)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.admin-section {
    padding: 2rem 0;
    background: var(--gray-50);
    min-height: 100vh;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.admin-header h2 {
    margin: 0;
    color: var(--gray-900);
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    display: flex;
    gap: 1rem;
    transition: all var(--transition-base);
    border-left: 4px solid var(--primary-color);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-card.stat-users { border-left-color: #3498db; }
.stat-card.stat-providers { border-left-color: #e74c3c; }
.stat-card.stat-listings { border-left-color: #2ecc71; }
.stat-card.stat-bookings { border-left-color: #f39c12; }

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
}

.stat-details h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stat-details p {
    margin: 0;
    color: var(--gray-600);
}

.info-card {
    padding: 2rem;
    border-radius: var(--radius-lg);
    color: white;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.info-icon {
    font-size: 3rem;
    opacity: 0.8;
}

.info-details h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.info-details p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.admin-card {
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
}

.card-body {
    padding: 1.5rem;
}

.quick-stat-item {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.quick-stat-item:last-child {
    border-bottom: none;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    object-fit: cover;
}

.activity-info {
    flex: 1;
}

.activity-info strong {
    display: block;
    color: var(--gray-900);
}

.activity-info small {
    color: var(--gray-600);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Booking Chart
const ctx = document.getElementById('bookingChart');
if (ctx) {
    const monthlyData = <?php echo json_encode($monthlyBookings); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Bookings',
                data: monthlyData.map(d => d.count),
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }, {
                label: 'Revenue (৳)',
                data: monthlyData.map(d => d.revenue),
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>

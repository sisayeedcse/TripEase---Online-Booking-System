<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

// Get date range
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Overall Statistics
$totalUsers = db('users')->count();
$totalProviders = db('providers')->count();
$totalListings = db('listings')->count();
$totalBookings = db('bookings')->count();
$totalRevenue = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed')"
)[0]['total'];

// Date Range Statistics
$periodBookings = db('bookings')->raw(
    "SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) BETWEEN ? AND ?",
    [$startDate, $endDate]
)[0]['count'];

$periodRevenue = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total FROM bookings 
     WHERE status IN ('confirmed', 'completed') AND DATE(created_at) BETWEEN ? AND ?",
    [$startDate, $endDate]
)[0]['total'];

// Monthly Booking Trends (Last 6 months)
$monthlyData = db('bookings')->raw(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
     COUNT(*) as bookings, 
     SUM(total_price) as revenue,
     AVG(total_price) as avg_booking_value
     FROM bookings
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY DATE_FORMAT(created_at, '%Y-%m')
     ORDER BY month ASC"
);

// Top Listings
$topListings = db('listings')->raw(
    "SELECT listings.*, providers.business_name, COUNT(bookings.booking_id) as booking_count,
     SUM(bookings.total_price) as total_revenue
     FROM listings
     LEFT JOIN bookings ON listings.listing_id = bookings.listing_id
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     GROUP BY listings.listing_id
     ORDER BY booking_count DESC
     LIMIT 10"
);

// Top Providers
$topProviders = db('providers')->raw(
    "SELECT providers.*, COUNT(bookings.booking_id) as booking_count,
     SUM(bookings.total_price) as total_revenue
     FROM providers
     LEFT JOIN bookings ON providers.provider_id = bookings.provider_id
     GROUP BY providers.provider_id
     ORDER BY booking_count DESC
     LIMIT 10"
);

// Category Distribution
$categoryStats = db('listings')->raw(
    "SELECT category, COUNT(*) as count FROM listings GROUP BY category"
);

// Booking Status Distribution
$statusStats = db('bookings')->raw(
    "SELECT status, COUNT(*) as count FROM bookings GROUP BY status"
);

// User Growth (Last 12 months)
$userGrowth = db('users')->raw(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
     FROM users
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
     GROUP BY DATE_FORMAT(created_at, '%Y-%m')
     ORDER BY month ASC"
);

$pageTitle = 'Reports & Analytics - Admin';
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
                        <h2><i class="fas fa-chart-bar"></i> Reports & Analytics</h2>
                        <p class="text-muted">Platform performance and insights</p>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="admin-card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Overall Stats -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-users text-primary"></i>
                            <div>
                                <h4><?php echo $totalUsers; ?></h4>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-store text-success"></i>
                            <div>
                                <h4><?php echo $totalProviders; ?></h4>
                                <p>Total Providers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-list text-info"></i>
                            <div>
                                <h4><?php echo $totalListings; ?></h4>
                                <p>Total Listings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-calendar-check text-warning"></i>
                            <div>
                                <h4><?php echo $totalBookings; ?></h4>
                                <p>Total Bookings</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Period Stats -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="info-card bg-gradient-success">
                            <div class="info-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="info-details">
                                <h4><?php echo format_price($totalRevenue); ?></h4>
                                <p>Total Revenue (All Time)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-card bg-gradient-info">
                            <div class="info-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="info-details">
                                <h4><?php echo format_price($periodRevenue); ?></h4>
                                <p>Period Revenue (<?php echo date('M d', strtotime($startDate)); ?> - <?php echo date('M d', strtotime($endDate)); ?>)</p>
                                <small><?php echo $periodBookings; ?> bookings</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Monthly Trends -->
                    <div class="col-lg-8 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-line"></i> Booking Trends (Last 6 Months)</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="bookingTrendsChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="col-lg-4 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-pie"></i> Category Distribution</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Top Listings -->
                    <div class="col-lg-6 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-trophy"></i> Top 10 Listings</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Listing</th>
                                                <th>Provider</th>
                                                <th>Bookings</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topListings as $listing): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($listing['title']); ?></td>
                                                <td><?php echo htmlspecialchars($listing['business_name']); ?></td>
                                                <td><span class="badge bg-primary"><?php echo $listing['booking_count']; ?></span></td>
                                                <td><strong><?php echo format_price($listing['total_revenue']); ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Providers -->
                    <div class="col-lg-6 mb-4">
                        <div class="admin-card">
                            <div class="card-header">
                                <h4><i class="fas fa-star"></i> Top 10 Providers</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Provider</th>
                                                <th>Bookings</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topProviders as $provider): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($provider['business_name']); ?></td>
                                                <td><span class="badge bg-success"><?php echo $provider['booking_count']; ?></span></td>
                                                <td><strong><?php echo format_price($provider['total_revenue']); ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Growth -->
                <div class="admin-card">
                    <div class="card-header">
                        <h4><i class="fas fa-users"></i> User Growth (Last 12 Months)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="userGrowthChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Booking Trends Chart
const bookingTrendsCtx = document.getElementById('bookingTrendsChart');
if (bookingTrendsCtx) {
    const monthlyData = <?php echo json_encode($monthlyData); ?>;
    
    new Chart(bookingTrendsCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Bookings',
                data: monthlyData.map(d => d.bookings),
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }, {
                label: 'Revenue (৳)',
                data: monthlyData.map(d => d.revenue),
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
}

// Category Chart
const categoryCtx = document.getElementById('categoryChart');
if (categoryCtx) {
    const categoryData = <?php echo json_encode($categoryStats); ?>;
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(d => d.category.charAt(0).toUpperCase() + d.category.slice(1)),
            datasets: [{
                data: categoryData.map(d => d.count),
                backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
}

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart');
if (userGrowthCtx) {
    const userGrowthData = <?php echo json_encode($userGrowth); ?>;
    
    new Chart(userGrowthCtx, {
        type: 'bar',
        data: {
            labels: userGrowthData.map(d => d.month),
            datasets: [{
                label: 'New Users',
                data: userGrowthData.map(d => d.count),
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>

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
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'cancel':
            db('bookings')->where('booking_id', $bookingId)->update(['status' => 'cancelled']);
            flash_message('success', 'Booking cancelled successfully');
            break;
    }
    redirect(base_url('admin/bookings.php'));
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT bookings.*, users.name as user_name, users.email as user_email,
          listings.title as listing_title, providers.business_name
          FROM bookings
          LEFT JOIN users ON bookings.user_id = users.user_id
          LEFT JOIN listings ON bookings.listing_id = listings.listing_id
          LEFT JOIN providers ON bookings.provider_id = providers.provider_id
          WHERE 1=1";
$params = [];

if ($filter === 'pending') {
    $query .= " AND bookings.status = 'pending'";
} elseif ($filter === 'confirmed') {
    $query .= " AND bookings.status = 'confirmed'";
} elseif ($filter === 'completed') {
    $query .= " AND bookings.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $query .= " AND bookings.status = 'cancelled'";
}

if ($search) {
    $query .= " AND (bookings.booking_reference LIKE ? OR users.name LIKE ? OR listings.title LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$query .= " ORDER BY bookings.created_at DESC LIMIT 100";

$bookings = db('bookings')->raw($query, $params);

$totalBookings = db('bookings')->count();
$pendingBookings = db('bookings')->where('status', 'pending')->count();
$confirmedBookings = db('bookings')->where('status', 'confirmed')->count();
$completedBookings = db('bookings')->where('status', 'completed')->count();
$totalRevenue = db('bookings')->raw(
    "SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed')"
)[0]['total'];

$pageTitle = 'Booking Management - Admin';
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
                        <h2><i class="fas fa-calendar-check"></i> Booking Management</h2>
                        <p class="text-muted">Monitor and manage all bookings</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <div>
                                <h4><?php echo $totalBookings; ?></h4>
                                <p>Total Bookings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingBookings; ?></h4>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $confirmedBookings; ?></h4>
                                <p>Confirmed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-dollar-sign text-info"></i>
                            <div>
                                <h4><?php echo format_price($totalRevenue); ?></h4>
                                <p>Total Revenue</p>
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
                                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                                        Pending (<?php echo $pendingBookings; ?>)
                                    </a>
                                    <a href="?filter=confirmed" class="filter-tab <?php echo $filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                                    <a href="?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                                    <a href="?filter=cancelled" class="filter-tab <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search bookings..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="admin-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>User</th>
                                        <th>Listing</th>
                                        <th>Provider</th>
                                        <th>Dates</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                        <td>
                                            <div>
                                                <?php echo htmlspecialchars($booking['user_name']); ?>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['listing_title']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['business_name']); ?></td>
                                        <td>
                                            <?php echo format_date($booking['start_date'], 'd M'); ?> - 
                                            <?php echo format_date($booking['end_date'], 'd M Y'); ?>
                                        </td>
                                        <td><strong><?php echo format_price($booking['total_price']); ?></strong></td>
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
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewBooking(<?php echo $booking['booking_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Cancel this booking?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function viewBooking(bookingId) {
    window.open('<?php echo base_url('user/booking-details.php?id='); ?>' + bookingId, '_blank');
}
</script>

<?php include '../includes/footer.php'; ?>

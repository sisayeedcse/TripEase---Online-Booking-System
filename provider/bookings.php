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
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    // Verify ownership
    $booking = db('bookings')->where('booking_id', $bookingId)->where('provider_id', $providerId)->first();
    
    if ($booking) {
        switch ($action) {
            case 'confirm':
                db('bookings')->where('booking_id', $bookingId)->update(['status' => 'confirmed']);
                // Notify user
                db('notifications')->insert([
                    'user_type' => 'user',
                    'user_id' => $booking['user_id'],
                    'title' => 'Booking Confirmed',
                    'message' => 'Your booking has been confirmed by the provider!',
                    'type' => 'booking',
                    'link' => '/user/booking-details.php?id=' . $bookingId
                ]);
                flash_message('success', 'Booking confirmed successfully');
                break;
            case 'decline':
                db('bookings')->where('booking_id', $bookingId)->update(['status' => 'declined']);
                // Notify user
                db('notifications')->insert([
                    'user_type' => 'user',
                    'user_id' => $booking['user_id'],
                    'title' => 'Booking Declined',
                    'message' => 'Your booking request has been declined.',
                    'type' => 'booking',
                    'link' => '/user/booking-details.php?id=' . $bookingId
                ]);
                flash_message('success', 'Booking declined');
                break;
            case 'complete':
                db('bookings')->where('booking_id', $bookingId)->update(['status' => 'completed']);
                // Notify user
                db('notifications')->insert([
                    'user_type' => 'user',
                    'user_id' => $booking['user_id'],
                    'title' => 'Trip Completed',
                    'message' => 'Your trip has been marked as completed. Please leave a review!',
                    'type' => 'booking',
                    'link' => '/user/add-review.php?booking=' . $bookingId
                ]);
                flash_message('success', 'Booking marked as completed');
                break;
        }
    }
    redirect(base_url('provider/bookings.php'));
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT bookings.*, users.name as user_name, users.email as user_email, 
          users.phone as user_phone, listings.title as listing_title
          FROM bookings
          LEFT JOIN users ON bookings.user_id = users.user_id
          LEFT JOIN listings ON bookings.listing_id = listings.listing_id
          WHERE bookings.provider_id = ?";
$params = [$providerId];

if ($filter === 'pending') {
    $query .= " AND bookings.status = 'pending'";
} elseif ($filter === 'confirmed') {
    $query .= " AND bookings.status = 'confirmed'";
} elseif ($filter === 'completed') {
    $query .= " AND bookings.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $query .= " AND bookings.status IN ('cancelled', 'declined')";
}

if ($search) {
    $query .= " AND (bookings.booking_reference LIKE ? OR users.name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$query .= " ORDER BY bookings.created_at DESC";

$bookings = db('bookings')->raw($query, $params);

// Get statistics
$totalBookings = db('bookings')->where('provider_id', $providerId)->count();
$pendingBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'pending')->count();
$confirmedBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'confirmed')->count();
$completedBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'completed')->count();

$pageTitle = 'Bookings Management - Provider';
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
                        <h2><i class="fas fa-calendar-check"></i> Bookings Management</h2>
                        <p class="text-muted">Manage your booking requests</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <div>
                                <h4><?php echo $totalBookings; ?></h4>
                                <p>Total Bookings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingBookings; ?></h4>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $confirmedBookings; ?></h4>
                                <p>Confirmed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-double text-info"></i>
                            <div>
                                <h4><?php echo $completedBookings; ?></h4>
                                <p>Completed</p>
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
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <?php if (!empty($bookings)): ?>
                <div class="admin-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Customer</th>
                                        <th>Listing</th>
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
                                                <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                                <small class="d-block text-muted"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['user_phone'] ?? 'N/A'); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['listing_title']); ?></td>
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
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Confirm">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                    <input type="hidden" name="action" value="decline">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Decline">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <?php elseif ($booking['status'] === 'confirmed'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="btn btn-sm btn-info" title="Mark Completed">
                                                        <i class="fas fa-check-double"></i>
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
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4>No Bookings Found</h4>
                    <p class="text-muted">
                        <?php if ($filter === 'pending'): ?>
                            No pending booking requests at the moment.
                        <?php else: ?>
                            You don't have any bookings yet.
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

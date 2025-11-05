<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$bookingId) {
    flash_message('error', 'Invalid booking');
    redirect(base_url('user/bookings.php'));
}

// Get booking details
$booking = db('bookings')->raw(
    "SELECT bookings.*, listings.title, listings.main_image, listings.location, 
     listings.category, listings.description, listings.capacity,
     providers.business_name, providers.phone as provider_phone, 
     providers.email as provider_email, providers.address as provider_address
     FROM bookings
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     LEFT JOIN providers ON bookings.provider_id = providers.provider_id
     WHERE bookings.booking_id = ? AND bookings.user_id = ?",
    [$bookingId, $userId]
);

if (empty($booking)) {
    flash_message('error', 'Booking not found');
    redirect(base_url('user/bookings.php'));
}

$booking = $booking[0];

// Check if review exists
$hasReview = db('reviews')
    ->where('user_id', $userId)
    ->where('booking_id', $bookingId)
    ->exists();

$pageTitle = 'Booking Details - TripEase';
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
                        <h2><i class="fas fa-file-alt"></i> Booking Details</h2>
                        <p class="text-muted">Reference: <?php echo htmlspecialchars($booking['booking_reference']); ?></p>
                    </div>
                    <div>
                        <span class="status-badge-large status-<?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <!-- Main Details -->
                    <div class="col-lg-8 mb-4">
                        <!-- Listing Info -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle"></i> Listing Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="listing-info-detail">
                                    <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                                         class="listing-detail-image">
                                    <div class="listing-detail-content">
                                        <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?>
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-<?php echo $booking['category'] === 'boat' ? 'ship' : 'bed'; ?>"></i> 
                                            <?php echo ucfirst($booking['category']); ?>
                                        </p>
                                        <a href="<?php echo base_url('listing-details.php?id=' . $booking['listing_id']); ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Listing
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-calendar-check"></i> Booking Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-hashtag"></i> Booking Reference</label>
                                            <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-calendar"></i> Booking Date</label>
                                            <strong><?php echo format_date($booking['booking_date'], 'd M Y'); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-calendar-alt"></i> Check-in</label>
                                            <strong><?php echo format_date($booking['start_date'], 'd M Y'); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-calendar-alt"></i> Check-out</label>
                                            <strong><?php echo format_date($booking['end_date'], 'd M Y'); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-clock"></i> Duration</label>
                                            <strong><?php echo $booking['duration']; ?> days</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-item">
                                            <label><i class="fas fa-dollar-sign"></i> Total Amount</label>
                                            <strong class="text-primary"><?php echo format_price($booking['total_price']); ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($booking['special_requests']): ?>
                                <div class="detail-item mt-3">
                                    <label><i class="fas fa-comment"></i> Special Requests</label>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Provider Info -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-store"></i> Provider Information</h4>
                            </div>
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($booking['business_name']); ?></h5>
                                <p class="mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['provider_address']); ?></p>
                                <p class="mb-2"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['provider_phone']); ?></p>
                                <p class="mb-0"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($booking['provider_email']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Status Card -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle"></i> Status</h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="status-icon status-<?php echo $booking['status']; ?>">
                                    <?php
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'confirmed' => 'fa-check-circle',
                                        'completed' => 'fa-check-double',
                                        'cancelled' => 'fa-times-circle',
                                        'declined' => 'fa-ban'
                                    ];
                                    $icon = $statusIcons[$booking['status']] ?? 'fa-question-circle';
                                    ?>
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <h4 class="mt-3"><?php echo ucfirst($booking['status']); ?></h4>
                                <p class="text-muted">
                                    <?php
                                    $statusMessages = [
                                        'pending' => 'Waiting for provider confirmation',
                                        'confirmed' => 'Your booking is confirmed!',
                                        'completed' => 'Trip completed successfully',
                                        'cancelled' => 'Booking was cancelled',
                                        'declined' => 'Provider declined this booking'
                                    ];
                                    echo $statusMessages[$booking['status']] ?? 'Status unknown';
                                    ?>
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-tasks"></i> Actions</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($booking['status'] === 'pending'): ?>
                                <button onclick="confirmAction('Cancel this booking?', () => window.location.href='cancel-booking.php?id=<?php echo $bookingId; ?>')" 
                                        class="btn btn-danger w-100 mb-2">
                                    <i class="fas fa-times"></i> Cancel Booking
                                </button>
                                <?php endif; ?>

                                <?php if ($booking['status'] === 'completed' && !$hasReview): ?>
                                <a href="<?php echo base_url('user/add-review.php?booking=' . $bookingId); ?>" 
                                   class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-star"></i> Write Review
                                </a>
                                <?php endif; ?>

                                <?php if ($hasReview): ?>
                                <a href="<?php echo base_url('user/reviews.php'); ?>" 
                                   class="btn btn-outline-success w-100 mb-2">
                                    <i class="fas fa-star"></i> View Your Review
                                </a>
                                <?php endif; ?>

                                <a href="<?php echo base_url('user/bookings.php'); ?>" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="fas fa-arrow-left"></i> Back to Bookings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.status-badge-large {
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-full);
    font-size: 1rem;
    font-weight: 700;
}

.listing-info-detail {
    display: flex;
    gap: 1.5rem;
}

.listing-detail-image {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.listing-detail-content h3 {
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-item label {
    display: block;
    color: var(--gray-600);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.detail-item strong {
    color: var(--gray-900);
    font-size: 1.1rem;
}

.status-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.status-icon.status-pending {
    background: #FFF3CD;
    color: #856404;
}

.status-icon.status-confirmed {
    background: #D1ECF1;
    color: #0C5460;
}

.status-icon.status-completed {
    background: #D4EDDA;
    color: #155724;
}

.status-icon.status-cancelled,
.status-icon.status-declined {
    background: #F8D7DA;
    color: #721C24;
}

@media (max-width: 767px) {
    .listing-info-detail {
        flex-direction: column;
    }
    
    .listing-detail-image {
        width: 100%;
        height: 200px;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}
</style>

<script>
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
</script>

<?php include '../includes/footer.php'; ?>

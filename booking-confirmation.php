<?php
require_once 'config/config.php';
require_once 'config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$bookingRef = sanitize_input($_GET['ref'] ?? '');

if (!$bookingRef) {
    redirect(base_url('user/bookings.php'));
}

// Get booking details
$booking = db('bookings')->raw(
    "SELECT bookings.*, listings.title, listings.main_image, listings.location, listings.category,
     providers.business_name, providers.phone as provider_phone
     FROM bookings
     LEFT JOIN listings ON bookings.listing_id = listings.listing_id
     LEFT JOIN providers ON bookings.provider_id = providers.provider_id
     WHERE bookings.booking_reference = ? AND bookings.user_id = ?",
    [$bookingRef, get_user_id(ROLE_USER)]
);

if (empty($booking)) {
    flash_message('error', 'Booking not found');
    redirect(base_url('user/bookings.php'));
}

$booking = $booking[0];

$pageTitle = 'Booking Confirmation - TripEase';
include 'includes/header.php';
?>

<section class="confirmation-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Message -->
                <div class="confirmation-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1>Booking Confirmed!</h1>
                    <p class="lead">Your booking request has been submitted successfully</p>
                    <div class="booking-ref">
                        <strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking['booking_reference']); ?>
                    </div>
                </div>

                <!-- Booking Details Card -->
                <div class="booking-details-card">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                 alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                                 class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?>
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-store"></i> <?php echo htmlspecialchars($booking['business_name']); ?>
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label><i class="fas fa-calendar-alt"></i> Check-in</label>
                                <p><?php echo format_date($booking['start_date'], 'd M Y'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label><i class="fas fa-calendar-alt"></i> Check-out</label>
                                <p><?php echo format_date($booking['end_date'], 'd M Y'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label><i class="fas fa-clock"></i> Duration</label>
                                <p><?php echo $booking['duration']; ?> days</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label><i class="fas fa-dollar-sign"></i> Total Amount</label>
                                <p class="price-text"><?php echo format_price($booking['total_price']); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($booking['special_requests']): ?>
                    <div class="detail-group">
                        <label><i class="fas fa-comment"></i> Special Requests</label>
                        <p><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="status-badge status-<?php echo $booking['status']; ?>">
                        <i class="fas fa-info-circle"></i>
                        Status: <?php echo ucfirst($booking['status']); ?>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps-card">
                    <h4><i class="fas fa-list-check"></i> What's Next?</h4>
                    <ol>
                        <li>The service provider will review your booking request</li>
                        <li>You will receive a notification once the booking is confirmed</li>
                        <li>Contact the provider if you have any questions: <?php echo htmlspecialchars($booking['provider_phone']); ?></li>
                        <li>You can view and manage your booking in your dashboard</li>
                    </ol>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="<?php echo base_url('user/bookings.php'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-check"></i> View My Bookings
                    </a>
                    <a href="<?php echo base_url('search.php'); ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-search"></i> Browse More Listings
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.confirmation-section {
    padding: 4rem 0;
    background: var(--gray-50);
    min-height: calc(100vh - 200px);
}

.confirmation-header {
    text-align: center;
    margin-bottom: 3rem;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: var(--success-color);
    color: white;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    margin: 0 auto 2rem;
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

.confirmation-header h1 {
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.booking-ref {
    display: inline-block;
    background: var(--primary-light);
    color: var(--primary-color);
    padding: 1rem 2rem;
    border-radius: var(--radius-lg);
    font-size: 1.1rem;
    margin-top: 1rem;
}

.booking-details-card,
.next-steps-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.detail-group {
    margin-bottom: 1.5rem;
}

.detail-group label {
    font-weight: 600;
    color: var(--gray-700);
    display: block;
    margin-bottom: 0.5rem;
}

.detail-group p {
    margin: 0;
    color: var(--gray-900);
    font-size: 1.1rem;
}

.price-text {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.5rem !important;
}

.status-badge {
    padding: 1rem;
    border-radius: var(--radius-md);
    text-align: center;
    font-weight: 600;
    margin-top: 1.5rem;
}

.status-pending {
    background: #FFF3CD;
    color: #856404;
}

.status-confirmed {
    background: #D1ECF1;
    color: #0C5460;
}

.next-steps-card h4 {
    margin-bottom: 1.5rem;
    color: var(--gray-900);
}

.next-steps-card ol {
    padding-left: 1.5rem;
}

.next-steps-card li {
    padding: 0.5rem 0;
    color: var(--gray-700);
    line-height: 1.8;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 767px) {
    .confirmation-section {
        padding: 2rem 0;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        font-size: 2.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

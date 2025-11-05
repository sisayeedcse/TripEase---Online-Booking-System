<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();

// Get filter
$filter = sanitize_input($_GET['filter'] ?? 'all');

// Build query based on filter
$query = "SELECT bookings.*, listings.title, listings.main_image, listings.location, listings.category,
          providers.business_name, providers.phone as provider_phone
          FROM bookings
          LEFT JOIN listings ON bookings.listing_id = listings.listing_id
          LEFT JOIN providers ON bookings.provider_id = providers.provider_id
          WHERE bookings.user_id = ?";

$params = [$userId];

switch ($filter) {
    case 'upcoming':
        $query .= " AND bookings.status = 'confirmed' AND bookings.start_date >= CURDATE()";
        break;
    case 'pending':
        $query .= " AND bookings.status = 'pending'";
        break;
    case 'completed':
        $query .= " AND bookings.status = 'completed'";
        break;
    case 'cancelled':
        $query .= " AND bookings.status IN ('cancelled', 'declined')";
        break;
}

$query .= " ORDER BY bookings.created_at DESC";

$bookings = db('bookings')->raw($query, $params);

$pageTitle = 'My Bookings - TripEase';
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
                    <h2><i class="fas fa-calendar-check"></i> My Bookings</h2>
                    <p class="text-muted">Manage all your bookings in one place</p>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs mb-4">
                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All Bookings
                    </a>
                    <a href="?filter=upcoming" class="filter-tab <?php echo $filter === 'upcoming' ? 'active' : ''; ?>">
                        Upcoming
                    </a>
                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                        Pending
                    </a>
                    <a href="?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                        Completed
                    </a>
                    <a href="?filter=cancelled" class="filter-tab <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">
                        Cancelled
                    </a>
                </div>

                <!-- Bookings List -->
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card-full">
                        <div class="row">
                            <div class="col-md-3">
                                <img src="<?php echo uploads_url('listings/' . ($booking['main_image'] ?? 'default-listing.jpg')); ?>" 
                                     alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                                     class="booking-card-image">
                                <span class="category-badge badge-<?php echo $booking['category']; ?>">
                                    <i class="fas fa-<?php echo $booking['category'] === 'boat' ? 'ship' : 'bed'; ?>"></i>
                                    <?php echo ucfirst($booking['category']); ?>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h4><?php echo htmlspecialchars($booking['title']); ?></h4>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-store"></i> <?php echo htmlspecialchars($booking['business_name']); ?>
                                </p>
                                <div class="booking-dates-info">
                                    <div class="date-item">
                                        <small>Check-in</small>
                                        <strong><?php echo format_date($booking['start_date'], 'd M Y'); ?></strong>
                                    </div>
                                    <div class="date-separator">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="date-item">
                                        <small>Check-out</small>
                                        <strong><?php echo format_date($booking['end_date'], 'd M Y'); ?></strong>
                                    </div>
                                </div>
                                <p class="booking-ref mt-2">
                                    <strong>Ref:</strong> <?php echo htmlspecialchars($booking['booking_reference']); ?>
                                </p>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="booking-status">
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                    <div class="booking-price">
                                        <?php echo format_price($booking['total_price']); ?>
                                    </div>
                                    <small class="text-muted"><?php echo $booking['duration']; ?> days</small>
                                </div>
                                <div class="booking-actions mt-3">
                                    <a href="<?php echo base_url('user/booking-details.php?id=' . $booking['booking_id']); ?>" 
                                       class="btn btn-primary btn-sm w-100 mb-2">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                    <button onclick="confirmAction('Cancel this booking?', () => window.location.href='cancel-booking.php?id=<?php echo $booking['booking_id']; ?>')" 
                                            class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($booking['status'] === 'completed'): ?>
                                    <a href="<?php echo base_url('user/add-review.php?booking=' . $booking['booking_id']); ?>" 
                                       class="btn btn-outline-success btn-sm w-100">
                                        <i class="fas fa-star"></i> Write Review
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4>No bookings found</h4>
                        <p class="text-muted">
                            <?php if ($filter === 'all'): ?>
                                You haven't made any bookings yet. Start exploring!
                            <?php else: ?>
                                No <?php echo $filter; ?> bookings at the moment.
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo base_url('search.php'); ?>" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Listings
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    background: white;
    padding: 1rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.filter-tab {
    padding: 0.5rem 1.5rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--gray-700);
    background: var(--gray-50);
    transition: all var(--transition-fast);
}

.filter-tab:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.filter-tab.active {
    background: var(--primary-color);
    color: white;
}

.booking-card-full {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    transition: all var(--transition-base);
}

.booking-card-full:hover {
    box-shadow: var(--shadow-lg);
}

.booking-card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: var(--radius-md);
    position: relative;
}

.category-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-boat {
    background: var(--primary-color);
    color: white;
}

.badge-room {
    background: var(--secondary-color);
    color: white;
}

.booking-dates-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    margin-top: 1rem;
}

.date-item {
    display: flex;
    flex-direction: column;
}

.date-item small {
    color: var(--gray-600);
    font-size: 0.8rem;
}

.date-item strong {
    color: var(--gray-900);
}

.date-separator {
    color: var(--primary-color);
}

.booking-ref {
    color: var(--gray-600);
    font-size: 0.9rem;
}

.booking-status {
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.status-pending {
    background: #FFF3CD;
    color: #856404;
}

.status-confirmed {
    background: #D1ECF1;
    color: #0C5460;
}

.status-completed {
    background: #D4EDDA;
    color: #155724;
}

.status-cancelled,
.status-declined {
    background: #F8D7DA;
    color: #721C24;
}

.booking-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.empty-state {
    background: white;
    padding: 4rem 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
}

@media (max-width: 767px) {
    .booking-card-full .row > div {
        margin-bottom: 1rem;
    }
    
    .booking-card-full .text-end {
        text-align: left !important;
    }
    
    .booking-dates-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .date-separator {
        transform: rotate(90deg);
    }
}
</style>

<?php include '../includes/footer.php'; ?>

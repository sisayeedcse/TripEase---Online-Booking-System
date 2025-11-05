<?php
require_once 'config/config.php';
require_once 'config/database.php';

$listingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$listingId) {
    flash_message('error', 'Invalid listing');
    redirect(base_url('search.php'));
}

// Get listing details with provider info and ratings
$listing = db('listings')->raw(
    "SELECT listings.*, providers.business_name, providers.phone as provider_phone, 
     providers.email as provider_email, providers.address as provider_address,
     AVG(reviews.rating) as avg_rating, 
     COUNT(DISTINCT reviews.review_id) as review_count
     FROM listings 
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     LEFT JOIN reviews ON listings.listing_id = reviews.listing_id
     WHERE listings.listing_id = ? AND listings.status = 'active'
     GROUP BY listings.listing_id",
    [$listingId]
);

if (empty($listing)) {
    flash_message('error', 'Listing not found');
    redirect(base_url('search.php'));
}

$listing = $listing[0];

// Update view count
db('listings')->where('listing_id', $listingId)->raw(
    "UPDATE listings SET views = views + 1 WHERE listing_id = ?",
    [$listingId]
);

// Get reviews
$reviews = db('reviews')->raw(
    "SELECT reviews.*, users.name as user_name, users.profile_image
     FROM reviews
     LEFT JOIN users ON reviews.user_id = users.user_id
     WHERE reviews.listing_id = ? AND reviews.status = 'approved'
     ORDER BY reviews.created_at DESC
     LIMIT 10",
    [$listingId]
);

// Get related listings
$relatedListings = db('listings')->raw(
    "SELECT listings.*, AVG(reviews.rating) as avg_rating
     FROM listings
     LEFT JOIN reviews ON listings.listing_id = reviews.listing_id
     WHERE listings.category = ? 
     AND listings.listing_id != ? 
     AND listings.status = 'active'
     AND listings.approval_status = 'approved'
     GROUP BY listings.listing_id
     ORDER BY RAND()
     LIMIT 4",
    [$listing['category'], $listingId]
);

// Parse images
$images = !empty($listing['images']) ? explode(',', $listing['images']) : [];
$amenities = !empty($listing['amenities']) ? explode(',', $listing['amenities']) : [];

$pageTitle = htmlspecialchars($listing['title']) . ' - TripEase';
include 'includes/header.php';
?>

<section class="listing-details-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('search.php'); ?>">Search</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($listing['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8 mb-4">
                <!-- Image Gallery -->
                <div class="listing-gallery">
                    <div class="main-image">
                        <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" 
                             alt="<?php echo htmlspecialchars($listing['title']); ?>" 
                             id="mainImage">
                        <div class="image-badge">
                            <i class="fas fa-<?php echo $listing['category'] === 'boat' ? 'ship' : 'bed'; ?>"></i>
                            <?php echo ucfirst($listing['category']); ?>
                        </div>
                    </div>
                    <?php if (!empty($images)): ?>
                    <div class="thumbnail-images">
                        <img src="<?php echo uploads_url('listings/' . $listing['main_image']); ?>" 
                             class="thumbnail active" onclick="changeImage(this.src)">
                        <?php foreach (array_slice($images, 0, 4) as $image): ?>
                        <img src="<?php echo uploads_url('listings/' . trim($image)); ?>" 
                             class="thumbnail" onclick="changeImage(this.src)">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Title and Rating -->
                <div class="listing-header">
                    <h1><?php echo htmlspecialchars($listing['title']); ?></h1>
                    <div class="listing-meta-info">
                        <span class="location">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?>
                        </span>
                        <?php if ($listing['avg_rating']): ?>
                        <span class="rating">
                            <i class="fas fa-star"></i> <?php echo number_format($listing['avg_rating'], 1); ?>
                            (<?php echo $listing['review_count']; ?> reviews)
                        </span>
                        <?php endif; ?>
                        <span class="views">
                            <i class="fas fa-eye"></i> <?php echo $listing['views']; ?> views
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div class="listing-description">
                    <h3><i class="fas fa-info-circle"></i> Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                </div>

                <!-- Details -->
                <div class="listing-details-info">
                    <h3><i class="fas fa-list"></i> Details</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <strong>Category:</strong>
                                <span><?php echo ucfirst($listing['category']); ?></span>
                            </div>
                            <div class="detail-item">
                                <strong>Capacity:</strong>
                                <span><i class="fas fa-users"></i> <?php echo $listing['capacity']; ?> persons</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <strong>Price:</strong>
                                <span class="price-highlight"><?php echo format_price($listing['price']); ?>/<?php echo $listing['price_unit']; ?></span>
                            </div>
                            <div class="detail-item">
                                <strong>Location:</strong>
                                <span><?php echo htmlspecialchars($listing['location']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <?php if (!empty($amenities)): ?>
                <div class="listing-amenities">
                    <h3><i class="fas fa-check-circle"></i> Amenities</h3>
                    <div class="amenities-grid">
                        <?php foreach ($amenities as $amenity): ?>
                        <div class="amenity-item">
                            <i class="fas fa-check"></i> <?php echo htmlspecialchars(trim($amenity)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Provider Info -->
                <div class="provider-info-card">
                    <h3><i class="fas fa-store"></i> Provider Information</h3>
                    <div class="provider-details">
                        <h5><?php echo htmlspecialchars($listing['business_name']); ?></h5>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['provider_address']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($listing['provider_phone']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($listing['provider_email']); ?></p>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="reviews-section">
                    <h3><i class="fas fa-star"></i> Reviews (<?php echo $listing['review_count']; ?>)</h3>
                    
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <img src="<?php echo uploads_url('users/' . ($review['profile_image'] ?? 'default-avatar.png')); ?>" 
                                     alt="<?php echo htmlspecialchars($review['user_name']); ?>" 
                                     class="review-avatar">
                                <div class="review-info">
                                    <h6><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted"><?php echo time_ago($review['created_at']); ?></small>
                                </div>
                            </div>
                            <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking Sidebar -->
            <div class="col-lg-4">
                <div class="booking-card sticky-booking">
                    <div class="booking-price">
                        <span class="price"><?php echo format_price($listing['price']); ?></span>
                        <span class="unit">per <?php echo $listing['price_unit']; ?></span>
                    </div>

                    <?php if (is_logged_in(ROLE_USER)): ?>
                    <form action="<?php echo base_url('process-booking.php'); ?>" method="POST" id="bookingForm">
                        <input type="hidden" name="listing_id" value="<?php echo $listing['listing_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="startDate" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="endDate" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" name="guests" 
                                   min="1" max="<?php echo $listing['capacity']; ?>" value="1" required>
                            <small class="text-muted">Maximum: <?php echo $listing['capacity']; ?> persons</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Special Requests (Optional)</label>
                            <textarea class="form-control" name="special_requests" rows="3" 
                                      placeholder="Any special requirements..."></textarea>
                        </div>
                        
                        <div class="booking-summary">
                            <div class="summary-row">
                                <span>Duration:</span>
                                <span id="duration">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Price:</span>
                                <span id="priceCalc"><?php echo format_price($listing['price']); ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="totalPrice"><?php echo format_price($listing['price']); ?></span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="text-center">
                        <p class="mb-3">Please login to book this listing</p>
                        <a href="<?php echo base_url('login.php'); ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login to Book
                        </a>
                        <p class="mt-3 mb-0">
                            <small>Don't have an account? 
                                <a href="<?php echo base_url('register.php'); ?>">Sign up</a>
                            </small>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Share -->
                <div class="share-card mt-3">
                    <h6><i class="fas fa-share-alt"></i> Share this listing</h6>
                    <div class="share-buttons">
                        <a href="#" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="share-btn link"><i class="fas fa-link"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Listings -->
        <?php if (!empty($relatedListings)): ?>
        <div class="related-listings">
            <h3><i class="fas fa-th-large"></i> Similar Listings</h3>
            <div class="row">
                <?php foreach ($relatedListings as $related): ?>
                <div class="col-md-3 mb-4">
                    <div class="listing-card">
                        <div class="listing-image">
                            <img src="<?php echo uploads_url('listings/' . ($related['main_image'] ?? 'default-listing.jpg')); ?>" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <?php if ($related['avg_rating']): ?>
                            <div class="listing-rating">
                                <i class="fas fa-star"></i> <?php echo number_format($related['avg_rating'], 1); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="listing-body">
                            <h5 class="listing-title"><?php echo htmlspecialchars($related['title']); ?></h5>
                            <p class="listing-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($related['location']); ?>
                            </p>
                            <div class="listing-meta">
                                <span class="listing-price"><?php echo format_price($related['price']); ?></span>
                            </div>
                            <a href="<?php echo base_url('listing-details.php?id=' . $related['listing_id']); ?>" 
                               class="btn btn-primary btn-block btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.listing-details-section {
    padding: 2rem 0 4rem;
    background: var(--gray-50);
}

.listing-gallery {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
}

.main-image {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-full);
    font-weight: 600;
}

.thumbnail-images {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
    padding: 1rem;
    background: var(--gray-100);
}

.thumbnail {
    height: 100px;
    object-fit: cover;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
    border: 3px solid transparent;
}

.thumbnail:hover,
.thumbnail.active {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.listing-header {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
}

.listing-header h1 {
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.listing-meta-info {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    color: var(--gray-600);
}

.listing-meta-info span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.listing-meta-info .rating {
    color: #FFD700;
}

.listing-description,
.listing-details-info,
.listing-amenities,
.provider-info-card,
.reviews-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
}

.listing-description h3,
.listing-details-info h3,
.listing-amenities h3,
.provider-info-card h3,
.reviews-section h3 {
    margin-bottom: 1.5rem;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
}

.detail-item:last-child {
    border-bottom: none;
}

.price-highlight {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.1rem;
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.amenity-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-700);
}

.amenity-item i {
    color: var(--success-color);
}

.review-card {
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
}

.review-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.review-avatar {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-full);
    object-fit: cover;
}

.review-rating i {
    color: #FFD700;
}

.review-rating i:not(.active) {
    color: var(--gray-300);
}

.booking-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border: 2px solid var(--primary-light);
}

.sticky-booking {
    position: sticky;
    top: 100px;
}

.booking-price {
    text-align: center;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--gray-200);
    margin-bottom: 1.5rem;
}

.booking-price .price {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.booking-price .unit {
    color: var(--gray-600);
}

.booking-summary {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}

.summary-row.total {
    border-top: 2px solid var(--gray-300);
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--primary-color);
}

.share-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.share-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.share-btn {
    flex: 1;
    padding: 0.75rem;
    border-radius: var(--radius-md);
    color: white;
    text-align: center;
    transition: all var(--transition-fast);
}

.share-btn.facebook { background: #1877F2; }
.share-btn.twitter { background: #1DA1F2; }
.share-btn.whatsapp { background: #25D366; }
.share-btn.link { background: var(--gray-700); }

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.related-listings {
    margin-top: 3rem;
}

.related-listings h3 {
    margin-bottom: 2rem;
}

@media (max-width: 991px) {
    .main-image {
        height: 300px;
    }
    
    .sticky-booking {
        position: static;
    }
    
    .thumbnail-images {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 767px) {
    .listing-meta-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .amenities-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Booking price calculation
const pricePerUnit = <?php echo $listing['price']; ?>;
const priceUnit = '<?php echo $listing['price_unit']; ?>';
const startDateInput = document.getElementById('startDate');
const endDateInput = document.getElementById('endDate');

if (startDateInput && endDateInput) {
    startDateInput.addEventListener('change', calculatePrice);
    endDateInput.addEventListener('change', calculatePrice);
}

function calculatePrice() {
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    if (startDate && endDate && endDate > startDate) {
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let duration = diffDays;
        if (priceUnit === 'hour') {
            duration = diffDays * 24;
        }
        
        const totalPrice = pricePerUnit * duration;
        
        document.getElementById('duration').textContent = duration + ' ' + priceUnit + (duration > 1 ? 's' : '');
        document.getElementById('priceCalc').textContent = '<?php echo CURRENCY_SYMBOL; ?> ' + pricePerUnit.toFixed(2) + ' × ' + duration;
        document.getElementById('totalPrice').textContent = '<?php echo CURRENCY_SYMBOL; ?> ' + totalPrice.toFixed(2);
    }
}

// Set end date minimum
if (startDateInput) {
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>

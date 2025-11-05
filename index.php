<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Get featured listings
$featuredBoats = db('listings')
    ->where('category', 'boat')
    ->where('status', 'active')
    ->where('approval_status', 'approved')
    ->orderBy('views', 'DESC')
    ->limit(6)
    ->get();

$featuredRooms = db('listings')
    ->where('category', 'room')
    ->where('status', 'active')
    ->where('approval_status', 'approved')
    ->orderBy('views', 'DESC')
    ->limit(6)
    ->get();

// Get top-rated listings
$topRated = db('listings')
    ->select('listings.*, AVG(reviews.rating) as avg_rating, COUNT(reviews.review_id) as review_count')
    ->leftJoin('reviews', 'listings.listing_id', '=', 'reviews.listing_id')
    ->where('listings.status', 'active')
    ->where('listings.approval_status', 'approved')
    ->raw("SELECT listings.*, AVG(reviews.rating) as avg_rating, COUNT(reviews.review_id) as review_count, providers.business_name
           FROM listings 
           LEFT JOIN reviews ON listings.listing_id = reviews.listing_id
           LEFT JOIN providers ON listings.provider_id = providers.provider_id
           WHERE listings.status = 'active' AND listings.approval_status = 'approved'
           GROUP BY listings.listing_id
           HAVING avg_rating IS NOT NULL
           ORDER BY avg_rating DESC, review_count DESC
           LIMIT 4");

$pageTitle = 'Home - TripEase';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-modern">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">Discover Your Next <span class="text-gradient">Local Adventure</span></h1>
                <p class="hero-subtitle">Book boats and rooms from trusted local providers. Experience the beauty of local travel.</p>
                    
                <div class="mt-4">
                    <a href="search.php" class="btn btn-gradient btn-lg me-3 hover-lift">
                        <i class="fas fa-search"></i> Explore Now
                    </a>
                    <a href="#featured" class="btn btn-glass btn-lg hover-lift">
                        <i class="fas fa-arrow-down"></i> Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <div class="card-glass p-4 hover-lift">
                        <form action="search.php" method="GET" class="search-form-modern">
                            <h4 class="text-white mb-4"><i class="fas fa-search"></i> Find Your Perfect Stay</h4>
                            <div class="mb-3">
                                <label class="form-label text-white">Location</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-map-marker-alt input-icon text-white"></i>
                                    <input type="text" name="location" placeholder="Where to?" class="form-control form-control-modern">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Check-in Date</label>
                                <input type="date" name="date" class="form-control form-control-modern" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Category</label>
                                <select name="category" class="form-control form-control-modern">
                                    <option value="">All Categories</option>
                                    <option value="boat">Boats</option>
                                    <option value="room">Rooms</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-gradient w-100 btn-lg">
                                <i class="fas fa-search"></i> Search Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <i class="fas fa-ship stat-icon"></i>
                    <h3 class="stat-number"><?php echo db('listings')->where('category', 'boat')->where('status', 'active')->count(); ?>+</h3>
                    <p class="stat-label">Boats Available</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <i class="fas fa-bed stat-icon"></i>
                    <h3 class="stat-number"><?php echo db('listings')->where('category', 'room')->where('status', 'active')->count(); ?>+</h3>
                    <p class="stat-label">Rooms Listed</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <i class="fas fa-users stat-icon"></i>
                    <h3 class="stat-number"><?php echo db('users')->where('status', 'active')->count(); ?>+</h3>
                    <p class="stat-label">Happy Travelers</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <i class="fas fa-star stat-icon"></i>
                    <h3 class="stat-number"><?php echo db('reviews')->count(); ?>+</h3>
                    <p class="stat-label">Reviews</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Boats -->
<?php if (!empty($featuredBoats)): ?>
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Boats</h2>
            <a href="search.php?category=boat" class="btn btn-outline-primary">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="row">
            <?php foreach ($featuredBoats as $boat): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="listing-card">
                    <div class="listing-image">
                        <img src="<?php echo uploads_url('listings/' . ($boat['main_image'] ?? 'default-boat.jpg')); ?>" alt="<?php echo htmlspecialchars($boat['title']); ?>">
                        <div class="listing-badge">
                            <i class="fas fa-ship"></i> Boat
                        </div>
                    </div>
                    <div class="listing-body">
                        <h5 class="listing-title"><?php echo htmlspecialchars($boat['title']); ?></h5>
                        <p class="listing-location">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($boat['location']); ?>
                        </p>
                        <div class="listing-meta">
                            <span class="listing-price"><?php echo format_price($boat['price']); ?>/<?php echo $boat['price_unit']; ?></span>
                            <span class="listing-capacity">
                                <i class="fas fa-users"></i> <?php echo $boat['capacity']; ?>
                            </span>
                        </div>
                        <a href="listing-details.php?id=<?php echo $boat['listing_id']; ?>" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Rooms -->
<?php if (!empty($featuredRooms)): ?>
<section class="featured-section bg-light">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Rooms</h2>
            <a href="search.php?category=room" class="btn btn-outline-primary">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="row">
            <?php foreach ($featuredRooms as $room): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="listing-card">
                    <div class="listing-image">
                        <img src="<?php echo uploads_url('listings/' . ($room['main_image'] ?? 'default-room.jpg')); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>">
                        <div class="listing-badge badge-room">
                            <i class="fas fa-bed"></i> Room
                        </div>
                    </div>
                    <div class="listing-body">
                        <h5 class="listing-title"><?php echo htmlspecialchars($room['title']); ?></h5>
                        <p class="listing-location">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($room['location']); ?>
                        </p>
                        <div class="listing-meta">
                            <span class="listing-price"><?php echo format_price($room['price']); ?>/<?php echo $room['price_unit']; ?></span>
                            <span class="listing-capacity">
                                <i class="fas fa-users"></i> <?php echo $room['capacity']; ?>
                            </span>
                        </div>
                        <a href="listing-details.php?id=<?php echo $room['listing_id']; ?>" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Top Rated -->
<?php if (!empty($topRated)): ?>
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Top Rated Services</h2>
            <p class="section-subtitle">Highly recommended by our travelers</p>
        </div>
        
        <div class="row">
            <?php foreach ($topRated as $listing): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="listing-card">
                    <div class="listing-image">
                        <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                        <div class="listing-rating">
                            <i class="fas fa-star"></i> <?php echo number_format($listing['avg_rating'], 1); ?>
                        </div>
                    </div>
                    <div class="listing-body">
                        <h5 class="listing-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                        <p class="listing-provider">
                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($listing['business_name']); ?>
                        </p>
                        <p class="listing-reviews">
                            <?php echo $listing['review_count']; ?> reviews
                        </p>
                        <a href="listing-details.php?id=<?php echo $listing['listing_id']; ?>" class="btn btn-outline-primary btn-block">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<section class="how-it-works-section bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Book your perfect adventure in 3 simple steps</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <i class="fas fa-search step-icon"></i>
                    <h4>Search & Explore</h4>
                    <p>Browse through our collection of boats and rooms. Filter by location, price, and availability.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <i class="fas fa-calendar-check step-icon"></i>
                    <h4>Book Instantly</h4>
                    <p>Select your preferred dates and confirm your booking with just a few clicks.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <i class="fas fa-smile-beam step-icon"></i>
                    <h4>Enjoy Your Trip</h4>
                    <p>Meet your host and enjoy your local adventure. Don't forget to leave a review!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">What Our Travelers Say</h2>
            <p class="section-subtitle">Real experiences from real people</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Amazing experience! The boat was exactly as described and the owner was very friendly. Highly recommended!"</p>
                    <div class="testimonial-author">
                        <img src="<?php echo assets_url('images/avatar1.jpg'); ?>" alt="User" class="testimonial-avatar">
                        <div>
                            <h6>Sarah Ahmed</h6>
                            <p>Dhaka, Bangladesh</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Perfect room for our weekend getaway. Clean, comfortable, and great location. Will definitely book again!"</p>
                    <div class="testimonial-author">
                        <img src="<?php echo assets_url('images/avatar2.jpg'); ?>" alt="User" class="testimonial-avatar">
                        <div>
                            <h6>Karim Rahman</h6>
                            <p>Chittagong, Bangladesh</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"TripEase made our family vacation so easy to plan. Great platform with reliable hosts!"</p>
                    <div class="testimonial-author">
                        <img src="<?php echo assets_url('images/avatar3.jpg'); ?>" alt="User" class="testimonial-avatar">
                        <div>
                            <h6>Nadia Islam</h6>
                            <p>Sylhet, Bangladesh</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="cta-title">Ready to Start Your Adventure?</h2>
                <p class="cta-text">Join thousands of travelers discovering amazing local experiences</p>
            </div>
            <div class="col-lg-4 text-lg-right">
                <a href="search.php" class="btn btn-light btn-lg">Explore Now</a>
            </div>
        </div>
    </div>
</section>

<!-- Provider CTA -->
<section class="provider-cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Are You a Service Provider?</h2>
                <p>List your boats or rooms on TripEase and reach thousands of potential customers</p>
                <ul class="provider-benefits">
                    <li><i class="fas fa-check-circle"></i> Easy listing management</li>
                    <li><i class="fas fa-check-circle"></i> Secure booking system</li>
                    <li><i class="fas fa-check-circle"></i> Direct customer communication</li>
                    <li><i class="fas fa-check-circle"></i> Performance analytics</li>
                </ul>
                <a href="provider/register.php" class="btn btn-primary btn-lg">Become a Provider</a>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo assets_url('images/provider-illustration.svg'); ?>" alt="Become a Provider" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

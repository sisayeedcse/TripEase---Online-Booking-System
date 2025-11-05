<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Get search parameters
$location = sanitize_input($_GET['location'] ?? '');
$category = sanitize_input($_GET['category'] ?? '');
$date = sanitize_input($_GET['date'] ?? '');
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
$sortBy = sanitize_input($_GET['sort'] ?? 'newest');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Build query
$query = db('listings')
    ->select('listings.*, providers.business_name, AVG(reviews.rating) as avg_rating, COUNT(DISTINCT reviews.review_id) as review_count')
    ->leftJoin('providers', 'listings.provider_id', '=', 'providers.provider_id')
    ->leftJoin('reviews', 'listings.listing_id', '=', 'reviews.listing_id')
    ->where('listings.status', 'active')
    ->where('listings.approval_status', 'approved');

// Apply filters
if (!empty($location)) {
    $query->whereLike('listings.location', $location);
}

if (!empty($category)) {
    $query->where('listings.category', $category);
}

if ($minPrice > 0) {
    $query->where('listings.price', '>=', $minPrice);
}

if ($maxPrice < 999999) {
    $query->where('listings.price', '<=', $maxPrice);
}

// Get total count for pagination
$totalQuery = clone $query;
$total = $totalQuery->count();
$totalPages = ceil($total / $perPage);

// Apply sorting
switch ($sortBy) {
    case 'price_low':
        $query->orderBy('listings.price', 'ASC');
        break;
    case 'price_high':
        $query->orderBy('listings.price', 'DESC');
        break;
    case 'rating':
        $query->orderBy('avg_rating', 'DESC');
        break;
    case 'popular':
        $query->orderBy('listings.views', 'DESC');
        break;
    default:
        $query->orderBy('listings.created_at', 'DESC');
}

// Get listings with proper grouping
$listings = db('listings')->raw(
    "SELECT listings.*, providers.business_name, 
     AVG(reviews.rating) as avg_rating, 
     COUNT(DISTINCT reviews.review_id) as review_count
     FROM listings 
     LEFT JOIN providers ON listings.provider_id = providers.provider_id
     LEFT JOIN reviews ON listings.listing_id = reviews.listing_id
     WHERE listings.status = 'active' 
     AND listings.approval_status = 'approved'
     " . (!empty($location) ? "AND listings.location LIKE '%$location%'" : "") . "
     " . (!empty($category) ? "AND listings.category = '$category'" : "") . "
     " . ($minPrice > 0 ? "AND listings.price >= $minPrice" : "") . "
     " . ($maxPrice < 999999 ? "AND listings.price <= $maxPrice" : "") . "
     GROUP BY listings.listing_id
     " . ($sortBy === 'price_low' ? "ORDER BY listings.price ASC" : 
         ($sortBy === 'price_high' ? "ORDER BY listings.price DESC" :
         ($sortBy === 'rating' ? "ORDER BY avg_rating DESC" :
         ($sortBy === 'popular' ? "ORDER BY listings.views DESC" :
         "ORDER BY listings.created_at DESC")))) . "
     LIMIT $perPage OFFSET $offset"
);

$pageTitle = 'Search Listings - TripEase';
include 'includes/header.php';
?>

<section class="search-section">
    <div class="container">
        <!-- Search Header -->
        <div class="search-header">
            <h1><i class="fas fa-search"></i> Explore Listings</h1>
            <p class="text-muted">
                <?php if ($total > 0): ?>
                    Found <?php echo $total; ?> listing<?php echo $total != 1 ? 's' : ''; ?>
                    <?php if ($location): ?>in <?php echo htmlspecialchars($location); ?><?php endif; ?>
                <?php else: ?>
                    No listings found
                <?php endif; ?>
            </p>
        </div>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filters-card">
                    <h5><i class="fas fa-filter"></i> Filters</h5>
                    
                    <form method="GET" action="" id="searchFilterForm">
                        <!-- Location -->
                        <div class="filter-group">
                            <label class="filter-label">Location</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?php echo htmlspecialchars($location); ?>" 
                                   placeholder="Enter location">
                        </div>
                        
                        <!-- Category -->
                        <div class="filter-group">
                            <label class="filter-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="boat" <?php echo $category === 'boat' ? 'selected' : ''; ?>>Boats</option>
                                <option value="room" <?php echo $category === 'room' ? 'selected' : ''; ?>>Rooms</option>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="filter-group">
                            <label class="filter-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" 
                                           value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>" 
                                           placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" 
                                           value="<?php echo $maxPrice < 999999 ? $maxPrice : ''; ?>" 
                                           placeholder="Max">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="filter-group">
                            <label class="filter-label">Date</label>
                            <input type="date" name="date" class="form-control" 
                                   value="<?php echo htmlspecialchars($date); ?>" 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <!-- Buttons -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="<?php echo base_url('search.php'); ?>" class="btn btn-outline-secondary btn-block mt-2">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Listings Grid -->
            <div class="col-lg-9">
                <!-- Sort Bar -->
                <div class="sort-bar">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            Showing <?php echo min($offset + 1, $total); ?>-<?php echo min($offset + $perPage, $total); ?> of <?php echo $total; ?>
                        </span>
                        <div class="sort-dropdown">
                            <label>Sort by:</label>
                            <select name="sort" class="form-select form-select-sm" onchange="window.location.href='?<?php echo http_build_query(array_merge($_GET, ['sort' => ''])); ?>' + this.value">
                                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="popular" <?php echo $sortBy === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Listings -->
                <?php if (!empty($listings)): ?>
                    <div class="row">
                        <?php foreach ($listings as $listing): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="listing-card">
                                <div class="listing-image">
                                    <img src="<?php echo uploads_url('listings/' . ($listing['main_image'] ?? 'default-listing.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($listing['title']); ?>">
                                    <div class="listing-badge <?php echo $listing['category'] === 'room' ? 'badge-room' : ''; ?>">
                                        <i class="fas fa-<?php echo $listing['category'] === 'boat' ? 'ship' : 'bed'; ?>"></i> 
                                        <?php echo ucfirst($listing['category']); ?>
                                    </div>
                                    <?php if ($listing['avg_rating']): ?>
                                    <div class="listing-rating">
                                        <i class="fas fa-star"></i> <?php echo number_format($listing['avg_rating'], 1); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="listing-body">
                                    <h5 class="listing-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                                    <p class="listing-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?>
                                    </p>
                                    <p class="listing-provider">
                                        <i class="fas fa-store"></i> <?php echo htmlspecialchars($listing['business_name']); ?>
                                    </p>
                                    <div class="listing-meta">
                                        <span class="listing-price"><?php echo format_price($listing['price']); ?>/<?php echo $listing['price_unit']; ?></span>
                                        <span class="listing-capacity">
                                            <i class="fas fa-users"></i> <?php echo $listing['capacity']; ?>
                                        </span>
                                    </div>
                                    <?php if ($listing['review_count'] > 0): ?>
                                    <p class="listing-reviews">
                                        <i class="fas fa-comment"></i> <?php echo $listing['review_count']; ?> review<?php echo $listing['review_count'] != 1 ? 's' : ''; ?>
                                    </p>
                                    <?php endif; ?>
                                    <a href="<?php echo base_url('listing-details.php?id=' . $listing['listing_id']); ?>" 
                                       class="btn btn-primary btn-block">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No listings found</h4>
                        <p class="text-muted">Try adjusting your search filters or browse all listings</p>
                        <a href="<?php echo base_url('search.php'); ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.search-section {
    padding: 2rem 0 4rem;
    background: var(--gray-50);
    min-height: calc(100vh - 200px);
}

.search-header {
    text-align: center;
    margin-bottom: 2rem;
}

.search-header h1 {
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.filters-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 100px;
}

.filters-card h5 {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gray-200);
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-label {
    font-weight: var(--font-weight-medium);
    margin-bottom: 0.5rem;
    display: block;
    color: var(--gray-700);
}

.filter-buttons {
    margin-top: 2rem;
}

.sort-bar {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.sort-dropdown {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sort-dropdown label {
    margin: 0;
    font-weight: var(--font-weight-medium);
}

.sort-dropdown .form-select {
    width: auto;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius-lg);
}

@media (max-width: 991px) {
    .filters-card {
        position: static;
        margin-bottom: 2rem;
    }
}

@media (max-width: 767px) {
    .sort-bar {
        padding: 1rem;
    }
    
    .sort-bar .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .sort-dropdown {
        width: 100%;
    }
    
    .sort-dropdown .form-select {
        width: 100%;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

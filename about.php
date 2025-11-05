<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'About Us - TripEase';
include 'includes/header.php';
?>

<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1>About TripEase</h1>
                <p class="lead">Your trusted platform for discovering and booking authentic local travel experiences</p>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo assets_url('images/about-hero.svg'); ?>" alt="About TripEase" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<section class="about-content">
    <div class="container">
        <!-- Mission & Vision -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To connect travelers with authentic local experiences by providing a seamless platform for booking boats and accommodations from trusted local service providers.</p>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To become the leading platform for local travel experiences, empowering communities and creating memorable journeys for travelers across Bangladesh and beyond.</p>
                </div>
            </div>
        </div>

        <!-- Story -->
        <div class="story-section">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h2>Our Story</h2>
                    <p>TripEase was born from a simple idea: making local travel accessible, transparent, and enjoyable for everyone. We noticed that travelers often struggled to find reliable local service providers, while boat and room owners had limited reach to potential customers.</p>
                    <p>Founded in 2024, we set out to bridge this gap by creating a platform that benefits both travelers and service providers. Our team is passionate about promoting local tourism and supporting small businesses in the travel industry.</p>
                    <p>Today, TripEase connects thousands of travelers with verified local providers, offering everything from scenic boat rides to comfortable accommodations in beautiful locations.</p>
                </div>
                <div class="col-lg-6">
                    <img src="<?php echo assets_url('images/story.jpg'); ?>" alt="Our Story" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="values-section">
            <h2 class="text-center mb-5">Our Core Values</h2>
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="value-card">
                        <i class="fas fa-shield-alt"></i>
                        <h5>Trust & Safety</h5>
                        <p>We verify all service providers to ensure safe and reliable experiences</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="value-card">
                        <i class="fas fa-handshake"></i>
                        <h5>Transparency</h5>
                        <p>Clear pricing, honest reviews, and open communication</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="value-card">
                        <i class="fas fa-users"></i>
                        <h5>Community</h5>
                        <p>Supporting local businesses and fostering connections</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="value-card">
                        <i class="fas fa-star"></i>
                        <h5>Excellence</h5>
                        <p>Committed to providing the best service and experience</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-highlight">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-highlight">
                        <h2><?php echo db('listings')->where('status', 'active')->count(); ?>+</h2>
                        <p>Active Listings</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-highlight">
                        <h2><?php echo db('users')->where('status', 'active')->count(); ?>+</h2>
                        <p>Happy Travelers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-highlight">
                        <h2><?php echo db('providers')->where('status', 'active')->count(); ?>+</h2>
                        <p>Verified Providers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-highlight">
                        <h2><?php echo db('bookings')->where('status', 'completed')->count(); ?>+</h2>
                        <p>Completed Bookings</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div class="why-choose-section">
            <h2 class="text-center mb-5">Why Choose TripEase?</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-check-circle"></i>
                        <h5>Verified Providers</h5>
                        <p>All service providers are verified to ensure quality and reliability</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-bolt"></i>
                        <h5>Instant Booking</h5>
                        <p>Book your perfect experience in just a few clicks</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-headset"></i>
                        <h5>24/7 Support</h5>
                        <p>Our support team is always here to help you</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-dollar-sign"></i>
                        <h5>Best Prices</h5>
                        <p>Competitive pricing with no hidden fees</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h5>Mobile Friendly</h5>
                        <p>Book on the go with our responsive platform</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-star"></i>
                        <h5>Trusted Reviews</h5>
                        <p>Real reviews from verified travelers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="about-cta">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3>Ready to Start Your Journey?</h3>
                    <p>Join thousands of travelers discovering amazing local experiences</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo base_url('search.php'); ?>" class="btn btn-light btn-lg">Explore Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.about-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 4rem 0;
}

.about-hero h1 {
    color: white;
    font-size: 3rem;
    margin-bottom: 1rem;
}

.about-content {
    padding: 4rem 0;
}

.about-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    height: 100%;
    text-align: center;
}

.about-icon {
    width: 80px;
    height: 80px;
    background: var(--primary-light);
    color: var(--primary-color);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.story-section {
    padding: 3rem 0;
}

.story-section h2 {
    color: var(--gray-900);
    margin-bottom: 1.5rem;
}

.story-section p {
    color: var(--gray-700);
    line-height: 1.8;
    margin-bottom: 1rem;
}

.values-section {
    padding: 3rem 0;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    margin: 3rem 0;
    padding: 3rem 2rem;
}

.value-card {
    text-align: center;
    padding: 2rem 1rem;
}

.value-card i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.value-card h5 {
    color: var(--gray-900);
    margin-bottom: 0.75rem;
}

.value-card p {
    color: var(--gray-600);
    margin: 0;
}

.stats-highlight {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-lg);
    margin: 3rem 0;
}

.stat-highlight h2 {
    color: white;
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-highlight p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 1.1rem;
}

.why-choose-section {
    padding: 3rem 0;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    text-align: center;
    height: 100%;
    transition: all var(--transition-base);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.feature-card i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.feature-card h5 {
    color: var(--gray-900);
    margin-bottom: 0.75rem;
}

.feature-card p {
    color: var(--gray-600);
    margin: 0;
}

.about-cta {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-lg);
    margin-top: 3rem;
}

.about-cta h3 {
    color: white;
    margin-bottom: 0.5rem;
}

.about-cta p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

@media (max-width: 767px) {
    .about-hero {
        padding: 2rem 0;
    }
    
    .about-hero h1 {
        font-size: 2rem;
    }
    
    .about-content {
        padding: 2rem 0;
    }
    
    .values-section,
    .stats-highlight,
    .about-cta {
        padding: 2rem 1rem;
    }
    
    .stat-highlight h2 {
        font-size: 2rem;
    }
    
    .about-cta {
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

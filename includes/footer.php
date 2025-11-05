    </main>
    <!-- End Main Content -->

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <!-- About -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="footer-title">
                                <i class="fas fa-plane-departure"></i> TripEase
                            </h5>
                            <p class="footer-text">
                                Your trusted platform for booking local boats and rooms. 
                                Connecting travelers with authentic local experiences.
                            </p>
                            <div class="social-links">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="footer-title">Quick Links</h5>
                            <ul class="footer-links">
                                <li><a href="<?php echo base_url(); ?>">Home</a></li>
                                <li><a href="<?php echo base_url('search.php'); ?>">Explore</a></li>
                                <li><a href="<?php echo base_url('about.php'); ?>">About Us</a></li>
                                <li><a href="<?php echo base_url('contact.php'); ?>">Contact</a></li>
                                <li><a href="<?php echo base_url('faq.php'); ?>">FAQ</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="footer-title">Categories</h5>
                            <ul class="footer-links">
                                <li><a href="<?php echo base_url('search.php?category=boat'); ?>">Boats</a></li>
                                <li><a href="<?php echo base_url('search.php?category=room'); ?>">Rooms</a></li>
                                <li><a href="<?php echo base_url('provider/register.php'); ?>">Become a Provider</a></li>
                                <li><a href="<?php echo base_url('terms.php'); ?>">Terms of Service</a></li>
                                <li><a href="<?php echo base_url('privacy.php'); ?>">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="footer-title">Contact Us</h5>
                            <ul class="footer-contact">
                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Dhaka, Bangladesh</span>
                                </li>
                                <li>
                                    <i class="fas fa-phone"></i>
                                    <span>+880 1234-567890</span>
                                </li>
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <span>info@tripease.com</span>
                                </li>
                                <li>
                                    <i class="fas fa-clock"></i>
                                    <span>24/7 Support Available</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="footer-copyright">
                            &copy; <?php echo date('Y'); ?> TripEase. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-payment">
                            <span>We Accept:</span>
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-paypal"></i>
                            <i class="fab fa-cc-amex"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bottom Navigation (Mobile Only) -->
    <div class="bottom-nav d-lg-none">
        <a href="<?php echo base_url(); ?>" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?php echo base_url('search.php'); ?>" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : ''; ?>">
            <i class="fas fa-search"></i>
            <span>Explore</span>
        </a>
        <?php if (is_logged_in(ROLE_USER)): ?>
            <a href="<?php echo base_url('user/bookings.php'); ?>" class="bottom-nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Bookings</span>
            </a>
            <a href="<?php echo base_url('user/profile.php'); ?>" class="bottom-nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        <?php else: ?>
            <a href="<?php echo base_url('login.php'); ?>" class="bottom-nav-item">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
            <a href="<?php echo base_url('register.php'); ?>" class="bottom-nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Sign Up</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for compatibility) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo assets_url('js/main.js'); ?>"></script>
    <script src="<?php echo assets_url('js/modern-ui.js'); ?>"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo assets_url($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            document.getElementById('mobileMenuOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        document.getElementById('mobileMenuClose')?.addEventListener('click', function() {
            document.getElementById('mobileMenuOverlay').classList.remove('active');
            document.body.style.overflow = '';
        });
        
        document.getElementById('mobileMenuOverlay')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Scroll to Top
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        scrollToTopBtn?.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Auto-hide flash messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-floating');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

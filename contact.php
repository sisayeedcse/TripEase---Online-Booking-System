<?php
require_once 'config/config.php';
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Insert contact message
        $inserted = db('contact_messages')->insert([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => 'new'
        ]);
        
        if ($inserted) {
            $success = 'Thank you for contacting us! We will get back to you soon.';
            // Clear form
            $name = $email = $subject = $message = '';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}

$pageTitle = 'Contact Us - TripEase';
include 'includes/header.php';
?>

<section class="contact-hero">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1><i class="fas fa-envelope"></i> Get in Touch</h1>
                <p class="lead">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="contact-card">
                    <h3><i class="fas fa-paper-plane"></i> Send us a Message</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="contact-form needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                       placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       placeholder="john@example.com" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo htmlspecialchars($subject ?? ''); ?>" 
                                   placeholder="How can we help?" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" 
                                      placeholder="Tell us more about your inquiry..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <h4><i class="fas fa-info-circle"></i> Contact Information</h4>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Address</h6>
                            <p>Dhaka, Bangladesh</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Phone</h6>
                            <p>+880 1234-567890</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Email</h6>
                            <p>info@tripease.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h6>Working Hours</h6>
                            <p>24/7 Support Available</p>
                        </div>
                    </div>
                    
                    <div class="social-links-contact">
                        <h6>Follow Us</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="faq-card mt-4">
                    <h5><i class="fas fa-question-circle"></i> Quick Links</h5>
                    <ul class="quick-links">
                        <li><a href="<?php echo base_url('faq.php'); ?>"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="<?php echo base_url('about.php'); ?>"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="<?php echo base_url('terms.php'); ?>"><i class="fas fa-chevron-right"></i> Terms of Service</a></li>
                        <li><a href="<?php echo base_url('privacy.php'); ?>"><i class="fas fa-chevron-right"></i> Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.contact-hero h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.contact-section {
    padding: 4rem 0;
    background: var(--gray-50);
}

.contact-card,
.contact-info-card,
.faq-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-md);
}

.contact-card h3,
.contact-info-card h4,
.faq-card h5 {
    margin-bottom: 1.5rem;
    color: var(--gray-900);
}

.contact-info-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.contact-info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-light);
    color: var(--primary-color);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.contact-details h6 {
    font-weight: var(--font-weight-semibold);
    margin-bottom: 0.25rem;
    color: var(--gray-900);
}

.contact-details p {
    margin: 0;
    color: var(--gray-600);
}

.social-links-contact {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-200);
}

.social-links-contact h6 {
    margin-bottom: 1rem;
}

.quick-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.quick-links li {
    margin-bottom: 0.75rem;
}

.quick-links a {
    color: var(--gray-700);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color var(--transition-fast);
}

.quick-links a:hover {
    color: var(--primary-color);
}

@media (max-width: 767px) {
    .contact-hero {
        padding: 2rem 0;
    }
    
    .contact-hero h1 {
        font-size: 2rem;
    }
    
    .contact-section {
        padding: 2rem 0;
    }
    
    .contact-card,
    .contact-info-card,
    .faq-card {
        padding: 1.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

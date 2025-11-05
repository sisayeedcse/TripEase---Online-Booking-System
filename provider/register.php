<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/Auth.php';

if (is_logged_in()) {
    redirect(base_url());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = sanitize_input($_POST['business_name'] ?? '');
    $ownerName = sanitize_input($_POST['owner_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($businessName) || empty($ownerName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // Register provider
        $result = Auth::registerProvider($businessName, $ownerName, $email, $password, $phone, $description, $address);
        
        if ($result['success']) {
            $success = $result['message'];
            // Clear form
            $businessName = $ownerName = $email = $phone = $address = $description = '';
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Provider Registration - TripEase';
include '../includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2><i class="fas fa-store"></i> Become a Service Provider</h2>
                        <p>List your boats or rooms and reach thousands of travelers</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <hr>
                            <p class="mb-0">
                                <a href="<?php echo base_url('login.php'); ?>" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Login Now
                                </a>
                            </p>
                        </div>
                    <?php else: ?>
                    
                    <!-- Benefits Section -->
                    <div class="benefits-section mb-4">
                        <h5><i class="fas fa-check-circle text-success"></i> Why Join TripEase?</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="benefits-list">
                                    <li><i class="fas fa-check"></i> Reach thousands of travelers</li>
                                    <li><i class="fas fa-check"></i> Easy listing management</li>
                                    <li><i class="fas fa-check"></i> Secure booking system</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="benefits-list">
                                    <li><i class="fas fa-check"></i> Real-time notifications</li>
                                    <li><i class="fas fa-check"></i> Performance analytics</li>
                                    <li><i class="fas fa-check"></i> 24/7 support</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="" class="auth-form needs-validation" novalidate>
                        <h5 class="mb-3"><i class="fas fa-building"></i> Business Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" 
                                       value="<?php echo htmlspecialchars($businessName ?? ''); ?>" 
                                       placeholder="e.g., Sunset Boat Tours" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                       value="<?php echo htmlspecialchars($ownerName ?? ''); ?>" 
                                       placeholder="Your full name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       placeholder="business@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>" 
                                       placeholder="+880 1234-567890" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Business Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" 
                                      placeholder="Full business address"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Business Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Tell travelers about your business..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            <small class="text-muted">Describe your services, experience, and what makes you unique</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3"><i class="fas fa-lock"></i> Account Security</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="At least 6 characters" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Re-enter password" required>
                            </div>
                        </div>
                        
                        <!-- Terms & Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="<?php echo base_url('terms.php'); ?>" target="_blank">Terms of Service</a> 
                                    and <a href="<?php echo base_url('privacy.php'); ?>" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="verification" name="verification" required>
                                <label class="form-check-label" for="verification">
                                    I understand that my account will be reviewed and verified by TripEase admin before activation
                                </label>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-user-plus"></i> Register as Provider
                        </button>
                        
                        <!-- Login Link -->
                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? 
                                <a href="<?php echo base_url('login.php'); ?>" class="text-primary fw-bold">
                                    Login
                                </a>
                            </p>
                        </div>
                        
                        <!-- User Registration -->
                        <div class="text-center mt-2">
                            <p class="mb-0 text-muted">Looking to book services? 
                                <a href="<?php echo base_url('register.php'); ?>" class="text-primary">
                                    Register as Traveler
                                </a>
                            </p>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.benefits-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-md);
    border-left: 4px solid var(--success-color);
}

.benefits-section h5 {
    color: var(--gray-900);
    margin-bottom: 1rem;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefits-list li {
    padding: 0.5rem 0;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.benefits-list i {
    color: var(--success-color);
}
</style>

<?php include '../includes/footer.php'; ?>

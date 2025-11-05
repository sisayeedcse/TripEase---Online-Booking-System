<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/Auth.php';

// Redirect if already logged in
if (is_logged_in(ROLE_USER)) {
    redirect(base_url('user/dashboard.php'));
} elseif (is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('provider/dashboard.php'));
} elseif (is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('admin/dashboard.php'));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? 'user';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // Attempt login based on user type
        if ($userType === 'provider') {
            $result = Auth::loginProvider($email, $password);
        } elseif ($userType === 'admin') {
            $result = Auth::loginAdmin($email, $password);
        } else {
            $result = Auth::loginUser($email, $password);
        }
        
        if ($result['success']) {
            flash_message('success', $result['message']);
            
            // Redirect based on user type
            if ($userType === 'provider') {
                redirect(base_url('provider/dashboard.php'));
            } elseif ($userType === 'admin') {
                redirect(base_url('admin/dashboard.php'));
            } else {
                redirect(base_url('user/dashboard.php'));
            }
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Login - TripEase';
include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
                        <p>Login to your TripEase account</p>
                    </div>
                    
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
                    
                    <form method="POST" action="" class="auth-form needs-validation" novalidate>
                        <!-- User Type Selection -->
                        <div class="mb-4">
                            <label class="form-label">Login As</label>
                            <div class="user-type-selector">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type" id="userType1" value="user" checked>
                                    <label class="form-check-label" for="userType1">
                                        <i class="fas fa-user"></i> Traveler
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type" id="userType2" value="provider">
                                    <label class="form-check-label" for="userType2">
                                        <i class="fas fa-store"></i> Provider
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type" id="userType3" value="admin">
                                    <label class="form-check-label" for="userType3">
                                        <i class="fas fa-cog"></i> Admin
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       placeholder="your@email.com" required>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="<?php echo base_url('forgot-password.php'); ?>" class="text-primary">
                                Forgot Password?
                            </a>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        
                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account? 
                                <a href="<?php echo base_url('register.php'); ?>" class="text-primary fw-bold">
                                    Sign Up
                                </a>
                            </p>
                        </div>
                        
                        <!-- Provider Registration -->
                        <div class="text-center mt-2">
                            <p class="mb-0 text-muted">Are you a service provider? 
                                <a href="<?php echo base_url('provider/register.php'); ?>" class="text-success fw-bold">
                                    Register Here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.auth-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
}

.auth-card {
    background: white;
    border-radius: 1rem;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h2 {
    color: var(--gray-900);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: var(--gray-600);
    margin: 0;
}

.user-type-selector {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.user-type-selector .form-check {
    flex: 1;
    min-width: 120px;
}

.user-type-selector .form-check-label {
    display: block;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-300);
    border-radius: 0.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.user-type-selector .form-check-input {
    display: none;
}

.user-type-selector .form-check-input:checked + .form-check-label {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.input-group-text {
    background: var(--gray-100);
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: none;
}

.input-group .form-control:focus + .input-group-text {
    border-color: var(--primary-color);
}

@media (max-width: 767px) {
    .auth-section {
        padding: 2rem 0;
    }
    
    .auth-card {
        padding: 1.5rem;
    }
    
    .user-type-selector {
        flex-direction: column;
    }
}
</style>

<script>
// Toggle password visibility
document.getElementById('togglePassword')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>

<?php include 'includes/footer.php'; ?>

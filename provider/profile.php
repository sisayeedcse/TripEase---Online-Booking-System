<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/Auth.php';

if (!is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('login.php'));
}

$providerId = get_user_id(ROLE_PROVIDER);
$provider = db('providers')->where('provider_id', $providerId)->first();

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $businessName = sanitize_input($_POST['business_name'] ?? '');
    $ownerName = sanitize_input($_POST['owner_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    
    if (empty($businessName) || empty($ownerName) || empty($email) || empty($phone)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Check if email is already taken
        $existingProvider = db('providers')
            ->where('email', $email)
            ->where('provider_id', '!=', $providerId)
            ->first();
        
        if ($existingProvider) {
            $error = 'Email already in use by another provider';
        } else {
            // Handle logo upload
            $logo = $provider['logo'];
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = upload_image($_FILES['logo'], PROVIDER_UPLOAD_DIR, 'provider_');
                if ($uploadResult['success']) {
                    if ($provider['logo'] && $provider['logo'] !== 'default-provider.png') {
                        delete_image(PROVIDER_UPLOAD_DIR . $provider['logo']);
                    }
                    $logo = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['message'];
                }
            }
            
            if (!$error) {
                $updated = db('providers')->where('provider_id', $providerId)->update([
                    'business_name' => $businessName,
                    'owner_name' => $ownerName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'description' => $description,
                    'logo' => $logo
                ]);
                
                if ($updated) {
                    $_SESSION['provider_name'] = $businessName;
                    $_SESSION['provider_email'] = $email;
                    $success = 'Profile updated successfully';
                    $provider = db('providers')->where('provider_id', $providerId)->first();
                } else {
                    $error = 'Failed to update profile';
                }
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All password fields are required';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } else {
        $result = Auth::changePassword($providerId, $currentPassword, $newPassword, ROLE_PROVIDER);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Business Profile - Provider';
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
                    <h2><i class="fas fa-user"></i> Business Profile</h2>
                    <p class="text-muted">Manage your business information</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Profile Information -->
                    <div class="col-lg-8 mb-4">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-building"></i> Business Information</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <!-- Logo -->
                                    <div class="text-center mb-4">
                                        <img src="<?php echo uploads_url('providers/' . ($provider['logo'] ?? 'default-provider.png')); ?>" 
                                             alt="Logo" 
                                             class="profile-image-large" 
                                             id="logoPreview">
                                        <div class="mt-3">
                                            <label for="logo" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-camera"></i> Change Logo
                                            </label>
                                            <input type="file" id="logo" name="logo" 
                                                   accept="image/*" class="d-none" onchange="previewImage(this)">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Business Name *</label>
                                            <input type="text" class="form-control" name="business_name" 
                                                   value="<?php echo htmlspecialchars($provider['business_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Owner Name *</label>
                                            <input type="text" class="form-control" name="owner_name" 
                                                   value="<?php echo htmlspecialchars($provider['owner_name']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($provider['email']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" name="phone" 
                                                   value="<?php echo htmlspecialchars($provider['phone']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Business Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($provider['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Business Description</label>
                                        <textarea class="form-control" name="description" rows="4" 
                                                  placeholder="Tell customers about your business..."><?php echo htmlspecialchars($provider['description'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Verification Status</label>
                                        <div>
                                            <span class="badge bg-<?php 
                                                echo $provider['verification_status'] === 'verified' ? 'success' : 
                                                    ($provider['verification_status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?> fs-6">
                                                <i class="fas fa-<?php 
                                                    echo $provider['verification_status'] === 'verified' ? 'check-circle' : 
                                                        ($provider['verification_status'] === 'pending' ? 'clock' : 'times-circle'); 
                                                ?>"></i>
                                                <?php echo ucfirst($provider['verification_status']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Member Since</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo format_date($provider['created_at'], 'd M Y'); ?>" readonly>
                                    </div>

                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Change Password -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h4><i class="fas fa-lock"></i> Change Password</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password" 
                                               minlength="6" required>
                                        <small class="text-muted">At least 6 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>

                                    <button type="submit" name="change_password" class="btn btn-warning w-100">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Business Stats -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-bar"></i> Business Stats</h4>
                            </div>
                            <div class="card-body">
                                <div class="stat-item">
                                    <span>Total Listings</span>
                                    <strong><?php echo db('listings')->where('provider_id', $providerId)->count(); ?></strong>
                                </div>
                                <div class="stat-item">
                                    <span>Total Bookings</span>
                                    <strong><?php echo db('bookings')->where('provider_id', $providerId)->count(); ?></strong>
                                </div>
                                <div class="stat-item">
                                    <span>Completed Trips</span>
                                    <strong><?php echo db('bookings')->where('provider_id', $providerId)->where('status', 'completed')->count(); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.profile-image-large {
    width: 150px;
    height: 150px;
    border-radius: var(--radius-full);
    object-fit: cover;
    border: 4px solid var(--primary-light);
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-item span {
    color: var(--gray-600);
}

.stat-item strong {
    color: var(--primary-color);
    font-size: 1.25rem;
}
</style>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../includes/footer.php'; ?>

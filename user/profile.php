<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/Auth.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    
    if (empty($name) || empty($email)) {
        $error = 'Name and email are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Check if email is already taken by another user
        $existingUser = db('users')
            ->where('email', $email)
            ->where('user_id', '!=', $userId)
            ->first();
        
        if ($existingUser) {
            $error = 'Email already in use by another account';
        } else {
            // Handle profile image upload
            $profileImage = $user['profile_image'];
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = upload_image($_FILES['profile_image'], USER_UPLOAD_DIR, 'user_');
                if ($uploadResult['success']) {
                    // Delete old image if not default
                    if ($user['profile_image'] !== 'default-avatar.png') {
                        delete_image(USER_UPLOAD_DIR . $user['profile_image']);
                    }
                    $profileImage = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['message'];
                }
            }
            
            if (!$error) {
                $updated = db('users')->where('user_id', $userId)->update([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'profile_image' => $profileImage
                ]);
                
                if ($updated) {
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $success = 'Profile updated successfully';
                    $user = db('users')->where('user_id', $userId)->first();
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
        $result = Auth::changePassword($userId, $currentPassword, $newPassword, ROLE_USER);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'My Profile - TripEase';
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
                    <h2><i class="fas fa-user"></i> My Profile</h2>
                    <p class="text-muted">Manage your account information</p>
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
                                <h4><i class="fas fa-user-edit"></i> Profile Information</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <!-- Profile Image -->
                                    <div class="text-center mb-4">
                                        <img src="<?php echo uploads_url('users/' . ($user['profile_image'] ?? 'default-avatar.png')); ?>" 
                                             alt="Profile" 
                                             class="profile-image-large" 
                                             id="profilePreview">
                                        <div class="mt-3">
                                            <label for="profile_image" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-camera"></i> Change Photo
                                            </label>
                                            <input type="file" id="profile_image" name="profile_image" 
                                                   accept="image/*" class="d-none" onchange="previewImage(this)">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" name="name" 
                                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Member Since</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo format_date($user['created_at'], 'd M Y'); ?>" readonly>
                                    </div>

                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="col-lg-4 mb-4">
                        <div class="dashboard-card">
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

                        <!-- Account Stats -->
                        <div class="dashboard-card mt-4">
                            <div class="card-header">
                                <h4><i class="fas fa-chart-bar"></i> Account Stats</h4>
                            </div>
                            <div class="card-body">
                                <div class="stat-item">
                                    <span>Total Bookings</span>
                                    <strong><?php echo db('bookings')->where('user_id', $userId)->count(); ?></strong>
                                </div>
                                <div class="stat-item">
                                    <span>Completed Trips</span>
                                    <strong><?php echo db('bookings')->where('user_id', $userId)->where('status', 'completed')->count(); ?></strong>
                                </div>
                                <div class="stat-item">
                                    <span>Reviews Written</span>
                                    <strong><?php echo db('reviews')->where('user_id', $userId)->count(); ?></strong>
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
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../includes/footer.php'; ?>

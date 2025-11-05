<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

$error = '';
$success = '';

// Get current settings
$settings = [];
$settingsData = db('settings')->get();
foreach ($settingsData as $setting) {
    $settings[$setting['setting_key']] = $setting['setting_value'];
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'site_name' => sanitize_input($_POST['site_name'] ?? ''),
        'site_tagline' => sanitize_input($_POST['site_tagline'] ?? ''),
        'contact_email' => sanitize_input($_POST['contact_email'] ?? ''),
        'contact_phone' => sanitize_input($_POST['contact_phone'] ?? ''),
        'contact_address' => sanitize_input($_POST['contact_address'] ?? ''),
        'currency_symbol' => sanitize_input($_POST['currency_symbol'] ?? '৳'),
        'currency_code' => sanitize_input($_POST['currency_code'] ?? 'BDT'),
        'booking_cancellation_hours' => (int)($_POST['booking_cancellation_hours'] ?? 24),
        'commission_percentage' => (float)($_POST['commission_percentage'] ?? 10),
        'max_booking_days' => (int)($_POST['max_booking_days'] ?? 30),
        'enable_reviews' => isset($_POST['enable_reviews']) ? '1' : '0',
        'enable_notifications' => isset($_POST['enable_notifications']) ? '1' : '0',
        'require_provider_verification' => isset($_POST['require_provider_verification']) ? '1' : '0',
        'require_listing_approval' => isset($_POST['require_listing_approval']) ? '1' : '0',
    ];
    
    // Update each setting
    foreach ($updates as $key => $value) {
        $existing = db('settings')->where('setting_key', $key)->first();
        if ($existing) {
            db('settings')->where('setting_key', $key)->update(['setting_value' => $value]);
        } else {
            db('settings')->insert(['setting_key' => $key, 'setting_value' => $value]);
        }
    }
    
    // Log activity
    db('activity_logs')->insert([
        'user_type' => 'admin',
        'user_id' => $adminId,
        'action' => 'settings_updated',
        'description' => 'Updated platform settings',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    $success = 'Settings updated successfully';
    
    // Refresh settings
    $settings = [];
    $settingsData = db('settings')->get();
    foreach ($settingsData as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
}

$pageTitle = 'Platform Settings - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-10 col-md-9">
                <div class="admin-header">
                    <div>
                        <h2><i class="fas fa-cog"></i> Platform Settings</h2>
                        <p class="text-muted">Configure your platform</p>
                    </div>
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

                <form method="POST">
                    <div class="row">
                        <!-- General Settings -->
                        <div class="col-lg-8 mb-4">
                            <div class="admin-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-info-circle"></i> General Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Site Name</label>
                                        <input type="text" class="form-control" name="site_name" 
                                               value="<?php echo htmlspecialchars($settings['site_name'] ?? 'TripEase'); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Site Tagline</label>
                                        <input type="text" class="form-control" name="site_tagline" 
                                               value="<?php echo htmlspecialchars($settings['site_tagline'] ?? 'Your Local Travel Companion'); ?>">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" name="contact_email" 
                                                   value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'info@tripease.com'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Phone</label>
                                            <input type="tel" class="form-control" name="contact_phone" 
                                                   value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+880 1234-567890'); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contact Address</label>
                                        <textarea class="form-control" name="contact_address" rows="2"><?php echo htmlspecialchars($settings['contact_address'] ?? 'Dhaka, Bangladesh'); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Currency Settings -->
                            <div class="admin-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-dollar-sign"></i> Currency Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Currency Symbol</label>
                                            <input type="text" class="form-control" name="currency_symbol" 
                                                   value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '৳'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Currency Code</label>
                                            <input type="text" class="form-control" name="currency_code" 
                                                   value="<?php echo htmlspecialchars($settings['currency_code'] ?? 'BDT'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Settings -->
                            <div class="admin-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-calendar-check"></i> Booking Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cancellation Policy (Hours)</label>
                                            <input type="number" class="form-control" name="booking_cancellation_hours" 
                                                   value="<?php echo htmlspecialchars($settings['booking_cancellation_hours'] ?? '24'); ?>" min="1">
                                            <small class="text-muted">Minimum hours before check-in to cancel</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Max Booking Days</label>
                                            <input type="number" class="form-control" name="max_booking_days" 
                                                   value="<?php echo htmlspecialchars($settings['max_booking_days'] ?? '30'); ?>" min="1">
                                            <small class="text-muted">Maximum days per booking</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Commission Percentage</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="commission_percentage" 
                                                   value="<?php echo htmlspecialchars($settings['commission_percentage'] ?? '10'); ?>" 
                                                   min="0" max="100" step="0.1">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">Platform commission on each booking</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Toggles -->
                            <div class="admin-card">
                                <div class="card-header">
                                    <h4><i class="fas fa-toggle-on"></i> Feature Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="enable_reviews" 
                                               id="enable_reviews" <?php echo ($settings['enable_reviews'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="enable_reviews">
                                            <strong>Enable Reviews</strong>
                                            <small class="d-block text-muted">Allow users to write reviews</small>
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="enable_notifications" 
                                               id="enable_notifications" <?php echo ($settings['enable_notifications'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="enable_notifications">
                                            <strong>Enable Notifications</strong>
                                            <small class="d-block text-muted">Send system notifications</small>
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="require_provider_verification" 
                                               id="require_provider_verification" <?php echo ($settings['require_provider_verification'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="require_provider_verification">
                                            <strong>Require Provider Verification</strong>
                                            <small class="d-block text-muted">Providers must be verified before adding listings</small>
                                        </label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_listing_approval" 
                                               id="require_listing_approval" <?php echo ($settings['require_listing_approval'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="require_listing_approval">
                                            <strong>Require Listing Approval</strong>
                                            <small class="d-block text-muted">Listings must be approved before going live</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Save Settings -->
                            <div class="admin-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-save"></i> Save Changes</h4>
                                </div>
                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-save"></i> Update Settings
                                    </button>
                                </div>
                            </div>

                            <!-- System Info -->
                            <div class="admin-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-info-circle"></i> System Info</h4>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <span>PHP Version</span>
                                        <strong><?php echo phpversion(); ?></strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Database</span>
                                        <strong>MySQL</strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Upload Max Size</span>
                                        <strong><?php echo ini_get('upload_max_filesize'); ?></strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Memory Limit</span>
                                        <strong><?php echo ini_get('memory_limit'); ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="admin-card">
                                <div class="card-header">
                                    <h4><i class="fas fa-chart-bar"></i> Quick Stats</h4>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <span>Total Users</span>
                                        <strong><?php echo db('users')->count(); ?></strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Total Providers</span>
                                        <strong><?php echo db('providers')->count(); ?></strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Total Listings</span>
                                        <strong><?php echo db('listings')->count(); ?></strong>
                                    </div>
                                    <div class="info-item">
                                        <span>Total Bookings</span>
                                        <strong><?php echo db('bookings')->count(); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item span {
    color: var(--gray-600);
}

.info-item strong {
    color: var(--gray-900);
}

.form-check-label strong {
    display: block;
    margin-bottom: 0.25rem;
}
</style>

<?php include '../includes/footer.php'; ?>

<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_ADMIN)) {
    redirect(base_url('login.php'));
}

$adminId = get_user_id(ROLE_ADMIN);
$admin = db('admins')->where('admin_id', $adminId)->first();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $providerId = (int)$_POST['provider_id'];
        $action = $_POST['action'];
        
        switch ($action) {
            case 'verify':
                db('providers')->where('provider_id', $providerId)->update([
                    'verification_status' => 'verified'
                ]);
                // Notify provider
                db('notifications')->insert([
                    'user_type' => 'provider',
                    'user_id' => $providerId,
                    'title' => 'Account Verified',
                    'message' => 'Your provider account has been verified! You can now add listings.',
                    'type' => 'system'
                ]);
                flash_message('success', 'Provider verified successfully');
                break;
            case 'reject':
                db('providers')->where('provider_id', $providerId)->update([
                    'verification_status' => 'rejected'
                ]);
                flash_message('success', 'Provider rejected');
                break;
            case 'block':
                db('providers')->where('provider_id', $providerId)->update(['status' => 'blocked']);
                flash_message('success', 'Provider blocked successfully');
                break;
            case 'activate':
                db('providers')->where('provider_id', $providerId)->update(['status' => 'active']);
                flash_message('success', 'Provider activated successfully');
                break;
            case 'delete':
                db('providers')->raw("DELETE FROM providers WHERE provider_id = ?", [$providerId]);
                flash_message('success', 'Provider deleted successfully');
                break;
        }
        redirect(base_url('admin/providers.php'));
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM providers WHERE 1=1";
$params = [];

if ($filter === 'pending') {
    $query .= " AND verification_status = 'pending'";
} elseif ($filter === 'verified') {
    $query .= " AND verification_status = 'verified'";
} elseif ($filter === 'rejected') {
    $query .= " AND verification_status = 'rejected'";
} elseif ($filter === 'blocked') {
    $query .= " AND status = 'blocked'";
}

if ($search) {
    $query .= " AND (business_name LIKE ? OR owner_name LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$query .= " ORDER BY created_at DESC";

$providers = db('providers')->raw($query, $params);

$totalProviders = db('providers')->count();
$pendingProviders = db('providers')->where('verification_status', 'pending')->count();
$verifiedProviders = db('providers')->where('verification_status', 'verified')->count();

$pageTitle = 'Provider Management - Admin';
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
                        <h2><i class="fas fa-store"></i> Provider Management</h2>
                        <p class="text-muted">Verify and manage service providers</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-store text-primary"></i>
                            <div>
                                <h4><?php echo $totalProviders; ?></h4>
                                <p>Total Providers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-clock text-warning"></i>
                            <div>
                                <h4><?php echo $pendingProviders; ?></h4>
                                <p>Pending Verification</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $verifiedProviders; ?></h4>
                                <p>Verified Providers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="admin-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="filter-tabs-inline">
                                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                                        All
                                    </a>
                                    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                                        Pending (<?php echo $pendingProviders; ?>)
                                    </a>
                                    <a href="?filter=verified" class="filter-tab <?php echo $filter === 'verified' ? 'active' : ''; ?>">
                                        Verified
                                    </a>
                                    <a href="?filter=rejected" class="filter-tab <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
                                        Rejected
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search providers..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Providers Table -->
                <div class="admin-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Business</th>
                                        <th>Owner</th>
                                        <th>Contact</th>
                                        <th>Listings</th>
                                        <th>Joined</th>
                                        <th>Verification</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($providers as $provider): ?>
                                    <?php
                                    $listingCount = db('listings')->where('provider_id', $provider['provider_id'])->count();
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="<?php echo uploads_url('providers/' . ($provider['logo'] ?? 'default-provider.png')); ?>" 
                                                     alt="" class="user-avatar">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($provider['business_name']); ?></strong>
                                                    <small class="d-block text-muted"><?php echo htmlspecialchars($provider['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($provider['owner_name']); ?></td>
                                        <td>
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($provider['phone']); ?>
                                        </td>
                                        <td><span class="badge bg-primary"><?php echo $listingCount; ?></span></td>
                                        <td><?php echo format_date($provider['created_at'], 'd M Y'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $provider['verification_status'] === 'verified' ? 'success' : 
                                                    ($provider['verification_status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($provider['verification_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $provider['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($provider['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if ($provider['verification_status'] === 'pending'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                    <input type="hidden" name="action" value="verify">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Verify">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($provider['status'] === 'active'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                    <input type="hidden" name="action" value="block">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Block">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Activate">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Delete this provider? This will also delete all their listings.')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

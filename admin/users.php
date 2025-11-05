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
        $userId = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        switch ($action) {
            case 'block':
                db('users')->where('user_id', $userId)->update(['status' => 'blocked']);
                flash_message('success', 'User blocked successfully');
                break;
            case 'activate':
                db('users')->where('user_id', $userId)->update(['status' => 'active']);
                flash_message('success', 'User activated successfully');
                break;
            case 'delete':
                db('users')->raw("DELETE FROM users WHERE user_id = ?", [$userId]);
                flash_message('success', 'User deleted successfully');
                break;
        }
        redirect(base_url('admin/users.php'));
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($filter === 'active') {
    $query .= " AND status = 'active'";
} elseif ($filter === 'blocked') {
    $query .= " AND status = 'blocked'";
}

if ($search) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$query .= " ORDER BY created_at DESC";

$users = db('users')->raw($query, $params);

$totalUsers = db('users')->count();
$activeUsers = db('users')->where('status', 'active')->count();
$blockedUsers = db('users')->where('status', 'blocked')->count();

$pageTitle = 'User Management - Admin';
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
                        <h2><i class="fas fa-users"></i> User Management</h2>
                        <p class="text-muted">Manage all travelers on the platform</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-users text-primary"></i>
                            <div>
                                <h4><?php echo $totalUsers; ?></h4>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4><?php echo $activeUsers; ?></h4>
                                <p>Active Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card-small">
                            <i class="fas fa-ban text-danger"></i>
                            <div>
                                <h4><?php echo $blockedUsers; ?></h4>
                                <p>Blocked Users</p>
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
                                        All (<?php echo $totalUsers; ?>)
                                    </a>
                                    <a href="?filter=active" class="filter-tab <?php echo $filter === 'active' ? 'active' : ''; ?>">
                                        Active (<?php echo $activeUsers; ?>)
                                    </a>
                                    <a href="?filter=blocked" class="filter-tab <?php echo $filter === 'blocked' ? 'active' : ''; ?>">
                                        Blocked (<?php echo $blockedUsers; ?>)
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="admin-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Contact</th>
                                        <th>Bookings</th>
                                        <th>Reviews</th>
                                        <th>Joined</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <?php
                                    $bookingCount = db('bookings')->where('user_id', $user['user_id'])->count();
                                    $reviewCount = db('reviews')->where('user_id', $user['user_id'])->count();
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="<?php echo uploads_url('users/' . ($user['profile_image'] ?? 'default-avatar.png')); ?>" 
                                                     alt="" class="user-avatar">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                    <small class="d-block text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                                        </td>
                                        <td><span class="badge bg-primary"><?php echo $bookingCount; ?></span></td>
                                        <td><span class="badge bg-warning"><?php echo $reviewCount; ?></span></td>
                                        <td><?php echo format_date($user['created_at'], 'd M Y'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewUser(<?php echo $user['user_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($user['status'] === 'active'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <input type="hidden" name="action" value="block">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                            onclick="return confirm('Block this user?')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Delete this user? This action cannot be undone.')">
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

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card-small {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card-small i {
    font-size: 2rem;
}

.stat-card-small h4 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
}

.stat-card-small p {
    margin: 0;
    color: var(--gray-600);
}

.filter-tabs-inline {
    display: flex;
    gap: 0.5rem;
}

.filter-tabs-inline .filter-tab {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--gray-700);
    background: var(--gray-100);
    font-size: 0.9rem;
    transition: all var(--transition-fast);
}

.filter-tabs-inline .filter-tab:hover {
    background: var(--gray-200);
}

.filter-tabs-inline .filter-tab.active {
    background: var(--primary-color);
    color: white;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    object-fit: cover;
}
</style>

<script>
function viewUser(userId) {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const modalBody = document.getElementById('userModalBody');
    
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.show();
    
    // Fetch user details (you can create an AJAX endpoint)
    fetch(`get-user-details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            modalBody.innerHTML = `
                <div class="text-center mb-4">
                    <img src="${data.profile_image}" class="rounded-circle" width="100" height="100">
                    <h4 class="mt-3">${data.name}</h4>
                </div>
                <table class="table">
                    <tr><th>Email:</th><td>${data.email}</td></tr>
                    <tr><th>Phone:</th><td>${data.phone || 'N/A'}</td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-${data.status === 'active' ? 'success' : 'danger'}">${data.status}</span></td></tr>
                    <tr><th>Total Bookings:</th><td>${data.bookings}</td></tr>
                    <tr><th>Reviews Written:</th><td>${data.reviews}</td></tr>
                    <tr><th>Member Since:</th><td>${data.created_at}</td></tr>
                </table>
            `;
        })
        .catch(() => {
            modalBody.innerHTML = '<div class="alert alert-danger">Failed to load user details</div>';
        });
}
</script>

<?php include '../includes/footer.php'; ?>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-sidebar">
    <!-- Admin Profile -->
    <div class="sidebar-profile">
        <div class="admin-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <h5><?php echo htmlspecialchars($admin['name']); ?></h5>
        <p class="text-muted"><?php echo ucfirst($admin['role']); ?></p>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <a href="<?php echo base_url('admin/dashboard.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="sidebar-section">Management</div>
        
        <a href="<?php echo base_url('admin/users.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Users</span>
            <span class="badge bg-primary"><?php echo db('users')->count(); ?></span>
        </a>
        
        <a href="<?php echo base_url('admin/providers.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'providers.php' ? 'active' : ''; ?>">
            <i class="fas fa-store"></i>
            <span>Providers</span>
            <?php
            $pendingProviders = db('providers')->where('verification_status', 'pending')->count();
            if ($pendingProviders > 0):
            ?>
            <span class="badge bg-warning"><?php echo $pendingProviders; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="<?php echo base_url('admin/listings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'listings.php' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i>
            <span>Listings</span>
            <?php
            $pendingListings = db('listings')->where('approval_status', 'pending')->count();
            if ($pendingListings > 0):
            ?>
            <span class="badge bg-info"><?php echo $pendingListings; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="<?php echo base_url('admin/bookings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        
        <a href="<?php echo base_url('admin/reviews.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'reviews.php' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i>
            <span>Reviews</span>
        </a>
        
        <div class="sidebar-section">System</div>
        
        <a href="<?php echo base_url('admin/reports.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        
        <a href="<?php echo base_url('admin/settings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="<?php echo base_url(); ?>" class="sidebar-link">
            <i class="fas fa-home"></i>
            <span>View Website</span>
        </a>
        
        <a href="<?php echo base_url('logout.php'); ?>" class="sidebar-link text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<style>
.admin-sidebar {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    position: sticky;
    top: 100px;
}

.sidebar-profile {
    padding: 2rem;
    text-align: center;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
}

.admin-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 1rem;
}

.sidebar-profile h5 {
    color: white;
    margin-bottom: 0.25rem;
}

.sidebar-profile .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.9rem;
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-section {
    padding: 0.75rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--gray-500);
    letter-spacing: 0.5px;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: all var(--transition-fast);
    position: relative;
}

.sidebar-link:hover {
    background: var(--gray-50);
    color: var(--primary-color);
}

.sidebar-link.active {
    background: var(--primary-light);
    color: var(--primary-color);
    font-weight: 600;
}

.sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary-color);
}

.sidebar-link i {
    width: 20px;
    text-align: center;
}

.sidebar-link .badge {
    margin-left: auto;
    font-size: 0.7rem;
}

.sidebar-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 0.5rem 1.5rem;
}

@media (max-width: 991px) {
    .admin-sidebar {
        position: static;
        margin-bottom: 2rem;
    }
}
</style>

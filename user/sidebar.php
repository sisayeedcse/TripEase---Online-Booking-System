<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="dashboard-sidebar">
    <!-- User Profile -->
    <div class="sidebar-profile">
        <img src="<?php echo uploads_url('users/' . ($user['profile_image'] ?? 'default-avatar.png')); ?>" 
             alt="<?php echo htmlspecialchars($user['name']); ?>" 
             class="profile-avatar">
        <h5><?php echo htmlspecialchars($user['name']); ?></h5>
        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <a href="<?php echo base_url('user/dashboard.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="<?php echo base_url('user/bookings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>My Bookings</span>
        </a>
        
        <a href="<?php echo base_url('user/reviews.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'reviews.php' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i>
            <span>My Reviews</span>
        </a>
        
        <a href="<?php echo base_url('user/notifications.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'notifications.php' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
            <?php
            $unreadCount = db('notifications')
                ->where('user_type', 'user')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();
            if ($unreadCount > 0):
            ?>
            <span class="badge bg-danger"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="<?php echo base_url('user/profile.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="<?php echo base_url('search.php'); ?>" class="sidebar-link">
            <i class="fas fa-search"></i>
            <span>Browse Listings</span>
        </a>
        
        <a href="<?php echo base_url('contact.php'); ?>" class="sidebar-link">
            <i class="fas fa-headset"></i>
            <span>Support</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="<?php echo base_url('logout.php'); ?>" class="sidebar-link text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<style>
.dashboard-sidebar {
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
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-full);
    object-fit: cover;
    border: 4px solid white;
    margin-bottom: 1rem;
}

.sidebar-profile h5 {
    color: white;
    margin-bottom: 0.25rem;
}

.sidebar-profile .text-muted {
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 0.9rem;
}

.sidebar-nav {
    padding: 1rem 0;
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
}

.sidebar-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 0.5rem 1.5rem;
}

@media (max-width: 991px) {
    .dashboard-sidebar {
        position: static;
        margin-bottom: 2rem;
    }
}

@media (max-width: 767px) {
    .sidebar-profile {
        padding: 1.5rem;
    }
    
    .profile-avatar {
        width: 60px;
        height: 60px;
    }
    
    .sidebar-link {
        padding: 0.75rem 1rem;
    }
}
</style>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Provider Sidebar Styles (Inline backup) -->
<style>
.dashboard-sidebar{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);overflow:hidden;position:sticky;top:100px}
.sidebar-profile{padding:2rem 1.5rem;text-align:center;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff}
.profile-avatar{width:80px;height:80px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,255,255,.3);margin-bottom:1rem;box-shadow:0 4px 12px rgba(0,0,0,.2)}
.sidebar-profile h5{color:#fff;margin-bottom:.5rem;font-weight:600;font-size:1.1rem}
.sidebar-profile .text-muted{color:rgba(255,255,255,.8)!important;font-size:.85rem;margin-bottom:.75rem}
.sidebar-nav{padding:1rem 0}
.sidebar-section{padding:.75rem 1.5rem;font-size:.75rem;font-weight:700;text-transform:uppercase;color:#6c757d;letter-spacing:.5px;margin-top:.5rem}
.sidebar-link{display:flex;align-items:center;gap:.75rem;padding:1rem 1.5rem;color:#495057;text-decoration:none;transition:all .3s ease;position:relative;font-weight:500}
.sidebar-link:hover{background:linear-gradient(90deg,rgba(102,126,234,.1) 0%,transparent 100%);color:#667eea;padding-left:2rem}
.sidebar-link.active{background:linear-gradient(90deg,rgba(102,126,234,.15) 0%,transparent 100%);color:#667eea;font-weight:600}
.sidebar-link.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)}
.sidebar-link i{width:20px;text-align:center;font-size:1.1rem}
.sidebar-link .badge{margin-left:auto;font-size:.7rem;padding:.3rem .6rem}
.sidebar-link.text-danger{color:#dc3545}
.sidebar-link.text-danger:hover{background:linear-gradient(90deg,rgba(220,53,69,.1) 0%,transparent 100%);color:#dc3545}
.sidebar-divider{height:1px;background:#e9ecef;margin:.5rem 1.5rem}
@media (max-width:991px){.dashboard-sidebar{position:static;margin-bottom:2rem}}
@media (max-width:767px){.sidebar-profile{padding:1.5rem 1rem}.profile-avatar{width:60px;height:60px}.sidebar-profile h5{font-size:1rem}.sidebar-link{padding:.75rem 1rem}.sidebar-section{padding:.5rem 1rem}}
</style>

<div class="dashboard-sidebar">
    <!-- Provider Profile -->
    <div class="sidebar-profile">
        <img src="<?php echo uploads_url('providers/' . ($provider['logo'] ?? 'default-provider.png')); ?>" 
             alt="<?php echo htmlspecialchars($provider['business_name']); ?>" 
             class="profile-avatar">
        <h5><?php echo htmlspecialchars($provider['business_name']); ?></h5>
        <p class="text-muted"><?php echo htmlspecialchars($provider['email']); ?></p>
        <?php if ($provider['verification_status'] === 'verified'): ?>
        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified</span>
        <?php endif; ?>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <a href="<?php echo base_url('provider/dashboard.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="sidebar-section">Listings</div>
        
        <a href="<?php echo base_url('provider/listings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'listings.php' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i>
            <span>My Listings</span>
            <span class="badge bg-primary"><?php echo db('listings')->where('provider_id', $providerId)->count(); ?></span>
        </a>
        
        <a href="<?php echo base_url('provider/add-listing.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'add-listing.php' ? 'active' : ''; ?>">
            <i class="fas fa-plus"></i>
            <span>Add New Listing</span>
        </a>
        
        <div class="sidebar-section">Bookings</div>
        
        <a href="<?php echo base_url('provider/bookings.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
            <?php
            $pendingBookings = db('bookings')->where('provider_id', $providerId)->where('status', 'pending')->count();
            if ($pendingBookings > 0):
            ?>
            <span class="badge bg-warning"><?php echo $pendingBookings; ?></span>
            <?php endif; ?>
        </a>
        
        <div class="sidebar-section">Account</div>
        
        <a href="<?php echo base_url('provider/profile.php'); ?>" 
           class="sidebar-link <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
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

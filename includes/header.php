<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pageTitle ?? 'TripEase - Your Local Travel Companion'; ?></title>
    <meta name="description" content="Book boats and rooms from trusted local providers. Discover your next local adventure with TripEase.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo assets_url('images/favicon.png'); ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo assets_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets_url('css/modern-ui.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets_url('css/responsive.css'); ?>">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo assets_url($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Top Bar (Mobile) -->
    <div class="top-bar d-lg-none">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo base_url(); ?>" class="logo-mobile">
                    <i class="fas fa-plane-departure"></i> TripEase
                </a>
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <i class="fas fa-plane-departure text-primary"></i>
                <span class="brand-text">TripEase</span>
            </a>
            
            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : ''; ?>" href="<?php echo base_url('search.php'); ?>">
                            <i class="fas fa-search"></i> Explore
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large"></i> Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo base_url('search.php?category=boat'); ?>">
                                <i class="fas fa-ship"></i> Boats
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('search.php?category=room'); ?>">
                                <i class="fas fa-bed"></i> Rooms
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo base_url('about.php'); ?>">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="<?php echo base_url('contact.php'); ?>">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <!-- User Menu -->
                <ul class="navbar-nav">
                    <?php if (is_logged_in(ROLE_USER)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo uploads_url('users/' . ($_SESSION['user_image'] ?? 'default-avatar.png')); ?>" 
                                     alt="User" class="user-avatar">
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo base_url('user/dashboard.php'); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('user/bookings.php'); ?>">
                                    <i class="fas fa-calendar-check"></i> My Bookings
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('user/profile.php'); ?>">
                                    <i class="fas fa-user"></i> Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('logout.php'); ?>">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php elseif (is_logged_in(ROLE_PROVIDER)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('provider/dashboard.php'); ?>">
                                <i class="fas fa-store"></i> Provider Dashboard
                            </a>
                        </li>
                    <?php elseif (is_logged_in(ROLE_ADMIN)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('admin/dashboard.php'); ?>">
                                <i class="fas fa-cog"></i> Admin Panel
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('login.php'); ?>">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="<?php echo base_url('register.php'); ?>">
                                <i class="fas fa-user-plus"></i> Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <h5>Menu</h5>
                <button class="mobile-menu-close" id="mobileMenuClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-menu-body">
                <ul class="mobile-nav-list">
                    <li><a href="<?php echo base_url(); ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo base_url('search.php'); ?>"><i class="fas fa-search"></i> Explore</a></li>
                    <li><a href="<?php echo base_url('search.php?category=boat'); ?>"><i class="fas fa-ship"></i> Boats</a></li>
                    <li><a href="<?php echo base_url('search.php?category=room'); ?>"><i class="fas fa-bed"></i> Rooms</a></li>
                    <li><a href="<?php echo base_url('about.php'); ?>"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="<?php echo base_url('contact.php'); ?>"><i class="fas fa-envelope"></i> Contact</a></li>
                    
                    <?php if (is_logged_in(ROLE_USER)): ?>
                        <li class="mobile-menu-divider"></li>
                        <li><a href="<?php echo base_url('user/dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="<?php echo base_url('user/bookings.php'); ?>"><i class="fas fa-calendar-check"></i> My Bookings</a></li>
                        <li><a href="<?php echo base_url('user/profile.php'); ?>"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="<?php echo base_url('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php elseif (is_logged_in(ROLE_PROVIDER)): ?>
                        <li class="mobile-menu-divider"></li>
                        <li><a href="<?php echo base_url('provider/dashboard.php'); ?>"><i class="fas fa-store"></i> Provider Dashboard</a></li>
                    <?php elseif (is_logged_in(ROLE_ADMIN)): ?>
                        <li class="mobile-menu-divider"></li>
                        <li><a href="<?php echo base_url('admin/dashboard.php'); ?>"><i class="fas fa-cog"></i> Admin Panel</a></li>
                    <?php else: ?>
                        <li class="mobile-menu-divider"></li>
                        <li><a href="<?php echo base_url('login.php'); ?>"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="<?php echo base_url('register.php'); ?>"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php
    $flash = get_flash_message();
    if ($flash):
    ?>
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show alert-floating" role="alert">
        <i class="fas fa-<?php echo $flash['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">

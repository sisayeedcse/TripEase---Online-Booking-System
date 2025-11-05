<?php
require_once 'config/config.php';
require_once 'includes/Auth.php';

// Determine user type
$userType = 'user';
if (isset($_SESSION['provider_id'])) {
    $userType = 'provider';
} elseif (isset($_SESSION['admin_id'])) {
    $userType = 'admin';
}

// Logout
Auth::logout($userType);

// Redirect to home
flash_message('success', 'You have been logged out successfully');
redirect(base_url());

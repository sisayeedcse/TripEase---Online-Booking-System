<?php
/**
 * Authentication Class
 * Handles user authentication, registration, and password management
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    
    /**
     * Register a new user
     */
    public static function registerUser($name, $email, $password, $phone = null) {
        // Check if email already exists
        $existing = db('users')->where('email', $email)->first();
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
        
        // Insert user
        $userId = db('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'status' => 'active'
        ]);
        
        if ($userId) {
            // Log activity
            self::logActivity('user', $userId, 'registration', 'User registered successfully');
            
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    /**
     * Register a new provider
     */
    public static function registerProvider($businessName, $ownerName, $email, $password, $phone, $description = null, $address = null) {
        // Check if email already exists
        $existing = db('providers')->where('email', $email)->first();
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
        
        // Insert provider
        $providerId = db('providers')->insert([
            'business_name' => $businessName,
            'owner_name' => $ownerName,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'description' => $description,
            'address' => $address,
            'verification_status' => 'pending',
            'status' => 'active'
        ]);
        
        if ($providerId) {
            // Log activity
            self::logActivity('provider', $providerId, 'registration', 'Provider registered successfully');
            
            // Notify admin
            self::createNotification('admin', 1, 'New Provider Registration', 
                "New provider '$businessName' has registered and awaits verification", 
                'provider_registration', '/admin/providers.php');
            
            return ['success' => true, 'message' => 'Registration successful. Your account is pending verification.', 'provider_id' => $providerId];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    /**
     * Login user
     */
    public static function loginUser($email, $password) {
        $user = db('users')->where('email', $email)->first();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account has been blocked. Please contact support.'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = 'user';
        
        // Log activity
        self::logActivity('user', $user['user_id'], 'login', 'User logged in');
        
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }
    
    /**
     * Login provider
     */
    public static function loginProvider($email, $password) {
        $provider = db('providers')->where('email', $email)->first();
        
        if (!$provider) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if ($provider['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account has been blocked. Please contact support.'];
        }
        
        if (!password_verify($password, $provider['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session
        $_SESSION['provider_id'] = $provider['provider_id'];
        $_SESSION['provider_name'] = $provider['business_name'];
        $_SESSION['provider_email'] = $provider['email'];
        $_SESSION['provider_role'] = 'provider';
        $_SESSION['verification_status'] = $provider['verification_status'];
        
        // Log activity
        self::logActivity('provider', $provider['provider_id'], 'login', 'Provider logged in');
        
        return ['success' => true, 'message' => 'Login successful', 'provider' => $provider];
    }
    
    /**
     * Login admin
     */
    public static function loginAdmin($email, $password) {
        $admin = db('admins')->where('email', $email)->first();
        
        if (!$admin) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if ($admin['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account is inactive. Please contact system administrator.'];
        }
        
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        
        // Log activity
        self::logActivity('admin', $admin['admin_id'], 'login', 'Admin logged in');
        
        return ['success' => true, 'message' => 'Login successful', 'admin' => $admin];
    }
    
    /**
     * Logout
     */
    public static function logout($userType = 'user') {
        $userId = get_user_id($userType);
        if ($userId) {
            self::logActivity($userType, $userId, 'logout', ucfirst($userType) . ' logged out');
        }
        
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Request password reset
     */
    public static function requestPasswordReset($email, $userType = 'user') {
        $table = $userType === 'provider' ? 'providers' : ($userType === 'admin' ? 'admins' : 'users');
        $idField = $userType === 'provider' ? 'provider_id' : ($userType === 'admin' ? 'admin_id' : 'user_id');
        
        $user = db($table)->where('email', $email)->first();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        // Generate reset token
        $token = generate_reset_token();
        $expiry = date('Y-m-d H:i:s', time() + RESET_TOKEN_EXPIRY);
        
        // Update user with reset token
        db($table)->where($idField, $user[$idField])->update([
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ]);
        
        // In a real application, send email with reset link
        $resetLink = base_url("reset-password.php?token=$token&type=$userType");
        
        return [
            'success' => true, 
            'message' => 'Password reset link has been sent to your email',
            'reset_link' => $resetLink, // For testing purposes
            'token' => $token
        ];
    }
    
    /**
     * Reset password
     */
    public static function resetPassword($token, $newPassword, $userType = 'user') {
        $table = $userType === 'provider' ? 'providers' : ($userType === 'admin' ? 'admins' : 'users');
        $idField = $userType === 'provider' ? 'provider_id' : ($userType === 'admin' ? 'admin_id' : 'user_id');
        
        $user = db($table)
            ->where('reset_token', $token)
            ->where('reset_token_expiry', '>', date('Y-m-d H:i:s'))
            ->first();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
        
        // Update password and clear reset token
        $updated = db($table)->where($idField, $user[$idField])->update([
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_token_expiry' => null
        ]);
        
        if ($updated) {
            self::logActivity($userType, $user[$idField], 'password_reset', 'Password reset successfully');
            return ['success' => true, 'message' => 'Password reset successful'];
        }
        
        return ['success' => false, 'message' => 'Password reset failed'];
    }
    
    /**
     * Change password
     */
    public static function changePassword($userId, $currentPassword, $newPassword, $userType = 'user') {
        $table = $userType === 'provider' ? 'providers' : ($userType === 'admin' ? 'admins' : 'users');
        $idField = $userType === 'provider' ? 'provider_id' : ($userType === 'admin' ? 'admin_id' : 'user_id');
        
        $user = db($table)->where($idField, $userId)->first();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
        
        // Update password
        $updated = db($table)->where($idField, $userId)->update([
            'password' => $hashedPassword
        ]);
        
        if ($updated) {
            self::logActivity($userType, $userId, 'password_change', 'Password changed successfully');
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Password change failed'];
    }
    
    /**
     * Log user activity
     */
    private static function logActivity($userType, $userId, $action, $description = null) {
        db('activity_logs')->insert([
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
    
    /**
     * Create notification
     */
    private static function createNotification($userType, $userId, $title, $message, $type, $link = null) {
        db('notifications')->insert([
            'user_type' => $userType,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false
        ]);
    }
}

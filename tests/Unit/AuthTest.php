<?php
/**
 * Authentication Class Unit Tests
 * Tests for Auth class functionality including registration, login, and password management
 */

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class AuthTest extends BaseTestCase
{
    /**
     * Test user registration with valid data
     */
    public function testUserRegistrationSuccess()
    {
        $this->beginTransaction();
        
        $result = Auth::registerUser(
            'Test User',
            'newuser' . uniqid() . '@example.com',
            'SecurePassword123',
            '01234567890'
        );
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertGreaterThan(0, $result['user_id']);
        $this->assertEquals('Registration successful', $result['message']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test user registration with duplicate email
     */
    public function testUserRegistrationDuplicateEmail()
    {
        $this->beginTransaction();
        
        $email = 'duplicate' . uniqid() . '@example.com';
        
        Auth::registerUser('User One', $email, 'password123', '01111111111');
        $result = Auth::registerUser('User Two', $email, 'password456', '02222222222');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Email already registered', $result['message']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test provider registration with valid data
     */
    public function testProviderRegistrationSuccess()
    {
        $this->beginTransaction();
        
        $result = Auth::registerProvider(
            'Test Business',
            'Test Owner',
            'provider' . uniqid() . '@example.com',
            'SecurePassword123',
            '01234567890',
            'Test description',
            'Test address'
        );
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('provider_id', $result);
        $this->assertGreaterThan(0, $result['provider_id']);
        $this->assertStringContainsString('verification', $result['message']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test user login with valid credentials
     */
    public function testUserLoginSuccess()
    {
        $this->beginTransaction();
        
        $email = 'logintest' . uniqid() . '@example.com';
        $password = 'LoginPassword123';
        
        Auth::registerUser('Login Test', $email, $password);
        
        $result = Auth::loginUser($email, $password);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($email, $_SESSION['user_email']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test user login with invalid password
     */
    public function testUserLoginInvalidPassword()
    {
        $this->beginTransaction();
        
        $email = 'logintest2' . uniqid() . '@example.com';
        Auth::registerUser('Login Test 2', $email, 'CorrectPassword');
        
        $result = Auth::loginUser($email, 'WrongPassword');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid email or password', $result['message']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test user login with non-existent email
     */
    public function testUserLoginNonExistentEmail()
    {
        $result = Auth::loginUser('nonexistent@example.com', 'password');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid email or password', $result['message']);
    }

    /**
     * Test user login with blocked account
     */
    public function testUserLoginBlockedAccount()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser(['status' => 'blocked']);
        
        $result = Auth::loginUser($user['email'], 'password123');
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('blocked', strtolower($result['message']));
        
        $this->rollbackTransaction();
    }

    /**
     * Test provider login with valid credentials
     */
    public function testProviderLoginSuccess()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        
        $result = Auth::loginProvider($provider['email'], 'password123');
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('provider', $result);
        $this->assertEquals($provider['provider_id'], $_SESSION['provider_id']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test admin login with valid credentials
     */
    public function testAdminLoginSuccess()
    {
        $this->beginTransaction();
        
        $adminId = db('admins')->insert([
            'name' => 'Test Admin',
            'email' => 'testadmin' . uniqid() . '@example.com',
            'password' => password_hash('AdminPass123', PASSWORD_BCRYPT),
            'role' => 'moderator',
            'status' => 'active'
        ]);
        
        $admin = db('admins')->where('admin_id', $adminId)->first();
        
        $result = Auth::loginAdmin($admin['email'], 'AdminPass123');
        
        $this->assertTrue($result['success']);
        $this->assertEquals($adminId, $_SESSION['admin_id']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test password reset request
     */
    public function testPasswordResetRequest()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $result = Auth::requestPasswordReset($user['email'], 'user');
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('reset_link', $result);
        
        // Verify token is saved in database
        $updatedUser = db('users')->where('user_id', $user['user_id'])->first();
        $this->assertNotNull($updatedUser['reset_token']);
        $this->assertNotNull($updatedUser['reset_token_expiry']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test password reset with valid token
     */
    public function testPasswordResetSuccess()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $resetResult = Auth::requestPasswordReset($user['email'], 'user');
        
        $newPassword = 'NewSecurePassword123';
        $result = Auth::resetPassword($resetResult['token'], $newPassword, 'user');
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Password reset successful', $result['message']);
        
        // Verify new password works
        $loginResult = Auth::loginUser($user['email'], $newPassword);
        $this->assertTrue($loginResult['success']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test password reset with invalid token
     */
    public function testPasswordResetInvalidToken()
    {
        $result = Auth::resetPassword('invalid_token_12345', 'NewPassword', 'user');
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid or expired', $result['message']);
    }

    /**
     * Test password change with correct current password
     */
    public function testPasswordChangeSuccess()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $result = Auth::changePassword(
            $user['user_id'],
            'password123',
            'NewPassword456',
            'user'
        );
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Password changed successfully', $result['message']);
        
        // Verify new password works
        $loginResult = Auth::loginUser($user['email'], 'NewPassword456');
        $this->assertTrue($loginResult['success']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test password change with incorrect current password
     */
    public function testPasswordChangeIncorrectCurrentPassword()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $result = Auth::changePassword(
            $user['user_id'],
            'WrongPassword',
            'NewPassword456',
            'user'
        );
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Current password is incorrect', $result['message']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test logout functionality
     */
    public function testLogout()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        
        $result = Auth::logout('user');
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($_SESSION);
        
        $this->rollbackTransaction();
    }

    /**
     * Test password hashing
     */
    public function testPasswordHashing()
    {
        $this->beginTransaction();
        
        $password = 'TestPassword123';
        $email = 'hashtest' . uniqid() . '@example.com';
        
        Auth::registerUser('Hash Test', $email, $password);
        
        $user = db('users')->where('email', $email)->first();
        
        // Verify password is hashed
        $this->assertNotEquals($password, $user['password']);
        
        // Verify password can be verified
        $this->assertTrue(password_verify($password, $user['password']));
        
        $this->rollbackTransaction();
    }
}

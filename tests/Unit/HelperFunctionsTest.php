<?php
/**
 * Helper Functions Unit Tests
 * Tests for utility functions in config.php
 */

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends BaseTestCase
{
    /**
     * Test base_url helper
     */
    public function testBaseUrlHelper()
    {
        $url = base_url('search.php');
        $this->assertStringContainsString('search.php', $url);
        $this->assertStringStartsWith('http', $url);
    }

    /**
     * Test assets_url helper
     */
    public function testAssetsUrlHelper()
    {
        $url = assets_url('css/style.css');
        $this->assertStringContainsString('assets', $url);
        $this->assertStringContainsString('css/style.css', $url);
    }

    /**
     * Test uploads_url helper
     */
    public function testUploadsUrlHelper()
    {
        $url = uploads_url('users/profile.jpg');
        $this->assertStringContainsString('uploads', $url);
        $this->assertStringContainsString('users/profile.jpg', $url);
    }

    /**
     * Test sanitize_input function
     */
    public function testSanitizeInput()
    {
        $input = '  <script>alert("XSS")</script>  ';
        $sanitized = sanitize_input($input);
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertNotEquals($input, $sanitized);
    }

    /**
     * Test format_price function
     */
    public function testFormatPrice()
    {
        $price = 1500.50;
        $formatted = format_price($price);
        
        $this->assertStringContainsString('1,500.50', $formatted);
        $this->assertStringContainsString(CURRENCY_SYMBOL, $formatted);
    }

    /**
     * Test format_date function
     */
    public function testFormatDate()
    {
        $date = '2024-12-25';
        $formatted = format_date($date);
        
        $this->assertNotEmpty($formatted);
        $this->assertNotEquals($date, $formatted);
    }

    /**
     * Test time_ago function with recent time
     */
    public function testTimeAgoRecent()
    {
        $now = date('Y-m-d H:i:s');
        $timeAgo = time_ago($now);
        
        $this->assertEquals('Just now', $timeAgo);
    }

    /**
     * Test time_ago function with minutes ago
     */
    public function testTimeAgoMinutes()
    {
        $time = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $timeAgo = time_ago($time);
        
        $this->assertStringContainsString('minute', $timeAgo);
        $this->assertStringContainsString('5', $timeAgo);
    }

    /**
     * Test time_ago function with hours ago
     */
    public function testTimeAgoHours()
    {
        $time = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $timeAgo = time_ago($time);
        
        $this->assertStringContainsString('hour', $timeAgo);
        $this->assertStringContainsString('3', $timeAgo);
    }

    /**
     * Test time_ago function with days ago
     */
    public function testTimeAgoDays()
    {
        $time = date('Y-m-d H:i:s', strtotime('-2 days'));
        $timeAgo = time_ago($time);
        
        $this->assertStringContainsString('day', $timeAgo);
        $this->assertStringContainsString('2', $timeAgo);
    }

    /**
     * Test generate_booking_reference
     */
    public function testGenerateBookingReference()
    {
        $ref1 = generate_booking_reference();
        $ref2 = generate_booking_reference();
        
        $this->assertNotEmpty($ref1);
        $this->assertNotEmpty($ref2);
        $this->assertNotEquals($ref1, $ref2);
        $this->assertStringStartsWith('TE', $ref1);
    }

    /**
     * Test generate_reset_token
     */
    public function testGenerateResetToken()
    {
        $token1 = generate_reset_token();
        $token2 = generate_reset_token();
        
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(64, strlen($token1)); // 32 bytes = 64 hex chars
    }

    /**
     * Test flash_message and get_flash_message
     */
    public function testFlashMessage()
    {
        flash_message('success', 'Test message');
        
        $this->assertArrayHasKey('flash_message', $_SESSION);
        $this->assertEquals('success', $_SESSION['flash_message']['type']);
        $this->assertEquals('Test message', $_SESSION['flash_message']['message']);
        
        $message = get_flash_message();
        
        $this->assertEquals('success', $message['type']);
        $this->assertEquals('Test message', $message['message']);
        
        // Flash message should be cleared after retrieval
        $secondRetrieval = get_flash_message();
        $this->assertNull($secondRetrieval);
    }

    /**
     * Test is_logged_in function
     */
    public function testIsLoggedIn()
    {
        // Not logged in
        $_SESSION = [];
        $this->assertFalse(is_logged_in());
        
        // User logged in
        $_SESSION['user_id'] = 1;
        $this->assertTrue(is_logged_in());
        $this->assertTrue(is_logged_in(ROLE_USER));
        
        // Provider logged in
        $_SESSION = ['provider_id' => 1];
        $this->assertTrue(is_logged_in(ROLE_PROVIDER));
        
        // Admin logged in
        $_SESSION = ['admin_id' => 1];
        $this->assertTrue(is_logged_in(ROLE_ADMIN));
    }

    /**
     * Test get_user_id function
     */
    public function testGetUserId()
    {
        $_SESSION = [];
        
        // User ID
        $_SESSION['user_id'] = 123;
        $this->assertEquals(123, get_user_id(ROLE_USER));
        
        // Provider ID
        $_SESSION = ['provider_id' => 456];
        $this->assertEquals(456, get_user_id(ROLE_PROVIDER));
        
        // Admin ID
        $_SESSION = ['admin_id' => 789];
        $this->assertEquals(789, get_user_id(ROLE_ADMIN));
        
        // No ID
        $_SESSION = [];
        $this->assertNull(get_user_id(ROLE_USER));
    }

    /**
     * Test password hashing constants
     */
    public function testPasswordHashingConstants()
    {
        $this->assertTrue(defined('PASSWORD_HASH_ALGO'));
        $this->assertTrue(defined('PASSWORD_HASH_COST'));
        $this->assertEquals(PASSWORD_BCRYPT, PASSWORD_HASH_ALGO);
        $this->assertIsInt(PASSWORD_HASH_COST);
    }

    /**
     * Test database configuration constants
     */
    public function testDatabaseConstants()
    {
        $this->assertTrue(defined('DB_HOST'));
        $this->assertTrue(defined('DB_NAME'));
        $this->assertTrue(defined('DB_USER'));
        $this->assertTrue(defined('DB_CHARSET'));
        
        $this->assertEquals('tripease', DB_NAME);
        $this->assertEquals('localhost', DB_HOST);
    }

    /**
     * Test application constants
     */
    public function testApplicationConstants()
    {
        $this->assertTrue(defined('APP_NAME'));
        $this->assertTrue(defined('APP_URL'));
        $this->assertTrue(defined('APP_VERSION'));
        
        $this->assertEquals('TripEase', APP_NAME);
    }

    /**
     * Test role constants
     */
    public function testRoleConstants()
    {
        $this->assertEquals('user', ROLE_USER);
        $this->assertEquals('provider', ROLE_PROVIDER);
        $this->assertEquals('admin', ROLE_ADMIN);
        $this->assertEquals('super_admin', ROLE_SUPER_ADMIN);
        $this->assertEquals('moderator', ROLE_MODERATOR);
    }

    /**
     * Test booking status constants
     */
    public function testBookingStatusConstants()
    {
        $this->assertEquals('pending', BOOKING_PENDING);
        $this->assertEquals('confirmed', BOOKING_CONFIRMED);
        $this->assertEquals('completed', BOOKING_COMPLETED);
        $this->assertEquals('cancelled', BOOKING_CANCELLED);
        $this->assertEquals('declined', BOOKING_DECLINED);
    }

    /**
     * Test listing status constants
     */
    public function testListingStatusConstants()
    {
        $this->assertEquals('active', LISTING_ACTIVE);
        $this->assertEquals('inactive', LISTING_INACTIVE);
        $this->assertEquals('pending', LISTING_PENDING);
        $this->assertEquals('rejected', LISTING_REJECTED);
    }

    /**
     * Test file upload configuration
     */
    public function testFileUploadConfiguration()
    {
        $this->assertTrue(defined('MAX_FILE_SIZE'));
        $this->assertTrue(defined('ALLOWED_IMAGE_TYPES'));
        $this->assertTrue(defined('ALLOWED_IMAGE_EXTENSIONS'));
        
        $this->assertGreaterThan(0, MAX_FILE_SIZE);
        $this->assertIsArray(ALLOWED_IMAGE_TYPES);
        $this->assertIsArray(ALLOWED_IMAGE_EXTENSIONS);
    }
}

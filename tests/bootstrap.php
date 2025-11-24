<?php
/**
 * PHPUnit Bootstrap File
 * Initializes the testing environment
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define test mode
define('TEST_MODE', true);

// Set timezone
date_default_timezone_set('Asia/Dhaka');

// Load project root path
define('TEST_ROOT', dirname(__DIR__));

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration files
require_once TEST_ROOT . '/config/config.php';
require_once TEST_ROOT . '/config/database.php';
require_once TEST_ROOT . '/includes/Auth.php';

// Load Composer autoloader if exists
if (file_exists(TEST_ROOT . '/vendor/autoload.php')) {
    require_once TEST_ROOT . '/vendor/autoload.php';
}

/**
 * Base Test Case Class
 * Provides common functionality for all tests
 */
abstract class BaseTestCase extends PHPUnit\Framework\TestCase
{
    protected $db;
    protected static $dbSetup = false;

    /**
     * Set up test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Get database instance
        $this->db = Database::getInstance()->getConnection();
        
        // Clean session data
        $_SESSION = [];
        
        // Reset superglobals
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        // Clear any test data if needed
        $_SESSION = [];
        
        parent::tearDown();
    }

    /**
     * Begin database transaction
     */
    protected function beginTransaction()
    {
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
    }

    /**
     * Rollback database transaction
     */
    protected function rollbackTransaction()
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    /**
     * Create a test user
     */
    protected function createTestUser($overrides = [])
    {
        $defaults = [
            'name' => 'Test User',
            'email' => 'testuser' . uniqid() . '@example.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'phone' => '01234567890',
            'status' => 'active'
        ];
        
        $data = array_merge($defaults, $overrides);
        $userId = db('users')->insert($data);
        
        return ['user_id' => $userId] + $data;
    }

    /**
     * Create a test provider
     */
    protected function createTestProvider($overrides = [])
    {
        $defaults = [
            'business_name' => 'Test Provider',
            'owner_name' => 'Test Owner',
            'email' => 'provider' . uniqid() . '@example.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'phone' => '01234567890',
            'description' => 'Test provider description',
            'verification_status' => 'verified',
            'status' => 'active'
        ];
        
        $data = array_merge($defaults, $overrides);
        $providerId = db('providers')->insert($data);
        
        return ['provider_id' => $providerId] + $data;
    }

    /**
     * Create a test listing
     */
    protected function createTestListing($providerId, $overrides = [])
    {
        $defaults = [
            'provider_id' => $providerId,
            'title' => 'Test Listing',
            'description' => 'Test listing description',
            'category' => 'boat',
            'location' => 'Test Location',
            'price' => 1000.00,
            'price_unit' => 'hour',
            'capacity' => 5,
            'amenities' => 'WiFi, AC',
            'main_image' => 'test-image.jpg',
            'status' => 'active',
            'approval_status' => 'approved'
        ];
        
        $data = array_merge($defaults, $overrides);
        $listingId = db('listings')->insert($data);
        
        return ['listing_id' => $listingId] + $data;
    }

    /**
     * Create a test booking
     */
    protected function createTestBooking($userId, $listingId, $providerId, $overrides = [])
    {
        $defaults = [
            'user_id' => $userId,
            'listing_id' => $listingId,
            'provider_id' => $providerId,
            'booking_date' => date('Y-m-d'),
            'start_date' => date('Y-m-d', strtotime('+1 day')),
            'end_date' => date('Y-m-d', strtotime('+2 days')),
            'duration' => 1,
            'total_price' => 1000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_reference' => 'TEST' . uniqid()
        ];
        
        $data = array_merge($defaults, $overrides);
        $bookingId = db('bookings')->insert($data);
        
        return ['booking_id' => $bookingId] + $data;
    }

    /**
     * Clean up test data
     */
    protected function cleanupTestData()
    {
        // Delete test records
        $this->db->exec("DELETE FROM bookings WHERE booking_reference LIKE 'TEST%'");
        $this->db->exec("DELETE FROM users WHERE email LIKE '%@example.com'");
        $this->db->exec("DELETE FROM providers WHERE email LIKE '%@example.com'");
    }

    /**
     * Assert array has keys
     */
    protected function assertArrayHasKeys(array $keys, array $array, $message = '')
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array, $message);
        }
    }
}

echo "\n✓ Test Bootstrap Loaded Successfully\n";
echo "Database: " . DB_NAME . "\n";
echo "Test Mode: Enabled\n\n";

<?php
/**
 * Database Class Unit Tests
 * Tests for Database singleton and QueryBuilder functionality
 */

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends BaseTestCase
{
    /**
     * Test database singleton pattern
     */
    public function testDatabaseSingletonInstance()
    {
        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();
        
        $this->assertInstanceOf(Database::class, $instance1);
        $this->assertSame($instance1, $instance2, 'Both instances should be the same object');
    }

    /**
     * Test database connection returns PDO instance
     */
    public function testGetConnectionReturnsPDO()
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();
        
        $this->assertInstanceOf(PDO::class, $connection);
    }

    /**
     * Test database connection is active
     */
    public function testDatabaseConnectionIsActive()
    {
        $db = Database::getInstance()->getConnection();
        
        // Simple query to verify connection
        $stmt = $db->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        $this->assertEquals(1, $result['test']);
    }

    /**
     * Test QueryBuilder select method
     */
    public function testQueryBuilderSelect()
    {
        $this->beginTransaction();
        
        $result = db('users')->select('user_id, name, email')->get();
        
        $this->assertIsArray($result);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder where method
     */
    public function testQueryBuilderWhere()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser([
            'email' => 'unique_test@example.com'
        ]);
        
        $result = db('users')
            ->where('email', 'unique_test@example.com')
            ->first();
        
        $this->assertNotNull($result);
        $this->assertEquals('unique_test@example.com', $result['email']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder insert method
     */
    public function testQueryBuilderInsert()
    {
        $this->beginTransaction();
        
        $userId = db('users')->insert([
            'name' => 'Insert Test User',
            'email' => 'insert_test@example.com',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'status' => 'active'
        ]);
        
        $this->assertIsNumeric($userId);
        $this->assertGreaterThan(0, $userId);
        
        // Verify insertion
        $user = db('users')->where('user_id', $userId)->first();
        $this->assertEquals('Insert Test User', $user['name']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder update method
     */
    public function testQueryBuilderUpdate()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $updated = db('users')
            ->where('user_id', $user['user_id'])
            ->update(['name' => 'Updated Name']);
        
        $this->assertTrue($updated);
        
        // Verify update
        $updatedUser = db('users')->where('user_id', $user['user_id'])->first();
        $this->assertEquals('Updated Name', $updatedUser['name']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder delete method
     */
    public function testQueryBuilderDelete()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $deleted = db('users')
            ->where('user_id', $user['user_id'])
            ->delete();
        
        $this->assertTrue($deleted);
        
        // Verify deletion
        $deletedUser = db('users')->where('user_id', $user['user_id'])->first();
        $this->assertNull($deletedUser);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder count method
     */
    public function testQueryBuilderCount()
    {
        $this->beginTransaction();
        
        $this->createTestUser();
        $this->createTestUser();
        
        $count = db('users')
            ->where('email', 'LIKE', '%@example.com')
            ->count();
        
        $this->assertGreaterThanOrEqual(2, $count);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder whereLike method
     */
    public function testQueryBuilderWhereLike()
    {
        $this->beginTransaction();
        
        $this->createTestUser(['email' => 'searchable@example.com']);
        
        $results = db('users')
            ->whereLike('email', 'searchable')
            ->get();
        
        $this->assertNotEmpty($results);
        $this->assertStringContainsString('searchable', $results[0]['email']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder orderBy method
     */
    public function testQueryBuilderOrderBy()
    {
        $this->beginTransaction();
        
        $this->createTestUser(['name' => 'Zulu User']);
        $this->createTestUser(['name' => 'Alpha User']);
        
        $results = db('users')
            ->where('email', 'LIKE', '%@example.com')
            ->orderBy('name', 'ASC')
            ->get();
        
        $this->assertNotEmpty($results);
        $this->assertLessThan(
            $results[count($results) - 1]['name'],
            $results[0]['name']
        );
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder limit method
     */
    public function testQueryBuilderLimit()
    {
        $this->beginTransaction();
        
        $this->createTestUser();
        $this->createTestUser();
        $this->createTestUser();
        
        $results = db('users')
            ->where('email', 'LIKE', '%@example.com')
            ->limit(2)
            ->get();
        
        $this->assertCount(2, $results);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder join method
     */
    public function testQueryBuilderJoin()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $result = db('listings')
            ->select('listings.*, providers.business_name')
            ->join('providers', 'listings.provider_id', '=', 'providers.provider_id')
            ->where('listings.listing_id', $listing['listing_id'])
            ->first();
        
        $this->assertNotNull($result);
        $this->assertArrayHasKey('business_name', $result);
        $this->assertEquals($provider['business_name'], $result['business_name']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder exists method
     */
    public function testQueryBuilderExists()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        
        $exists = db('users')
            ->where('user_id', $user['user_id'])
            ->exists();
        
        $this->assertTrue($exists);
        
        $notExists = db('users')
            ->where('user_id', 999999)
            ->exists();
        
        $this->assertFalse($notExists);
        
        $this->rollbackTransaction();
    }

    /**
     * Test QueryBuilder transaction methods
     */
    public function testQueryBuilderTransactions()
    {
        $qb = db('users');
        
        $qb->beginTransaction();
        $this->assertTrue($this->db->inTransaction());
        
        $userId = $qb->insert([
            'name' => 'Transaction Test',
            'email' => 'transaction@example.com',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'status' => 'active'
        ]);
        
        $qb->rollback();
        
        // Verify rollback
        $user = db('users')->where('user_id', $userId)->first();
        $this->assertNull($user);
    }
}

<?php
/**
 * Search and Listing Tests
 * Tests for search functionality, filters, and listing retrieval
 */

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class SearchTest extends BaseTestCase
{
    /**
     * Test basic listing retrieval
     */
    public function testGetActiveListings()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id']);
        $this->createTestListing($provider['provider_id']);
        
        $listings = db('listings')
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->get();
        
        $this->assertGreaterThanOrEqual(2, count($listings));
        
        $this->rollbackTransaction();
    }

    /**
     * Test search by location
     */
    public function testSearchByLocation()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], ['location' => 'Cox\'s Bazar']);
        $this->createTestListing($provider['provider_id'], ['location' => 'Sundarbans']);
        
        $results = db('listings')
            ->whereLike('location', 'Cox')
            ->where('status', 'active')
            ->get();
        
        $this->assertNotEmpty($results);
        $this->assertStringContainsString('Cox', $results[0]['location']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test search by category
     */
    public function testSearchByCategory()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], ['category' => 'boat']);
        $this->createTestListing($provider['provider_id'], ['category' => 'room']);
        
        $boats = db('listings')
            ->where('category', 'boat')
            ->where('status', 'active')
            ->get();
        
        $this->assertNotEmpty($boats);
        foreach ($boats as $boat) {
            $this->assertEquals('boat', $boat['category']);
        }
        
        $this->rollbackTransaction();
    }

    /**
     * Test search by price range
     */
    public function testSearchByPriceRange()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], ['price' => 500.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 1500.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 2500.00]);
        
        $results = db('listings')
            ->where('price', '>=', 1000)
            ->where('price', '<=', 2000)
            ->where('status', 'active')
            ->get();
        
        $this->assertNotEmpty($results);
        foreach ($results as $listing) {
            $this->assertGreaterThanOrEqual(1000, $listing['price']);
            $this->assertLessThanOrEqual(2000, $listing['price']);
        }
        
        $this->rollbackTransaction();
    }

    /**
     * Test sorting by price ascending
     */
    public function testSortByPriceLowToHigh()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], ['price' => 3000.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 1000.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 2000.00]);
        
        $results = db('listings')
            ->where('status', 'active')
            ->where('provider_id', $provider['provider_id'])
            ->orderBy('price', 'ASC')
            ->get();
        
        $this->assertGreaterThanOrEqual(3, count($results));
        
        // Verify sorting
        $prices = array_column($results, 'price');
        $sortedPrices = $prices;
        sort($sortedPrices, SORT_NUMERIC);
        $this->assertEquals($sortedPrices, $prices);
        
        $this->rollbackTransaction();
    }

    /**
     * Test sorting by price descending
     */
    public function testSortByPriceHighToLow()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], ['price' => 1000.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 3000.00]);
        $this->createTestListing($provider['provider_id'], ['price' => 2000.00]);
        
        $results = db('listings')
            ->where('status', 'active')
            ->where('provider_id', $provider['provider_id'])
            ->orderBy('price', 'DESC')
            ->get();
        
        $this->assertGreaterThanOrEqual(3, count($results));
        
        // Verify descending order
        for ($i = 0; $i < count($results) - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $results[$i + 1]['price'],
                $results[$i]['price']
            );
        }
        
        $this->rollbackTransaction();
    }

    /**
     * Test pagination
     */
    public function testListingPagination()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        
        // Create 15 listings
        for ($i = 0; $i < 15; $i++) {
            $this->createTestListing($provider['provider_id'], [
                'title' => 'Listing ' . $i
            ]);
        }
        
        $perPage = 10;
        $page1 = db('listings')
            ->where('status', 'active')
            ->where('provider_id', $provider['provider_id'])
            ->limit($perPage, 0)
            ->get();
        
        $page2 = db('listings')
            ->where('status', 'active')
            ->where('provider_id', $provider['provider_id'])
            ->limit($perPage, $perPage)
            ->get();
        
        $this->assertCount(10, $page1);
        $this->assertGreaterThanOrEqual(5, count($page2));
        
        $this->rollbackTransaction();
    }

    /**
     * Test listing with provider details
     */
    public function testListingWithProviderJoin()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider(['business_name' => 'Unique Provider Name']);
        $listing = $this->createTestListing($provider['provider_id']);
        
        $result = db('listings')
            ->select('listings.*, providers.business_name')
            ->join('providers', 'listings.provider_id', '=', 'providers.provider_id')
            ->where('listings.listing_id', $listing['listing_id'])
            ->first();
        
        $this->assertNotNull($result);
        $this->assertEquals('Unique Provider Name', $result['business_name']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test listing with average rating
     */
    public function testListingWithAverageRating()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        // Create bookings
        $booking1 = $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id'], [
            'status' => 'completed'
        ]);
        $booking2 = $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id'], [
            'status' => 'completed'
        ]);
        
        // Create reviews
        db('reviews')->insert([
            'booking_id' => $booking1['booking_id'],
            'user_id' => $user['user_id'],
            'listing_id' => $listing['listing_id'],
            'provider_id' => $provider['provider_id'],
            'rating' => 4,
            'comment' => 'Good service',
            'status' => 'approved'
        ]);
        
        db('reviews')->insert([
            'booking_id' => $booking2['booking_id'],
            'user_id' => $user['user_id'],
            'listing_id' => $listing['listing_id'],
            'provider_id' => $provider['provider_id'],
            'rating' => 5,
            'comment' => 'Excellent!',
            'status' => 'approved'
        ]);
        
        // Get listing with average rating
        $result = db('listings')->raw(
            "SELECT listings.*, AVG(reviews.rating) as avg_rating 
             FROM listings 
             LEFT JOIN reviews ON listings.listing_id = reviews.listing_id
             WHERE listings.listing_id = ?
             GROUP BY listings.listing_id",
            [$listing['listing_id']]
        );
        
        $this->assertNotEmpty($result);
        $this->assertEquals(4.5, round($result[0]['avg_rating'], 1));
        
        $this->rollbackTransaction();
    }

    /**
     * Test listing view counter
     */
    public function testListingViewIncrement()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id'], ['views' => 0]);
        
        // Increment views (using PHP arithmetic)
        $current = db('listings')->where('listing_id', $listing['listing_id'])->first();
        db('listings')
            ->where('listing_id', $listing['listing_id'])
            ->update(['views' => $current['views'] + 1]);
        
        $updated = db('listings')->where('listing_id', $listing['listing_id'])->first();
        
        $this->assertGreaterThan(0, $updated['views']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test combined filters
     */
    public function testCombinedSearchFilters()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $this->createTestListing($provider['provider_id'], [
            'category' => 'boat',
            'location' => 'Cox\'s Bazar',
            'price' => 1500.00
        ]);
        
        $results = db('listings')
            ->where('category', 'boat')
            ->whereLike('location', 'Cox')
            ->where('price', '<=', 2000)
            ->where('status', 'active')
            ->get();
        
        $this->assertNotEmpty($results);
        $this->assertEquals('boat', $results[0]['category']);
        $this->assertLessThanOrEqual(2000, $results[0]['price']);
        
        $this->rollbackTransaction();
    }
}

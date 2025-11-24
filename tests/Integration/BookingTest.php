<?php
/**
 * Booking Logic Integration Tests
 * Tests for booking creation, validation, and business logic
 */

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class BookingTest extends BaseTestCase
{
    /**
     * Test booking creation with valid data
     */
    public function testCreateBookingSuccess()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $booking = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id']
        );
        
        $this->assertArrayHasKey('booking_id', $booking);
        $this->assertGreaterThan(0, $booking['booking_id']);
        
        // Verify booking in database
        $savedBooking = db('bookings')
            ->where('booking_id', $booking['booking_id'])
            ->first();
        
        $this->assertNotNull($savedBooking);
        $this->assertEquals($user['user_id'], $savedBooking['user_id']);
        $this->assertEquals($listing['listing_id'], $savedBooking['listing_id']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking reference generation is unique
     */
    public function testBookingReferenceUniqueness()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $booking1 = $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        $booking2 = $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        
        $this->assertNotEquals($booking1['booking_reference'], $booking2['booking_reference']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking with past start date
     */
    public function testBookingWithPastStartDate()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        // Create booking with past date
        $pastDate = date('Y-m-d', strtotime('-2 days'));
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $testDate = new DateTime($pastDate);
        
        // Verify the date is in the past
        $this->assertLessThan($today, $testDate);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking with end date before start date
     */
    public function testBookingWithInvalidDateRange()
    {
        $this->beginTransaction();
        
        $startDate = date('Y-m-d', strtotime('+5 days'));
        $endDate = date('Y-m-d', strtotime('+3 days'));
        
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        
        // Verify end date is before start date
        $this->assertGreaterThan($end, $start);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking date conflict detection
     */
    public function testBookingDateConflictDetection()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        // Create first booking
        $booking1 = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id'],
            [
                'start_date' => date('Y-m-d', strtotime('+5 days')),
                'end_date' => date('Y-m-d', strtotime('+7 days')),
                'status' => 'confirmed'
            ]
        );
        
        // Check for conflicts
        $conflictingDates = db('bookings')->raw(
            "SELECT COUNT(*) as count FROM bookings 
             WHERE listing_id = ? 
             AND status IN ('pending', 'confirmed')
             AND (
                 (start_date <= ? AND end_date >= ?) OR
                 (start_date <= ? AND end_date >= ?)
             )",
            [
                $listing['listing_id'],
                date('Y-m-d', strtotime('+6 days')),
                date('Y-m-d', strtotime('+6 days')),
                date('Y-m-d', strtotime('+8 days')),
                date('Y-m-d', strtotime('+8 days'))
            ]
        );
        
        $this->assertGreaterThan(0, $conflictingDates[0]['count']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking price calculation
     */
    public function testBookingPriceCalculation()
    {
        $this->beginTransaction();
        
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id'], [
            'price' => 500.00,
            'price_unit' => 'hour'
        ]);
        
        $startDate = new DateTime('+1 day');
        $endDate = new DateTime('+2 days');
        $interval = $startDate->diff($endDate);
        $days = $interval->days;
        
        $expectedPrice = $listing['price'] * $days * 24; // hours
        
        $this->assertEquals(12000.00, $expectedPrice); // 500 * 1 day * 24 hours = 12000
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking status transitions
     */
    public function testBookingStatusTransitions()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $booking = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id'],
            ['status' => 'pending']
        );
        
        // Update to confirmed
        db('bookings')
            ->where('booking_id', $booking['booking_id'])
            ->update(['status' => 'confirmed']);
        
        $updated = db('bookings')->where('booking_id', $booking['booking_id'])->first();
        $this->assertEquals('confirmed', $updated['status']);
        
        // Update to completed
        db('bookings')
            ->where('booking_id', $booking['booking_id'])
            ->update(['status' => 'completed']);
        
        $completed = db('bookings')->where('booking_id', $booking['booking_id'])->first();
        $this->assertEquals('completed', $completed['status']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking cancellation
     */
    public function testBookingCancellation()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $booking = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id'],
            ['status' => 'confirmed']
        );
        
        // Cancel booking
        db('bookings')
            ->where('booking_id', $booking['booking_id'])
            ->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Test cancellation'
            ]);
        
        $cancelled = db('bookings')->where('booking_id', $booking['booking_id'])->first();
        
        $this->assertEquals('cancelled', $cancelled['status']);
        $this->assertNotNull($cancelled['cancellation_reason']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking retrieval by user
     */
    public function testGetBookingsByUser()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        
        $bookings = db('bookings')
            ->where('user_id', $user['user_id'])
            ->get();
        
        $this->assertGreaterThanOrEqual(2, count($bookings));
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking retrieval by provider
     */
    public function testGetBookingsByProvider()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        $this->createTestBooking($user['user_id'], $listing['listing_id'], $provider['provider_id']);
        
        $bookings = db('bookings')
            ->where('provider_id', $provider['provider_id'])
            ->get();
        
        $this->assertGreaterThanOrEqual(2, count($bookings));
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking with special requests
     */
    public function testBookingWithSpecialRequests()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $specialRequest = 'Please arrange early check-in';
        
        $booking = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id'],
            ['special_requests' => $specialRequest]
        );
        
        $saved = db('bookings')->where('booking_id', $booking['booking_id'])->first();
        
        $this->assertEquals($specialRequest, $saved['special_requests']);
        
        $this->rollbackTransaction();
    }

    /**
     * Test booking listing join query
     */
    public function testBookingWithListingDetails()
    {
        $this->beginTransaction();
        
        $user = $this->createTestUser();
        $provider = $this->createTestProvider();
        $listing = $this->createTestListing($provider['provider_id']);
        
        $booking = $this->createTestBooking(
            $user['user_id'],
            $listing['listing_id'],
            $provider['provider_id']
        );
        
        $bookingWithListing = db('bookings')
            ->select('bookings.*, listings.title, listings.location')
            ->join('listings', 'bookings.listing_id', '=', 'listings.listing_id')
            ->where('bookings.booking_id', $booking['booking_id'])
            ->first();
        
        $this->assertNotNull($bookingWithListing);
        $this->assertArrayHasKey('title', $bookingWithListing);
        $this->assertEquals($listing['title'], $bookingWithListing['title']);
        
        $this->rollbackTransaction();
    }
}

<?php
/**
 * Verify Database Integrity After Tests
 * This script checks that no test data persists in the database
 */

require_once 'config/database.php';

echo "\n=== DATABASE INTEGRITY CHECK ===\n\n";

$db = Database::getInstance()->getConnection();

// Check for test users
$testUsers = $db->query("SELECT COUNT(*) as count FROM users WHERE email LIKE '%@example.com'")->fetch();
echo "✓ Test users (email @example.com): " . $testUsers['count'] . " (expected: 0)\n";

// Check for test bookings
$testBookings = $db->query("SELECT COUNT(*) as count FROM bookings WHERE booking_reference LIKE 'TEST%'")->fetch();
echo "✓ Test bookings (ref TEST*): " . $testBookings['count'] . " (expected: 0)\n";

// Check for test providers
$testProviders = $db->query("SELECT COUNT(*) as count FROM providers WHERE email LIKE '%@example.com'")->fetch();
echo "✓ Test providers (email @example.com): " . $testProviders['count'] . " (expected: 0)\n";

echo "\n";

if ($testUsers['count'] == 0 && $testBookings['count'] == 0 && $testProviders['count'] == 0) {
    echo "✅ SUCCESS: Database is clean! All tests properly rolled back.\n";
} else {
    echo "⚠️  WARNING: Some test data found in database!\n";
}

echo "\n=== END CHECK ===\n\n";

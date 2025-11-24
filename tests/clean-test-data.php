<?php
/**
 * Clean Test Data from Database
 * Removes any test records that may have been created
 */

require_once 'config/database.php';

echo "\n=== CLEANING TEST DATA ===\n\n";

$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();
    
    // Delete test bookings
    $stmt = $db->prepare("DELETE FROM bookings WHERE booking_reference LIKE 'TEST%'");
    $stmt->execute();
    $bookings = $stmt->rowCount();
    echo "✓ Deleted $bookings test bookings\n";
    
    // Delete test reviews
    $stmt = $db->prepare("DELETE FROM reviews WHERE user_id IN (SELECT user_id FROM users WHERE email LIKE '%@example.com')");
    $stmt->execute();
    $reviews = $stmt->rowCount();
    echo "✓ Deleted $reviews test reviews\n";
    
    // Delete test listings
    $stmt = $db->prepare("DELETE FROM listings WHERE provider_id IN (SELECT provider_id FROM providers WHERE email LIKE '%@example.com')");
    $stmt->execute();
    $listings = $stmt->rowCount();
    echo "✓ Deleted $listings test listings\n";
    
    // Delete test notifications
    $stmt = $db->prepare("DELETE FROM notifications WHERE (user_type = 'user' AND user_id IN (SELECT user_id FROM users WHERE email LIKE '%@example.com')) OR (user_type = 'provider' AND user_id IN (SELECT provider_id FROM providers WHERE email LIKE '%@example.com'))");
    $stmt->execute();
    $notifications = $stmt->rowCount();
    echo "✓ Deleted $notifications test notifications\n";
    
    // Delete test activity logs
    $stmt = $db->prepare("DELETE FROM activity_logs WHERE (user_type = 'user' AND user_id IN (SELECT user_id FROM users WHERE email LIKE '%@example.com')) OR (user_type = 'provider' AND user_id IN (SELECT provider_id FROM providers WHERE email LIKE '%@example.com'))");
    $stmt->execute();
    $logs = $stmt->rowCount();
    echo "✓ Deleted $logs test activity logs\n";
    
    // Delete test users
    $stmt = $db->prepare("DELETE FROM users WHERE email LIKE '%@example.com'");
    $stmt->execute();
    $users = $stmt->rowCount();
    echo "✓ Deleted $users test users\n";
    
    // Delete test providers
    $stmt = $db->prepare("DELETE FROM providers WHERE email LIKE '%@example.com'");
    $stmt->execute();
    $providers = $stmt->rowCount();
    echo "✓ Deleted $providers test providers\n";
    
    $db->commit();
    
    echo "\n✅ SUCCESS: All test data cleaned!\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== END CLEANUP ===\n\n";

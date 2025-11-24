# TripEase Unit Testing Guide

## 📋 Table of Contents

1. [Overview](#overview)
2. [Test Structure](#test-structure)
3. [Installation](#installation)
4. [Running Tests](#running-tests)
5. [Test Coverage](#test-coverage)
6. [Writing Tests](#writing-tests)
7. [Best Practices](#best-practices)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

This testing suite provides comprehensive unit and integration tests for the TripEase booking system. It uses PHPUnit as the testing framework and includes tests for:

- **Database Operations** - Connection, Query Builder, CRUD operations
- **Authentication** - Registration, login, password management
- **Booking Logic** - Creation, validation, conflict detection
- **Search Functionality** - Filters, sorting, pagination
- **Helper Functions** - Utility functions and helpers

### Key Features

✅ Isolated test environment using transactions (no database changes)  
✅ Uses existing `tripease` database for testing  
✅ Comprehensive test coverage for core functionality  
✅ Professional PHPUnit configuration  
✅ Automated test data creation  
✅ Easy-to-extend test structure

---

## 📁 Test Structure

```
tests/
├── bootstrap.php              # Test initialization and BaseTestCase class
├── Unit/                      # Unit tests for individual components
│   ├── DatabaseTest.php       # Database and QueryBuilder tests
│   ├── AuthTest.php          # Authentication tests
│   └── HelperFunctionsTest.php # Helper function tests
├── Integration/               # Integration tests for workflows
│   ├── BookingTest.php       # Booking logic tests
│   └── SearchTest.php        # Search functionality tests
├── Fixtures/                  # Test data and fixtures
│   └── test_data.php         # Sample test data
├── coverage/                  # Code coverage reports (generated)
└── results/                   # Test results (generated)

phpunit.xml                    # PHPUnit configuration
composer.json                  # Dependencies and scripts
```

---

## 🚀 Installation

### Step 1: Install Composer (if not installed)

Download and install Composer from: https://getcomposer.org/

For Windows (XAMPP), download the installer or use:

```powershell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

### Step 2: Install PHPUnit via Composer

Navigate to your TripEase directory:

```powershell
cd C:\xampp\htdocs\TripEase
```

Install dependencies:

```powershell
composer install
```

This will install PHPUnit and create the `vendor/` directory.

### Step 3: Verify Installation

```powershell
vendor\bin\phpunit --version
```

You should see PHPUnit version information (9.5 or higher).

---

## 🧪 Running Tests

### Run All Tests

```powershell
vendor\bin\phpunit
```

### Run Only Unit Tests

```powershell
vendor\bin\phpunit --testsuite "Unit Tests"
```

### Run Only Integration Tests

```powershell
vendor\bin\phpunit --testsuite "Integration Tests"
```

### Run Specific Test File

```powershell
vendor\bin\phpunit tests/Unit/DatabaseTest.php
```

### Run Specific Test Method

```powershell
vendor\bin\phpunit --filter testUserRegistrationSuccess
```

### Run Tests with Verbose Output

```powershell
vendor\bin\phpunit --verbose
```

### Run Tests with Colors (Better Readability)

```powershell
vendor\bin\phpunit --colors=always
```

---

## 📊 Test Coverage

### Generate HTML Coverage Report

```powershell
vendor\bin\phpunit --coverage-html tests/coverage
```

Then open `tests/coverage/index.html` in your browser to view the detailed coverage report.

### Generate Text Coverage Report

```powershell
vendor\bin\phpunit --coverage-text
```

### Coverage Requirements

- Enable Xdebug or PCOV extension for code coverage
- For XAMPP, enable xdebug in php.ini:
  ```ini
  zend_extension = "C:\xampp\php\ext\php_xdebug.dll"
  ```

---

## ✍️ Writing Tests

### Creating a New Test Class

```php
<?php
require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;

class MyFeatureTest extends BaseTestCase
{
    /**
     * Test description
     */
    public function testMyFeature()
    {
        $this->beginTransaction();

        // Arrange: Set up test data
        $user = $this->createTestUser();

        // Act: Execute the code being tested
        $result = someFunction($user['user_id']);

        // Assert: Verify the results
        $this->assertTrue($result['success']);
        $this->assertEquals('Expected', $result['message']);

        $this->rollbackTransaction();
    }
}
```

### Available Helper Methods (from BaseTestCase)

```php
// Transaction management (prevents DB changes)
$this->beginTransaction();
$this->rollbackTransaction();

// Test data creation
$user = $this->createTestUser(['name' => 'Custom Name']);
$provider = $this->createTestProvider();
$listing = $this->createTestListing($providerId);
$booking = $this->createTestBooking($userId, $listingId, $providerId);

// Cleanup
$this->cleanupTestData();

// Assertions
$this->assertArrayHasKeys(['key1', 'key2'], $array);
```

### Common Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);
$this->assertSame($expected, $actual); // Strict comparison

// Boolean
$this->assertTrue($condition);
$this->assertFalse($condition);

// Null
$this->assertNull($value);
$this->assertNotNull($value);

// Arrays
$this->assertArrayHasKey('key', $array);
$this->assertCount(5, $array);
$this->assertEmpty($array);
$this->assertNotEmpty($array);

// Strings
$this->assertStringContainsString('substring', $string);
$this->assertStringStartsWith('prefix', $string);

// Numbers
$this->assertGreaterThan(5, $value);
$this->assertLessThan(10, $value);
$this->assertGreaterThanOrEqual(5, $value);
```

---

## 🎯 Best Practices

### 1. Use Transactions

Always wrap tests in transactions to prevent database changes:

```php
public function testSomething()
{
    $this->beginTransaction();
    // Test code
    $this->rollbackTransaction();
}
```

### 2. Test Naming Convention

- Use descriptive test method names starting with `test`
- Format: `test[FeatureName][Condition][ExpectedResult]`
- Examples:
  - `testUserRegistrationSuccess()`
  - `testUserLoginInvalidPassword()`
  - `testBookingWithPastStartDate()`

### 3. Follow AAA Pattern

- **Arrange**: Set up test data and preconditions
- **Act**: Execute the code being tested
- **Assert**: Verify the results

### 4. One Assertion Focus Per Test

Each test should verify one specific behavior or outcome.

### 5. Use Test Data Fixtures

For complex test data, use the fixtures in `tests/Fixtures/test_data.php`.

### 6. Clean Up After Tests

The `tearDown()` method in BaseTestCase handles cleanup, but you can add custom cleanup if needed.

### 7. Test Edge Cases

- Empty inputs
- Null values
- Invalid data
- Boundary conditions
- Error scenarios

---

## 🔧 Troubleshooting

### Issue: "Class 'PHPUnit\Framework\TestCase' not found"

**Solution:** Run `composer install` to install PHPUnit.

### Issue: "Database connection failed"

**Solution:**

1. Ensure XAMPP MySQL is running
2. Verify database credentials in `phpunit.xml`
3. Ensure `tripease` database exists and has schema loaded

### Issue: "Cannot modify header information"

**Solution:** This is normal during testing. PHPUnit captures output.

### Issue: Tests are affecting the database

**Solution:**

- Ensure you're using `beginTransaction()` and `rollbackTransaction()`
- Check that transactions are properly closed
- Verify no commits are happening in test code

### Issue: "No tests executed"

**Solution:**

- Verify test file names end with `Test.php`
- Ensure test methods start with `test`
- Check that test class extends `BaseTestCase`

### Issue: Code coverage not working

**Solution:**

- Install and enable Xdebug or PCOV extension
- Check PHP version compatibility
- Verify extension in `php -m` output

### Issue: Slow test execution

**Solution:**

- Run specific test suites instead of all tests
- Optimize database queries
- Use `--stop-on-failure` flag for faster feedback
- Consider test parallelization for large test suites

---

## 📈 Test Metrics

### Current Test Coverage

| Component            | Tests        | Status       |
| -------------------- | ------------ | ------------ |
| Database Operations  | 15 tests     | ✅ Complete  |
| Authentication       | 14 tests     | ✅ Complete  |
| Booking Logic        | 12 tests     | ✅ Complete  |
| Search Functionality | 11 tests     | ✅ Complete  |
| Helper Functions     | 18 tests     | ✅ Complete  |
| **Total**            | **70 tests** | **✅ Ready** |

---

## 🎓 Test Examples

### Example 1: Testing User Registration

```php
public function testUserRegistrationSuccess()
{
    $this->beginTransaction();

    $result = Auth::registerUser(
        'John Doe',
        'john@example.com',
        'SecurePass123',
        '01712345678'
    );

    $this->assertTrue($result['success']);
    $this->assertArrayHasKey('user_id', $result);

    $this->rollbackTransaction();
}
```

### Example 2: Testing Database Queries

```php
public function testQueryBuilderWhere()
{
    $this->beginTransaction();

    $user = $this->createTestUser(['email' => 'test@example.com']);

    $result = db('users')
        ->where('email', 'test@example.com')
        ->first();

    $this->assertNotNull($result);
    $this->assertEquals('test@example.com', $result['email']);

    $this->rollbackTransaction();
}
```

### Example 3: Testing Business Logic

```php
public function testBookingDateConflictDetection()
{
    $this->beginTransaction();

    $user = $this->createTestUser();
    $provider = $this->createTestProvider();
    $listing = $this->createTestListing($provider['provider_id']);

    // Create existing booking
    $this->createTestBooking($user['user_id'], $listing['listing_id'],
        $provider['provider_id'], [
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-05',
            'status' => 'confirmed'
        ]);

    // Check for conflicts
    $conflicts = db('bookings')->raw(
        "SELECT COUNT(*) as count FROM bookings
         WHERE listing_id = ? AND status = 'confirmed'
         AND start_date <= ? AND end_date >= ?",
        [$listing['listing_id'], '2024-03-03', '2024-03-03']
    );

    $this->assertGreaterThan(0, $conflicts[0]['count']);

    $this->rollbackTransaction();
}
```

---

## 📞 Support

For questions or issues with testing:

1. Check this documentation
2. Review existing test files for examples
3. Consult PHPUnit documentation: https://phpunit.de/documentation.html

---

## ✅ Quick Reference Commands

```powershell
# Install dependencies
composer install

# Run all tests
vendor\bin\phpunit

# Run with coverage
vendor\bin\phpunit --coverage-html tests/coverage

# Run specific suite
vendor\bin\phpunit --testsuite "Unit Tests"

# Run with filter
vendor\bin\phpunit --filter testUserLogin

# Run verbose
vendor\bin\phpunit --verbose --colors=always
```

---

**Last Updated:** November 2024  
**PHPUnit Version:** 9.5+  
**PHP Version:** 7.4+

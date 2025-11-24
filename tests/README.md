# TripEase Testing Suite

## Overview

Professional unit and integration testing suite for the TripEase booking system using PHPUnit 9.5+.

## Quick Start

```powershell
# Install dependencies
composer install

# Run all tests
vendor\bin\phpunit

# Run with coverage
vendor\bin\phpunit --coverage-html tests/coverage
```

## Test Structure

```
tests/
├── bootstrap.php              # Test initialization
├── Unit/                      # Component tests (47 tests)
│   ├── DatabaseTest.php       # Database & Query Builder
│   ├── AuthTest.php          # Authentication system
│   └── HelperFunctionsTest.php # Helper functions
├── Integration/               # Feature tests (23 tests)
│   ├── BookingTest.php       # Booking workflows
│   └── SearchTest.php        # Search & filtering
└── Fixtures/
    └── test_data.php         # Sample test data
```

## Key Features

✅ **70 comprehensive tests** covering core functionality  
✅ **Transaction-based** - No database pollution  
✅ **BaseTestCase** - Helper methods for easy testing  
✅ **Test fixtures** - Reusable sample data  
✅ **Coverage reports** - HTML & text reports

## Test Coverage

| Component            | Tests  | Status |
| -------------------- | ------ | ------ |
| Database Operations  | 15     | ✅     |
| Authentication       | 14     | ✅     |
| Booking Logic        | 12     | ✅     |
| Search Functionality | 11     | ✅     |
| Helper Functions     | 18     | ✅     |
| **Total**            | **70** | **✅** |

## Running Tests

### All Tests

```powershell
vendor\bin\phpunit
```

### Specific Test Suite

```powershell
vendor\bin\phpunit --testsuite "Unit Tests"
vendor\bin\phpunit --testsuite "Integration Tests"
```

### Specific Test File

```powershell
vendor\bin\phpunit tests/Unit/DatabaseTest.php
vendor\bin\phpunit tests/Unit/AuthTest.php
```

### Specific Test Method

```powershell
vendor\bin\phpunit --filter testUserLogin
```

### With Coverage

```powershell
vendor\bin\phpunit --coverage-html tests/coverage
vendor\bin\phpunit --coverage-text
```

## Writing Tests

### Basic Test Template

```php
<?php
require_once __DIR__ . '/../bootstrap.php';
use PHPUnit\Framework\TestCase;

class MyFeatureTest extends BaseTestCase
{
    public function testSomething()
    {
        $this->beginTransaction();

        // Arrange
        $user = $this->createTestUser();

        // Act
        $result = someFunction($user['user_id']);

        // Assert
        $this->assertTrue($result['success']);

        $this->rollbackTransaction();
    }
}
```

### Helper Methods

```php
// Create test data
$user = $this->createTestUser(['name' => 'John']);
$provider = $this->createTestProvider();
$listing = $this->createTestListing($providerId);
$booking = $this->createTestBooking($userId, $listingId, $providerId);

// Transaction control
$this->beginTransaction();
$this->rollbackTransaction();

// Custom assertions
$this->assertArrayHasKeys(['key1', 'key2'], $array);
```

## Database Safety

All tests use database transactions and automatically rollback changes:

```php
public function testExample()
{
    $this->beginTransaction();  // Start transaction

    // Test code here - any DB changes are temporary

    $this->rollbackTransaction(); // Undo all changes
}
```

**Result:** Your `tripease` database remains unchanged after running tests.

## Common Commands

```powershell
# Run all tests
vendor\bin\phpunit

# Unit tests only
vendor\bin\phpunit --testsuite "Unit Tests"

# Integration tests only
vendor\bin\phpunit --testsuite "Integration Tests"

# Verbose output
vendor\bin\phpunit --verbose

# Pretty colors
vendor\bin\phpunit --colors=always

# Stop on first failure
vendor\bin\phpunit --stop-on-failure

# Test names output
vendor\bin\phpunit --testdox
```

## Assertions Reference

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);
$this->assertSame($expected, $actual); // Strict

// Boolean
$this->assertTrue($value);
$this->assertFalse($value);

// Null
$this->assertNull($value);
$this->assertNotNull($value);

// Arrays
$this->assertArrayHasKey('key', $array);
$this->assertCount(5, $array);
$this->assertEmpty($array);
$this->assertNotEmpty($array);

// Strings
$this->assertStringContainsString('text', $string);
$this->assertStringStartsWith('prefix', $string);

// Numbers
$this->assertGreaterThan(5, $value);
$this->assertLessThan(10, $value);
```

## Test Results Symbols

- `.` = Passed test ✅
- `F` = Failed test ❌
- `E` = Error (exception) ⚠️
- `S` = Skipped test ⏭️
- `I` = Incomplete test 🚧

## Documentation

- **TESTING_SETUP_STEPS.md** - Complete setup guide
- **TESTING_GUIDE.md** - Comprehensive documentation
- **TESTING_QUICK_REFERENCE.md** - Command reference
- **TESTING_IMPLEMENTATION_SUMMARY.md** - Project summary

## Requirements

- PHP 7.4+
- PHPUnit 9.5+
- Composer
- MySQL (XAMPP)
- Xdebug (for coverage reports)

## Troubleshooting

### PHPUnit not found

```powershell
composer install
```

### Database connection error

- Check MySQL is running in XAMPP
- Verify credentials in `config/config.php`

### Tests affecting database

- Ensure `beginTransaction()` and `rollbackTransaction()` are used
- Check BaseTestCase is extended

### No coverage

- Enable Xdebug in `php.ini`
- Restart Apache

## Contributing

When adding new features:

1. Write tests first (TDD)
2. Ensure tests pass
3. Check coverage is maintained
4. Update documentation if needed

## License

Part of the TripEase project.

---

**For detailed instructions, see TESTING_SETUP_STEPS.md**

**Happy Testing! 🚀**

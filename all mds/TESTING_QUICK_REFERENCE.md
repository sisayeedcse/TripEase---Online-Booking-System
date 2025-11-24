# TripEase Testing - Quick Reference Card

## 📦 One-Time Setup

```powershell
# 1. Install Composer (if needed)
# Download: https://getcomposer.org/Composer-Setup.exe

# 2. Install PHPUnit
cd C:\xampp\htdocs\TripEase
composer install

# 3. Verify installation
vendor\bin\phpunit --version
```

---

## 🚀 Running Tests

### Most Common Commands

```powershell
# Run all tests
vendor\bin\phpunit

# Run all tests with pretty output
vendor\bin\phpunit --colors=always

# Run only unit tests (fast)
vendor\bin\phpunit --testsuite "Unit Tests"

# Run only integration tests
vendor\bin\phpunit --testsuite "Integration Tests"
```

### Running Specific Tests

```powershell
# Specific test file
vendor\bin\phpunit tests/Unit/DatabaseTest.php
vendor\bin\phpunit tests/Unit/AuthTest.php
vendor\bin\phpunit tests/Integration/BookingTest.php

# Specific test method
vendor\bin\phpunit --filter testUserLogin
vendor\bin\phpunit --filter testCreateBooking
```

### Test Output Options

```powershell
# Verbose output
vendor\bin\phpunit --verbose

# Stop on first failure
vendor\bin\phpunit --stop-on-failure

# Test names and results
vendor\bin\phpunit --testdox
```

---

## 📊 Code Coverage

```powershell
# HTML report (open tests/coverage/index.html after)
vendor\bin\phpunit --coverage-html tests/coverage

# Text report (in terminal)
vendor\bin\phpunit --coverage-text

# Quick coverage summary
vendor\bin\phpunit --coverage-text --colors=always
```

**Note:** Requires Xdebug extension enabled in `php.ini`

---

## 📁 Test File Structure

```
tests/
├── bootstrap.php              # Test setup (auto-loaded)
├── Unit/                      # Component tests
│   ├── DatabaseTest.php       # 15 tests - DB & Query Builder
│   ├── AuthTest.php          # 14 tests - Login/Registration
│   └── HelperFunctionsTest.php # 18 tests - Utility functions
├── Integration/               # Feature tests
│   ├── BookingTest.php       # 12 tests - Booking workflow
│   └── SearchTest.php        # 11 tests - Search & filters
└── Fixtures/
    └── test_data.php         # Sample data

Total: 70 tests, 150+ assertions
```

---

## ✍️ Writing New Tests

### Basic Test Template

```php
<?php
require_once __DIR__ . '/../bootstrap.php';
use PHPUnit\Framework\TestCase;

class MyTest extends BaseTestCase
{
    public function testMyFeature()
    {
        $this->beginTransaction();

        // Arrange - Set up test data
        $user = $this->createTestUser();

        // Act - Execute the code
        $result = myFunction($user['user_id']);

        // Assert - Check results
        $this->assertTrue($result['success']);

        $this->rollbackTransaction();
    }
}
```

### Helper Methods Available

```php
// Create test data
$user = $this->createTestUser(['name' => 'John']);
$provider = $this->createTestProvider();
$listing = $this->createTestListing($providerId);
$booking = $this->createTestBooking($userId, $listingId, $providerId);

// Transaction control (prevents DB changes!)
$this->beginTransaction();
$this->rollbackTransaction();

// Custom assertions
$this->assertArrayHasKeys(['key1', 'key2'], $array);
```

---

## ✅ Common Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);

// Boolean
$this->assertTrue($value);
$this->assertFalse($value);

// Null checks
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

---

## 🔧 Troubleshooting

### Common Issues

| Issue                     | Solution                                             |
| ------------------------- | ---------------------------------------------------- |
| PHPUnit not found         | Run `composer install`                               |
| Database connection error | Check MySQL is running in XAMPP                      |
| Class not found           | Verify `bootstrap.php` is loaded                     |
| Tests affecting DB        | Use `beginTransaction()` and `rollbackTransaction()` |
| No coverage               | Enable Xdebug in `php.ini`                           |

### Quick Fixes

```powershell
# Reinstall dependencies
composer install

# Clear Composer cache
composer clear-cache

# Verify PHP version
php -v

# Check database connection
php -r "new PDO('mysql:host=localhost;dbname=tripease', 'root', '');"
```

---

## 📈 Test Results Interpretation

```
..F..S..E..                          11 / 70 (15%)
```

**Symbols:**

- `.` = Passed test ✅
- `F` = Failed test (assertion failed) ❌
- `E` = Error (exception thrown) ⚠️
- `S` = Skipped test ⏭️
- `I` = Incomplete test 🚧

**Final Output:**

```
OK (70 tests, 150 assertions)          ← All passed! ✅
FAILURES! Tests: 70, Assertions: 150, Failures: 2    ← Some failed ❌
```

---

## 🎯 Best Practices

✅ **DO:**

- Run tests before committing code
- Write tests for new features
- Use transactions in tests
- Test edge cases and errors
- Keep tests independent
- Use descriptive test names

❌ **DON'T:**

- Skip `beginTransaction()`/`rollbackTransaction()`
- Write tests that depend on others
- Test implementation details
- Leave failing tests
- Commit without running tests

---

## 📚 Documentation Files

- **TESTING_SETUP_STEPS.md** - Complete step-by-step setup guide
- **TESTING_GUIDE.md** - Comprehensive testing documentation
- **phpunit.xml** - PHPUnit configuration
- **composer.json** - Dependencies and scripts

---

## 🔗 Quick Links

- **PHPUnit Docs:** https://phpunit.de/documentation.html
- **Composer:** https://getcomposer.org/
- **Project Path:** `C:\xampp\htdocs\TripEase`
- **Coverage Report:** `tests/coverage/index.html`

---

## 💡 Pro Tips

1. **Run tests frequently** - After each change
2. **Use `--filter`** - For faster feedback
3. **Check coverage** - Aim for >80%
4. **Add tests for bugs** - Write test, then fix
5. **Keep tests fast** - Unit tests should be <1s each

---

## 📞 Need Help?

1. Check **TESTING_GUIDE.md** for detailed info
2. Review existing test files for examples
3. Verify XAMPP services are running
4. Check database exists and has schema

---

**Test Often, Test Early, Test Everything! 🚀**

_Last updated: November 2024_

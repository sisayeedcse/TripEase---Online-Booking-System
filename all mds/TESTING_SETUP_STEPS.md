# TripEase Testing - Step-by-Step Setup Guide

## 🚀 Complete Setup Instructions

Follow these steps carefully to set up and run the testing suite for TripEase.

---

## ✅ Prerequisites Checklist

Before starting, ensure you have:

- ✅ XAMPP installed and running
- ✅ MySQL database running
- ✅ TripEase database (`tripease`) created and populated
- ✅ PHP version 7.4 or higher
- ✅ Internet connection (for Composer installation)

---

## 📝 Step 1: Verify Database Setup

### 1.1 Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** (if testing web pages)
3. Start **MySQL**
4. Ensure both services are running (green indicators)

### 1.2 Verify Database Exists

1. Open browser: `http://localhost/phpmyadmin`
2. Check if `tripease` database exists
3. Verify tables are present:
   - users
   - providers
   - listings
   - bookings
   - reviews
   - admins
   - notifications
   - activity_logs

### 1.3 Test Database Connection

```powershell
# Open PowerShell in TripEase directory
cd C:\xampp\htdocs\TripEase

# Test PHP and database
php -r "echo 'PHP Version: ' . phpversion() . PHP_EOL;"
```

**Expected Output:** PHP version number (e.g., PHP Version: 8.0.28)

---

## 📦 Step 2: Install Composer

### 2.1 Check if Composer is Installed

```powershell
composer --version
```

**If Composer is installed:** You'll see version info → Skip to Step 3  
**If NOT installed:** Continue with installation below

### 2.2 Install Composer (Windows)

**Option A: Using Installer (Recommended)**

1. Download: https://getcomposer.org/Composer-Setup.exe
2. Run the installer
3. Follow installation wizard
4. Select PHP executable: `C:\xampp\php\php.exe`
5. Complete installation
6. Restart PowerShell/Command Prompt

**Option B: Manual Installation**

```powershell
# Download Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verify installer
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

# Install Composer
php composer-setup.php

# Remove installer
php -r "unlink('composer-setup.php');"

# Move to global location (optional)
move composer.phar C:\xampp\php\composer.phar

# Create batch file for easy access
echo @php "C:\xampp\php\composer.phar" %* > C:\xampp\php\composer.bat
```

### 2.3 Verify Composer Installation

```powershell
composer --version
```

**Expected Output:**

```
Composer version 2.x.x
```

---

## 🔧 Step 3: Install PHPUnit and Dependencies

### 3.1 Navigate to Project Directory

```powershell
cd C:\xampp\htdocs\TripEase
```

### 3.2 Install Dependencies

```powershell
composer install
```

**What this does:**

- Downloads PHPUnit (version 9.5)
- Creates `vendor/` directory
- Sets up autoloading
- Installs testing dependencies

**Expected Output:**

```
Loading composer repositories with package information
Installing dependencies (including require-dev) from lock file
  - Installing phpunit/phpunit (9.5.x)
...
Generating autoload files
```

### 3.3 Verify PHPUnit Installation

```powershell
vendor\bin\phpunit --version
```

**Expected Output:**

```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.
```

---

## 🧪 Step 4: Run Your First Test

### 4.1 Run All Tests

```powershell
vendor\bin\phpunit
```

**What happens:**

- PHPUnit reads configuration from `phpunit.xml`
- Loads test bootstrap (`tests/bootstrap.php`)
- Discovers all test files
- Runs tests in transactions (no DB changes)
- Displays results

**Expected Output:**

```
✓ Test Bootstrap Loaded Successfully
Database: tripease
Test Mode: Enabled

PHPUnit 9.5.x by Sebastian Bergmann and contributors.

Runtime: PHP 8.0.28
Configuration: C:\xampp\htdocs\TripEase\phpunit.xml

.................................................................  70 / 70 (100%)

Time: 00:02.345, Memory: 10.00 MB

OK (70 tests, 150 assertions)
```

### 4.2 Understanding Test Results

**Green Dots (.)**: Passed tests ✅  
**Red F**: Failed test ❌  
**Yellow S**: Skipped test ⚠️  
**Yellow I**: Incomplete test ⚠️

---

## 📊 Step 5: Run Specific Test Suites

### 5.1 Run Unit Tests Only

```powershell
vendor\bin\phpunit --testsuite "Unit Tests"
```

**Tests included:**

- DatabaseTest.php (15 tests)
- AuthTest.php (14 tests)
- HelperFunctionsTest.php (18 tests)

### 5.2 Run Integration Tests Only

```powershell
vendor\bin\phpunit --testsuite "Integration Tests"
```

**Tests included:**

- BookingTest.php (12 tests)
- SearchTest.php (11 tests)

### 5.3 Run Specific Test File

```powershell
vendor\bin\phpunit tests/Unit/DatabaseTest.php
```

### 5.4 Run Specific Test Method

```powershell
vendor\bin\phpunit --filter testUserRegistrationSuccess
```

### 5.5 Run with Verbose Output

```powershell
vendor\bin\phpunit --verbose --colors=always
```

---

## 📈 Step 6: Generate Code Coverage Report

### 6.1 Enable Xdebug (Required for Coverage)

**Check if Xdebug is installed:**

```powershell
php -m | Select-String xdebug
```

**If Xdebug is NOT installed:**

1. Open `php.ini` file:

   - Location: `C:\xampp\php\php.ini`

2. Find and uncomment (remove semicolon):

   ```ini
   zend_extension = "C:\xampp\php\ext\php_xdebug.dll"
   ```

3. Add Xdebug configuration:

   ```ini
   [XDebug]
   xdebug.mode = coverage
   xdebug.start_with_request = yes
   ```

4. Restart Apache in XAMPP

5. Verify installation:
   ```powershell
   php -v
   ```
   Should show "with Xdebug"

### 6.2 Generate HTML Coverage Report

```powershell
vendor\bin\phpunit --coverage-html tests/coverage
```

**Output:**

- Creates `tests/coverage/` directory
- Generates HTML report with detailed coverage metrics

### 6.3 View Coverage Report

1. Open file browser
2. Navigate to: `C:\xampp\htdocs\TripEase\tests\coverage\`
3. Open `index.html` in web browser

**What you'll see:**

- Overall coverage percentage
- Coverage by file/class/method
- Line-by-line coverage
- Uncovered code highlighted

### 6.4 Generate Text Coverage Report

```powershell
vendor\bin\phpunit --coverage-text
```

**Output:**

```
Code Coverage Report:
  2024-11-24 10:00:00

 Summary:
  Classes: 80.00% (4/5)
  Methods: 85.71% (18/21)
  Lines:   90.50% (181/200)
```

---

## 🎯 Step 7: Verify Everything Works

### 7.1 Complete Test Run

```powershell
# Full test suite with colors
vendor\bin\phpunit --colors=always

# Should show: OK (70 tests, 150+ assertions)
```

### 7.2 Check Database (No Changes)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `tripease` database
3. Check any table (e.g., `users`)
4. Verify no test records exist
   - No emails with `@example.com`
   - No booking references starting with `TEST`

**Why?** All tests use transactions and rollback changes.

### 7.3 Test Individual Components

**Test Database:**

```powershell
vendor\bin\phpunit tests/Unit/DatabaseTest.php
```

**Test Authentication:**

```powershell
vendor\bin\phpunit tests/Unit/AuthTest.php
```

**Test Bookings:**

```powershell
vendor\bin\phpunit tests/Integration/BookingTest.php
```

**Test Search:**

```powershell
vendor\bin\phpunit tests/Integration/SearchTest.php
```

**Test Helpers:**

```powershell
vendor\bin\phpunit tests/Unit/HelperFunctionsTest.php
```

---

## 🔍 Step 8: Troubleshooting Common Issues

### Issue 1: "Class 'PHPUnit\Framework\TestCase' not found"

**Solution:**

```powershell
composer install
```

### Issue 2: Database Connection Failed

**Solution:**

1. Check XAMPP MySQL is running
2. Verify database credentials in `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'tripease');
   ```

### Issue 3: Tests Fail with SQL Errors

**Solution:**

1. Ensure database schema is loaded:
   ```sql
   -- Import schema.sql in phpMyAdmin
   ```
2. Verify all required tables exist

### Issue 4: Permission Denied Errors

**Solution:**

```powershell
# Run PowerShell as Administrator
# Or adjust folder permissions
```

### Issue 5: Slow Test Execution

**Solution:**

```powershell
# Run specific test suites instead
vendor\bin\phpunit --testsuite "Unit Tests"
```

---

## 📚 Step 9: Understanding Test Structure

### Test Files Overview

```
tests/
├── bootstrap.php           → Initializes testing environment
├── Unit/                   → Tests for individual components
│   ├── DatabaseTest.php    → 15 tests for DB operations
│   ├── AuthTest.php        → 14 tests for authentication
│   └── HelperFunctionsTest.php → 18 tests for helpers
├── Integration/            → Tests for workflows
│   ├── BookingTest.php     → 12 tests for bookings
│   └── SearchTest.php      → 11 tests for search
└── Fixtures/
    └── test_data.php       → Sample test data
```

### What Each Test Suite Covers

**DatabaseTest.php:**

- Singleton pattern
- PDO connection
- Query Builder methods (select, where, insert, update, delete)
- Joins, limits, ordering
- Transactions

**AuthTest.php:**

- User registration
- Provider registration
- Login (user, provider, admin)
- Password reset
- Password change
- Logout

**BookingTest.php:**

- Booking creation
- Date validation
- Conflict detection
- Price calculation
- Status transitions
- Cancellation

**SearchTest.php:**

- Location filtering
- Category filtering
- Price range filtering
- Sorting (price, rating, date)
- Pagination
- Joined queries

**HelperFunctionsTest.php:**

- URL helpers
- Sanitization
- Formatting (price, date, time)
- Token generation
- Flash messages
- Session helpers
- Constants

---

## 🎓 Step 10: Running Tests Regularly

### Daily Testing Workflow

**Before starting development:**

```powershell
vendor\bin\phpunit --testsuite "Unit Tests"
```

**After making changes:**

```powershell
# Run affected test file
vendor\bin\phpunit tests/Unit/AuthTest.php

# Or run all tests
vendor\bin\phpunit
```

**Before committing code:**

```powershell
# Full test run with coverage
vendor\bin\phpunit --coverage-text
```

### Continuous Testing Tips

1. **Run tests frequently** - After each significant change
2. **Focus on affected areas** - Use `--filter` for specific tests
3. **Monitor coverage** - Aim for >80% code coverage
4. **Fix failures immediately** - Don't accumulate failing tests
5. **Add tests for bugs** - Write test first, then fix

---

## 📋 Quick Command Reference

```powershell
# Installation
composer install

# Run all tests
vendor\bin\phpunit

# Run specific suite
vendor\bin\phpunit --testsuite "Unit Tests"
vendor\bin\phpunit --testsuite "Integration Tests"

# Run specific file
vendor\bin\phpunit tests/Unit/DatabaseTest.php

# Run specific test
vendor\bin\phpunit --filter testUserLogin

# Verbose output
vendor\bin\phpunit --verbose

# With colors
vendor\bin\phpunit --colors=always

# Coverage report
vendor\bin\phpunit --coverage-html tests/coverage
vendor\bin\phpunit --coverage-text

# Stop on first failure
vendor\bin\phpunit --stop-on-failure

# Show test output
vendor\bin\phpunit --testdox
```

---

## ✅ Success Checklist

After completing this guide, you should be able to:

- ✅ Install Composer and PHPUnit
- ✅ Run the complete test suite
- ✅ Run specific test suites and tests
- ✅ Generate code coverage reports
- ✅ Understand test results
- ✅ Troubleshoot common issues
- ✅ Write new tests
- ✅ Integrate testing into development workflow

---

## 🎉 Congratulations!

You've successfully set up professional unit testing for TripEase!

**Next Steps:**

1. Run the full test suite: `vendor\bin\phpunit`
2. Review the TESTING_GUIDE.md for detailed documentation
3. Explore individual test files to understand patterns
4. Write tests for new features you develop
5. Maintain >80% code coverage

**Questions or Issues?**

- Review TESTING_GUIDE.md
- Check existing test files for examples
- Consult PHPUnit documentation

---

**Happy Testing! 🚀**

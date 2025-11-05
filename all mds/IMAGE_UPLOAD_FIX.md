# 🔧 Image Upload & Profile Edit - Complete Fix

## 🎯 ISSUES IDENTIFIED

### **Issue 1: Images Not Uploading/Showing**
**Problem:**
- Images upload but don't display
- Path configuration issue
- Database stores filename only (correct)
- Display needs proper URL construction

### **Issue 2: Profile Not Editable**
**Problem:**
- Form might not be submitting correctly
- Need to verify form action
- Check if data is saving to database

---

## ✅ HOW IMAGE STORAGE WORKS

### **Database Storage (Correct):**
```sql
-- Users table
profile_image: "user_abc123_1699123456.jpg"  (filename only)

-- Providers table  
logo: "provider_def456_1699123457.jpg"  (filename only)

-- Listings table
main_image: "listing_ghi789_1699123458.jpg"  (filename only)
images: "gallery_1.jpg,gallery_2.jpg"  (comma-separated filenames)
```

### **File System Storage:**
```
uploads/
├── users/
│   └── user_abc123_1699123456.jpg
├── providers/
│   └── provider_def456_1699123457.jpg
└── listings/
    ├── listing_ghi789_1699123458.jpg
    └── gallery_1.jpg
```

### **Display in HTML:**
```php
// Correct way
<img src="<?php echo uploads_url('users/' . $user['profile_image']); ?>">
// Outputs: http://localhost/TripEase/uploads/users/user_abc123.jpg

// With fallback
<img src="<?php echo uploads_url('users/' . ($user['profile_image'] ?? 'default-avatar.png')); ?>">
```

---

## 🔧 SOLUTION

### **Step 1: Verify Upload Directories Exist**

Create these folders if missing:
```
TripEase/
└── uploads/
    ├── users/
    ├── providers/
    └── listings/
```

**Windows Command:**
```cmd
cd C:\xampp\htdocs\TripEase
mkdir uploads\users uploads\providers uploads\listings
```

**Or manually:** Create the folders in File Explorer

---

### **Step 2: Create Default Images**

You need these default images in the uploads folders:

1. **`uploads/users/default-avatar.png`**
2. **`uploads/providers/default-provider.png`**
3. **`uploads/listings/default-listing.jpg`**

**Quick Fix:** Copy any image and rename it, or download placeholder images.

---

### **Step 3: Verify uploads_url() Function**

Check `config/config.php` has this function:
```php
function uploads_url($path = '') {
    return UPLOADS_URL . $path;
}
```

Where `UPLOADS_URL` is defined as:
```php
define('UPLOADS_URL', BASE_URL . 'uploads/');
// Result: http://localhost/TripEase/uploads/
```

---

### **Step 4: Test Image Upload**

**Test Page:** `test-upload.php` (already created)
```
http://localhost/TripEase/test-upload.php
```

This will show:
- ✅ If upload directory exists
- ✅ If directory is writable
- ✅ Upload settings
- ✅ Test upload functionality

---

## 🔧 FIX PROFILE EDITING

### **Issue: Profile Not Saving**

**Check 1: Form Has Correct Action**
```php
<form method="POST" enctype="multipart/form-data">
    <!-- Form fields -->
    <button type="submit" name="update_profile">Save Changes</button>
</form>
```

**Check 2: POST Data is Being Received**
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // This should trigger
    $name = sanitize_input($_POST['name'] ?? '');
    // ... rest of code
}
```

**Check 3: Database Update Works**
```php
$updated = db('users')->where('user_id', $userId)->update([
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'profile_image' => $profileImage
]);

if ($updated) {
    $success = 'Profile updated successfully';
}
```

---

## 🧪 TESTING PROCEDURE

### **Test 1: User Profile**
```
1. Go to: http://localhost/TripEase/user/profile.php
2. Change name to "Test User"
3. Change email to "test@example.com"
4. Upload a profile image
5. Click "Save Changes"
6. Check if:
   - Success message appears
   - Name changed in header
   - Image displays
   - Database updated
```

### **Test 2: Provider Profile**
```
1. Go to: http://localhost/TripEase/provider/profile.php
2. Change business name
3. Upload logo
4. Click "Save Changes"
5. Check if:
   - Success message appears
   - Logo displays in sidebar
   - Database updated
```

### **Test 3: Listing Images**
```
1. Go to: http://localhost/TripEase/provider/add-listing.php
2. Fill in listing details
3. Upload main image
4. Upload gallery images
5. Submit form
6. Check if:
   - Listing created
   - Images display in listings page
   - Images display in listing details
```

---

## 🔍 DEBUGGING STEPS

### **Step 1: Check PHP Errors**
Add to top of profile.php:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### **Step 2: Check Upload Directory Permissions**
```cmd
# Windows: Right-click folder → Properties → Security
# Make sure "Users" has Write permission
```

### **Step 3: Check File Upload Settings**
Check `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
file_uploads = On
```

### **Step 4: Debug Upload Function**
Add debug output:
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    print_r($_POST);
    print_r($_FILES);
    echo '</pre>';
    exit; // Temporary
}
```

---

## 📝 COMMON ISSUES & FIXES

### **Issue: "No file uploaded"**
**Fix:**
- Check form has `enctype="multipart/form-data"`
- Check file input name matches PHP code
- Check file size under limit

### **Issue: "Failed to move uploaded file"**
**Fix:**
- Check upload directory exists
- Check directory has write permissions
- Check path is correct

### **Issue: "Image not displaying"**
**Fix:**
- Check filename stored in database
- Check file exists in uploads folder
- Check uploads_url() function works
- Check BASE_URL is correct

### **Issue: "Profile not saving"**
**Fix:**
- Check form has submit button with name="update_profile"
- Check POST data is being received
- Check database connection
- Check for PHP errors

---

## ✅ VERIFICATION CHECKLIST

### **Upload Directories:**
- [ ] `uploads/` folder exists
- [ ] `uploads/users/` exists
- [ ] `uploads/providers/` exists
- [ ] `uploads/listings/` exists
- [ ] Directories have write permission

### **Default Images:**
- [ ] `default-avatar.png` exists
- [ ] `default-provider.png` exists
- [ ] `default-listing.jpg` exists

### **Configuration:**
- [ ] `BASE_URL` is correct
- [ ] `UPLOADS_URL` is correct
- [ ] `upload_image()` function exists
- [ ] `uploads_url()` function exists

### **Profile Forms:**
- [ ] Form has `method="POST"`
- [ ] Form has `enctype="multipart/form-data"`
- [ ] Submit button has `name` attribute
- [ ] PHP processes POST correctly

### **Database:**
- [ ] Tables exist
- [ ] Columns are correct type
- [ ] Data can be inserted
- [ ] Data can be updated

---

## 🚀 QUICK FIX SCRIPT

Create `fix-uploads.php` in root:
```php
<?php
// Create upload directories
$dirs = [
    'uploads',
    'uploads/users',
    'uploads/providers',
    'uploads/listings'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir<br>";
    } else {
        echo "✓ Exists: $dir<br>";
    }
}

// Check permissions
foreach ($dirs as $dir) {
    if (is_writable($dir)) {
        echo "✅ Writable: $dir<br>";
    } else {
        echo "❌ Not writable: $dir<br>";
    }
}

// Test file creation
$testFile = 'uploads/test.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ Can write files<br>";
    unlink($testFile);
} else {
    echo "❌ Cannot write files<br>";
}
?>
```

Run: `http://localhost/TripEase/fix-uploads.php`

---

## 📞 SUPPORT

If issues persist, check:
1. PHP error log
2. Apache error log
3. Browser console
4. Network tab (for failed requests)

---

**This guide covers all aspects of image upload and profile editing!**

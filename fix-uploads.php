<?php
/**
 * Upload Directory Fix Script
 * Run this to create and verify upload directories
 */

echo "<h1>🔧 TripEase - Upload Directory Fix</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5}h1{color:#333}.success{color:green}.error{color:red}.info{color:blue}</style>";

// Create upload directories
$dirs = [
    'uploads',
    'uploads/users',
    'uploads/providers',
    'uploads/listings'
];

echo "<h2>📁 Creating Directories</h2>";
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✅ Created: <strong>$dir</strong></p>";
        } else {
            echo "<p class='error'>❌ Failed to create: <strong>$dir</strong></p>";
        }
    } else {
        echo "<p class='info'>✓ Already exists: <strong>$dir</strong></p>";
    }
}

// Check permissions
echo "<h2>🔐 Checking Permissions</h2>";
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p class='success'>✅ Writable: <strong>$dir</strong></p>";
        } else {
            echo "<p class='error'>❌ Not writable: <strong>$dir</strong> - Please set permissions to 755</p>";
        }
    }
}

// Test file creation
echo "<h2>📝 Testing File Write</h2>";
$testFile = 'uploads/test.txt';
if (file_put_contents($testFile, 'test content')) {
    echo "<p class='success'>✅ Can write files successfully</p>";
    unlink($testFile);
} else {
    echo "<p class='error'>❌ Cannot write files - Check directory permissions</p>";
}

// Check PHP settings
echo "<h2>⚙️ PHP Upload Settings</h2>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>file_uploads:</strong> " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</p>";
echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";

// Check for default images
echo "<h2>🖼️ Checking Default Images</h2>";
$defaultImages = [
    'uploads/users/default-avatar.png',
    'uploads/providers/default-provider.png',
    'uploads/listings/default-listing.jpg'
];

foreach ($defaultImages as $img) {
    if (file_exists($img)) {
        echo "<p class='success'>✅ Exists: <strong>$img</strong></p>";
    } else {
        echo "<p class='error'>❌ Missing: <strong>$img</strong> - Please add this file</p>";
    }
}

// Test image upload simulation
echo "<h2>🧪 Test Image Upload</h2>";
echo "<form method='POST' enctype='multipart/form-data' style='background:white;padding:20px;border-radius:8px;margin:20px 0'>";
echo "<p><input type='file' name='test_image' accept='image/*'></p>";
echo "<p><button type='submit' name='test_upload' style='background:#007bff;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer'>Test Upload</button></p>";
echo "</form>";

if (isset($_POST['test_upload']) && isset($_FILES['test_image'])) {
    if ($_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['test_image']['tmp_name'];
        $name = 'test_' . time() . '_' . $_FILES['test_image']['name'];
        $destination = 'uploads/users/' . $name;
        
        if (move_uploaded_file($tmpName, $destination)) {
            echo "<p class='success'>✅ <strong>Upload successful!</strong> File saved as: $destination</p>";
            echo "<p><img src='$destination' style='max-width:200px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1)'></p>";
            // Clean up test file
            // unlink($destination);
        } else {
            echo "<p class='error'>❌ <strong>Upload failed!</strong> Could not move file to destination</p>";
        }
    } else {
        echo "<p class='error'>❌ <strong>Upload error!</strong> Error code: " . $_FILES['test_image']['error'] . "</p>";
    }
}

// Summary
echo "<h2>📊 Summary</h2>";
$allGood = true;
foreach ($dirs as $dir) {
    if (!is_dir($dir) || !is_writable($dir)) {
        $allGood = false;
        break;
    }
}

if ($allGood) {
    echo "<p class='success' style='font-size:18px;font-weight:bold'>✅ All checks passed! Upload system is ready.</p>";
    echo "<p><a href='test-upload.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block'>Go to Upload Test Page</a></p>";
} else {
    echo "<p class='error' style='font-size:18px;font-weight:bold'>❌ Some issues found. Please fix the errors above.</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='admin/dashboard.php'>Admin Dashboard</a> | <a href='provider/dashboard.php'>Provider Dashboard</a></p>";
?>

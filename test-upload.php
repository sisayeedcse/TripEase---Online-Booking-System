<?php
require_once 'config/config.php';
require_once 'config/database.php';

$message = '';
$uploadedFiles = [];

// Handle upload test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Test main image upload
    if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
        $result = upload_image($_FILES['test_image'], LISTING_UPLOAD_DIR, 'test_');
        if ($result['success']) {
            $message = '<div class="alert alert-success">✅ SUCCESS! Image uploaded: ' . $result['filename'] . '</div>';
            $uploadedFiles[] = $result['filename'];
        } else {
            $message = '<div class="alert alert-danger">❌ ERROR: ' . $result['message'] . '</div>';
        }
    }
    
    // Test multiple images
    if (isset($_FILES['test_gallery'])) {
        foreach ($_FILES['test_gallery']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['test_gallery']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['test_gallery']['name'][$key],
                    'type' => $_FILES['test_gallery']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['test_gallery']['error'][$key],
                    'size' => $_FILES['test_gallery']['size'][$key]
                ];
                $result = upload_image($file, LISTING_UPLOAD_DIR, 'gallery_test_');
                if ($result['success']) {
                    $uploadedFiles[] = $result['filename'];
                }
            }
        }
        if (!empty($uploadedFiles)) {
            $message .= '<div class="alert alert-success">✅ Gallery images uploaded: ' . count($uploadedFiles) . ' files</div>';
        }
    }
}

// Get existing files
$existingFiles = [];
if (is_dir(LISTING_UPLOAD_DIR)) {
    $files = scandir(LISTING_UPLOAD_DIR);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file(LISTING_UPLOAD_DIR . $file)) {
            $existingFiles[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload Test - TripEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding: 2rem; background: #f8f9fa; }
        .test-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .preview-image { max-width: 200px; max-height: 200px; margin: 10px; border-radius: 8px; }
        .status-item { padding: 0.5rem; margin: 0.5rem 0; border-left: 4px solid #28a745; background: #d4edda; border-radius: 4px; }
        .status-item.error { border-left-color: #dc3545; background: #f8d7da; }
        .file-item { padding: 0.5rem; margin: 0.5rem 0; background: #e9ecef; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h1><i class="fas fa-vial"></i> Image Upload System Test</h1>
            <p class="text-muted">Test the image upload functionality for TripEase listings</p>
            <hr>
            
            <!-- System Status -->
            <h3><i class="fas fa-check-circle"></i> System Status</h3>
            <div class="status-item <?php echo is_dir(LISTING_UPLOAD_DIR) ? '' : 'error'; ?>">
                <strong>Upload Directory:</strong> 
                <?php if (is_dir(LISTING_UPLOAD_DIR)): ?>
                    ✅ EXISTS - <?php echo LISTING_UPLOAD_DIR; ?>
                <?php else: ?>
                    ❌ NOT FOUND - <?php echo LISTING_UPLOAD_DIR; ?>
                <?php endif; ?>
            </div>
            
            <div class="status-item <?php echo is_writable(LISTING_UPLOAD_DIR) ? '' : 'error'; ?>">
                <strong>Directory Writable:</strong> 
                <?php if (is_writable(LISTING_UPLOAD_DIR)): ?>
                    ✅ YES - Can write files
                <?php else: ?>
                    ❌ NO - Check permissions (need 755)
                <?php endif; ?>
            </div>
            
            <div class="status-item">
                <strong>Max Upload Size:</strong> 
                ✅ <?php echo ini_get('upload_max_filesize'); ?> (PHP Setting)
            </div>
            
            <div class="status-item">
                <strong>Allowed Types:</strong> 
                ✅ JPG, PNG, WEBP
            </div>
            
            <div class="status-item">
                <strong>Max File Size:</strong> 
                ✅ <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?> MB
            </div>
        </div>

        <!-- Upload Test Form -->
        <div class="test-card">
            <h3><i class="fas fa-upload"></i> Test Upload</h3>
            
            <?php echo $message; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label"><strong>Test Single Image Upload:</strong></label>
                    <input type="file" class="form-control" name="test_image" accept="image/*" onchange="previewSingle(this)">
                    <div id="singlePreview" class="mt-2"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Test Multiple Images Upload:</strong></label>
                    <input type="file" class="form-control" name="test_gallery[]" accept="image/*" multiple onchange="previewMultiple(this)">
                    <div id="multiplePreview" class="mt-2 d-flex flex-wrap"></div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload"></i> Upload Test Images
                </button>
            </form>
        </div>

        <!-- Uploaded Files -->
        <?php if (!empty($uploadedFiles)): ?>
        <div class="test-card">
            <h3><i class="fas fa-check"></i> Just Uploaded</h3>
            <div class="row">
                <?php foreach ($uploadedFiles as $file): ?>
                <div class="col-md-3">
                    <img src="<?php echo uploads_url('listings/' . $file); ?>" class="img-thumbnail" alt="Uploaded">
                    <p class="small text-muted"><?php echo $file; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Existing Files -->
        <?php if (!empty($existingFiles)): ?>
        <div class="test-card">
            <h3><i class="fas fa-images"></i> Existing Files in Directory</h3>
            <p class="text-muted">Total: <?php echo count($existingFiles); ?> files</p>
            <div class="row">
                <?php foreach (array_slice($existingFiles, 0, 12) as $file): ?>
                <div class="col-md-2">
                    <img src="<?php echo uploads_url('listings/' . $file); ?>" class="img-thumbnail" alt="<?php echo $file; ?>">
                    <p class="small text-muted text-truncate"><?php echo $file; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($existingFiles) > 12): ?>
            <p class="text-muted">... and <?php echo count($existingFiles) - 12; ?> more files</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Instructions -->
        <div class="test-card">
            <h3><i class="fas fa-info-circle"></i> How to Fix Issues</h3>
            
            <h5>If directory doesn't exist:</h5>
            <pre class="bg-light p-3 rounded">mkdir -p uploads/listings
chmod 755 uploads/listings</pre>

            <h5>If not writable:</h5>
            <pre class="bg-light p-3 rounded">chmod 755 uploads/listings</pre>

            <h5>If upload fails:</h5>
            <ul>
                <li>Check file size is under <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?> MB</li>
                <li>Check file type is JPG, PNG, or WEBP</li>
                <li>Check PHP upload_max_filesize setting</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo base_url('provider/add-listing.php'); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Go to Add Listing Page
            </a>
            <a href="<?php echo base_url(); ?>" class="btn btn-outline-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
    function previewSingle(input) {
        const preview = document.getElementById('singlePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMultiple(input) {
        const preview = document.getElementById('multiplePreview');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }
    </script>
</body>
</html>

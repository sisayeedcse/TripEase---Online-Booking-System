<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('login.php'));
}

$providerId = get_user_id(ROLE_PROVIDER);
$provider = db('providers')->where('provider_id', $providerId)->first();
$listingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$listingId) {
    flash_message('error', 'Invalid listing');
    redirect(base_url('provider/listings.php'));
}

// Get listing - verify ownership
$listing = db('listings')
    ->where('listing_id', $listingId)
    ->where('provider_id', $providerId)
    ->first();

if (!$listing) {
    flash_message('error', 'Listing not found');
    redirect(base_url('provider/listings.php'));
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $category = sanitize_input($_POST['category'] ?? '');
    $location = sanitize_input($_POST['location'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $priceUnit = sanitize_input($_POST['price_unit'] ?? 'day');
    $capacity = (int)($_POST['capacity'] ?? 1);
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';
    
    // Validation
    if (empty($title) || empty($description) || empty($category) || empty($location)) {
        $error = 'Please fill in all required fields';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } elseif ($capacity <= 0) {
        $error = 'Capacity must be at least 1';
    } else {
        $mainImage = $listing['main_image'];
        $imagesString = $listing['images'];
        
        // Handle main image update
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $mainImageResult = upload_image($_FILES['main_image'], LISTING_UPLOAD_DIR, 'listing_');
            if ($mainImageResult['success']) {
                // Delete old image
                if ($listing['main_image'] && $listing['main_image'] !== 'default-listing.jpg') {
                    delete_image(LISTING_UPLOAD_DIR . $listing['main_image']);
                }
                $mainImage = $mainImageResult['filename'];
            }
        }
        
        // Handle gallery images update
        if (isset($_FILES['gallery_images'])) {
            $galleryImages = !empty($listing['images']) ? explode(',', $listing['images']) : [];
            
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gallery_images']['name'][$key],
                        'type' => $_FILES['gallery_images']['type'][$key],
                        'tmp_name' => $tmp_name,
                        'error' => $_FILES['gallery_images']['error'][$key],
                        'size' => $_FILES['gallery_images']['size'][$key]
                    ];
                    $result = upload_image($file, LISTING_UPLOAD_DIR, 'gallery_');
                    if ($result['success']) {
                        $galleryImages[] = $result['filename'];
                    }
                }
            }
            
            $imagesString = !empty($galleryImages) ? implode(',', $galleryImages) : '';
        }
        
        // Update listing
        $updated = db('listings')->where('listing_id', $listingId)->update([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'location' => $location,
            'price' => $price,
            'price_unit' => $priceUnit,
            'capacity' => $capacity,
            'main_image' => $mainImage,
            'images' => $imagesString,
            'amenities' => $amenities,
            'approval_status' => 'pending' // Reset to pending after edit
        ]);
        
        if ($updated) {
            // Log activity
            db('activity_logs')->insert([
                'user_type' => 'provider',
                'user_id' => $providerId,
                'action' => 'listing_updated',
                'description' => "Updated listing: {$title}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            
            flash_message('success', 'Listing updated successfully! It will be reviewed again by admin.');
            redirect(base_url('provider/listings.php'));
        } else {
            $error = 'Failed to update listing. Please try again.';
        }
    }
}

// Parse existing amenities
$existingAmenities = !empty($listing['amenities']) ? explode(',', $listing['amenities']) : [];

$pageTitle = 'Edit Listing - Provider';
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-4 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="dashboard-header">
                    <div>
                        <h2><i class="fas fa-edit"></i> Edit Listing</h2>
                        <p class="text-muted">Update your listing information</p>
                    </div>
                    <a href="<?php echo base_url('provider/listings.php'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Listings
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="listingForm">
                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <!-- Basic Information -->
                            <div class="dashboard-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Listing Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" 
                                               value="<?php echo htmlspecialchars($listing['title']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category" required>
                                            <option value="boat" <?php echo $listing['category'] === 'boat' ? 'selected' : ''; ?>>Boat</option>
                                            <option value="room" <?php echo $listing['category'] === 'room' ? 'selected' : ''; ?>>Room</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="location" 
                                               value="<?php echo htmlspecialchars($listing['location']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="6" required><?php echo htmlspecialchars($listing['description']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Capacity -->
                            <div class="dashboard-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-dollar-sign"></i> Pricing & Capacity</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" class="form-control" name="price" 
                                                       value="<?php echo $listing['price']; ?>" min="1" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price Unit <span class="text-danger">*</span></label>
                                            <select class="form-select" name="price_unit" required>
                                                <option value="hour" <?php echo $listing['price_unit'] === 'hour' ? 'selected' : ''; ?>>Per Hour</option>
                                                <option value="day" <?php echo $listing['price_unit'] === 'day' ? 'selected' : ''; ?>>Per Day</option>
                                                <option value="night" <?php echo $listing['price_unit'] === 'night' ? 'selected' : ''; ?>>Per Night</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="capacity" 
                                               value="<?php echo $listing['capacity']; ?>" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="dashboard-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-images"></i> Images</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Current Main Image</label>
                                        <div class="current-image-preview">
                                            <img src="<?php echo uploads_url('listings/' . $listing['main_image']); ?>" 
                                                 alt="Current main image" class="img-thumbnail">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Change Main Image (Optional)</label>
                                        <input type="file" class="form-control" name="main_image" 
                                               accept="image/*" onchange="previewMainImage(this)">
                                        <div id="mainImagePreview" class="mt-3"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Add More Gallery Images (Optional)</label>
                                        <input type="file" class="form-control" name="gallery_images[]" 
                                               accept="image/*" multiple onchange="previewGalleryImages(this)">
                                        <div id="galleryPreview" class="mt-3 row"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h4><i class="fas fa-check-circle"></i> Amenities</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="WiFi" id="wifi"
                                                       <?php echo in_array('WiFi', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="wifi">WiFi</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Air Conditioning" id="ac"
                                                       <?php echo in_array('Air Conditioning', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="ac">Air Conditioning</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Life Jackets" id="lifejackets"
                                                       <?php echo in_array('Life Jackets', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="lifejackets">Life Jackets</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Parking" id="parking"
                                                       <?php echo in_array('Parking', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="parking">Parking</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Food Service" id="food"
                                                       <?php echo in_array('Food Service', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="food">Food Service</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Bathroom" id="bathroom"
                                                       <?php echo in_array('Bathroom', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="bathroom">Bathroom</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Music System" id="music"
                                                       <?php echo in_array('Music System', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="music">Music System</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Pet Friendly" id="pets"
                                                       <?php echo in_array('Pet Friendly', $existingAmenities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="pets">Pet Friendly</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <div class="dashboard-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-save"></i> Update</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Note:</strong> After updating, your listing will be reviewed again by admin.
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <a href="<?php echo base_url('provider/listings.php'); ?>" class="btn btn-outline-secondary w-100 mt-2">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.current-image-preview img {
    max-width: 300px;
    max-height: 200px;
    object-fit: cover;
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: var(--radius-md);
    margin-top: 1rem;
}

.gallery-preview-item {
    margin-bottom: 1rem;
}

.gallery-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
}
</style>

<script>
function previewMainImage(input) {
    const preview = document.getElementById('mainImagePreview');
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

function previewGalleryImages(input) {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-4 gallery-preview-item';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                
                col.appendChild(img);
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>

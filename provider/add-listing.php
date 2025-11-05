<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_PROVIDER)) {
    redirect(base_url('login.php'));
}

$providerId = get_user_id(ROLE_PROVIDER);
$provider = db('providers')->where('provider_id', $providerId)->first();

// Check verification
if ($provider['verification_status'] !== 'verified') {
    flash_message('error', 'Your account must be verified before adding listings');
    redirect(base_url('provider/dashboard.php'));
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
    } elseif (!isset($_FILES['main_image']) || $_FILES['main_image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a main image';
    } else {
        // Upload main image
        $mainImageResult = upload_image($_FILES['main_image'], LISTING_UPLOAD_DIR, 'listing_');
        
        if (!$mainImageResult['success']) {
            $error = $mainImageResult['message'];
        } else {
            $mainImage = $mainImageResult['filename'];
            
            // Upload gallery images
            $galleryImages = [];
            if (isset($_FILES['gallery_images'])) {
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
            }
            
            $imagesString = !empty($galleryImages) ? implode(',', $galleryImages) : '';
            
            // Insert listing
            $listingId = db('listings')->insert([
                'provider_id' => $providerId,
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
                'status' => 'inactive',
                'approval_status' => 'pending'
            ]);
            
            if ($listingId) {
                // Notify admin
                $admins = db('admins')->get();
                foreach ($admins as $admin) {
                    db('notifications')->insert([
                        'user_type' => 'admin',
                        'user_id' => $admin['admin_id'],
                        'title' => 'New Listing Pending Approval',
                        'message' => "{$provider['business_name']} submitted a new listing: {$title}",
                        'type' => 'system',
                        'link' => '/admin/listings.php?filter=pending'
                    ]);
                }
                
                // Log activity
                db('activity_logs')->insert([
                    'user_type' => 'provider',
                    'user_id' => $providerId,
                    'action' => 'listing_created',
                    'description' => "Created listing: {$title}",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
                ]);
                
                flash_message('success', 'Listing created successfully! It will be reviewed by admin before going live.');
                redirect(base_url('provider/listings.php'));
            } else {
                $error = 'Failed to create listing. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add New Listing - Provider';
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
                        <h2><i class="fas fa-plus"></i> Add New Listing</h2>
                        <p class="text-muted">Create a new boat or room listing</p>
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
                        <!-- Main Form -->
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
                                               placeholder="e.g., Luxury Boat Tour in Cox's Bazar" required>
                                        <small class="text-muted">Choose a catchy, descriptive title</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="boat">Boat</option>
                                            <option value="room">Room</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="location" 
                                               placeholder="e.g., Cox's Bazar, Chittagong" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="6" 
                                                  placeholder="Describe your listing in detail..." required></textarea>
                                        <small class="text-muted">Minimum 50 characters</small>
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
                                                       min="1" step="0.01" placeholder="5000" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price Unit <span class="text-danger">*</span></label>
                                            <select class="form-select" name="price_unit" required>
                                                <option value="hour">Per Hour</option>
                                                <option value="day" selected>Per Day</option>
                                                <option value="night">Per Night</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="capacity" 
                                               min="1" placeholder="10" required>
                                        <small class="text-muted">Maximum number of persons</small>
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
                                        <label class="form-label">Main Image <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="main_image" 
                                               accept="image/*" required onchange="previewMainImage(this)">
                                        <small class="text-muted">This will be the primary image (Max 5MB, JPG/PNG)</small>
                                        <div id="mainImagePreview" class="mt-3"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Gallery Images (Optional)</label>
                                        <input type="file" class="form-control" name="gallery_images[]" 
                                               accept="image/*" multiple onchange="previewGalleryImages(this)">
                                        <small class="text-muted">Upload up to 5 additional images</small>
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
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="WiFi" id="wifi">
                                                <label class="form-check-label" for="wifi">WiFi</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Air Conditioning" id="ac">
                                                <label class="form-check-label" for="ac">Air Conditioning</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Life Jackets" id="lifejackets">
                                                <label class="form-check-label" for="lifejackets">Life Jackets</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Parking" id="parking">
                                                <label class="form-check-label" for="parking">Parking</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Food Service" id="food">
                                                <label class="form-check-label" for="food">Food Service</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Bathroom" id="bathroom">
                                                <label class="form-check-label" for="bathroom">Bathroom</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Music System" id="music">
                                                <label class="form-check-label" for="music">Music System</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="amenities[]" value="Pet Friendly" id="pets">
                                                <label class="form-check-label" for="pets">Pet Friendly</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Submit Card -->
                            <div class="dashboard-card mb-4">
                                <div class="card-header">
                                    <h4><i class="fas fa-paper-plane"></i> Publish</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> Your listing will be reviewed by admin before going live.
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-check"></i> Submit for Approval
                                    </button>
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h4><i class="fas fa-lightbulb"></i> Tips</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="tips-list">
                                        <li><i class="fas fa-check text-success"></i> Use high-quality images</li>
                                        <li><i class="fas fa-check text-success"></i> Write detailed descriptions</li>
                                        <li><i class="fas fa-check text-success"></i> Set competitive prices</li>
                                        <li><i class="fas fa-check text-success"></i> List all amenities</li>
                                        <li><i class="fas fa-check text-success"></i> Be accurate with capacity</li>
                                    </ul>
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
.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.tips-list li:last-child {
    border-bottom: none;
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: var(--radius-md);
    margin-top: 1rem;
}

.gallery-preview-item {
    position: relative;
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
        Array.from(input.files).forEach((file, index) => {
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

// Form validation
document.getElementById('listingForm').addEventListener('submit', function(e) {
    const description = document.querySelector('[name="description"]').value;
    if (description.length < 50) {
        e.preventDefault();
        alert('Description must be at least 50 characters long');
    }
});
</script>

<?php include '../includes/footer.php'; ?>

# 🎨 TripEase - Quick UI Reference

## ⚡ INSTANT COPY-PASTE COMPONENTS

---

## 🎯 BUTTONS

### Gradient Button
```html
<button class="btn btn-gradient hover-lift">
    <i class="fas fa-search"></i> Search Now
</button>
```

### Glass Button
```html
<button class="btn btn-glass hover-lift">
    <i class="fas fa-arrow-down"></i> Learn More
</button>
```

### Primary Button
```html
<button class="btn btn-primary hover-lift">
    <i class="fas fa-save"></i> Save Changes
</button>
```

---

## 📦 CARDS

### Modern Card
```html
<div class="card-modern hover-lift fade-in">
    <div class="card-body">
        <h5 class="card-title">Card Title</h5>
        <p class="card-text">Card content here</p>
    </div>
</div>
```

### Glass Card
```html
<div class="card-glass p-4 hover-lift">
    <h4 class="text-white mb-3">Glass Card</h4>
    <p class="text-white">Glassmorphism effect</p>
</div>
```

### Gradient Card
```html
<div class="card-gradient p-4 hover-lift">
    <h3 class="text-white mb-2">Welcome!</h3>
    <p class="text-white mb-0">Gradient background</p>
</div>
```

---

## 📊 STAT CARDS

### Primary Stat
```html
<div class="stat-card hover-lift">
    <div class="stat-icon primary">
        <i class="fas fa-users"></i>
    </div>
    <div class="stat-details">
        <h3>1,234</h3>
        <p class="text-muted mb-0">Total Users</p>
    </div>
</div>
```

### Success Stat
```html
<div class="stat-card hover-lift">
    <div class="stat-icon success">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="stat-details">
        <h3>567</h3>
        <p class="text-muted mb-0">Completed</p>
    </div>
</div>
```

### Warning Stat
```html
<div class="stat-card hover-lift">
    <div class="stat-icon warning">
        <i class="fas fa-clock"></i>
    </div>
    <div class="stat-details">
        <h3>89</h3>
        <p class="text-muted mb-0">Pending</p>
    </div>
</div>
```

---

## 📝 FORMS

### Modern Input
```html
<div class="mb-3">
    <label class="form-label-modern">Full Name</label>
    <input type="text" class="form-control-modern" placeholder="Enter your name">
</div>
```

### Input with Icon
```html
<div class="mb-3">
    <label class="form-label-modern">Location</label>
    <div class="input-group-modern">
        <i class="fas fa-map-marker-alt input-icon"></i>
        <input type="text" class="form-control-modern" placeholder="Where to?">
    </div>
</div>
```

---

## 🏆 BADGES

### Gradient Badges
```html
<span class="badge-modern badge-gradient-primary">Primary</span>
<span class="badge-modern badge-gradient-success">Success</span>
<span class="badge-modern badge-gradient-warning">Warning</span>
```

---

## 📋 TABLES

### Modern Table
```html
<div class="table-modern">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>john@example.com</td>
                <td><span class="badge-modern badge-gradient-success">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-primary">Edit</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

---

## 🎨 ANIMATIONS

### Fade In
```html
<div class="fade-in">
    Content fades in
</div>
```

### Slide In Left
```html
<div class="slide-in-left">
    Content slides from left
</div>
```

### Slide In Right
```html
<div class="slide-in-right">
    Content slides from right
</div>
```

### Hover Lift
```html
<div class="hover-lift">
    Lifts up on hover
</div>
```

### Hover Scale
```html
<div class="hover-scale">
    Scales up on hover
</div>
```

---

## 🎯 ALERTS

### Modern Alert
```html
<div class="alert alert-modern alert-success">
    <i class="fas fa-check-circle"></i>
    Success message here!
</div>

<div class="alert alert-modern alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    Error message here!
</div>

<div class="alert alert-modern alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    Warning message here!
</div>
```

---

## 🎪 HERO SECTION

### Modern Hero
```html
<section class="hero-modern">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    Your Amazing <span class="text-gradient">Headline</span>
                </h1>
                <p class="hero-subtitle">
                    Your compelling subtitle goes here
                </p>
                <div class="mt-4">
                    <a href="#" class="btn btn-gradient btn-lg me-3 hover-lift">
                        <i class="fas fa-rocket"></i> Get Started
                    </a>
                    <a href="#" class="btn btn-glass btn-lg hover-lift">
                        <i class="fas fa-play"></i> Watch Demo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
```

---

## 💫 JAVASCRIPT FUNCTIONS

### Show Toast
```javascript
showToast('Success message!', 'success');
showToast('Error message!', 'danger');
showToast('Warning message!', 'warning');
```

### Show/Hide Loader
```javascript
showLoader();
// Do something
hideLoader();
```

### Copy to Clipboard
```javascript
copyToClipboard('Text to copy');
```

---

## 🎨 UTILITY CLASSES

### Text
```html
<h1 class="text-gradient">Gradient Text</h1>
```

### Background
```html
<div class="bg-gradient-primary">Primary Gradient</div>
<div class="bg-gradient-secondary">Secondary Gradient</div>
<div class="bg-gradient-success">Success Gradient</div>
```

### Shadows
```html
<div class="shadow-modern">Medium Shadow</div>
<div class="shadow-modern-lg">Large Shadow</div>
```

### Borders
```html
<div class="rounded-modern">16px Radius</div>
<div class="rounded-modern-lg">24px Radius</div>
```

---

## 📱 RESPONSIVE GRID

### 4 Column Layout
```html
<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="stat-card hover-lift">...</div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="stat-card hover-lift">...</div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="stat-card hover-lift">...</div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="stat-card hover-lift">...</div>
    </div>
</div>
```

---

## 🎯 COMPLETE DASHBOARD HEADER

```html
<div class="dashboard-header mb-4 fade-in">
    <div class="card-gradient p-4 rounded-modern-lg">
        <h2 class="text-white mb-2">
            Welcome back, <?php echo $user['name']; ?>! 👋
        </h2>
        <p class="text-white mb-0" style="opacity: 0.9;">
            Here's what's happening today
        </p>
    </div>
</div>
```

---

## 📊 COMPLETE STAT CARD ROW

```html
<div class="row mb-4">
    <!-- Stat 1 -->
    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left">
        <div class="stat-card hover-lift">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-details">
                <h3>1,234</h3>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left" style="animation-delay: 0.1s;">
        <div class="stat-card hover-lift">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3>567</h3>
                <p class="text-muted mb-0">Active</p>
            </div>
        </div>
    </div>
    
    <!-- Stat 3 -->
    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left" style="animation-delay: 0.2s;">
        <div class="stat-card hover-lift">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <h3>89</h3>
                <p class="text-muted mb-0">Pending</p>
            </div>
        </div>
    </div>
    
    <!-- Stat 4 -->
    <div class="col-lg-3 col-sm-6 mb-3 slide-in-left" style="animation-delay: 0.3s;">
        <div class="stat-card hover-lift">
            <div class="stat-icon danger">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-details">
                <h3>$12,345</h3>
                <p class="text-muted mb-0">Revenue</p>
            </div>
        </div>
    </div>
</div>
```

---

## 🎨 COLOR REFERENCE

### Gradients
```css
--primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
--secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
--success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
--warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
```

### Shadows
```css
--shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
--shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
--shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.16);
--shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.2);
--shadow-colored: 0 8px 24px rgba(102, 126, 234, 0.3);
```

---

## ⚡ QUICK TIPS

1. **Add `hover-lift`** to any element for lift effect
2. **Add `fade-in`** for fade animation on scroll
3. **Add `slide-in-left`** for slide animation
4. **Use `card-modern`** instead of `card`
5. **Use `btn-gradient`** for eye-catching buttons
6. **Use `stat-card`** for statistics
7. **Use `form-control-modern`** for inputs
8. **Use `table-modern`** for tables

---

**That's it! Copy, paste, and customize!** 🚀

<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!is_logged_in(ROLE_USER)) {
    redirect(base_url('login.php'));
}

$userId = get_user_id(ROLE_USER);
$user = db('users')->where('user_id', $userId)->first();

// Handle mark as read
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $notificationId = (int)$_GET['id'];
    db('notifications')
        ->where('notification_id', $notificationId)
        ->where('user_id', $userId)
        ->where('user_type', 'user')
        ->update(['is_read' => true]);
    redirect(base_url('user/notifications.php'));
}

// Handle mark all as read
if (isset($_GET['mark_all_read'])) {
    db('notifications')
        ->where('user_id', $userId)
        ->where('user_type', 'user')
        ->update(['is_read' => true]);
    flash_message('success', 'All notifications marked as read');
    redirect(base_url('user/notifications.php'));
}

// Handle delete notification
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $notificationId = (int)$_GET['id'];
    db('notifications')
        ->where('notification_id', $notificationId)
        ->where('user_id', $userId)
        ->where('user_type', 'user')
        ->raw("DELETE FROM notifications WHERE notification_id = ? AND user_id = ? AND user_type = 'user'", 
              [$notificationId, $userId]);
    flash_message('success', 'Notification deleted');
    redirect(base_url('user/notifications.php'));
}

// Get filter
$filter = $_GET['filter'] ?? 'all';

// Build query
$query = "SELECT * FROM notifications WHERE user_type = 'user' AND user_id = ?";
$params = [$userId];

if ($filter === 'unread') {
    $query .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $query .= " AND is_read = 1";
}

$query .= " ORDER BY created_at DESC";

$notifications = db('notifications')->raw($query, $params);

// Get counts
$totalCount = db('notifications')->where('user_type', 'user')->where('user_id', $userId)->count();
$unreadCount = db('notifications')->where('user_type', 'user')->where('user_id', $userId)->where('is_read', false)->count();

$pageTitle = 'Notifications - TripEase';
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
                        <h2><i class="fas fa-bell"></i> Notifications</h2>
                        <p class="text-muted">Stay updated with your bookings and activities</p>
                    </div>
                    <?php if ($unreadCount > 0): ?>
                    <a href="?mark_all_read=1" class="btn btn-outline-primary">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs mb-4">
                    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All (<?php echo $totalCount; ?>)
                    </a>
                    <a href="?filter=unread" class="filter-tab <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                        Unread (<?php echo $unreadCount; ?>)
                    </a>
                    <a href="?filter=read" class="filter-tab <?php echo $filter === 'read' ? 'active' : ''; ?>">
                        Read
                    </a>
                </div>

                <!-- Notifications List -->
                <?php if (!empty($notifications)): ?>
                    <div class="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                            <div class="notification-icon-wrapper">
                                <div class="notification-icon type-<?php echo $notification['type']; ?>">
                                    <?php
                                    $icons = [
                                        'booking' => 'fa-calendar-check',
                                        'payment' => 'fa-dollar-sign',
                                        'review' => 'fa-star',
                                        'message' => 'fa-envelope',
                                        'system' => 'fa-info-circle',
                                        'promotion' => 'fa-gift'
                                    ];
                                    $icon = $icons[$notification['type']] ?? 'fa-bell';
                                    ?>
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                            </div>

                            <div class="notification-content">
                                <h5><?php echo htmlspecialchars($notification['title']); ?></h5>
                                <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                <div class="notification-meta">
                                    <span class="notification-time">
                                        <i class="fas fa-clock"></i> <?php echo time_ago($notification['created_at']); ?>
                                    </span>
                                    <?php if (!$notification['is_read']): ?>
                                    <span class="badge bg-primary">New</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="notification-actions">
                                <?php if ($notification['link']): ?>
                                <a href="<?php echo base_url($notification['link']); ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!$notification['is_read']): ?>
                                <a href="?mark_read=1&id=<?php echo $notification['notification_id']; ?>" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Mark as Read">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                
                                <a href="?delete=1&id=<?php echo $notification['notification_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Delete"
                                   onclick="return confirm('Delete this notification?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h4>No Notifications</h4>
                        <p class="text-muted">
                            <?php if ($filter === 'unread'): ?>
                                You have no unread notifications.
                            <?php elseif ($filter === 'read'): ?>
                                You have no read notifications.
                            <?php else: ?>
                                You don't have any notifications yet.
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo base_url('search.php'); ?>" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Listings
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    background: white;
    padding: 1rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.filter-tab {
    padding: 0.5rem 1.5rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--gray-700);
    background: var(--gray-50);
    transition: all var(--transition-fast);
}

.filter-tab:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.filter-tab.active {
    background: var(--primary-color);
    color: white;
}

.notifications-list {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.notification-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    transition: all var(--transition-fast);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: var(--gray-50);
}

.notification-item.unread {
    background: #f0f8ff;
    border-left: 4px solid var(--primary-color);
}

.notification-icon-wrapper {
    flex-shrink: 0;
}

.notification-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.notification-icon.type-booking {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.notification-icon.type-payment {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.notification-icon.type-review {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.notification-icon.type-message {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
}

.notification-icon.type-system {
    background: linear-gradient(135deg, #34495e, #2c3e50);
}

.notification-icon.type-promotion {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.notification-content {
    flex: 1;
}

.notification-content h5 {
    margin-bottom: 0.5rem;
    color: var(--gray-900);
    font-size: 1rem;
}

.notification-content p {
    margin-bottom: 0.5rem;
    color: var(--gray-700);
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.notification-time {
    color: var(--gray-600);
    font-size: 0.85rem;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
}

.empty-state {
    background: white;
    padding: 4rem 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
}

@media (max-width: 767px) {
    .notification-item {
        flex-direction: column;
    }
    
    .notification-actions {
        justify-content: center;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}
</style>

<?php include '../includes/footer.php'; ?>

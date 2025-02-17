<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

// Fetch tasks assigned to the employee
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Tasks WHERE AssignedTo = ?");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch notifications for the employee
$stmt = $conn->prepare("SELECT * FROM Notifications WHERE UserID = ? ORDER BY CreatedAt DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark all notifications as read if "Mark All as Read" is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_all_read'])) {
    $stmt = $conn->prepare("UPDATE Notifications SET IsRead = TRUE WHERE UserID = ?");
    $stmt->execute([$user_id]);

    // Refresh the page to reflect the changes
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Notification Icon */
.notification-icon {
    position: relative;
    cursor: pointer;
    font-size: 1.5rem;
    color: #667eea;
    margin-left: auto;
    margin-right: 1rem;
}

.notification-count {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #ff4d4d;
    color: #fff;
    border-radius: 50%;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Notification Dropdown */
.notification-dropdown {
    display: none;
    position: fixed;
    top: 70px;
    right: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
}

.notification-dropdown h3 {
    padding: 1rem;
    margin: 0;
    background: #667eea;
    color: #fff;
    border-radius: 10px 10px 0 0;
}

.notification-dropdown ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notification-dropdown li {
    padding: 1rem;
    border-bottom: 1px solid #ddd;
}

.notification-dropdown li.unread {
    background: #f9f9f9;
}

.notification-dropdown li.read {
    opacity: 0.7;
}

.notification-dropdown p {
    margin: 0;
    font-size: 0.9rem;
}

.notification-dropdown small {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #666;
}
/* Mark All as Read Button */
.mark-all-read-form {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #ddd;
}

.btn-mark-all-read {
    width: 100%;
    padding: 0.5rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.btn-mark-all-read:hover {
    background: #45a049;
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Employee Dashboard</h1>
        <nav class="animated fadeInUp">
            <a href="tasks.php" class="btn-primary">View Tasks</a>
            <div class="notification-icon" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <?php if (count($notifications) > 0): ?>
                    <span class="notification-count"><?php echo count($notifications); ?></span>
                <?php endif; ?>
            </div>
            <a href="../../logout.php" class="btn-danger">Logout</a>
        </nav>

        <!-- Notification Dropdown -->
        <div class="notification-dropdown" id="notificationDropdown">
            <h3>Notifications</h3>
            <form method="POST" class="mark-all-read-form">
                <button type="submit" name="mark_all_read" class="btn-mark-all-read">Mark All as Read</button>
            </form>
            <?php if (count($notifications) > 0): ?>
                <ul>
                    <?php foreach ($notifications as $notification): ?>
                        <li class="<?php echo $notification['IsRead'] ? 'read' : 'unread'; ?>">
                            <p><?php echo $notification['Message']; ?></p>
                            <small><?php echo $notification['CreatedAt']; ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No new notifications.</p>
            <?php endif; ?>
        </div>

        <!-- Display Tasks -->
        <section class="animated fadeInUp">
            <h2>Your Tasks</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo $task['Title']; ?></td>
                            <td><?php echo $task['Description']; ?></td>
                            <td><?php echo $task['Status']; ?></td>
                            <td><?php echo $task['Deadline']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        // JavaScript to toggle notification dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('notificationDropdown');
            const icon = document.querySelector('.notification-icon');
            if (!icon.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
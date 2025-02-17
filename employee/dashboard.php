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
      /* Base styles */
:root {
    --primary-blue: #0066cc;
    --light-blue: #e6f3ff;
    --hover-blue: #0052a3;
    --text-dark: #333333;
    --border-color: #d1e3ff;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #ffffff;
    color: var(--text-dark);
    margin: 0;
    padding: 0;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Navigation */
nav {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    margin-bottom: 2rem;
}

/* Buttons */
.btn-primary {
    background-color: var(--primary-blue);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: var(--hover-blue);
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

th {
    background-color: var(--primary-blue);
    color: white;
    padding: 1rem;
    text-align: left;
}

td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}

tr:hover {
    background-color: var(--light-blue);
}

/* Headings */
h1, h2 {
    color: var(--primary-blue);
    margin-bottom: 1.5rem;
}

/* Notification styles update */
.notification-icon {
    color: var(--primary-blue);
}

.notification-dropdown {
    background: white;
    border-color: var(--border-color);
}

.notification-dropdown h3 {
    background: var(--primary-blue);
}

.notification-dropdown li.unread {
    background: var(--light-blue);
}

.btn-mark-all-read {
    background: var(--primary-blue);
}

.btn-mark-all-read:hover {
    background: var(--hover-blue);
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
}

.fadeInDown {
    animation-name: fadeInDown;
}

.fadeInUp {
    animation-name: fadeInUp;
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
            <a href="../logout.php" class="btn-danger">Logout</a>
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
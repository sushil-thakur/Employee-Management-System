<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

// Fetch notifications for the employee
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Notifications WHERE UserID = ? ORDER BY CreatedAt DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark notifications as read
$stmt = $conn->prepare("UPDATE Notifications SET IsRead = TRUE WHERE UserID = ?");
$stmt->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Notifications</h1>
        <ul class="animated fadeInUp">
            <?php foreach ($notifications as $notification): ?>
                <li class="<?php echo $notification['IsRead'] ? 'read' : 'unread'; ?>">
                    <p><?php echo $notification['Message']; ?></p>
                    <small><?php echo $notification['CreatedAt']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
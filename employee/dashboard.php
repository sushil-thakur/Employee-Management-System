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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Employee Dashboard</h1>
        <nav class="animated fadeInUp">
            <a href="tasks.php" class="btn-primary">View Tasks</a>
            <a href="notifications.php" class="btn-primary">Notifications</a>
            <a href="../../logout.php" class="btn-danger">Logout</a>
        </nav>
    </div>
</body>
</html>
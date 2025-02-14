<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

// Fetch all tasks
$stmt = $conn->query("SELECT * FROM Tasks");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Manage Tasks</h1>
        <a href="add_task.php" class="btn-primary">Add New Task</a>
        <table class="animated fadeInUp">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo $task['Title']; ?></td>
                        <td><?php echo $task['Description']; ?></td>
                        <td><?php echo $task['AssignedTo']; ?></td>
                        <td><?php echo $task['Status']; ?></td>
                        <td><?php echo $task['Deadline']; ?></td>
                        <td>
                            <a href="edit_task.php?id=<?php echo $task['TaskID']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_task.php?id=<?php echo $task['TaskID']; ?>" class="btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
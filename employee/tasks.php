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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskID = $_POST['taskID'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE Tasks SET Status = ? WHERE TaskID = ?");
    $stmt->execute([$status, $taskID]);

    // Redirect to Employee Dashboard after updating
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Your Tasks</h1>
        <table class="animated fadeInUp">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
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
                        <td><?php echo $task['Status']; ?></td>
                        <td><?php echo $task['Deadline']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="taskID" value="<?php echo $task['TaskID']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php echo $task['Status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="In Progress" <?php echo $task['Status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Completed" <?php echo $task['Status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                                <button type="submit" class="btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: manage_tasks.php');
    exit();
}

$taskID = $_GET['id'];

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM Tasks WHERE TaskID = ?");
$stmt->execute([$taskID]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: manage_tasks.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assignedTo = $_POST['assignedTo'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE Tasks SET Title = ?, Description = ?, AssignedTo = ?, Deadline = ? WHERE TaskID = ?");
    $stmt->execute([$title, $description, $assignedTo, $deadline, $taskID]);

    header('Location: manage_tasks.php');
    exit();
}

// Fetch all employees
$stmt = $conn->query("SELECT * FROM Users WHERE Role = 'Employee'");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Edit Task</h1>
        <form method="POST" class="animated fadeInUp">
            <input type="text" name="title" placeholder="Task Title" value="<?php echo $task['Title']; ?>" required>
            <textarea name="description" placeholder="Task Description" required><?php echo $task['Description']; ?></textarea>
            <select name="assignedTo" required>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?php echo $employee['UserID']; ?>" <?php echo $employee['UserID'] == $task['AssignedTo'] ? 'selected' : ''; ?>><?php echo $employee['Username']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($task['Deadline'])); ?>" required>
            <button type="submit" class="btn-primary">Update Task</button>
        </form>
    </div>
</body>
</html>
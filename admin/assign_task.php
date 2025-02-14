<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assignedTo = $_POST['assignedTo'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("INSERT INTO Tasks (Title, Description, AssignedTo, AssignedBy, Deadline) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $assignedTo, $_SESSION['user_id'], $deadline]);

    // Send notification
    $message = "You have been assigned a new task: $title";
    $stmt = $conn->prepare("INSERT INTO Notifications (UserID, Message) VALUES (?, ?)");
    $stmt->execute([$assignedTo, $message]);

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
    <title>Assign Task</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/animations.css">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Assign Task</h1>
        <form method="POST" class="animated fadeInUp">
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description" required></textarea>
            <select name="assignedTo" required>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?php echo $employee['UserID']; ?>"><?php echo $employee['Username']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="deadline" required>
            <button type="submit" class="btn-primary">Assign Task</button>
        </form>
    </div>
</body>
</html>
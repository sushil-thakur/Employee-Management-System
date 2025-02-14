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

// Delete the task
$stmt = $conn->prepare("DELETE FROM Tasks WHERE TaskID = ?");
$stmt->execute([$taskID]);

header('Location: manage_tasks.php');
exit();
?>
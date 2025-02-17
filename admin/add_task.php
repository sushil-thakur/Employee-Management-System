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
    <title>Add Task</title>
   <style>
    /* Base styles and CSS reset */
/* Base styles and CSS reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(
        135deg,
        #1a1c2e 0%,
        #2a2d4f 100%
    );
    background-attachment: fixed;
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    position: relative;
    overflow-x: hidden;
}

/* Dashboard Container with softer glassmorphism */
.dashboard-container {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 3rem;
    width: 100%;
    max-width: 800px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Typography */
h1 {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    text-align: center;
    font-weight: 700;
    background: linear-gradient(to right, #fff, #a5b4fc);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Form Elements */
form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

input, textarea, select {
    width: 100%;
    padding: 1rem;
    border-radius: 12px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #a5b4fc;
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 0 4px rgba(165, 180, 252, 0.1);
}

/* Improved Select Styling for Better Visibility */
select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.5em;
}

/* Make select options dark with white text for better visibility */
select option {
    background-color: #2a2d4f;
    color: #ffffff;
    padding: 1rem;
}

textarea {
    min-height: 150px;
    resize: vertical;
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Placeholder Styles */
::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 1rem;
    }
    
    .dashboard-container {
        padding: 2rem;
    }
    
    h1 {
        font-size: 2rem;
    }
}

/* Form field focus states for better visibility */
input:focus::placeholder,
textarea:focus::placeholder {
    color: rgba(255, 255, 255, 0.3);
}

/* Datetime input specific styling */
input[type="datetime-local"] {
    color: #ffffff;
}

input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0.7;
    cursor: pointer;
}

/* Improved select dropdown text contrast */
select, select option {
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.5);
}
   </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Add Task</h1>
        <form method="POST" class="animated fadeInUp">
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description" required></textarea>
            <select name="assignedTo" required>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?php echo $employee['UserID']; ?>"><?php echo $employee['Username']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="deadline" required>
            <button type="submit" class="btn-primary">Add Task</button>
        </form>
    </div>
</body>
</html>
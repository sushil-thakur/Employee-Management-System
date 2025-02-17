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
    <style>
        /* Base reset and variables */
:root {
    --primary-color: #0047AB;
    --secondary-color: #4169E1;
    --hover-color: #1E90FF;
    --background: #F0F8FF;
    --white: #FFFFFF;
    --shadow: rgba(0, 71, 171, 0.2);
}

body {
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, var(--background) 0%, var(--white) 100%);
    min-height: 100vh;
    font-family: 'Arial', sans-serif;
}

/* Dashboard Container */
.dashboard-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    transform-origin: top center;
    animation: dashboardEnter 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

/* Heading Styles */
h1 {
    color: var(--primary-color);
    text-align: center;
    font-size: 2.5em;
    margin-bottom: 30px;
    position: relative;
    text-shadow: 2px 2px 4px var(--shadow);
}

h1::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    animation: underlineExpand 0.8s ease-out forwards;
}

/* Form Styling */
form {
    background: var(--white);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px var(--shadow);
    transform-origin: top;
    animation: formSlide 0.6s ease-out forwards;
}

input, textarea, select {
    width: 100%;
    padding: 15px;
    margin-bottom: 20px;
    border: 2px solid #E0E0FF;
    border-radius: 10px;
    background: var(--white);
    font-size: 16px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

input:focus, textarea:focus, select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px var(--shadow);
    outline: none;
    transform: translateY(-2px);
}

textarea {
    min-height: 120px;
    resize: vertical;
}

/* Button Styling */
.btn-primary {
    width: 100%;
    padding: 15px;
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: 0.5s;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px var(--shadow);
}

.btn-primary:hover::before {
    left: 100%;
}

/* Animations */
@keyframes dashboardEnter {
    0% {
        opacity: 0;
        transform: scale(0.95) translateY(-30px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes formSlide {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes underlineExpand {
    0% {
        width: 0;
        opacity: 0;
    }
    100% {
        width: 100px;
        opacity: 1;
    }
}

/* Existing Animation Classes */
.animated {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.fadeInDown {
    animation: fadeInDown 1s ease-out;
}

.fadeInUp {
    animation: fadeInUp 1s ease-out;
}

@keyframes fadeInDown {
    0% {
        opacity: 0;
        transform: translateY(-40px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(40px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        margin: 20px auto;
        padding: 15px;
    }
    
    form {
        padding: 20px;
    }
    
    h1 {
        font-size: 2em;
    }
    
    input, textarea, select, .btn-primary {
        padding: 12px;
    }
}
    </style>
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
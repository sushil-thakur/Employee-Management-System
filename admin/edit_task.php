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
    <title>Edit Task - Advanced UI</title>
    <style>
        :root {
            --primary-blue: #0055ff;
            --secondary-blue: #00288a;
            --accent-blue: #40a9ff;
            --pure-white: #ffffff;
            --ghost-white: #f8f9ff;
            --intense-shadow: 0 10px 30px rgba(0, 85, 255, 0.15),
                             0 5px 15px rgba(0, 40, 138, 0.1);
            --glow-effect: 0 0 20px rgba(64, 169, 255, 0.3);
            --text-gray: #4a5568;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--ghost-white) 0%, var(--pure-white) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .dashboard-container {
            background: var(--pure-white);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: var(--intense-shadow);
            width: 100%;
            max-width: 700px;
            position: relative;
            overflow: hidden;
            animation: containerSlideUp 0.6s ease-out forwards;
        }

        .dashboard-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-blue));
            animation: shimmer 2s infinite linear;
        }

        h1 {
            color: var(--secondary-blue);
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            animation: fadeIn 0.8s ease-out;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary-blue);
            border-radius: 2px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .input-group {
            position: relative;
            transform-style: preserve-3d;
            animation: slideInFromLeft 0.5s ease-out forwards;
            opacity: 0;
        }

        .input-group:nth-child(1) { animation-delay: 0.2s; }
        .input-group:nth-child(2) { animation-delay: 0.3s; }
        .input-group:nth-child(3) { animation-delay: 0.4s; }
        .input-group:nth-child(4) { animation-delay: 0.5s; }

        input, textarea, select {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid transparent;
            border-radius: 12px;
            background: var(--ghost-white);
            font-size: 1.1rem;
            color: var(--text-gray);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.5;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5em;
            padding-right: 3rem;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: var(--pure-white);
            box-shadow: var(--glow-effect);
            transform: translateY(-2px);
        }

        input[type="datetime-local"] {
            padding: 1.1rem 1.5rem;
        }

        .btn-primary {
            margin-top: 1rem;
            padding: 1.2rem 2rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: var(--pure-white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: slideUpFade 0.5s ease-out 0.6s forwards;
            opacity: 0;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--intense-shadow);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        @keyframes containerSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shimmer {
            0% { background-position: -500px 0; }
            100% { background-position: 500px 0; }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 2rem;
                border-radius: 20px;
                margin: 1rem;
            }

            h1 {
                font-size: 2rem;
                margin-bottom: 2rem;
            }

            input, textarea, select {
                padding: 1rem 1.2rem;
                font-size: 1rem;
            }

            textarea {
                min-height: 120px;
            }

            .btn-primary {
                padding: 1rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Edit Task</h1>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="title" placeholder="Task Title" value="<?php echo htmlspecialchars($task['Title']); ?>" required>
            </div>
            <div class="input-group">
                <textarea name="description" placeholder="Task Description" required><?php echo htmlspecialchars($task['Description']); ?></textarea>
            </div>
            <div class="input-group">
                <select name="assignedTo" required>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee['UserID']); ?>" 
                                <?php echo $employee['UserID'] == $task['AssignedTo'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['Username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <input type="datetime-local" name="deadline" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($task['Deadline'])); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Task</button>
        </form>
    </div>
</body>
</html>
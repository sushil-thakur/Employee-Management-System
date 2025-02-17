<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}
include '../includes/db.php';

// Fetch all employees
$stmt = $conn->query("SELECT * FROM Users WHERE Role = 'Employee'");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tasks with employee names
$stmt = $conn->query("
    SELECT Tasks.TaskID, Tasks.Title, Tasks.Description, Tasks.Status, Tasks.Deadline, Users.Username 
    FROM Tasks 
    INNER JOIN Users ON Tasks.AssignedTo = Users.UserID
");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --danger-color: #ef233c;
            --success-color: #2a9d8f;
            --warning-color: #ff9f1c;
            --background-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #2b2d42;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        h1 {
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        h2 {
            color: var(--text-primary);
            margin: 2rem 0 1rem;
            font-size: 1.8rem;
        }

        nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn-primary, .btn-danger {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary-color);
        }

        .btn-danger {
            background: var(--danger-color);
        }

        .btn-primary:hover, .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        section {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        /* Animations */
        .animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fadeInDown {
            animation-name: fadeInDown;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fadeInUp {
            animation-name: fadeInUp;
        }

        /* Status Styles */
        td:nth-child(4) {
            font-weight: 600;
        }

        td:nth-child(4):contains('Completed') {
            color: var(--success-color);
        }

        td:nth-child(4):contains('Pending') {
            color: var(--warning-color);
        }

        td:nth-child(4):contains('In Progress') {
            color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .btn-primary, .btn-danger {
                padding: 0.7rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Admin Dashboard</h1>
        <nav class="animated fadeInUp">
            <a href="add_employee.php" class="btn-primary">Add Employee</a>
            <a href="manage_tasks.php" class="btn-primary">Manage Tasks</a>
            <a href="assign_task.php" class="btn-primary">Assign Task</a>
            <a href="../logout.php" class="btn-danger">Logout</a>
        </nav>
        <!-- Display Employees -->
        <section class="animated fadeInUp">
            <h2>Employees</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Joined On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo $employee['UserID']; ?></td>
                            <td><?php echo $employee['Username']; ?></td>
                            <td><?php echo $employee['Role']; ?></td>
                            <td><?php echo $employee['CreatedAt']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <!-- Display Tasks -->
        <section class="animated fadeInUp">
            <h2>Tasks</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo $task['TaskID']; ?></td>
                            <td><?php echo $task['Title']; ?></td>
                            <td><?php echo $task['Username']; ?></td> <!-- Display employee name -->
                            <td><?php echo $task['Status']; ?></td>
                            <td><?php echo $task['Deadline']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
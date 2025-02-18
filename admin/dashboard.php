<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

// Handle Employee Deletion
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];

    // Step 1: Delete related notifications
    $stmt = $conn->prepare("DELETE FROM notifications WHERE UserID = ?");
    $stmt->execute([$deleteID]);

    // Step 2: Delete related tasks
    $stmt = $conn->prepare("DELETE FROM tasks WHERE AssignedTo = ?");
    $stmt->execute([$deleteID]);

    // Delete related salary records
    $deleteSalaries = $conn->prepare("DELETE FROM salaries WHERE UserID = ?");
    $deleteSalaries->execute([$deleteID]);

    // Step 3: Delete the employee
    $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
    $stmt->execute([$deleteID]);

    // Redirect back to the dashboard
    header('Location: dashboard.php');
    exit();
}

// Fetch all employees
$stmt = $conn->query("SELECT * FROM users WHERE Role = 'Employee'");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tasks with employee names
$stmt = $conn->query("SELECT tasks.TaskID, tasks.Title, tasks.Status, tasks.Deadline, users.Username 
                     FROM tasks 
                     INNER JOIN users ON tasks.AssignedTo = users.UserID");
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
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--text-primary);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            height: 100vh;
            padding: 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.6s ease-out;
            z-index: 1000;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .sidebar h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 2px;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 0.9rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 0;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-50%);
            transition: width 0.3s ease;
        }

        .sidebar a:hover::before {
            width: 100%;
        }

        .sidebar a:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar .btn-danger {
            background: var(--danger-color);
            margin-top: auto;
            transform-origin: center;
            transition: all 0.3s ease;
        }

        .sidebar .btn-danger:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.5rem;
            position: relative;
            padding-bottom: 0.8rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            transform-origin: center;
            animation: sectionEntry 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes sectionEntry {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        th {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            padding: 1.2rem 1rem;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        /* Delete Button Styles */
        .action-cell {
            position: relative;
            width: 100px;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            position: absolute;
            left: 50%;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            text-decoration: none;
            font-size: 0.9rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete::before {
            content: '🗑️';
            margin-right: 5px;
            font-size: 1rem;
        }

        tr:hover .btn-delete {
            opacity: 1;
            transform: translateX(-50%) translateY(-50%);
        }

        .btn-delete:hover {
            background: #d32f2f;
            box-shadow: 0 4px 15px rgba(239, 35, 60, 0.2);
            transform: translateX(-50%) translateY(-50%) scale(1.05);
        }

        /* Status Styles */
        td:nth-child(4) {
            font-weight: 600;
            position: relative;
            padding-left: 1.5rem;
        }

        td:nth-child(4)::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* Ensure proper row height */
        tbody tr {
            height: 60px;
            position: relative;
        }

        /* Dim effect for other cells */
        tr:hover td:not(:last-child) {
            opacity: 0.7;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                animation: none;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            section {
                padding: 1.5rem;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            td, th {
                padding: 0.8rem;
            }

            .btn-delete {
                position: static;
                transform: none;
                opacity: 1;
                margin: 0 auto;
            }

            tr:hover .btn-delete {
                transform: none;
            }

            .btn-delete:hover {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="add_employee.php">Add Employee</a>
            <a href="manage_tasks.php">Manage Tasks</a>
            <a href="assign_task.php">Assign Task</a>
            <a href="manage_salaries.php">Manage Salaries</a>
            <a href="../logout.php" class="btn-danger">Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h1>Admin Dashboard</h1>
        
        <section>
            <h2>Employees</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Joined On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['UserID']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Username']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Role']); ?></td>
                            <td><?php echo htmlspecialchars($employee['CreatedAt']); ?></td>
                            <td class="action-cell">
                                <a href="dashboard.php?delete_id=<?php echo htmlspecialchars($employee['UserID']); ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this employee?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
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
                            <td><?php echo htmlspecialchars($task['TaskID']); ?></td>
                            <td><?php echo htmlspecialchars($task['Title']); ?></td>
                            <td><?php echo htmlspecialchars($task['Username']); ?></td>
                            <td><?php echo htmlspecialchars($task['Status']); ?></td>
                            <td><?php echo htmlspecialchars($task['Deadline']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
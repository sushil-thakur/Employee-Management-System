<?php session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';
$stmt = $conn->query("
    SELECT Tasks.TaskID, Tasks.Title, Tasks.Description, Tasks.Status, Tasks.Deadline, Users.Username 
    FROM Tasks 
    INNER JOIN Users ON Tasks.AssignedTo = Users.UserID
");


$stmt = $conn->query("SELECT * FROM Tasks");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.6s ease-out;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        h1 {
            font-size: 2.2rem;
            color: #2d3748;
            animation: fadeDown 0.8s ease-out;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, #4776E6, #8E54E9);
            border-radius: 2px;
            animation: widthGrow 0.8s ease-out;
        }

        .btn-primary {
            background: linear-gradient(to right, #4776E6, #8E54E9);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease-out;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
        }

        thead {
            background: linear-gradient(to right, #4776E6, #8E54E9);
            color: white;
        }

        th {
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        th:first-child { border-radius: 15px 0 0 0; }
        th:last-child { border-radius: 0 15px 0 0; }

        tbody tr {
            transition: all 0.3s ease;
            animation: fadeIn 0.6s ease-out backwards;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        td {
            padding: 1.2rem 1rem;
            color: #4a5568;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.3s ease;
        }

        .status-badge i {
            font-size: 0.75rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
        }

        .btn-edit {
            background: #4776E6;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-2px);
            filter: brightness(110%);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes widthGrow {
            from { width: 0; }
            to { width: 60px; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .dashboard-container {
                padding: 1rem;
            }

            .header-section {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            td, th {
                padding: 1rem 0.5rem;
            }

            .btn-edit, .btn-delete {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header-section">
            <h1>Manage Tasks</h1>
            <a href="add_task.php" class="btn-primary">
                <i class="fas fa-plus"></i> Add New Task
            </a>
        </div>
        <div class="table-container">
            <table>
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
                    <?php foreach ($tasks as $index => $task): ?>
                        <tr style="animation-delay: <?php echo $index * 0.1; ?>s">
                            <td><?php echo $task['Title']; ?></td>
                            <td><?php echo $task['Description']; ?></td>
                            <td><?php echo $task['AssignedTo']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($task['Status']); ?>">
                                    <i class="fas fa-circle"></i>
                                    <?php echo $task['Status']; ?>
                                </span>
                            </td>
                            <td><?php echo $task['Deadline']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_task.php?id=<?php echo $task['TaskID']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_task.php?id=<?php echo $task['TaskID']; ?>" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
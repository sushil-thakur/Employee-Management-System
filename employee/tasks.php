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
  <style>
    /* Task Page Specific Styles */
:root {
    --primary-blue: #0066cc;
    --light-blue: #e6f3ff;
    --hover-blue: #0052a3;
    --text-dark: #333333;
    --border-color: #d1e3ff;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Enhanced Table Styles for Tasks */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
    border-radius: 8px;
    overflow: hidden;
}

th {
    background-color: var(--primary-blue);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}

td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

tr:last-child td {
    border-bottom: none;
}

tr:hover {
    background-color: var(--light-blue);
}

/* Form Elements in Table */
select {
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background-color: white;
    margin-right: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-dark);
    width: 140px;
}

select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.1);
}

/* Status-specific colors */
select option[value="Pending"] {
    color: #f0ad4e;
}

select option[value="In Progress"] {
    color: var(--primary-blue);
}

select option[value="Completed"] {
    color: #5cb85c;
}

/* Update Button */
.btn-primary {
    background-color: var(--primary-blue);
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: var(--hover-blue);
}

/* Page Title */
h1 {
    color: var(--primary-blue);
    margin-bottom: 1.5rem;
    font-size: 2rem;
}

/* Animations */
.animated {
    animation-duration: 0.5s;
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

.fadeInDown {
    animation-name: fadeInDown;
}

.fadeInUp {
    animation-name: fadeInUp;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
    
    td, th {
        padding: 0.75rem;
    }
    
    select {
        width: 120px;
    }
}
  </style>
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
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

// Fetch existing salaries
$stmt = $conn->query("SELECT Salaries.*, Users.Username FROM Salaries INNER JOIN Users ON Salaries.UserID = Users.UserID");
$salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add/Update Salary
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['userID'];
    $amount = $_POST['amount'];
    $effectiveDate = $_POST['effectiveDate'];

    // Check if salary already exists for the employee
    $stmt = $conn->prepare("SELECT * FROM Salaries WHERE UserID = ?");
    $stmt->execute([$userID]);
    $existingSalary = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingSalary) {
        // Update existing salary
        $stmt = $conn->prepare("UPDATE Salaries SET Amount = ?, EffectiveDate = ? WHERE UserID = ?");
        $stmt->execute([$amount, $effectiveDate, $userID]);
    } else {
        // Insert new salary
        $stmt = $conn->prepare("INSERT INTO Salaries (UserID, Amount, EffectiveDate) VALUES (?, ?, ?)");
        $stmt->execute([$userID, $amount, $effectiveDate]);
    }

    header('Location: manage_salaries.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Salaries</title>
    <style>
        /* Base Layout */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.dashboard-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 3rem;
    width: 100%;
    max-width: 1200px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    color: #2a5298;
}

/* Typography & Headers */
h1, h2 {
    color: #1e3c72;
    margin-bottom: 1.5rem;
}

h1 {
    font-size: 2.5rem;
    text-align: center;
    font-weight: 800;
    background: linear-gradient(45deg, #1e3c72, #2a5298);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

h2 {
    font-size: 1.8rem;
    margin-top: 2rem;
    color: #1e3c72;
}

/* Navigation */
nav {
    margin: 2rem 0;
    display: flex;
    gap: 1rem;
}

/* Form Elements */
form {
    display: grid;
    gap: 1.5rem;
    margin: 2rem 0;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

input, select {
    width: 100%;
    padding: 1rem;
    border-radius: 12px;
    border: 2px solid #e0e7ff;
    background: #ffffff;
    color: #1e3c72;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

input:focus, select:focus {
    outline: none;
    border-color: #2a5298;
    box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
    transform: translateY(-2px);
}

select option {
    background-color: #ffffff;
    color: #1e3c72;
    padding: 0.5rem;
}

/* Enhanced Table Styling */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 2rem 0;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

th, td {
    padding: 1.2rem;
    text-align: left;
    border-bottom: 1px solid #e0e7ff;
    transition: all 0.3s ease;
}

th {
    background: #1e3c72;
    color: #ffffff;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
}

tr:last-child td {
    border-bottom: none;
}

tbody tr {
    transition: all 0.3s ease;
}

tbody tr:hover {
    background: #f0f5ff;
    transform: translateX(6px);
}

/* Button Styling */
.btn-primary {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(42, 82, 152, 0.3);
    background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Animations */
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

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Animation Classes */
.animated {
    animation-duration: 0.8s;
    animation-fill-mode: both;
}

.fadeInDown {
    animation-name: fadeInDown;
}

.fadeInUp {
    animation-name: fadeInUp;
}

/* Table Row Animations */
tbody tr {
    animation: slideIn 0.5s ease-out forwards;
    opacity: 0;
}

tbody tr:nth-child(1) { animation-delay: 0.1s; }
tbody tr:nth-child(2) { animation-delay: 0.2s; }
tbody tr:nth-child(3) { animation-delay: 0.3s; }
tbody tr:nth-child(4) { animation-delay: 0.4s; }
tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* Form Element Animations */
form > * {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

form > *:nth-child(1) { animation-delay: 0.2s; }
form > *:nth-child(2) { animation-delay: 0.3s; }
form > *:nth-child(3) { animation-delay: 0.4s; }
form > *:nth-child(4) { animation-delay: 0.5s; }

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 1rem;
    }
    
    .dashboard-container {
        padding: 1.5rem;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
    
    form {
        grid-template-columns: 1fr;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f0f5ff;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #1e3c72;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #2a5298;
}

/* Input placeholder color */
::placeholder {
    color: #94a3b8;
}

/* Number input arrows styling */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
    background: #f0f5ff;
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="animated fadeInDown">Manage Salaries</h1>
        <nav class="animated fadeInUp">
            <a href="dashboard.php" class="btn-primary">Back to Dashboard</a>
        </nav>

        <!-- Add/Update Salary Form -->
        <section class="animated fadeInUp">
            <h2>Set/Update Salary</h2>
            <form method="POST">
                <select name="userID" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['UserID']; ?>"><?php echo $employee['Username']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="amount" placeholder="Salary Amount" step="0.01" required>
                <input type="date" name="effectiveDate" required>
                <button type="submit" class="btn-primary">Save</button>
            </form>
        </section>

        <!-- Display Existing Salaries -->
        <section class="animated fadeInUp">
            <h2>Existing Salaries</h2>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Salary Amount</th>
                        <th>Effective Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $salary): ?>
                        <tr>
                            <td><?php echo $salary['Username']; ?></td>
                            <td><?php echo number_format($salary['Amount'], 2); ?></td>
                            <td><?php echo $salary['EffectiveDate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
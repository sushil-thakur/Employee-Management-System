<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("INSERT INTO Users (Username, PasswordHash, Role) VALUES (?, ?, 'Employee')");
    $stmt->execute([$username, $password]);
    
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --background-color: #f5f6fa;
            --card-background: #ffffff;
            --input-background: #f8f9fa;
            --box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .dashboard-container {
            background: var(--card-background);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUpFade 0.6s ease-out forwards;
        }

        h1 {
            color: var(--secondary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.2rem;
            font-weight: 600;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.3s forwards;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        input {
            padding: 1rem 1.5rem;
            border: 2px solid transparent;
            border-radius: 8px;
            background: var(--input-background);
            font-size: 1rem;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-20px);
        }

        input:first-child {
            animation: slideInFromLeft 0.5s ease-out 0.6s forwards;
        }

        input:nth-child(2) {
            animation: slideInFromLeft 0.5s ease-out 0.8s forwards;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--card-background);
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
        }

        .btn-primary {
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFade 0.5s ease-out 1s forwards;
        }

        .btn-primary:hover {
            background: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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

        /* Add smooth transition for error states */
        .error {
            border-color: var(--error-color) !important;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Success feedback animation */
        .success {
            border-color: var(--success-color) !important;
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .dashboard-container {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            input, .btn-primary {
                padding: 0.8rem 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Add Employee</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-primary">Add Employee</button>
        </form>
    </div>
</body>
</html>
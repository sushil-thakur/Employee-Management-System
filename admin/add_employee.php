<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: ../../index.php');
    exit();
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];  // Get email input
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->rowCount() > 0) {
        echo "<script>alert('Email already exists. Use a different one.');</script>";
    } else {
        // Insert user data into the Users table
        $stmt = $conn->prepare("INSERT INTO Users (Username, Email, PasswordHash, Role) VALUES (?, ?, ?, 'Employee')");
        $stmt->execute([$username, $email, $password]);

        header('Location: dashboard.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee - Advanced UI</title>
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
            max-width: 540px;
            position: relative;
            overflow: hidden;
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
        }

        input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid transparent;
            border-radius: 12px;
            background: var(--ghost-white);
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: var(--pure-white);
            box-shadow: var(--glow-effect);
            transform: translateY(-2px);
        }

        input::placeholder {
            color: #a0aec0;
            transition: all 0.3s ease;
        }

        input:focus::placeholder {
            opacity: 0;
            transform: translateX(10px);
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

        @keyframes shimmer {
            0% { background-position: -500px 0; }
            100% { background-position: 500px 0; }
        }

        @media (max-width: 600px) {
            .dashboard-container {
                padding: 2rem;
                border-radius: 20px;
            }

            h1 {
                font-size: 2rem;
                margin-bottom: 2rem;
            }

            input {
                padding: 1rem 1.2rem;
                font-size: 1rem;
            }

            .btn-primary {
                padding: 1rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Add Employee</h1>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-primary">Add Employee</button>
        </form>
    </div>
</body>
</html>

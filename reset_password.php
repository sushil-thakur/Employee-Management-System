<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['token'])) {
    header('Location: index.php');
    exit();
}

$token = $_GET['token'];

// Check if the token is valid and not expired
$stmt = $conn->prepare("SELECT * FROM PasswordResets WHERE Token = ? AND Expires > NOW()");
$stmt->execute([$token]);
$resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resetRequest) {
    $error = "Invalid or expired token.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Update the user's password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE Users SET PasswordHash = ? WHERE Email = ?");
        $stmt->execute([$hashedPassword, $resetRequest['Email']]);

        // Delete the reset token
        $stmt = $conn->prepare("DELETE FROM PasswordResets WHERE Token = ?");
        $stmt->execute([$token]);

        $success = "Your password has been reset successfully. <a href='index.php'>Login here</a>.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
  <style>
    /* style.css */
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --error-color: #ef4444;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --text-color: #1f2937;
    --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--bg-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

/* Animated background elements */
body::before,
body::after {
    content: '';
    position: fixed;
    width: 200vw;
    height: 200vh;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: 
        linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 40px 40px;
    animation: gridMove 20s linear infinite;
}

body::after {
    opacity: 0.5;
    animation-duration: 15s;
    background-size: 20px 20px;
    filter: blur(1px);
}

@keyframes gridMove {
    0% {
        transform: translate(-50%, -50%) rotate(0deg) scale(1);
    }
    50% {
        transform: translate(-50%, -50%) rotate(180deg) scale(1.5);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg) scale(1);
    }
}

.login-container {
    width: 100%;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 1;
    perspective: 2000px;
}

.login-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 0 0 1px rgba(255, 255, 255, 0.2);
    width: 100%;
    max-width: 400px;
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    animation: boxEntry 1.2s cubic-bezier(0.2, 0.8, 0.2, 1);
}

@keyframes boxEntry {
    0% {
        opacity: 0;
        transform: translateZ(-100px) rotateX(10deg);
    }
    100% {
        opacity: 1;
        transform: translateZ(0) rotateX(0);
    }
}

h1 {
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5rem;
    font-weight: 700;
    position: relative;
    animation: titleGlow 2s ease-in-out infinite;
}

@keyframes titleGlow {
    0%, 100% {
        text-shadow: 0 0 20px rgba(79, 70, 229, 0);
    }
    50% {
        text-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
    }
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: relative;
}

input {
    padding: 15px;
    border: 2px solid rgba(79, 70, 229, 0.1);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.9);
    transform-origin: center;
}

input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    transform: scale(1.02);
}

/* Password strength indicator animation */
input[type="password"]:valid {
    border-color: var(--success-color);
    animation: passwordValid 0.5s ease;
}

@keyframes passwordValid {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 15px;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300%;
    height: 300%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.6s ease-out;
}

.btn-primary:hover::before {
    transform: translate(-50%, -50%) scale(1);
}

.btn-primary:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 
        0 10px 20px -10px rgba(79, 70, 229, 0.5),
        0 0 0 3px rgba(79, 70, 229, 0.1);
}

.btn-primary:active {
    transform: scale(0.98);
}

.error, .success {
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.error {
    background: #fef2f2;
    color: var(--error-color);
    border: 1px solid #fee2e2;
    animation: errorShake 0.6s cubic-bezier(0.36, 0, 0.66, -0.56);
}

.success {
    background: #ecfdf5;
    color: var(--success-color);
    border: 1px solid #d1fae5;
    animation: successPop 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes errorShake {
    0%, 100% {
        transform: translateX(0) rotate(0);
        background-color: #fef2f2;
    }
    25% {
        transform: translateX(-10px) rotate(-1deg);
        background-color: #fee2e2;
    }
    75% {
        transform: translateX(10px) rotate(1deg);
        background-color: #fee2e2;
    }
}

@keyframes successPop {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }
    50% {
        transform: scale(1.02);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Password match indicator */
input[name="confirm_password"]:valid {
    border-color: var(--success-color);
}

input[name="confirm_password"]:invalid {
    border-color: var(--warning-color);
}

/* Animated link in success message */
.success a {
    color: var(--primary-color);
    text-decoration: none;
    position: relative;
    font-weight: 500;
    transition: all 0.3s ease;
}

.success a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background: currentColor;
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.success a:hover::after {
    transform: scaleX(1);
}

/* Responsive Design */
@media (max-width: 480px) {
    .login-box {
        padding: 30px 20px;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    body::before,
    body::after {
        animation: none;
    }
}

/* Loading state animation */
.btn-primary.loading {
    background-image: linear-gradient(
        -45deg,
        var(--primary-color) 25%,
        var(--primary-hover) 25%,
        var(--primary-hover) 50%,
        var(--primary-color) 50%,
        var(--primary-color) 75%,
        var(--primary-hover) 75%,
        var(--primary-hover)
    );
    background-size: 40px 40px;
    animation: buttonLoading 1s linear infinite;
}

@keyframes buttonLoading {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 40px 0;
    }
}
  </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="animated fadeInDown">Reset Password</h1>
            <?php if (isset($error)): ?>
                <p class="error animated shake"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success animated fadeInUp"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" class="animated fadeInUp">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" class="btn-primary">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>

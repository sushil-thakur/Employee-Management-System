<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Check if the username exists in the database
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 day")); // Token expires in 1 day

        // Store the token in the database
        $stmt = $conn->prepare("INSERT INTO PasswordResets (Email, Token, Expires) VALUES (?, ?, ?)");
        $stmt->execute([$user['Email'], $token, $expires]);

        // Send the password reset email
        $resetLink = "http://localhost/task-management-System/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n$resetLink";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($user['Email'], $subject, $message, $headers)) {
            $success = "A password reset link has been sent to your email.";
        } else {
            $error = "Failed to send the password reset email.";
        }
    } else {
        $error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* style.css */
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --error-color: #ef4444;
    --success-color: #10b981;
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
.background-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

body::before,
body::after {
    content: '';
    position: fixed;
    width: 100vmax;
    height: 100vmax;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px) 0 0/30px 30px;
    animation: backgroundRotate 60s linear infinite;
}

body::after {
    animation-duration: 30s;
    background-size: 20px 20px;
    opacity: 0.5;
}

@keyframes backgroundRotate {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

.login-container {
    width: 100%;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 2;
    perspective: 1500px;
}

.login-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    width: 100%;
    max-width: 400px;
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    animation: entryAnimation 1s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes entryAnimation {
    0% {
        opacity: 0;
        transform: translateY(30px) rotateX(10deg);
    }
    100% {
        opacity: 1;
        transform: translateY(0) rotateX(0);
    }
}

h1 {
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text-color), var(--primary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientText 6s ease infinite;
}

@keyframes gradientText {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
    transform-style: preserve-3d;
}

input {
    padding: 15px;
    border: 2px solid rgba(79, 70, 229, 0.1);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 
        0 0 0 3px rgba(79, 70, 229, 0.2),
        0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px) scale(1.01);
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
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -10px rgba(79, 70, 229, 0.5);
}

.btn-primary:active {
    transform: translateY(1px);
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
    animation: slideShake 0.6s cubic-bezier(0.36, 0, 0.66, -0.56) both;
}

.success {
    background: #ecfdf5;
    color: var(--success-color);
    border: 1px solid #d1fae5;
    animation: successPulse 2s ease infinite;
}

@keyframes slideShake {
    0%, 100% {
        transform: translateX(0) rotate(0);
    }
    25% {
        transform: translateX(-10px) rotate(-1deg);
    }
    75% {
        transform: translateX(10px) rotate(1deg);
    }
}

@keyframes successPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    position: relative;
    transition: all 0.3s ease;
}

a::after {
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

a:hover {
    color: var(--primary-hover);
}

a:hover::after {
    transform: scaleX(1);
}

/* Floating animation for the box */
.login-box {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotateX(0) rotateY(0);
    }
    50% {
        transform: translateY(-10px) rotateX(2deg) rotateY(-2deg);
    }
}

/* Enhanced loading state */
.btn-primary.loading {
    position: relative;
    pointer-events: none;
    animation: buttonLoad 1s infinite;
}

@keyframes buttonLoad {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(0.98);
    }
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .login-box {
        padding: 30px 20px;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .background-animation {
        display: none;
    }
}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="animated fadeInDown">Forgot Password</h1>
            <?php if (isset($error)): ?>
                <p class="error animated shake"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success animated fadeInUp"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" class="animated fadeInUp">
                <input type="text" name="username" placeholder="Enter your username" required>
                <button type="submit" class="btn-primary">Reset Password</button>
            </form>
            <p class="animated fadeInUp">Remember your password? <a href="index.php">Login here</a>.</p>
        </div>
    </div>
</body>
</html>

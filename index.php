<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['PasswordHash'])) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role'];
        if ($user['Role'] == 'Admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: employee/dashboard.php');
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
   <style>/* style.css */
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --error-color: #ef4444;
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
}

.login-container {
    width: 100%;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 100%;
    max-width: 400px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

h1 {
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5rem;
    font-weight: 700;
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

input {
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}

input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
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
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -10px rgba(79, 70, 229, 0.5);
}

.btn-primary:active {
    transform: translateY(0);
}

.error {
    background: #fef2f2;
    color: var(--error-color);
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    border: 1px solid #fee2e2;
}

p {
    text-align: center;
    margin-top: 20px;
    color: #6b7280;
}

a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

a:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

/* animations.css */
.animated {
    animation-duration: 0.8s;
    animation-fill-mode: both;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -30px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.fadeInDown {
    animation-name: fadeInDown;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.fadeInUp {
    animation-name: fadeInUp;
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-5px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(5px);
    }
}

.shake {
    animation-name: shake;
    animation-duration: 0.6s;
}

/* Responsive Design */
@media (max-width: 480px) {
    .login-box {
        padding: 30px 20px;
    }
    
    h1 {
        font-size: 2rem;
    }
}</style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="animated fadeInDown">Login</h1>
            <?php if (isset($error)): ?>
                <p class="error animated shake"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" class="animated fadeInUp">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <p class="animated fadeInUp">Forgot your password? <a href="forgot_password.php">Reset it here</a>.</p>
        </div>
    </div>
</body>
</html>
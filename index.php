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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
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
        </div>
    </div>
</body>
</html>
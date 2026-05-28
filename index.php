<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } else if ($_SESSION['role'] === 'organizer') {
        header("Location: organizer/dashboard.php");
        exit();
    } else if ($_SESSION['role'] === 'voter') {
        header("Location: voter/dashboard.php");
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $role = login($username, $password);

    if ($role === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } else if ($role === 'organizer') {
        header("Location: organizer/dashboard.php");
        exit();
    } else if ($role === 'voter') {
        header("Location: voter/dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Voting System - Login</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-logo">🏛️</div>
        <h1>Election Voting System</h1>
        <div class="login-subtitle">Please enter your credentials to access the portal</div>
        
        <form method="POST" action="" class="login-form">
            <?php if ($error !== '') { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="input" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="input" required>
            </div>
            <button type="submit" class="btn btn-primary login-btn">Login</button>
            <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
        </form>
    </div>
</body>
</html>


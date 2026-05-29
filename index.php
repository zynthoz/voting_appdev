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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php require_once 'includes/style.php'; ?>
</head>
<body class="d-flex align-items-center justify-content-center bg-light min-vh-100 py-5">
    <div class="card shadow-sm p-4 p-md-5 w-100" style="max-width: 450px;">
        <div class="text-center mb-4">
            <div class="text-uppercase tracking-wider text-secondary small fw-bold mb-1" style="letter-spacing: 2px;">PILIPINAS</div>
            <h2 class="fs-4 fw-bold mb-1">Voting Portal</h2>
            <div class="text-muted small">Election Voting System</div>
        </div>
        
        <form method="POST" action="" class="login-form">
            <?php if ($error !== '') { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
            <a href="forgot_password.php" class="d-block text-center mt-3 text-decoration-none">Forgot Password?</a>
            <a href="register.php" class="d-block text-center mt-3 text-decoration-none">Don't have an account? Register</a>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


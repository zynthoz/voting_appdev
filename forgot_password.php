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

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = mysqli_prepare($conn, "SELECT user_id, full_name FROM tbl_users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        
        $temp_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);

        
        $update_stmt = mysqli_prepare($conn, "UPDATE tbl_users SET password = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($update_stmt, 'si', $temp_password, $user['user_id']);
        mysqli_stmt_execute($update_stmt);

        log_action($user['user_id'], 'Password reset requested');
        $message = 'Your password has been reset. Your new temporary password is: <strong>' . $temp_password . '</strong>';
    } else {
        $error = 'No account found with that email address.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Voting System - Forgot Password</title>
    
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
            <?php if ($message !== '') { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Reset Password</button>
            <a href="index.php" class="d-block text-center mt-3 text-decoration-none">Back to Login</a>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

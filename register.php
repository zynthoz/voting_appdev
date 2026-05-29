<?php
require_once 'includes/auth.php';
require_once 'includes/mailer.php';

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

$step = 'register';
$error = '';
$message = '';


if (isset($_SESSION['reg_otp'])) {
    $step = 'verify';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['form_action'];

    if ($action === 'register') {
        $full_name = $_POST['full_name'];
        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $email     = $_POST['email'];

        
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM tbl_users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);

        if ($existing) {
            $error = 'Username already exists. Please choose a different one.';
        } else {
            
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            
            $_SESSION['reg_full_name'] = $full_name;
            $_SESSION['reg_username']  = $username;
            $_SESSION['reg_password']  = $password;
            $_SESSION['reg_email']     = $email;
            $_SESSION['reg_dob']       = $_POST['date_of_birth'];
            $_SESSION['reg_gender']    = $_POST['gender'];
            $_SESSION['reg_otp']       = $otp;

            
            $subject = 'Email Verification - Election Voting System';
            $body = "<p>Hello " . $full_name . ",</p><p>Your verification code is:</p><p style='font-size:24px;font-weight:bold;letter-spacing:4px;'>" . $otp . "</p><p>Enter this code to complete your registration.</p>";
            send_email($email, $full_name, $subject, $body);

            $step = 'verify';
            $message = 'A verification code has been sent to your email.';
        }

    } else if ($action === 'verify') {
        $entered_otp = $_POST['otp'];

        if ($entered_otp === $_SESSION['reg_otp']) {
            
            $full_name = $_SESSION['reg_full_name'];
            $username  = $_SESSION['reg_username'];
            $password  = $_SESSION['reg_password'];
            $email     = $_SESSION['reg_email'];
            $dob       = $_SESSION['reg_dob'];
            $gender    = $_SESSION['reg_gender'];
            $role      = 'voter';

            
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (full_name, username, password, role, email) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssss', $full_name, $username, $password, $role, $email);
            mysqli_stmt_execute($stmt);

            
            $voter_stmt = mysqli_prepare($conn, "INSERT INTO tbl_voters (voter_name, date_of_birth, gender, contact_information) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($voter_stmt, 'ssss', $full_name, $dob, $gender, $email);
            mysqli_stmt_execute($voter_stmt);

            
            unset($_SESSION['reg_full_name']);
            unset($_SESSION['reg_username']);
            unset($_SESSION['reg_password']);
            unset($_SESSION['reg_email']);
            unset($_SESSION['reg_dob']);
            unset($_SESSION['reg_gender']);
            unset($_SESSION['reg_otp']);

            $step = 'success';
        } else {
            $error = 'Invalid verification code. Please try again.';
            $step = 'verify';
        }

    } else if ($action === 'cancel') {
        
        unset($_SESSION['reg_full_name']);
        unset($_SESSION['reg_username']);
        unset($_SESSION['reg_password']);
        unset($_SESSION['reg_email']);
        unset($_SESSION['reg_otp']);

        $step = 'register';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Voting System - Register</title>
    
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
        <?php if ($step === 'register') { ?>
            <h1 class="fs-4 text-center mb-3">Create Account</h1>
            <div class="text-muted text-center mb-4">Register as a voter to participate in elections</div>

            <form method="POST" action="" class="login-form">
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <input type="hidden" name="form_action" value="register">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2">Register</button>
                <a href="index.php" class="d-block text-center mt-3 text-decoration-none">Already have an account? Login</a>
            </form>

        <?php } else if ($step === 'verify') { ?>
            <h1 class="fs-4 text-center mb-3">Verify Email</h1>
            <div class="text-muted text-center mb-4">Enter the 6-digit code sent to your email</div>

            <form method="POST" action="" class="login-form">
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if ($message !== '') { ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php } ?>
                <input type="hidden" name="form_action" value="verify">
                <div class="mb-3">
                    <label for="otp" class="form-label">Verification Code</label>
                    <input type="text" id="otp" name="otp" class="form-control" maxlength="6" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2">Verify</button>
            </form>
            <form method="POST" action="">
                <input type="hidden" name="form_action" value="cancel">
                <button type="submit" class="d-block text-center mt-3 text-decoration-none" style="background:none;border:none;cursor:pointer;width:100%;padding:0;">Cancel and start over</button>
            </form>

        <?php } else if ($step === 'success') { ?>
            <h1 class="fs-4 text-center mb-3">Registration Complete</h1>
            <div class="text-muted text-center mb-4">Your account has been created successfully</div>
            <div class="alert alert-success">You can now log in with your credentials.</div>
            <a href="index.php" class="btn btn-primary w-100 mt-2">Go to Login</a>
        <?php } ?>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

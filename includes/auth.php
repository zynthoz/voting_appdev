<?php
session_start();
require_once __DIR__ . '/db.php';

function login($username, $password) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT user_id, full_name, role, password FROM tbl_users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            log_action($user['user_id'], 'Logged in');

            return $user['role'];
        }
    }

    return false;
}

function logout() {
    if (isset($_SESSION['user_id'])) {
        log_action($_SESSION['user_id'], 'Logged out');
    }
    session_unset();
    session_destroy();
}

function check_role($required_role) {
    if (!is_logged_in()) {
        header("Location: /voting_appdev/index.php");
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        header("Location: /voting_appdev/index.php");
        exit();
    }
}

function is_logged_in() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

function log_action($user_id, $action) {
    global $conn;

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_logs (user_id, action) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $action);
    mysqli_stmt_execute($stmt);
}
?>

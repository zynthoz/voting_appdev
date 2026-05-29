<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Dashboard';

$users_count      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_users"))['cnt'];
$voters_count     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_voters"))['cnt'];
$candidates_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_candidates"))['cnt'];
$elections_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_elections"))['cnt'];
$votes_count      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tbl_votes"))['cnt'];

$logs_result = mysqli_query($conn, "SELECT l.action, l.datetime, u.full_name FROM tbl_logs l JOIN tbl_users u ON l.user_id = u.user_id ORDER BY l.datetime DESC LIMIT 10");
$recent_logs = mysqli_fetch_all($logs_result, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - <?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php require_once '../includes/style.php'; ?>
</head>
<body>
    <div class="d-flex vw-100 vh-100 overflow-hidden">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <aside class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; overflow-y: auto;">
            <div class="d-flex align-items-center mb-3 text-white text-decoration-none border-bottom border-secondary pb-3 w-100"><span class="fs-4 fw-bold w-100 text-center">Voting System</span></div>
            <div class="py-3 border-bottom border-secondary w-100 text-center">
                <span class="d-block fw-bold fs-5 mb-1"><?php echo $_SESSION['full_name']; ?></span>
                <span class="badge bg-secondary px-3 py-2"><?php echo strtoupper($_SESSION['role']); ?></span>
            </div>
            <ul class="nav nav-pills flex-column mb-auto mt-3 w-100 gap-1">
                <li class="nav-item"><a href="dashboard.php" class="nav-link text-white <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?> px-3 py-2">Dashboard</a></li>
                <li class="nav-item"><a href="users.php" class="nav-link text-white <?php echo ($current_page === 'users.php') ? 'active' : ''; ?> px-3 py-2">Users</a></li>
                <li class="nav-item"><a href="logs.php" class="nav-link text-white <?php echo ($current_page === 'logs.php') ? 'active' : ''; ?> px-3 py-2">Logs</a></li>
                <li class="nav-item"><a href="voters.php" class="nav-link text-white <?php echo ($current_page === 'voters.php') ? 'active' : ''; ?> px-3 py-2">Voters</a></li>
                <li class="nav-item"><a href="candidates.php" class="nav-link text-white <?php echo ($current_page === 'candidates.php') ? 'active' : ''; ?> px-3 py-2">Candidates</a></li>
                <li class="nav-item"><a href="positions.php" class="nav-link text-white <?php echo ($current_page === 'positions.php') ? 'active' : ''; ?> px-3 py-2">Positions</a></li>
                <li class="nav-item"><a href="elections.php" class="nav-link text-white <?php echo ($current_page === 'elections.php') ? 'active' : ''; ?> px-3 py-2">Elections</a></li>
                <li class="nav-item"><a href="votes.php" class="nav-link text-white <?php echo ($current_page === 'votes.php') ? 'active' : ''; ?> px-3 py-2">Votes</a></li>
                <li class="nav-item"><a href="vote_counts.php" class="nav-link text-white <?php echo ($current_page === 'vote_counts.php') ? 'active' : ''; ?> px-3 py-2">Vote Counts</a></li>
            </ul>
            <div class="mt-auto w-100 border-top border-secondary pt-3">
                <a href="../logout.php" class="nav-link text-danger fw-bold px-3 py-2 text-center w-100">Logout</a>
            </div>
        </aside>
        <div class="d-flex flex-column flex-grow-1 overflow-auto bg-light">
            <header class="d-flex justify-content-between align-items-center px-4 py-3 bg-white border-bottom shadow-sm">
                <h1 class="page-title fs-4 mb-0"><?php echo $page_title; ?></h1>
                <div class="text-muted"><span>Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span></div>
            </header>
            <main class="p-4 flex-grow-1">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo $page_title; ?></h2>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Users</div>
            <div class="fs-2 fw-bold"><?php echo $users_count; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Voters</div>
            <div class="fs-2 fw-bold"><?php echo $voters_count; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Candidates</div>
            <div class="fs-2 fw-bold"><?php echo $candidates_count; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Elections</div>
            <div class="fs-2 fw-bold"><?php echo $elections_count; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Votes Cast</div>
            <div class="fs-2 fw-bold"><?php echo $votes_count; ?></div>
        </div>
    </div>
</div>

<h3 style="margin: 24px 0 12px;">Recent Activity</h3>
<div class="table-responsive bg-white rounded shadow-sm border mb-4">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_logs as $log) { ?>
            <tr>
                <td><?php echo $log['full_name']; ?></td>
                <td><?php echo $log['action']; ?></td>
                <td><?php echo $log['datetime']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
</div>

<div class="dashboard-stats">
    <div class="stat-item">
        <div class="stat-label">Users</div>
        <div class="stat-value"><?php echo $users_count; ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Voters</div>
        <div class="stat-value"><?php echo $voters_count; ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Candidates</div>
        <div class="stat-value"><?php echo $candidates_count; ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Elections</div>
        <div class="stat-value"><?php echo $elections_count; ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Votes Cast</div>
        <div class="stat-value"><?php echo $votes_count; ?></div>
    </div>
</div>

<h3 style="margin: 24px 0 12px;">Recent Activity</h3>
<div class="table-container">
    <table class="data-table">
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

<?php require_once '../includes/footer.php'; ?>

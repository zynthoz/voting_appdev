<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Logs';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'l.datetime';
$allowed_cols = ['l.datetime', 'u.full_name', 'l.action'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_cols)) {
    $sort_col = $_GET['sort'];
}

$sort_dir = 'DESC';
if (isset($_GET['dir']) && $_GET['dir'] === 'ASC') {
    $sort_dir = 'ASC';
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT l.log_id, u.full_name, l.action, l.datetime FROM tbl_logs l JOIN tbl_users u ON l.user_id = u.user_id WHERE u.full_name LIKE ? OR l.action LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 'ss', $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$logs = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
</div>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search logs..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=u.full_name&dir=<?php echo ($sort_col === 'u.full_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">User</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=l.action&dir=<?php echo ($sort_col === 'l.action' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Action</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=l.datetime&dir=<?php echo ($sort_col === 'l.datetime' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Date &amp; Time</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log) { ?>
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

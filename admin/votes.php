<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Votes';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'v.vote_timestamp';
$allowed_cols = ['v.vote_timestamp', 'vt.voter_name', 'c.candidate_name'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_cols)) {
    $sort_col = $_GET['sort'];
}

$sort_dir = 'DESC';
if (isset($_GET['dir']) && $_GET['dir'] === 'ASC') {
    $sort_dir = 'ASC';
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT v.vote_id, vt.voter_name, c.candidate_name, v.vote_timestamp FROM tbl_votes v JOIN tbl_voters vt ON v.voter_id = vt.voter_id JOIN tbl_candidates c ON v.candidate_id = c.candidate_id WHERE vt.voter_name LIKE ? OR c.candidate_name LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 'ss', $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$votes = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
</div>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search by voter or candidate..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=vt.voter_name&dir=<?php echo ($sort_col === 'vt.voter_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Voter</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=c.candidate_name&dir=<?php echo ($sort_col === 'c.candidate_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Candidate</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=v.vote_timestamp&dir=<?php echo ($sort_col === 'v.vote_timestamp' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Timestamp</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($votes as $vote) { ?>
            <tr>
                <td><?php echo $vote['voter_name']; ?></td>
                <td><?php echo $vote['candidate_name']; ?></td>
                <td><?php echo $vote['vote_timestamp']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>

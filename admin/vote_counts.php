<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Vote Counts';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'c.candidate_name';
$allowed_cols = ['c.candidate_name', 'e.election_name', 'vc.vote_count'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_cols)) {
    $sort_col = $_GET['sort'];
}

$sort_dir = 'DESC';
if (isset($_GET['dir']) && $_GET['dir'] === 'ASC') {
    $sort_dir = 'ASC';
}

// Fetch candidates and elections for dropdowns
$candidates_result = mysqli_query($conn, "SELECT candidate_id, candidate_name FROM tbl_candidates ORDER BY candidate_name ASC");
$candidates_list = mysqli_fetch_all($candidates_result, MYSQLI_ASSOC);

$elections_result = mysqli_query($conn, "SELECT election_id, election_name FROM tbl_elections ORDER BY election_date DESC");
$elections_list = mysqli_fetch_all($elections_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $candidate_id = $_POST['candidate_id'];
        $election_id  = $_POST['election_id'];
        $vote_count   = $_POST['vote_count'];

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_vote_counts (candidate_id, election_id, vote_count) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iii', $candidate_id, $election_id, $vote_count);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Added vote count for candidate ID: $candidate_id, election ID: $election_id");
        $message = 'Vote count added successfully.';

    } else if ($action === 'edit') {
        $vote_count_id = $_POST['vote_count_id'];
        $candidate_id  = $_POST['candidate_id'];
        $election_id   = $_POST['election_id'];
        $vote_count    = $_POST['vote_count'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_vote_counts SET candidate_id=?, election_id=?, vote_count=? WHERE vote_count_id=?");
        mysqli_stmt_bind_param($stmt, 'iiii', $candidate_id, $election_id, $vote_count, $vote_count_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited vote count ID: $vote_count_id");
        $message = 'Vote count updated successfully.';

    } else if ($action === 'delete') {
        $vote_count_id = $_POST['vote_count_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_vote_counts WHERE vote_count_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $vote_count_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted vote count ID: $vote_count_id");
        $message = 'Vote count deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT vc.vote_count_id, c.candidate_name, e.election_name, vc.vote_count, vc.candidate_id, vc.election_id FROM tbl_vote_counts vc JOIN tbl_candidates c ON vc.candidate_id = c.candidate_id JOIN tbl_elections e ON vc.election_id = e.election_id WHERE c.candidate_name LIKE ? OR e.election_name LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 'ss', $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vote_counts = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add Vote Count</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search by candidate or election..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=c.candidate_name&dir=<?php echo ($sort_col === 'c.candidate_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Candidate</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=e.election_name&dir=<?php echo ($sort_col === 'e.election_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Election</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=vc.vote_count&dir=<?php echo ($sort_col === 'vc.vote_count' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Vote Count</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vote_counts as $vc) { ?>
            <tr>
                <td><?php echo $vc['candidate_name']; ?></td>
                <td><?php echo $vc['election_name']; ?></td>
                <td><?php echo $vc['vote_count']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($vc)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this vote count?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="vote_count_id" value="<?php echo $vc['vote_count_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-title">Add Vote Count</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Candidate</label>
                    <select name="candidate_id" class="select" required>
                        <?php foreach ($candidates_list as $c) { ?>
                        <option value="<?php echo $c['candidate_id']; ?>"><?php echo $c['candidate_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Election</label>
                    <select name="election_id" class="select" required>
                        <?php foreach ($elections_list as $e) { ?>
                        <option value="<?php echo $e['election_id']; ?>"><?php echo $e['election_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vote Count</label>
                    <input type="number" name="vote_count" class="input" value="0" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-title">Edit Vote Count</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="vote_count_id" id="edit_vote_count_id">
                <div class="form-group">
                    <label class="form-label">Candidate</label>
                    <select name="candidate_id" id="edit_candidate_id" class="select" required>
                        <?php foreach ($candidates_list as $c) { ?>
                        <option value="<?php echo $c['candidate_id']; ?>"><?php echo $c['candidate_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Election</label>
                    <select name="election_id" id="edit_election_id" class="select" required>
                        <?php foreach ($elections_list as $e) { ?>
                        <option value="<?php echo $e['election_id']; ?>"><?php echo $e['election_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vote Count</label>
                    <input type="number" name="vote_count" id="edit_vote_count" class="input" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(vc) {
    document.getElementById('edit_vote_count_id').value  = vc.vote_count_id;
    document.getElementById('edit_candidate_id').value   = vc.candidate_id;
    document.getElementById('edit_election_id').value    = vc.election_id;
    document.getElementById('edit_vote_count').value     = vc.vote_count;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Elections';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'election_date';
$allowed_cols = ['election_name', 'election_date'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_cols)) {
    $sort_col = $_GET['sort'];
}

$sort_dir = 'DESC';
if (isset($_GET['dir']) && $_GET['dir'] === 'ASC') {
    $sort_dir = 'ASC';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $election_name = $_POST['election_name'];
        $election_date = $_POST['election_date'];

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_elections (election_name, election_date) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $election_name, $election_date);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Added election: $election_name");
        $message = 'Election added successfully.';

    } else if ($action === 'edit') {
        $election_id   = $_POST['election_id'];
        $election_name = $_POST['election_name'];
        $election_date = $_POST['election_date'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_elections SET election_name=?, election_date=? WHERE election_id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $election_name, $election_date, $election_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited election ID: $election_id");
        $message = 'Election updated successfully.';

    } else if ($action === 'delete') {
        $election_id = $_POST['election_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_elections WHERE election_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $election_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted election ID: $election_id");
        $message = 'Election deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT election_id, election_name, election_date FROM tbl_elections WHERE election_name LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$elections = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add Election</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search elections..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=election_name&dir=<?php echo ($sort_col === 'election_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Election Name</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=election_date&dir=<?php echo ($sort_col === 'election_date' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Date</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($elections as $election) { ?>
            <tr>
                <td><?php echo $election['election_name']; ?></td>
                <td><?php echo $election['election_date']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($election)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this election?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="election_id" value="<?php echo $election['election_id']; ?>">
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
            <span class="modal-title">Add Election</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Election Name</label>
                    <input type="text" name="election_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Election Date</label>
                    <input type="date" name="election_date" class="input" required>
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
            <span class="modal-title">Edit Election</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="election_id" id="edit_election_id">
                <div class="form-group">
                    <label class="form-label">Election Name</label>
                    <input type="text" name="election_name" id="edit_election_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Election Date</label>
                    <input type="date" name="election_date" id="edit_election_date" class="input" required>
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
function openEditModal(election) {
    document.getElementById('edit_election_id').value   = election.election_id;
    document.getElementById('edit_election_name').value = election.election_name;
    document.getElementById('edit_election_date').value = election.election_date;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

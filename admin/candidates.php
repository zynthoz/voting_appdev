<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Candidates';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'candidate_name';
$allowed_cols = ['candidate_name', 'party_affiliation', 'election_position'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_cols)) {
    $sort_col = $_GET['sort'];
}

$sort_dir = 'ASC';
if (isset($_GET['dir']) && $_GET['dir'] === 'DESC') {
    $sort_dir = 'DESC';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $candidate_name    = $_POST['candidate_name'];
        $party_affiliation = $_POST['party_affiliation'];
        $election_position = $_POST['election_position'];

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_candidates (candidate_name, party_affiliation, election_position) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $candidate_name, $party_affiliation, $election_position);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Added candidate: $candidate_name");
        $message = 'Candidate added successfully.';

    } else if ($action === 'edit') {
        $candidate_id      = $_POST['candidate_id'];
        $candidate_name    = $_POST['candidate_name'];
        $party_affiliation = $_POST['party_affiliation'];
        $election_position = $_POST['election_position'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_candidates SET candidate_name=?, party_affiliation=?, election_position=? WHERE candidate_id=?");
        mysqli_stmt_bind_param($stmt, 'sssi', $candidate_name, $party_affiliation, $election_position, $candidate_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited candidate ID: $candidate_id");
        $message = 'Candidate updated successfully.';

    } else if ($action === 'delete') {
        $candidate_id = $_POST['candidate_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_candidates WHERE candidate_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $candidate_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted candidate ID: $candidate_id");
        $message = 'Candidate deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT candidate_id, candidate_name, party_affiliation, election_position FROM tbl_candidates WHERE candidate_name LIKE ? OR party_affiliation LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 'ss', $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add Candidate</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search candidates..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=candidate_name&dir=<?php echo ($sort_col === 'candidate_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Name</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=party_affiliation&dir=<?php echo ($sort_col === 'party_affiliation' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Party</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=election_position&dir=<?php echo ($sort_col === 'election_position' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Position</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate) { ?>
            <tr>
                <td><?php echo $candidate['candidate_name']; ?></td>
                <td><?php echo $candidate['party_affiliation']; ?></td>
                <td><?php echo $candidate['election_position']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($candidate)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this candidate?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
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
            <span class="modal-title">Add Candidate</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Candidate Name</label>
                    <input type="text" name="candidate_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Party Affiliation</label>
                    <input type="text" name="party_affiliation" class="input">
                </div>
                <div class="form-group">
                    <label class="form-label">Election Position</label>
                    <input type="text" name="election_position" class="input">
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
            <span class="modal-title">Edit Candidate</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="candidate_id" id="edit_candidate_id">
                <div class="form-group">
                    <label class="form-label">Candidate Name</label>
                    <input type="text" name="candidate_name" id="edit_candidate_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Party Affiliation</label>
                    <input type="text" name="party_affiliation" id="edit_party_affiliation" class="input">
                </div>
                <div class="form-group">
                    <label class="form-label">Election Position</label>
                    <input type="text" name="election_position" id="edit_election_position" class="input">
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
function openEditModal(candidate) {
    document.getElementById('edit_candidate_id').value        = candidate.candidate_id;
    document.getElementById('edit_candidate_name').value      = candidate.candidate_name;
    document.getElementById('edit_party_affiliation').value   = candidate.party_affiliation;
    document.getElementById('edit_election_position').value   = candidate.election_position;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

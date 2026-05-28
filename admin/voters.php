<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Voters';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'date_of_birth';
$allowed_cols = ['voter_name', 'date_of_birth', 'gender', 'contact_information'];
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
        $voter_name          = $_POST['voter_name'];
        $date_of_birth       = $_POST['date_of_birth'];
        $gender              = $_POST['gender'];
        $contact_information = $_POST['contact_information'];

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_voters (voter_name, date_of_birth, gender, contact_information) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $voter_name, $date_of_birth, $gender, $contact_information);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Added voter: $voter_name");
        $message = 'Voter added successfully.';

    } else if ($action === 'edit') {
        $voter_id            = $_POST['voter_id'];
        $voter_name          = $_POST['voter_name'];
        $date_of_birth       = $_POST['date_of_birth'];
        $gender              = $_POST['gender'];
        $contact_information = $_POST['contact_information'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_voters SET voter_name=?, date_of_birth=?, gender=?, contact_information=? WHERE voter_id=?");
        mysqli_stmt_bind_param($stmt, 'ssssi', $voter_name, $date_of_birth, $gender, $contact_information, $voter_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited voter ID: $voter_id");
        $message = 'Voter updated successfully.';

    } else if ($action === 'delete') {
        $voter_id = $_POST['voter_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_voters WHERE voter_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $voter_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted voter ID: $voter_id");
        $message = 'Voter deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT voter_id, voter_name, date_of_birth, gender, contact_information FROM tbl_voters WHERE voter_name LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$voters = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add Voter</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search voters..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=voter_name&dir=<?php echo ($sort_col === 'voter_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Name</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=date_of_birth&dir=<?php echo ($sort_col === 'date_of_birth' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Date of Birth</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=gender&dir=<?php echo ($sort_col === 'gender' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Gender</a></th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($voters as $voter) { ?>
            <tr>
                <td><?php echo $voter['voter_name']; ?></td>
                <td><?php echo $voter['date_of_birth']; ?></td>
                <td><?php echo $voter['gender']; ?></td>
                <td><?php echo $voter['contact_information']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($voter)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this voter?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="voter_id" value="<?php echo $voter['voter_id']; ?>">
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
            <span class="modal-title">Add Voter</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Voter Name</label>
                    <input type="text" name="voter_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="select">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Information</label>
                    <input type="text" name="contact_information" class="input">
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
            <span class="modal-title">Edit Voter</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="voter_id" id="edit_voter_id">
                <div class="form-group">
                    <label class="form-label">Voter Name</label>
                    <input type="text" name="voter_name" id="edit_voter_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="edit_date_of_birth" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" id="edit_gender" class="select">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Information</label>
                    <input type="text" name="contact_information" id="edit_contact_information" class="input">
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
function openEditModal(voter) {
    document.getElementById('edit_voter_id').value           = voter.voter_id;
    document.getElementById('edit_voter_name').value         = voter.voter_name;
    document.getElementById('edit_date_of_birth').value      = voter.date_of_birth;
    document.getElementById('edit_gender').value             = voter.gender;
    document.getElementById('edit_contact_information').value = voter.contact_information;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

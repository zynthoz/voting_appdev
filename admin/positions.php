<?php
require_once '../includes/auth.php';
check_role('admin');

$page_title = 'Positions';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'position_name';
$allowed_cols = ['position_name', 'description'];
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
        $position_name = $_POST['position_name'];
        $description   = $_POST['description'];

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_positions (position_name, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $position_name, $description);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Added position: $position_name");
        $message = 'Position added successfully.';

    } else if ($action === 'edit') {
        $position_id   = $_POST['position_id'];
        $position_name = $_POST['position_name'];
        $description   = $_POST['description'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_positions SET position_name=?, description=? WHERE position_id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $position_name, $description, $position_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited position ID: $position_id");
        $message = 'Position updated successfully.';

    } else if ($action === 'delete') {
        $position_id = $_POST['position_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_positions WHERE position_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $position_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted position ID: $position_id");
        $message = 'Position deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT position_id, position_name, description FROM tbl_positions WHERE position_name LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$positions = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add Position</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search positions..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=position_name&dir=<?php echo ($sort_col === 'position_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Position Name</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=description&dir=<?php echo ($sort_col === 'description' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Description</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($positions as $position) { ?>
            <tr>
                <td><?php echo $position['position_name']; ?></td>
                <td><?php echo $position['description']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($position)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this position?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="position_id" value="<?php echo $position['position_id']; ?>">
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
            <span class="modal-title">Add Position</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Position Name</label>
                    <input type="text" name="position_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="textarea" rows="3"></textarea>
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
            <span class="modal-title">Edit Position</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="position_id" id="edit_position_id">
                <div class="form-group">
                    <label class="form-label">Position Name</label>
                    <input type="text" name="position_name" id="edit_position_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_description" class="textarea" rows="3"></textarea>
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
function openEditModal(position) {
    document.getElementById('edit_position_id').value   = position.position_id;
    document.getElementById('edit_position_name').value = position.position_name;
    document.getElementById('edit_description').value   = position.description;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

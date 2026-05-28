<?php
require_once '../includes/auth.php';
check_role('admin');
require_once '../includes/mailer.php';

$page_title = 'Users';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sort_col = 'full_name';
$allowed_cols = ['full_name', 'username', 'role', 'email'];
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
        $full_name = $_POST['full_name'];
        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $role      = $_POST['role'];
        $email     = $_POST['email'];
        $hashed    = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (full_name, username, password, role, email) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $full_name, $username, $hashed, $role, $email);
        mysqli_stmt_execute($stmt);
        $new_id = mysqli_insert_id($conn);

        log_action($_SESSION['user_id'], "Added user: $username (role: $role)");

        if ($role === 'voter') {
            $subject = 'Welcome to the Election Voting System';
            $body    = "<p>Hello $full_name,</p><p>Your account has been created.</p><p>Username: $username<br>Password: $password</p>";
            send_email($email, $full_name, $subject, $body);
        }

        $message = 'User added successfully.';

    } else if ($action === 'edit') {
        $user_id   = $_POST['user_id'];
        $full_name = $_POST['full_name'];
        $username  = $_POST['username'];
        $role      = $_POST['role'];
        $email     = $_POST['email'];

        if ($_POST['password'] !== '') {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET full_name=?, username=?, password=?, role=?, email=? WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, 'sssssi', $full_name, $username, $hashed, $role, $email, $user_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET full_name=?, username=?, role=?, email=? WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, 'ssssi', $full_name, $username, $role, $email, $user_id);
        }
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Edited user ID: $user_id");
        $message = 'User updated successfully.';

    } else if ($action === 'delete') {
        $user_id = $_POST['user_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_users WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);

        log_action($_SESSION['user_id'], "Deleted user ID: $user_id");
        $message = 'User deleted successfully.';
    }
}

$search_param = "%$search%";
$stmt = mysqli_prepare($conn, "SELECT user_id, full_name, username, role, email FROM tbl_users WHERE full_name LIKE ? OR username LIKE ? ORDER BY $sort_col $sort_dir");
mysqli_stmt_bind_param($stmt, 'ss', $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">Add User</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-container">
    <div class="table-header-tools">
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" class="input" placeholder="Search users..." value="<?php echo $search; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_col; ?>">
            <input type="hidden" name="dir" value="<?php echo $sort_dir; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?search=<?php echo $search; ?>&sort=full_name&dir=<?php echo ($sort_col === 'full_name' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Full Name</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=username&dir=<?php echo ($sort_col === 'username' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Username</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=role&dir=<?php echo ($sort_col === 'role' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Role</a></th>
                <th><a href="?search=<?php echo $search; ?>&sort=email&dir=<?php echo ($sort_col === 'email' && $sort_dir === 'ASC') ? 'DESC' : 'ASC'; ?>">Email</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
            <tr>
                <td><?php echo $user['full_name']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                <td><?php echo $user['email']; ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
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
            <span class="modal-title">Add User</span>
            <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="select">
                        <option value="admin">Admin</option>
                        <option value="organizer">Organizer</option>
                        <option value="voter">Voter</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="input" required>
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
            <span class="modal-title">Edit User</span>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="edit_username" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="input">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" id="edit_role" class="select">
                        <option value="admin">Admin</option>
                        <option value="organizer">Organizer</option>
                        <option value="voter">Voter</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit_email" class="input" required>
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
function openEditModal(user) {
    document.getElementById('edit_user_id').value  = user.user_id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_username').value  = user.username;
    document.getElementById('edit_role').value      = user.role;
    document.getElementById('edit_email').value     = user.email;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

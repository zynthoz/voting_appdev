<?php
require_once '../includes/auth.php';
check_role('organizer');

$page_title = 'Voters';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
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
$stmt = mysqli_prepare($conn, "SELECT voter_id, voter_name, date_of_birth, gender, contact_information FROM tbl_voters WHERE voter_name LIKE ? ORDER BY date_of_birth DESC");
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$voters = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Voter</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-responsive bg-white rounded shadow-sm border mb-4">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
        <form method="GET" action="" class="d-flex gap-2 w-100" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Search voters..." value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
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

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Add Voter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label class="form-label">Voter Name</label>
                    <input type="text" name="voter_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Information</label>
                    <input type="text" name="contact_information" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Voter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="voter_id" id="edit_voter_id">
                <div class="mb-3">
                    <label class="form-label">Voter Name</label>
                    <input type="text" name="voter_name" id="edit_voter_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" id="edit_gender" class="form-select">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Information</label>
                    <input type="text" name="contact_information" id="edit_contact_information" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
function openEditModal(voter) {
    document.getElementById('edit_voter_id').value           = voter.voter_id;
    document.getElementById('edit_voter_name').value         = voter.voter_name;
    document.getElementById('edit_date_of_birth').value      = voter.date_of_birth;
    document.getElementById('edit_gender').value             = voter.gender;
    document.getElementById('edit_contact_information').value = voter.contact_information;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

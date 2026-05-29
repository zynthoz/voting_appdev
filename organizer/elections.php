<?php
require_once '../includes/auth.php';
check_role('organizer');

$page_title = 'Elections';
$message = '';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
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
$stmt = mysqli_prepare($conn, "SELECT election_id, election_name, election_date FROM tbl_elections WHERE election_name LIKE ? ORDER BY election_date DESC");
mysqli_stmt_bind_param($stmt, 's', $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$elections = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Election</button>
</div>

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="table-responsive bg-white rounded shadow-sm border mb-4">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
        <form method="GET" action="" class="d-flex gap-2 w-100" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Search elections..." value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        </form>
    </div>
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>Election Name</th>
                <th>Date</th>
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

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Add Election</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label class="form-label">Election Name</label>
                    <input type="text" name="election_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Election Date</label>
                    <input type="date" name="election_date" class="form-control" required>
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
            <h5 class="modal-title">Edit Election</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="election_id" id="edit_election_id">
                <div class="mb-3">
                    <label class="form-label">Election Name</label>
                    <input type="text" name="election_name" id="edit_election_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Election Date</label>
                    <input type="date" name="election_date" id="edit_election_date" class="form-control" required>
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
function openEditModal(election) {
    document.getElementById('edit_election_id').value   = election.election_id;
    document.getElementById('edit_election_name').value = election.election_name;
    document.getElementById('edit_election_date').value = election.election_date;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

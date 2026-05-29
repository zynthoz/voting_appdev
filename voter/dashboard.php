<?php
require_once '../includes/auth.php';
check_role('voter');

$page_title = 'Dashboard';


$election_result = mysqli_query($conn, "SELECT election_id, election_name, election_date FROM tbl_elections ORDER BY election_date DESC LIMIT 1");
$current_election = mysqli_fetch_assoc($election_result);


$has_voted = false;
$vote_summary = [];
if ($current_election) {
    $user_id = $_SESSION['user_id'];
    
    $voter_stmt = mysqli_prepare($conn, "SELECT voter_id FROM tbl_voters WHERE voter_name = ?");
    $full_name = $_SESSION['full_name'];
    mysqli_stmt_bind_param($voter_stmt, 's', $full_name);
    mysqli_stmt_execute($voter_stmt);
    $voter_result = mysqli_stmt_get_result($voter_stmt);
    $voter = mysqli_fetch_assoc($voter_result);

    if ($voter) {
        $vote_check = mysqli_prepare($conn, "SELECT v.vote_id, c.candidate_name, c.election_position FROM tbl_votes v JOIN tbl_candidates c ON v.candidate_id = c.candidate_id WHERE v.voter_id = ?");
        mysqli_stmt_bind_param($vote_check, 'i', $voter['voter_id']);
        mysqli_stmt_execute($vote_check);
        $vote_result = mysqli_stmt_get_result($vote_check);
        $vote_summary = mysqli_fetch_all($vote_result, MYSQLI_ASSOC);
        if (count($vote_summary) > 0) {
            $has_voted = true;
        }
    }
}

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
                    <li class="nav-item"><a href="candidates.php" class="nav-link text-white <?php echo ($current_page === 'candidates.php') ? 'active' : ''; ?> px-3 py-2">Candidates</a></li>
                    <li class="nav-item"><a href="vote.php" class="nav-link text-white <?php echo ($current_page === 'vote.php') ? 'active' : ''; ?> px-3 py-2">Cast Vote</a></li>
                    <li class="nav-item"><a href="results.php" class="nav-link text-white <?php echo ($current_page === 'results.php') ? 'active' : ''; ?> px-3 py-2">Results</a></li>
                </ul>
            <div class="mt-auto w-100 border-top border-secondary pt-3">
                <a href="../logout.php" class="nav-link text-danger fw-bold px-3 py-2 text-center w-100">Logout</a>
            </div>
        </aside>
        <div class="d-flex flex-column flex-grow-1 overflow-auto bg-light">
            <header class="d-flex justify-content-between align-items-center px-4 py-3 bg-white border-bottom shadow-sm">
                <h1 class="page-title"><?php echo $page_title; ?></h1>
                <div class="text-muted"><span>Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span></div>
            </header>
            <main class="p-4 flex-grow-1">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>
</div>

<?php if ($current_election) { ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Current Election</div>
            <div class="fs-2 fw-bold"><?php echo $current_election['election_name']; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Election Date</div>
            <div class="fs-2 fw-bold"><?php echo $current_election['election_date']; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-4 h-100">
            <div class="text-muted mb-2">Vote Status</div>
            <div class="fs-2 fw-bold"><?php if ($has_voted) { echo '✅ Voted'; } else { echo '⏳ Not Yet'; } ?></div>
        </div>
    </div>
</div>

<div class="quick-actions-panel">
    <a href="candidates.php" class="btn btn-secondary">View Candidates</a>
    <?php if (!$has_voted) { ?>
        <a href="vote.php" class="btn btn-primary">Cast Your Vote</a>
    <?php } ?>
    <a href="results.php" class="btn btn-secondary">View Results</a>
</div>

<?php if ($has_voted) { ?>
<h3 class="section-title">Your Vote Summary</h3>
<div class="table-responsive bg-white rounded shadow-sm border mb-4">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>Position</th>
                <th>Candidate</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vote_summary as $vs) { ?>
            <tr>
                <td><?php echo $vs['election_position']; ?></td>
                <td><?php echo $vs['candidate_name']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php } else { ?>
<div class="alert">No active elections at this time.</div>
<?php } ?>

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

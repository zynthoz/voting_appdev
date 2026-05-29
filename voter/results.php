<?php
require_once '../includes/auth.php';
check_role('voter');

$page_title = 'Results';


$election_result = mysqli_query($conn, "SELECT election_id, election_name, election_date FROM tbl_elections ORDER BY election_date DESC LIMIT 1");
$current_election = mysqli_fetch_assoc($election_result);

$results_by_position = [];
if ($current_election) {
    $stmt = mysqli_prepare($conn, "SELECT c.candidate_name, c.party_affiliation, c.election_position, vc.vote_count FROM tbl_vote_counts vc JOIN tbl_candidates c ON vc.candidate_id = c.candidate_id WHERE vc.election_id = ? ORDER BY c.election_position ASC, vc.vote_count DESC");
    mysqli_stmt_bind_param($stmt, 'i', $current_election['election_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $all_results = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($all_results as $row) {
        $results_by_position[$row['election_position']][] = $row;
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
    <h2><?php echo $page_title; ?></h2>
</div>

<?php if ($current_election) { ?>
<div class="mb-5 border-bottom pb-3">
    <h3 class="fw-bold text-primary"><?php echo $current_election['election_name']; ?></h3>
    <p class="text-muted mb-0">Election Date: <?php echo date('F j, Y', strtotime($current_election['election_date'])); ?></p>
</div>

<?php if (count($results_by_position) === 0) { ?>
    <div class="alert">No results available yet.</div>
<?php } ?>

<?php foreach ($results_by_position as $position => $candidates) { ?>
    <?php
    
    $max_votes = 0;
    foreach ($candidates as $c) {
        if ($c['vote_count'] > $max_votes) {
            $max_votes = $c['vote_count'];
        }
    }
    ?>
    <div class="mb-5">
        <h4 class="mb-3 border-start border-primary border-4 ps-3"><?php echo $position; ?></h4>
        <?php foreach ($candidates as $index => $candidate) { ?>
            <?php
            $bar_width = 0;
            if ($max_votes > 0) {
                $bar_width = ($candidate['vote_count'] / $max_votes) * 100;
            }
            $is_winner = ($index === 0 && $candidate['vote_count'] > 0);
            ?>
            <div class="card mb-3 shadow-sm <?php echo $is_winner ? 'border-success border-2 bg-success bg-opacity-10' : ''; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                <strong><?php echo $candidate['candidate_name']; ?></strong>
                            </h5>
                            <div class="text-muted small mb-2"><?php echo $candidate['party_affiliation']; ?></div>
                        </div>
                        <div class="text-end">
                            <span class="fs-4 fw-bold <?php echo $is_winner ? 'text-success' : 'text-primary'; ?>">
                                <?php echo $candidate['vote_count']; ?>
                            </span>
                            <span class="text-muted small d-block" style="margin-top: -5px;">votes</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<?php } else { ?>
    <div class="alert">No elections found.</div>
<?php } ?>

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

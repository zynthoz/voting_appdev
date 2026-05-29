<?php
require_once '../includes/auth.php';
check_role('voter');

$page_title = 'Cast Vote';
$message = '';
$has_voted = false;
$vote_summary = [];


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


$positions_result = mysqli_query($conn, "SELECT DISTINCT election_position FROM tbl_candidates ORDER BY election_position ASC");
$positions = mysqli_fetch_all($positions_result, MYSQLI_ASSOC);

$candidates_by_position = [];
foreach ($positions as $pos) {
    $pos_name = $pos['election_position'];
    $cand_stmt = mysqli_prepare($conn, "SELECT candidate_id, candidate_name, party_affiliation FROM tbl_candidates WHERE election_position = ? ORDER BY candidate_name ASC");
    mysqli_stmt_bind_param($cand_stmt, 's', $pos_name);
    mysqli_stmt_execute($cand_stmt);
    $cand_result = mysqli_stmt_get_result($cand_stmt);
    $candidates_by_position[$pos_name] = mysqli_fetch_all($cand_result, MYSQLI_ASSOC);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$has_voted && $voter) {
    $voter_id = $voter['voter_id'];

    foreach ($positions as $pos) {
        $pos_name = $pos['election_position'];
        $field_name = 'vote_' . md5($pos_name);

        if (isset($_POST[$field_name])) {
            $candidate_id = $_POST[$field_name];

            
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_votes (voter_id, candidate_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'ii', $voter_id, $candidate_id);
            mysqli_stmt_execute($stmt);

            
            $election_result = mysqli_query($conn, "SELECT election_id FROM tbl_elections ORDER BY election_date DESC LIMIT 1");
            $election = mysqli_fetch_assoc($election_result);

            if ($election) {
                
                $vc_check = mysqli_prepare($conn, "SELECT vote_count_id FROM tbl_vote_counts WHERE candidate_id = ? AND election_id = ?");
                mysqli_stmt_bind_param($vc_check, 'ii', $candidate_id, $election['election_id']);
                mysqli_stmt_execute($vc_check);
                $vc_result = mysqli_stmt_get_result($vc_check);
                $vc_row = mysqli_fetch_assoc($vc_result);

                if ($vc_row) {
                    $vc_update = mysqli_prepare($conn, "UPDATE tbl_vote_counts SET vote_count = vote_count + 1 WHERE vote_count_id = ?");
                    mysqli_stmt_bind_param($vc_update, 'i', $vc_row['vote_count_id']);
                    mysqli_stmt_execute($vc_update);
                } else {
                    $vc_insert = mysqli_prepare($conn, "INSERT INTO tbl_vote_counts (candidate_id, election_id, vote_count) VALUES (?, ?, 1)");
                    mysqli_stmt_bind_param($vc_insert, 'ii', $candidate_id, $election['election_id']);
                    mysqli_stmt_execute($vc_insert);
                }
            }
        }
    }

    log_action($_SESSION['user_id'], "Cast vote");

    $message = 'Your vote has been cast successfully!';
    $has_voted = true;

    
    $vote_check = mysqli_prepare($conn, "SELECT v.vote_id, c.candidate_name, c.election_position FROM tbl_votes v JOIN tbl_candidates c ON v.candidate_id = c.candidate_id WHERE v.voter_id = ?");
    mysqli_stmt_bind_param($vote_check, 'i', $voter['voter_id']);
    mysqli_stmt_execute($vote_check);
    $vote_result = mysqli_stmt_get_result($vote_check);
    $vote_summary = mysqli_fetch_all($vote_result, MYSQLI_ASSOC);
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

<?php if ($message !== '') { ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<?php if (!$voter) { ?>
    <div class="alert">Your account is not linked to a voter record. Please contact an administrator.</div>
<?php } else if ($has_voted) { ?>
    <div class="alert alert-success">You have already cast your vote.</div>

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
<?php } else { ?>
    <form method="POST" action="" id="voteForm">
        <?php foreach ($candidates_by_position as $position => $candidates) { ?>
        <div class="vote-position-group">
            <h3 class="vote-position-title"><?php echo $position; ?></h3>
            <div class="vote-candidates-list">
                <?php foreach ($candidates as $candidate) { ?>
                <label class="vote-candidate-option">
                    <input type="radio" name="vote_<?php echo md5($position); ?>" value="<?php echo $candidate['candidate_id']; ?>" required>
                    <div>
                        <div class="vote-candidate-name"><?php echo $candidate['candidate_name']; ?></div>
                        <div class="vote-candidate-party"><?php echo $candidate['party_affiliation']; ?></div>
                    </div>
                </label>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="vote-confirm-box">
            <p class="vote-confirm-text">Are you sure you want to submit your vote? This action cannot be undone.</p>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Submit your vote? This cannot be undone.')">Submit Vote</button>
        </div>
    </form>
<?php } ?>

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

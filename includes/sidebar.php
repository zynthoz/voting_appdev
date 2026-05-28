<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
$full_name = $_SESSION['full_name'] ?? 'User';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title">🏛️ Voting System</div>
    </div>
    
    <div class="sidebar-user">
        <span class="sidebar-user-name"><?php echo $full_name; ?></span>
        <div>
            <span class="badge badge-<?php echo $role; ?>"><?php echo $role; ?></span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <?php if ($role === 'admin') { ?>
                <li>
                    <a href="dashboard.php" class="sidebar-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="users.php" class="sidebar-link <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
                        Users
                    </a>
                </li>
                <li>
                    <a href="logs.php" class="sidebar-link <?php echo ($current_page === 'logs.php') ? 'active' : ''; ?>">
                        Logs
                    </a>
                </li>
                <li>
                    <a href="voters.php" class="sidebar-link <?php echo ($current_page === 'voters.php') ? 'active' : ''; ?>">
                        Voters
                    </a>
                </li>
                <li>
                    <a href="candidates.php" class="sidebar-link <?php echo ($current_page === 'candidates.php') ? 'active' : ''; ?>">
                        Candidates
                    </a>
                </li>
                <li>
                    <a href="positions.php" class="sidebar-link <?php echo ($current_page === 'positions.php') ? 'active' : ''; ?>">
                        Positions
                    </a>
                </li>
                <li>
                    <a href="elections.php" class="sidebar-link <?php echo ($current_page === 'elections.php') ? 'active' : ''; ?>">
                        Elections
                    </a>
                </li>
                <li>
                    <a href="votes.php" class="sidebar-link <?php echo ($current_page === 'votes.php') ? 'active' : ''; ?>">
                        Votes
                    </a>
                </li>
                <li>
                    <a href="vote_counts.php" class="sidebar-link <?php echo ($current_page === 'vote_counts.php') ? 'active' : ''; ?>">
                        Vote Counts
                    </a>
                </li>
            <?php } else if ($role === 'organizer') { ?>
                <li>
                    <a href="dashboard.php" class="sidebar-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="voters.php" class="sidebar-link <?php echo ($current_page === 'voters.php') ? 'active' : ''; ?>">
                        Voters
                    </a>
                </li>
                <li>
                    <a href="candidates.php" class="sidebar-link <?php echo ($current_page === 'candidates.php') ? 'active' : ''; ?>">
                        Candidates
                    </a>
                </li>
                <li>
                    <a href="positions.php" class="sidebar-link <?php echo ($current_page === 'positions.php') ? 'active' : ''; ?>">
                        Positions
                    </a>
                </li>
                <li>
                    <a href="elections.php" class="sidebar-link <?php echo ($current_page === 'elections.php') ? 'active' : ''; ?>">
                        Elections
                    </a>
                </li>
                <li>
                    <a href="votes.php" class="sidebar-link <?php echo ($current_page === 'votes.php') ? 'active' : ''; ?>">
                        Votes
                    </a>
                </li>
                <li>
                    <a href="vote_counts.php" class="sidebar-link <?php echo ($current_page === 'vote_counts.php') ? 'active' : ''; ?>">
                        Vote Counts
                    </a>
                </li>
            <?php } else if ($role === 'voter') { ?>
                <li>
                    <a href="dashboard.php" class="sidebar-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="candidates.php" class="sidebar-link <?php echo ($current_page === 'candidates.php') ? 'active' : ''; ?>">
                        Candidates
                    </a>
                </li>
                <li>
                    <a href="vote.php" class="sidebar-link <?php echo ($current_page === 'vote.php') ? 'active' : ''; ?>">
                        Cast Vote
                    </a>
                </li>
                <li>
                    <a href="results.php" class="sidebar-link <?php echo ($current_page === 'results.php') ? 'active' : ''; ?>">
                        Results
                    </a>
                </li>
            <?php } ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../logout.php" class="sidebar-link" style="color: oklch(0.85 0.05 25);">
            Logout
        </a>
    </div>
</aside>

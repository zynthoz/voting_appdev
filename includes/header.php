<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - <?php echo $page_title ?? 'Portal'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-shell">
        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <?php require_once 'sidebar.php'; ?>
        
        <!-- Main Content Wrapper -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: var(--space-md);">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">☰</button>
                    <h1 class="page-title"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                </div>
                
                <div class="top-header-user">
                    <span>Welcome, <strong><?php echo $_SESSION['full_name'] ?? 'User'; ?></strong></span>
                </div>
            </header>
            
            <!-- Page Content Body -->
            <main class="content-body">

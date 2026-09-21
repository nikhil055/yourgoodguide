<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();
$admin_stats = getAdminStats($pdo);
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Dashboard' ?> - Finchskills Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-hover: #3a56d4;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #4361ee;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #334155;
        }
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
        }
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0.75rem;
            margin: 0;
        }
        .sidebar-item {
            margin-bottom: 0.35rem;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar-link.active {
            background-color: var(--sidebar-active);
            color: #fff;
        }
        .sidebar-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        .topbar-admin {
            background: #fff;
            height: 70px;
            border-bottom: 1px solid var(--card-border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .content-area {
            padding: 2rem;
            flex-grow: 1;
        }
        .stat-card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .badge-counter {
            font-size: 0.72rem;
            padding: 0.25em 0.6em;
            border-radius: 50rem;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="adminSidebar">
    <a href="index.php" class="sidebar-brand">
        <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span>Finchskills Admin</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="index.php" class="sidebar-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="courses.php" class="sidebar-link <?= in_array($current_page, ['courses.php', 'course-add.php', 'course-edit.php']) ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Manage Courses</span>
                </div>
                <?php if (isset($admin_stats['total_courses'])): ?>
                    <span class="badge bg-secondary badge-counter"><?= $admin_stats['total_courses'] ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="students.php" class="sidebar-link <?= $current_page === 'students.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-users"></i>
                    <span>Registered Candidates</span>
                </div>
                <?php if (isset($admin_stats['total_students']) && $admin_stats['total_students'] > 0): ?>
                    <span class="badge bg-primary badge-counter"><?= $admin_stats['total_students'] ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="admissions.php" class="sidebar-link <?= $current_page === 'admissions.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>Admissions</span>
                </div>
                <?php if ($admin_stats['unread_admissions'] > 0): ?>
                    <span class="badge bg-danger badge-counter"><?= $admin_stats['unread_admissions'] ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="fees.php" class="sidebar-link <?= $current_page === 'fees.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Fee Submissions</span>
                </div>
                <?php if ($admin_stats['unread_fees'] > 0): ?>
                    <span class="badge bg-danger badge-counter"><?= $admin_stats['unread_fees'] ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="contacts.php" class="sidebar-link <?= $current_page === 'contacts.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    <span>Contact Inquiries</span>
                </div>
                <?php if ($admin_stats['unread_inquiries'] > 0): ?>
                    <span class="badge bg-danger badge-counter"><?= $admin_stats['unread_inquiries'] ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="payment-settings.php" class="sidebar-link <?= $current_page === 'payment-settings.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Payment Gateway</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="smtp-settings.php" class="sidebar-link <?= $current_page === 'smtp-settings.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-envelope-circle-check"></i>
                    <span>Email &amp; SMTP</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="settings.php" class="sidebar-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Site Settings</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <a href="../index.php" target="_blank" class="sidebar-link text-info">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Visit Website</span>
                </div>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="logout.php" class="sidebar-link text-danger">
                <div class="sidebar-link-content">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </div>
            </a>
        </li>
    </ul>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">
    <!-- TOPBAR -->
    <header class="topbar-admin">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-bold text-dark"><?= $page_title ?? 'Dashboard' ?></h5>
        </div>

        <div class="d-flex align-items-center gap-4">
            <!-- Notifications Dropdown -->
            <div class="dropdown">
                <a href="#" class="position-relative text-secondary fs-5" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-regular fa-bell"></i>
                    <?php if ($admin_stats['total_unread'] > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="width: 280px;">
                    <li class="p-2 border-bottom fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <span class="badge bg-primary rounded-pill"><?= $admin_stats['total_unread'] ?> New</span>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="admissions.php">
                            <span><i class="fa-solid fa-user-graduate me-2 text-primary"></i> Admissions</span>
                            <?php if ($admin_stats['unread_admissions'] > 0): ?>
                                <span class="badge bg-danger"><?= $admin_stats['unread_admissions'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="fees.php">
                            <span><i class="fa-solid fa-receipt me-2 text-success"></i> Fee Submissions</span>
                            <?php if ($admin_stats['unread_fees'] > 0): ?>
                                <span class="badge bg-danger"><?= $admin_stats['unread_fees'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="contacts.php">
                            <span><i class="fa-solid fa-envelope me-2 text-warning"></i> Inquiries</span>
                            <?php if ($admin_stats['unread_inquiries'] > 0): ?>
                                <span class="badge bg-danger"><?= $admin_stats['unread_inquiries'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Profile dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                        <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="fw-semibold text-dark d-none d-sm-inline"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item py-2" href="settings.php"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- CONTENT AREA -->
    <main class="content-area">

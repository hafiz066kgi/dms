<?php
$user = $_SESSION['user'] ?? null;
$name = $user['name'] ?? 'Guest';
$role = $user['role'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POLYPARTS DMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="msapplication-navbutton-color" content="#1a237e">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1a237e">	
    <!-- Bootstrap CSS & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/582b39455c9f01455d3fbe61a55bd124?family=Cooper+Black+W01" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
			font-size: 15px !important;
        }
        .polyparts-logo {
            font-family: 'Cooper Black W01', 'Cooper Black', serif, sans-serif;
            color: #1a237e;
            letter-spacing: 2px;
            font-size: 1.4rem;
        }
        .navbar-nav .nav-link {
            color: #1a237e !important;
            font-weight: 500;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .navbar-nav .nav-link.active,
        .navbar-nav .nav-link:hover {
            color: #3949ab !important;
        }
        .navbar-nav .nav-link.active {
            border-bottom: 2px solid #3949ab;
        }
        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #1a237e;
            margin-right: 10px;
        }
        .dropdown-menu {
            min-width: 210px;
        }
		
		
		.terminal-footer {
		  color: #8E8E93;
		  font-size: 0.8rem;
		  position: relative;
		  width: 100%;
		  text-align: center;
		  margin-top: 16px;
		  padding-bottom: 16px;
		}
		.terminal-footer a {
		  color: #0066CC;
		  text-decoration: none;
		}
		.terminal-footer a:hover {
		  text-decoration: underline;
		}

		
		
        /* Responsive: Hide this navbar on mobile, show mobile header/nav */
        @media (max-width: 991.98px) {
            .desktop-navbar { display: none !important; }
        }
        @media (min-width: 992px) {
            .mobile-header, .bottom-nav { display: none !important; }
        }
        /* Mobile styles (unchanged, for reference) */
        .mobile-header {
            background: #1a237e;
            color: #fff;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .mobile-header .menu-btn {
            font-size: 2rem;
            border: none;
            background: transparent;
            color: #fff;
            padding: 0 0.5rem;
        }
        .mobile-header .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            color: #1a237e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: bold;
            margin-left: 0.8rem;
        }
        .offcanvas-header {
            background: #1a237e;
            color: #fff;
        }
        .offcanvas-body {
            padding-top: 1rem;
        }
        .offcanvas-body .nav-link {
            color: #1a237e;
            font-size: 1.1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .offcanvas-body .nav-link.active,
        .offcanvas-body .nav-link:hover {
            background: #e3e7fa;
            color: #10287a;
        }
        .offcanvas-body .nav-link i { font-size: 1.1rem; }
        @media (max-width: 1767.98px) {
            .bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: #fff;
                box-shadow: 0 -2px 10px rgba(0,0,0,.08);
                display: flex;
                justify-content: space-around;
                align-items: center;
                z-index: 1050;
            }
            .bottom-nav a {
                color: #1a237e;
                text-align: center;
                font-size: 1.05rem;
                flex: 1;
                text-decoration: none;
                padding: 0.35rem 0;
                transition: background .2s;
                display: flex;
                flex-direction: column;
                align-items: center;
                font-weight: 500;
            }
            .bottom-nav a.active, .bottom-nav a:active {
                color: #3949ab;
                background: #f4f6fb;
            }
            body {
                padding-bottom: 60px !important;
            }
        }
		
    </style>
</head>
<body>
<?php if ($user): ?>

    <!-- DESKTOP NAVBAR HEADER (lg, xl) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 border-bottom shadow-sm desktop-navbar">
        <div class="container-fluid">
            <span class="navbar-brand polyparts-logo">
                POLYPARTS
            </span>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="index.php"
                           class="nav-link <?= (!isset($_GET['category']) && basename($_SERVER['PHP_SELF'])=='index.php')?'active':'' ?>">
                            <i class="bi bi-archive"></i> All
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?category=manuals"
                           class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='manuals')?'active':'' ?>">
                            <i class="bi bi-book"></i> Manuals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?category=procedures"
                           class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='procedures')?'active':'' ?>">
                            <i class="bi bi-file-earmark-text"></i> Procedures
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?category=work_instructions"
                           class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='work_instructions')?'active':'' ?>">
                            <i class="bi bi-list-check"></i> WI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?category=policies"
                           class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='policies')?'active':'' ?>">
                            <i class="bi bi-journal-check"></i> Policies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?category=others"
                           class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='others')?'active':'' ?>">
                            <i class="bi bi-folder2"></i> Others
                        </a>
                    </li>
                </ul>
            </div>
			<div class="d-flex align-items-center">
				<?php if ($user && is_array($user)): ?>
					<?php
					$photoPath = (!empty($user['photo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/dms/uploads/user_photos/' . $user['photo']))
						? '/dms/uploads/user_photos/' . htmlspecialchars($user['photo'])
						: 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=dee2e6&color=1a237e&size=40';
					?>
					<div class="dropdown me-2">
						<a class="d-flex align-items-center text-decoration-none dropdown-toggle"
						   href="#" id="navbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="<?= $photoPath ?>" alt="Profile"
								 style="height:36px;width:36px;object-fit:cover;border-radius:50%;border:1.5px solid #1a237e;margin-right:10px;">
							<span class="d-none d-md-block small">
								<strong class="fw-semibold"><?= htmlspecialchars($user['name'] ?? $user['username']) ?></strong><br>
								<span class="text-muted">
									<?= htmlspecialchars(ucfirst($user['role'])) ?>
									<?php if (!empty($user['department'])): ?>
										| <?= htmlspecialchars($user['department']) ?>
									<?php endif; ?>
								</span>
							</span>
						</a>
							<ul class="dropdown-menu dropdown-menu-end shadow small" aria-labelledby="navbarUserDropdown">
								<?php if ($user['role'] === 'superadmin'): ?>
									<li><a class="dropdown-item" href="/dms/public/add_admin.php"><i class="bi bi-person-plus"></i> Add Admin</a></li>
								<?php endif; ?>
								<?php if ($user['role'] === 'superadmin' || $user['role'] === 'admin'): ?>
									<li><a class="dropdown-item" href="/dms/public/manage_users.php"><i class="bi bi-people"></i> View Users</a></li>
									<li><a class="dropdown-item" href="/dms/public/log.php"><i class="bi bi-activity"></i> Activity Log</a></li>
								<?php endif; ?>
								<li><a class="dropdown-item" href="/dms/public/dashboard.php"><i class="bi bi-bar-chart-line"></i> Dashboard</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="/dms/public/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
							</ul>
					</div>
				<?php endif; ?>
			</div>
        </div>
    </nav>

    <!-- MOBILE HEADER, OFFCANVAS, BOTTOM NAV (unchanged from previous versions) -->
    <div class="mobile-header d-lg-none">
        <span class="polyparts-logo">POLYPARTS DMS</span>
        <div class="d-flex align-items-center">
            <button class="menu-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                <i class="bi bi-list"></i>
            </button>
                <img src="<?= $photoPath ?>" alt="Profile" class="profile-avatar">			
        </div>
    </div>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"> Profile</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
		<div class="offcanvas-body">
			<?php if ($user['role'] === 'superadmin'): ?>
				<a href="/dms/public/add_admin.php" class="nav-link">
					<i class="bi bi-person-plus"></i> Add Admin
				</a>
			<?php endif; ?>
			<?php if ($user['role'] === 'superadmin' || $user['role'] === 'admin'): ?>
				<a href="/dms/public/manage_users.php" class="nav-link">
					<i class="bi bi-people"></i> View Users
				</a>
				<a href="/dms/public/log.php" class="nav-link">
					<i class="bi bi-activity"></i> Activity Log
				</a>
			<?php endif; ?>
			<a href="dashboard.php" class="nav-link">
				<i class="bi bi-bar-chart-line"></i> Dashboard
			</a>
			<a href="logout.php" class="nav-link">
				<i class="bi bi-box-arrow-right"></i> Logout
			</a>
		</div>

    </div>
    <nav class="bottom-nav d-lg-none">
        <a href="index.php" class="<?= (!isset($_GET['category']) && basename($_SERVER['PHP_SELF'])=='index.php')?'active':'' ?>">
            <i class="bi bi-archive"></i>
            <div style="font-size:12px;">All</div>
        </a>
        <a href="index.php?category=manuals" class="<?= (isset($_GET['category']) && $_GET['category']=='manuals')?'active':'' ?>">
            <i class="bi bi-book"></i>
            <div style="font-size:12px;">Manuals</div>
        </a>
        <a href="index.php?category=procedures" class="<?= (isset($_GET['category']) && $_GET['category']=='procedures')?'active':'' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <div style="font-size:12px;">Procedures</div>
        </a>
        <a href="index.php?category=work_instructions" class="<?= (isset($_GET['category']) && $_GET['category']=='work_instructions')?'active':'' ?>">
            <i class="bi bi-list-check"></i>
            <div style="font-size:12px;">WI</div>
        </a>
        <a href="index.php?category=policies" class="<?= (isset($_GET['category']) && $_GET['category']=='policies')?'active':'' ?>">
            <i class="bi bi-journal-check"></i>
            <div style="font-size:12px;">Policies</div>
        </a>
        <a href="index.php?category=others" class="<?= (isset($_GET['category']) && $_GET['category']=='others')?'active':'' ?>">
            <i class="bi bi-folder2"></i>
            <div style="font-size:12px;">Others</div>
        </a>
    </nav>
<?php endif; ?>

<!-- Main Content (your page content continues here) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

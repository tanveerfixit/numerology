<?php
// includes/header.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
startSession();
$currentUser = getCurrentUser($db);

$pendingReqCount = 0;
if ($currentUser && $currentUser['role'] === 'admin') {
    try {
        $stmtCount = $db->query("SELECT COUNT(*) as cnt FROM users WHERE req_status = 'pending'");
        $pendingReqCount = (int)$stmtCount->fetch()['cnt'];
    } catch (Exception $e) {
        $pendingReqCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Huroof-e-Abjad & Geomancy'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent-color: #2563eb;
            --accent-hover: #1d4ed8;
            --gold-accent: #d97706;
            --danger-color: #dc2626;
            --success-color: #16a34a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Outfit', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.4;
        }

        header.app-header {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .logo-badge {
            background: var(--accent-color);
            color: #ffffff;
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
        }

        .header-right-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Profile Dropdown Icon Menu (Right Side) */
        .profile-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-icon-btn {
            background: #ffffff;
            border: 1px solid var(--card-border);
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.15s ease;
        }

        .profile-icon-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .profile-dropdown-menu {
            display: none;
            position: absolute;
            top: 120%;
            right: 0;
            left: auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 190px;
            z-index: 200;
            overflow: hidden;
        }

        .profile-dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: 0.6rem 0.85rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.85rem;
            color: #0f172a;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.12s ease;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
        }

        .dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0;
        }

        .logout-item {
            color: #dc2626;
        }

        .badge-count {
            background: #dc2626;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 50px;
            padding: 0.1rem 0.4rem;
        }

        .btn {
            background: #ffffff;
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.4rem 0.85rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.88rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s ease;
        }

        .btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        .btn-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: var(--danger-color);
        }

        .btn-danger:hover {
            background: #fee2e2;
        }

        .btn-sm {
            padding: 0.3rem 0.65rem;
            font-size: 0.8rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }

        .status-approved { background: #dcfce7; color: #15803d; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }

        main.container {
            flex: 1;
            width: 100%;
            padding: 1.5rem 2rem;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            header.app-header {
                padding: 0.5rem 1rem;
            }
            main.container {
                padding: 1rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <header class="app-header">
        <!-- Left Side: Heading / Logo (Navigates to Home Screen) -->
        <a href="index.php" class="logo-area">
            <span>Abjad & Geomancy</span>
            <span class="logo-badge">Numerical Wisdom</span>
        </a>
        
        <!-- Right Side: Profile Icon Dropdown Menu -->
        <div class="header-right-group">
            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-sm" style="position: relative;">
                    Admin Portal 🔔
                    <?php if ($pendingReqCount > 0): ?>
                        <span style="background: #dc2626; color: #ffffff; font-size: 0.7rem; font-weight: bold; border-radius: 50px; padding: 0.1rem 0.45rem; margin-left: 0.2rem;">
                            <?php echo $pendingReqCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <div class="profile-dropdown-wrapper">
                <button id="profileDropdownBtn" class="profile-icon-btn" aria-label="User Profile Options">
                    <span style="font-size: 1.1rem;">👤</span>
                    <?php if ($currentUser): ?>
                        <span style="font-weight: 600; font-size: 0.82rem;"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                    <?php else: ?>
                        <span style="font-weight: 600; font-size: 0.82rem;">Account</span>
                    <?php endif; ?>
                    <span style="font-size: 0.65rem; color: #64748b;">▼</span>
                </button>
                <div id="profileDropdownMenu" class="profile-dropdown-menu">
                    <?php if ($currentUser): ?>
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>
                            <span class="status-badge status-<?php echo htmlspecialchars($currentUser['status']); ?>"><?php echo htmlspecialchars($currentUser['status']); ?></span>
                        </div>
                        <a href="profile.php" class="dropdown-item">👤 My Profile</a>
                        <?php if ($currentUser && $currentUser['status'] === 'approved'): ?>
                            <a href="saved.php" class="dropdown-item">📜 Saved History Log</a>
                        <?php endif; ?>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <a href="admin.php" class="dropdown-item">
                                <span>🛡️ Admin Portal</span>
                                <?php if ($pendingReqCount > 0): ?>
                                    <span class="badge-count"><?php echo $pendingReqCount; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="api.php?action=logout" class="dropdown-item logout-item">🚪 Logout</a>
                    <?php else: ?>
                        <a href="index.php?auth=login" class="dropdown-item">🔑 Login</a>
                        <a href="index.php?auth=signup" class="dropdown-item">✨ Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('profileDropdownBtn');
            const menu = document.getElementById('profileDropdownMenu');
            if (btn && menu) {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    menu.classList.toggle('show');
                });
                document.addEventListener('click', () => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>

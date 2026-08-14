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
$elemColors = getElementColors($db);
$isStaffOrAdmin = ($currentUser && in_array($currentUser['role'], ['staff', 'admin']) && $currentUser['status'] === 'approved');
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Huroof-e-Abjad & Geomancy'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #f8fafc;
            --surface-card: #ffffff;
            --surface-subtle: #f1f5f9;
            --surface-active: #e2e8f0;
            --border-subtle: #e2e8f0;
            --border-medium: #cbd5e1;
            --border-focus: #2563eb;
            
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --primary-border: #bfdbfe;
            
            --accent-gold: #d97706;
            --accent-gold-light: #fef3c7;
            --accent-gold-border: #fde68a;
            
            /* Four Elements Dynamic Colors (Defaults: Fire=Yellow, Air=Red, Water=Blue, Earth=Black) */
            --fire-color: <?php echo htmlspecialchars($elemColors['fire']); ?>;
            --fire-bg: rgba(234, 179, 8, 0.08);
            --fire-border: rgba(234, 179, 8, 0.25);
            
            --air-color: <?php echo htmlspecialchars($elemColors['air']); ?>;
            --air-bg: rgba(220, 38, 38, 0.08);
            --air-border: rgba(220, 38, 38, 0.25);
            
            --water-color: <?php echo htmlspecialchars($elemColors['water']); ?>;
            --water-bg: rgba(37, 99, 235, 0.08);
            --water-border: rgba(37, 99, 235, 0.25);
            
            --earth-color: <?php echo htmlspecialchars($elemColors['earth']); ?>;
            --earth-bg: rgba(15, 23, 42, 0.06);
            --earth-border: rgba(15, 23, 42, 0.2);
            
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            
            --success: #16a34a;
            --success-bg: #f0fdf4;
            --success-border: #bbf7d0;

            --shadow-xs: 0 1px 2px 0 rgba(15, 23, 42, 0.04);
            --shadow-sm: 0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
            --shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.06), 0 4px 6px -4px rgba(15, 23, 42, 0.03);
            --shadow-xl: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);

            /* Radius Rules: 0 for inputs, divs, bg, cards, badges; 2px minimum for buttons */
            --radius-btn: 2px;
            --radius-sm: 0px;
            --radius-md: 0px;
            --radius-lg: 0px;
            --radius-full: 0px;
            
            --font-main: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-arabic: 'Amiri', serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            border-radius: 0;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-primary);
            font-family: var(--font-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Top Header Bar */
        header.app-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.65rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-xs);
            border-radius: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
        }

        .logo-icon-box {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
            flex-shrink: 0;
        }

        .logo-text-col {
            display: flex;
            flex-direction: column;
        }

        .logo-brand-name {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .logo-badge {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.03em;
            text-transform: uppercase;
            border-radius: 0;
        }

        /* Nav Links */
        .nav-links-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item-link {
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-btn);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.88rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease;
        }

        .nav-item-link:hover {
            color: var(--primary);
            background: var(--surface-subtle);
        }

        .nav-item-link.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 600;
        }

        /* Right Group */
        .header-right-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Buttons Design System: Minimum 2px radius */
        button, .btn {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            color: var(--text-primary);
            padding: 0.45rem 0.95rem;
            border-radius: var(--radius-btn) !important;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.88rem;
            font-family: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-xs);
            line-height: 1.3;
        }

        .btn:hover {
            background: var(--surface-subtle);
            border-color: #94a3b8;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
            border-radius: var(--radius-btn) !important;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.25);
        }

        .btn-secondary {
            background: var(--surface-subtle);
            border-color: var(--border-subtle);
            color: var(--text-primary);
            border-radius: var(--radius-btn) !important;
        }

        .btn-secondary:hover {
            background: var(--surface-active);
        }

        .btn-danger {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger);
            border-radius: var(--radius-btn) !important;
        }

        .btn-danger:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .btn-sm {
            padding: 0.32rem 0.65rem;
            font-size: 0.8rem;
            border-radius: var(--radius-btn) !important;
        }

        .btn-lg {
            padding: 0.7rem 1.4rem;
            font-size: 1rem;
            border-radius: var(--radius-btn) !important;
            font-weight: 600;
        }

        /* Profile Dropdown Component */
        .profile-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-icon-btn {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            padding: 0.35rem 0.75rem 0.35rem 0.45rem;
            border-radius: var(--radius-btn) !important;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-primary);
            transition: all 0.15s ease;
            box-shadow: var(--shadow-xs);
        }

        .profile-icon-btn:hover {
            background: var(--surface-subtle);
            border-color: #94a3b8;
        }

        .user-avatar-circle {
            width: 26px;
            height: 26px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            border-radius: 0 !important;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        .profile-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: 0 !important;
            box-shadow: var(--shadow-lg);
            min-width: 210px;
            z-index: 250;
            overflow: hidden;
            animation: dropdownFadeIn 0.15s ease-out forwards;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            background: var(--surface-subtle);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            border-radius: 0 !important;
        }

        .dropdown-header-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .dropdown-header-role {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: background 0.12s ease;
            border-radius: 0 !important;
        }

        .dropdown-item:hover {
            background: var(--surface-subtle);
            color: var(--primary);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 0.25rem 0;
        }

        .logout-item {
            color: var(--danger);
        }
        .logout-item:hover {
            background: var(--danger-bg);
            color: var(--danger);
        }

        /* Badges: Zero border-radius */
        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.15rem 0.5rem;
            border-radius: 0 !important;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-transform: uppercase;
        }

        .status-approved { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); }
        .status-pending { background: var(--accent-gold-light); color: var(--accent-gold); border: 1px solid var(--accent-gold-border); }
        .status-rejected { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); }

        .role-badge {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.12rem 0.45rem;
            border-radius: 0 !important;
            text-transform: uppercase;
        }
        .role-admin { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
        .role-staff { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
        .role-public { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .badge-count {
            background: var(--danger);
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 0 !important;
            padding: 0.1rem 0.4rem;
            line-height: 1;
        }

        /* Form Controls: Strictly ZERO border radius */
        input, textarea, select, .form-control {
            width: 100%;
            padding: 0.55rem 0.8rem;
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: 0 !important;
            font-size: 0.92rem;
            font-family: inherit;
            color: var(--text-primary);
            transition: all 0.15s ease;
        }

        input:focus, textarea:focus, select:focus, .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        /* Alerts: Zero border radius */
        .alert {
            padding: 0.85rem 1.15rem;
            border-radius: 0 !important;
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            line-height: 1.45;
        }
        .alert-warning { background: var(--accent-gold-light); color: #92400e; border: 1px solid var(--accent-gold-border); }
        .alert-danger { background: var(--danger-bg); color: #991b1b; border: 1px solid var(--danger-border); }
        .alert-success { background: var(--success-bg); color: #166534; border: 1px solid var(--success-border); }
        .alert-info { background: var(--primary-light); color: #1e40af; border: 1px solid var(--primary-border); }

        /* Universal Modal System */
        .modal-overlay-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
            box-sizing: border-box;
        }

        .modal-card-box {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: 0 !important;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
            box-shadow: var(--shadow-xl);
            box-sizing: border-box;
            animation: modalFadeIn 0.15s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Container */
        main.container {
            flex: 1;
            width: 100%;
            max-width: 1200px;
            padding: 2rem 1.5rem;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            header.app-header {
                padding: 0.6rem 1rem;
            }
            .nav-links-group {
                display: none;
            }
            main.container {
                padding: 1.25rem 1rem;
            }
        }
    </style>
    <script>
        window.APP_ELEM_COLORS = {
            fire: <?php echo json_encode($elemColors['fire']); ?>,
            air: <?php echo json_encode($elemColors['air']); ?>,
            water: <?php echo json_encode($elemColors['water']); ?>,
            earth: <?php echo json_encode($elemColors['earth']); ?>
        };
    </script>
</head>
<body>
    <header class="app-header">
        <!-- Left: Logo & Nav -->
        <div class="header-left">
            <a href="index.php" class="logo-area">
                <div class="logo-icon-box">
                    <span>🔤</span>
                </div>
                <div class="logo-text-col">
                    <span class="logo-brand-name">Abjad & Geomancy</span>
                    <span class="logo-badge">Classical Numerical Sciences</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="nav-links-group">
                <a href="index.php" class="nav-item-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                    <span>🏛️</span> Home
                </a>
                <a href="calculator.php" class="nav-item-link <?php echo $currentPage === 'calculator.php' ? 'active' : ''; ?>">
                    <span>🧮</span> Calculator
                </a>
                <?php if ($isStaffOrAdmin): ?>
                    <a href="saved.php" class="nav-item-link <?php echo $currentPage === 'saved.php' ? 'active' : ''; ?>">
                        <span>📜</span> Saved Names
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Right: Actions & Profile -->
        <div class="header-right-group">
            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-sm btn-secondary" style="position: relative;">
                    <span>🛡️ Admin Portal</span>
                    <?php if ($pendingReqCount > 0): ?>
                        <span class="badge-count"><?php echo $pendingReqCount; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <div class="profile-dropdown-wrapper">
                <button id="profileDropdownBtn" class="profile-icon-btn" aria-label="User Profile Options">
                    <?php if ($currentUser): ?>
                        <div class="user-avatar-circle"><?php echo strtoupper(substr($currentUser['username'], 0, 1)); ?></div>
                        <span style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                    <?php else: ?>
                        <span style="font-size: 1rem;">👤</span>
                        <span style="font-weight: 600; font-size: 0.85rem;">Account</span>
                    <?php endif; ?>
                    <span style="font-size: 0.65rem; color: var(--text-muted); margin-left: 0.1rem;">▼</span>
                </button>
                
                <div id="profileDropdownMenu" class="profile-dropdown-menu">
                    <?php if ($currentUser): ?>
                        <div class="dropdown-header">
                            <span class="dropdown-header-name"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                            <div style="display: flex; gap: 0.35rem; align-items: center; margin-top: 0.15rem;">
                                <span class="role-badge role-<?php echo htmlspecialchars($currentUser['role']); ?>"><?php echo htmlspecialchars($currentUser['role']); ?></span>
                                <span class="status-badge status-<?php echo htmlspecialchars($currentUser['status']); ?>"><?php echo htmlspecialchars($currentUser['status']); ?></span>
                            </div>
                        </div>
                        <a href="profile.php" class="dropdown-item">
                            <span>👤 My Profile & Chat</span>
                        </a>
                        <?php if ($isStaffOrAdmin): ?>
                            <a href="saved.php" class="dropdown-item">
                                <span>📜 Saved History Log</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <a href="admin.php" class="dropdown-item">
                                <span>🛡️ Admin Dashboard</span>
                                <?php if ($pendingReqCount > 0): ?>
                                    <span class="badge-count"><?php echo $pendingReqCount; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="api.php?action=logout" class="dropdown-item logout-item">
                            <span>🚪 Sign Out</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?auth=login" class="dropdown-item">
                            <span>🔑 Account Login</span>
                        </a>
                        <a href="index.php?auth=signup" class="dropdown-item">
                            <span>✨ Create New Account</span>
                        </a>
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

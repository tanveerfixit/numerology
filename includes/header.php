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

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .user-badge {
            font-size: 0.8rem;
            background: #f1f5f9;
            color: var(--text-muted);
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            border: 1px solid var(--card-border);
            text-transform: capitalize;
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
        <a href="index.php" class="logo-area">
            <span>Abjad & Geomancy</span>
            <span class="logo-badge">Numerical Wisdom</span>
        </a>
        
        <nav class="nav-links">
            <a href="index.php" class="btn btn-sm">Home</a>
            <a href="calculator.php" class="btn btn-primary btn-sm">Calculator</a>
            
            <?php if ($currentUser): ?>
                <a href="profile.php" class="btn btn-sm">My Profile</a>
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <a href="admin.php" class="btn btn-sm" style="position: relative;">
                        Admin Portal 🔔
                        <?php if ($pendingReqCount > 0): ?>
                            <span style="background: #dc2626; color: #ffffff; font-size: 0.7rem; font-weight: bold; border-radius: 50px; padding: 0.1rem 0.45rem; margin-left: 0.2rem;">
                                <?php echo $pendingReqCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                
                <span class="user-badge">
                    👤 <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong> 
                    (<?php echo htmlspecialchars($currentUser['role']); ?>)
                </span>
                <a href="api.php?action=logout" class="btn btn-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="index.php?auth=login" class="btn btn-sm">Login</a>
                <a href="index.php?auth=signup" class="btn btn-primary btn-sm">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

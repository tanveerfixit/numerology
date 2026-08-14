<?php
// admin.php
$pageTitle = 'Admin Portal - Executive Console & User Management';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    echo '<main class="container" style="text-align: center; padding: 4rem 1.5rem;">
            <div style="background: #ffffff; border: 1px solid var(--border-subtle); padding: 2.5rem; border-radius: 0; max-width: 500px; margin: 0 auto; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🔒</div>
                <h2 style="color: var(--danger); font-size: 1.35rem; margin-bottom: 0.5rem;">Administrator Access Restricted</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">You must possess verified Administrator role privileges to view the management console.</p>
                <a href="index.php" class="btn btn-primary" style="border-radius: 2px;">Return to Home</a>
            </div>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<style>
    .admin-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }

    /* Executive Top Bar */
    .admin-executive-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-xs);
    }

    .admin-brand-col {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .admin-shield-icon {
        width: 44px;
        height: 44px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        border: 1px solid var(--primary-border);
    }

    .admin-actions-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* KPI Metrics Grid */
    .admin-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    @media (max-width: 992px) {
        .admin-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 540px) {
        .admin-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        padding: 1.15rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }

    .kpi-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    .kpi-card.active-filter {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .kpi-icon-box {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .kpi-text-col {
        display: flex;
        flex-direction: column;
    }

    .kpi-metric-title {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }

    .kpi-metric-count {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
        margin-top: 0.15rem;
    }

    /* Main Console Card */
    .admin-card-panel {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* Search & Filter Toolbar */
    .console-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
    }

    .filter-tabs-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .filter-tab-btn {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 2px;
        transition: all 0.12s ease;
    }

    .filter-tab-btn:hover {
        background: #ffffff;
        color: var(--primary);
        border-color: var(--primary);
    }

    .filter-tab-btn.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        max-width: 340px;
        min-width: 200px;
    }

    @media (max-width: 640px) {
        .search-input-wrapper {
            max-width: 100%;
            width: 100%;
        }
    }

    .search-input-wrapper input {
        width: 100%;
        padding: 0.35rem 0.65rem 0.35rem 2rem;
        font-size: 0.85rem;
    }

    .search-icon-decor {
        position: absolute;
        left: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.82rem;
        color: var(--text-muted);
        pointer-events: none;
    }

    /* Desktop Table View */
    .user-table-wrapper {
        overflow-x: auto;
        border: 1px solid var(--border-subtle);
    }

    .user-data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.88rem;
    }

    .user-data-table th {
        background: var(--surface-subtle);
        padding: 0.65rem 0.85rem;
        font-weight: 700;
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-subtle);
        white-space: nowrap;
    }

    .user-data-table td {
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
    }

    .user-data-table tbody tr:hover {
        background: var(--surface-subtle);
    }

    /* Mobile Cards View (Hidden on desktop, shown on mobile) */
    .user-cards-mobile-view {
        display: none;
        flex-direction: column;
        gap: 0.75rem;
    }

    @media (max-width: 768px) {
        .user-table-wrapper {
            display: none;
        }
        .user-cards-mobile-view {
            display: flex;
        }
    }

    .user-mobile-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        box-shadow: var(--shadow-xs);
    }

    .user-card-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-profile-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-weight: 700;
        color: var(--text-primary);
        cursor: pointer;
        text-decoration: none;
    }

    .user-profile-link:hover {
        color: var(--primary);
    }

    .notification-chip {
        background: var(--danger-bg);
        color: var(--danger);
        border: 1px solid var(--danger-border);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Chat Stream in Admin Modal */
    .admin-chat-thread-box {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        padding: 0.65rem 0.85rem;
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        box-sizing: border-box;
    }

    .stream-bubble {
        padding: 0.55rem 0.75rem;
        font-size: 0.84rem;
        line-height: 1.4;
        position: relative;
        max-width: 90%;
        box-sizing: border-box;
        word-break: break-word;
    }

    .stream-bubble-user {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid var(--border-medium);
        color: var(--text-primary);
    }

    .stream-bubble-admin {
        align-self: flex-end;
        background: var(--primary-light);
        border: 1px solid var(--primary-border);
        color: #1e3a8a;
    }

    .bubble-meta-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.7rem;
        margin-bottom: 0.2rem;
        color: var(--text-muted);
        gap: 0.5rem;
    }

    /* Built-in Virtual Urdu Keyboard in Modal */
    .admin-kb-drawer {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        padding: 0.5rem;
        margin-top: 0.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        box-sizing: border-box;
        max-height: 130px;
        overflow-y: auto;
    }

    .admin-kb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(30px, 1fr));
        gap: 0.2rem;
        direction: rtl;
    }

    .admin-kb-tile {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        padding: 0.25rem 0.1rem;
        font-family: var(--font-arabic);
        font-size: 1.15rem;
        font-weight: 700;
        cursor: pointer;
        text-align: center;
        transition: all 0.1s ease;
        border-radius: 2px;
        color: var(--text-primary);
        line-height: 1;
    }

    .admin-kb-tile:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* 4 Elements Setting Grid */
    .elem-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
        box-sizing: border-box;
    }

    @media (max-width: 520px) {
        .elem-modal-grid {
            grid-template-columns: 1fr;
        }
    }

    .elem-setting-card {
        background: var(--surface-subtle);
        border: 1px solid var(--border-subtle);
        padding: 0.75rem;
        border-radius: 0;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        box-sizing: border-box;
        width: 100%;
        overflow: hidden;
    }
</style>

<main class="container">
    <div class="admin-dashboard-wrapper">

        <!-- Executive Header Banner -->
        <div class="admin-executive-banner">
            <div class="admin-brand-col">
                <div class="admin-shield-icon">🛡️</div>
                <div>
                    <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.15rem;">
                        Executive Management Console
                    </h1>
                    <p style="font-size: 0.8rem; color: var(--text-secondary);">
                        Logged in as Administrator (<strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>)
                    </p>
                </div>
            </div>

            <div class="admin-actions-bar">
                <button id="btnOpenElemSettings" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;" title="Configure 4 Elements Theme Colors">
                    <span>🎨</span> <span>Element Colors</span>
                </button>
                <button onclick="loadUsers()" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;">
                    <span>🔄</span> <span>Refresh Directory</span>
                </button>
            </div>
        </div>

        <!-- Interactive KPI Metric Tiles -->
        <div class="admin-kpi-grid">
            <div class="kpi-card active-filter" id="kpiCardAll" onclick="setFilter('all')">
                <div class="kpi-icon-box" style="background: var(--primary-light); color: var(--primary);">👥</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Total Registered</span>
                    <div class="kpi-metric-count" id="kpiTotalUsers">0</div>
                </div>
            </div>

            <div class="kpi-card" id="kpiCardPending" onclick="setFilter('pending')">
                <div class="kpi-icon-box" style="background: var(--accent-gold-light); color: var(--accent-gold);">⏳</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Pending Approvals</span>
                    <div class="kpi-metric-count" id="kpiPendingApprovals">0</div>
                </div>
            </div>

            <div class="kpi-card" id="kpiCardInquiries" onclick="setFilter('inquiries')">
                <div class="kpi-icon-box" style="background: #fdf2f8; color: #db2777;">💬</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Inquiries Waiting</span>
                    <div class="kpi-metric-count" id="kpiPendingChats">0</div>
                </div>
            </div>

            <div class="kpi-card" id="kpiCardStaff" onclick="setFilter('staff')">
                <div class="kpi-icon-box" style="background: #f0fdf4; color: var(--success);">🛡️</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Staff & Admins</span>
                    <div class="kpi-metric-count" id="kpiStaffCount">0</div>
                </div>
            </div>
        </div>

        <!-- User Directory Panel -->
        <div class="admin-card-panel">
            <!-- Search and Filter Bar -->
            <div class="console-toolbar">
                <div class="filter-tabs-group">
                    <button type="button" class="filter-tab-btn active" id="tabAll" onclick="setFilter('all')">All Accounts (<span id="countTabAll">0</span>)</button>
                    <button type="button" class="filter-tab-btn" id="tabPending" onclick="setFilter('pending')">Pending (<span id="countTabPending">0</span>)</button>
                    <button type="button" class="filter-tab-btn" id="tabInquiries" onclick="setFilter('inquiries')">Inquiries (<span id="countTabInquiries">0</span>)</button>
                    <button type="button" class="filter-tab-btn" id="tabStaff" onclick="setFilter('staff')">Staff/Admin (<span id="countTabStaff">0</span>)</button>
                </div>

                <div class="search-input-wrapper">
                    <span class="search-icon-decor">🔍</span>
                    <input type="text" id="adminSearchInput" class="form-control" placeholder="Search user, name, email, contact...">
                </div>
            </div>

            <!-- Desktop Data Table -->
            <div class="user-table-wrapper">
                <table class="user-data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Identity</th>
                            <th>Contact / Email</th>
                            <th>Role Access</th>
                            <th>Status</th>
                            <th>Consultation Status</th>
                            <th style="text-align: right;">Action Controls</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">Loading user records...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Adaptive Cards View -->
            <div class="user-cards-mobile-view" id="userCardsMobileContainer">
                <div style="padding: 1.5rem; text-align: center; color: var(--text-muted);">Loading user directory...</div>
            </div>
        </div>

    </div>
</main>

<!-- User Profile & Consultation Thread Modal -->
<div id="userProfileModal" class="modal-overlay-custom" style="display: none;">
    <div class="modal-card-box" style="max-width: 740px; width: 95%; max-height: 90vh; overflow-y: auto; text-align: left; border-radius: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 id="profileModalTitle" style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                <span>👤</span> User Profile & Consultation Thread
            </h3>
            <button onclick="closeProfileModal()" type="button" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem; border-radius: 2px;">✕ Close</button>
        </div>

        <div id="profileModalBody">
            <!-- Dynamically populated -->
        </div>
    </div>
</div>

<!-- 4 Elements Color Configuration Modal -->
<div id="elemSettingsModal" class="modal-overlay-custom" style="display: none;">
    <div class="modal-card-box" style="max-width: 580px; width: 95%; max-height: 85vh; overflow-y: auto; text-align: left; padding: 1.25rem; border-radius: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.65rem; margin-bottom: 0.85rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                    <span>🎨</span> 4 Elements Color Configuration
                </h3>
                <p style="font-size: 0.76rem; color: var(--text-muted); margin-top: 0.1rem;">Customize global theme colors for Fire, Air, Water, and Earth.</p>
            </div>
            <button id="btnCloseElemSettings" type="button" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem; border-radius: 2px;">✕ Close</button>
        </div>

        <div id="elemColorAlert" style="display: none; margin-bottom: 0.75rem;"></div>

        <div class="elem-modal-grid">
            <!-- Fire -->
            <div class="elem-setting-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 0.85rem; color: var(--text-primary);">🔥 Fire (آتشی)</strong>
                    <span id="previewBadgeFire" style="padding: 0.1rem 0.4rem; font-size: 0.68rem; font-weight: 700; background: <?php echo htmlspecialchars($elemColors['fire']); ?>; color: #000000; border-radius: 0;">Preview</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <input type="color" id="elemPickerFire" value="<?php echo htmlspecialchars($elemColors['fire']); ?>" style="width: 36px; height: 32px; padding: 0; border: 1px solid var(--border-medium); cursor: pointer; border-radius: 0; background: transparent; flex-shrink: 0;">
                    <input type="text" id="elemHexFire" class="form-control" value="<?php echo htmlspecialchars($elemColors['fire']); ?>" style="font-family: monospace; font-size: 0.82rem; text-transform: lowercase; border-radius: 0; height: 32px; padding: 0.2rem 0.35rem; width: 100%;">
                </div>
                <div style="font-size: 0.68rem; color: var(--text-muted);">Default: Yellow (<code>#eab308</code>)</div>
            </div>

            <!-- Air -->
            <div class="elem-setting-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 0.85rem; color: var(--text-primary);">💨 Air (بادی)</strong>
                    <span id="previewBadgeAir" style="padding: 0.1rem 0.4rem; font-size: 0.68rem; font-weight: 700; background: <?php echo htmlspecialchars($elemColors['air']); ?>; color: #ffffff; border-radius: 0;">Preview</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <input type="color" id="elemPickerAir" value="<?php echo htmlspecialchars($elemColors['air']); ?>" style="width: 36px; height: 32px; padding: 0; border: 1px solid var(--border-medium); cursor: pointer; border-radius: 0; background: transparent; flex-shrink: 0;">
                    <input type="text" id="elemHexAir" class="form-control" value="<?php echo htmlspecialchars($elemColors['air']); ?>" style="font-family: monospace; font-size: 0.82rem; text-transform: lowercase; border-radius: 0; height: 32px; padding: 0.2rem 0.35rem; width: 100%;">
                </div>
                <div style="font-size: 0.68rem; color: var(--text-muted);">Default: Red (<code>#dc2626</code>)</div>
            </div>

            <!-- Water -->
            <div class="elem-setting-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 0.85rem; color: var(--text-primary);">💧 Water (آبی)</strong>
                    <span id="previewBadgeWater" style="padding: 0.1rem 0.4rem; font-size: 0.68rem; font-weight: 700; background: <?php echo htmlspecialchars($elemColors['water']); ?>; color: #ffffff; border-radius: 0;">Preview</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <input type="color" id="elemPickerWater" value="<?php echo htmlspecialchars($elemColors['water']); ?>" style="width: 36px; height: 32px; padding: 0; border: 1px solid var(--border-medium); cursor: pointer; border-radius: 0; background: transparent; flex-shrink: 0;">
                    <input type="text" id="elemHexWater" class="form-control" value="<?php echo htmlspecialchars($elemColors['water']); ?>" style="font-family: monospace; font-size: 0.82rem; text-transform: lowercase; border-radius: 0; height: 32px; padding: 0.2rem 0.35rem; width: 100%;">
                </div>
                <div style="font-size: 0.68rem; color: var(--text-muted);">Default: Blue (<code>#2563eb</code>)</div>
            </div>

            <!-- Earth -->
            <div class="elem-setting-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 0.85rem; color: var(--text-primary);">🪨 Earth (خاکی)</strong>
                    <span id="previewBadgeEarth" style="padding: 0.1rem 0.4rem; font-size: 0.68rem; font-weight: 700; background: <?php echo htmlspecialchars($elemColors['earth']); ?>; color: #ffffff; border-radius: 0;">Preview</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <input type="color" id="elemPickerEarth" value="<?php echo htmlspecialchars($elemColors['earth']); ?>" style="width: 36px; height: 32px; padding: 0; border: 1px solid var(--border-medium); cursor: pointer; border-radius: 0; background: transparent; flex-shrink: 0;">
                    <input type="text" id="elemHexEarth" class="form-control" value="<?php echo htmlspecialchars($elemColors['earth']); ?>" style="font-family: monospace; font-size: 0.82rem; text-transform: lowercase; border-radius: 0; height: 32px; padding: 0.2rem 0.35rem; width: 100%;">
                </div>
                <div style="font-size: 0.68rem; color: var(--text-muted);">Default: Black (<code>#0f172a</code>)</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 0.65rem;">
            <button id="btnResetElemColors" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px; font-size: 0.78rem;">
                🔄 Reset Defaults
            </button>
            <div style="display: flex; gap: 0.4rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('elemSettingsModal').style.display='none'" style="border-radius: 2px; font-size: 0.78rem;">Cancel</button>
                <button id="btnSaveElemColors" type="button" class="btn btn-primary btn-sm" style="border-radius: 2px; font-size: 0.78rem;">
                    💾 Save Colors
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let usersList = [];
    let currentFilter = 'all';
    let activeModalUserId = null;
    let activeAdminInput = null;

    const arabicLetters = [
        'ا', 'آ', 'ب', 'پ', 'ت', 'ٹ', 'ث', 'ج', 'چ', 'ح', 'خ',
        'د', 'ڈ', 'ذ', 'ر', 'ڑ', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض',
        'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن',
        'ں', 'و', 'ہ', 'ھ', 'ء', 'ی', 'ے'
    ];

    function loadUsers() {
        fetch('api.php?action=list_users')
        .then(res => res.json())
        .then(users => {
            usersList = Array.isArray(users) ? users : [];
            updateKPIs();
            renderUserViews();
        })
        .catch(() => {
            document.getElementById('userTableBody').innerHTML = '<tr><td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--danger);">Failed to load user directory.</td></tr>';
            document.getElementById('userCardsMobileContainer').innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--danger);">Failed to load user directory.</div>';
        });
    }

    function updateKPIs() {
        const total = usersList.length;
        const pendingAppr = usersList.filter(u => u.status === 'pending').length;
        const pendingChats = usersList.filter(u => u.req_status === 'pending').length;
        const staffAndAdmin = usersList.filter(u => ['staff', 'admin'].includes(u.role)).length;

        document.getElementById('kpiTotalUsers').innerText = total;
        document.getElementById('kpiPendingApprovals').innerText = pendingAppr;
        document.getElementById('kpiPendingChats').innerText = pendingChats;
        document.getElementById('kpiStaffCount').innerText = staffAndAdmin;

        document.getElementById('countTabAll').innerText = total;
        document.getElementById('countTabPending').innerText = pendingAppr;
        document.getElementById('countTabInquiries').innerText = pendingChats;
        document.getElementById('countTabStaff').innerText = staffAndAdmin;
    }

    function setFilter(filter) {
        currentFilter = filter;

        // Update tabs active state
        ['All', 'Pending', 'Inquiries', 'Staff'].forEach(f => {
            const el = document.getElementById('tab' + f);
            if (el) el.classList.toggle('active', filter.toLowerCase() === f.toLowerCase());
            const kpi = document.getElementById('kpiCard' + f);
            if (kpi) kpi.classList.toggle('active-filter', filter.toLowerCase() === f.toLowerCase());
        });

        renderUserViews();
    }

    function getFilteredUsers() {
        const search = (document.getElementById('adminSearchInput')?.value || '').toLowerCase().trim();
        return usersList.filter(u => {
            if (currentFilter === 'pending' && u.status !== 'pending') return false;
            if (currentFilter === 'inquiries' && u.req_status !== 'pending') return false;
            if (currentFilter === 'staff' && !['staff', 'admin'].includes(u.role)) return false;

            if (search) {
                const matchU = u.username.toLowerCase().includes(search);
                const matchE = u.email.toLowerCase().includes(search);
                const matchN = (u.full_name || '').toLowerCase().includes(search);
                const matchC = (u.contact || '').toLowerCase().includes(search);
                if (!matchU && !matchE && !matchN && !matchC) return false;
            }
            return true;
        });
    }

    function renderUserViews() {
        const filtered = getFilteredUsers();
        renderUserTable(filtered);
        renderUserMobileCards(filtered);
    }

    function renderUserTable(list) {
        const tbody = document.getElementById('userTableBody');
        if (!tbody) return;

        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">No users matching current filter/search.</td></tr>';
            return;
        }

        let html = '';
        list.forEach(u => {
            const statusClass = 'status-' + u.status;
            let reqBadge = '<span style="color:var(--text-muted); font-size:0.75rem;">None</span>';
            if (u.req_status === 'pending') {
                reqBadge = '<span class="notification-chip">🔔 New Question</span>';
            } else if (u.req_status === 'replied') {
                reqBadge = '<span style="color:var(--success); font-weight:700; font-size:0.75rem;">✓ Replied</span>';
            }

            html += `
                <tr>
                    <td style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem;">#${u.id}</td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <a class="user-profile-link" onclick="openUserProfile(${u.id})">
                                <span style="font-weight: 700;">${escapeHtml(u.username)}</span>
                            </a>
                            ${u.full_name ? `<span style="font-size: 0.78rem; color: var(--text-secondary);">${escapeHtml(u.full_name)}</span>` : ''}
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; font-size: 0.82rem;">
                            <span>${escapeHtml(u.email)}</span>
                            ${u.contact ? `<span style="color: var(--text-muted); font-size: 0.76rem;">${escapeHtml(u.contact)}</span>` : ''}
                        </div>
                    </td>
                    <td>
                        <select onchange="updateRole(${u.id}, this.value)" class="form-control" style="width: auto; padding: 0.2rem 0.45rem; font-size: 0.78rem; border-radius: 0;">
                            <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                            <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                            <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </td>
                    <td>
                        <span class="status-badge ${statusClass}">${escapeHtml(u.status)}</span>
                    </td>
                    <td>${reqBadge}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <div style="display: inline-flex; gap: 0.3rem;">
                            ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" type="button" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.75rem; background: var(--success); border-color: var(--success); border-radius: 2px;">Approve</button>` : ''}
                            <button onclick="openUserProfile(${u.id})" type="button" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.75rem; border-radius: 2px;">
                                ${u.req_status === 'pending' ? '💬 Reply' : 'Inspect'}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function renderUserMobileCards(list) {
        const container = document.getElementById('userCardsMobileContainer');
        if (!container) return;

        if (!list.length) {
            container.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--text-muted);">No users matching current filter.</div>';
            return;
        }

        let html = '';
        list.forEach(u => {
            const statusClass = 'status-' + u.status;
            let reqBadge = '';
            if (u.req_status === 'pending') {
                reqBadge = '<span class="notification-chip">🔔 New Question</span>';
            } else if (u.req_status === 'replied') {
                reqBadge = '<span style="color:var(--success); font-weight:700; font-size:0.75rem;">✓ Replied</span>';
            }

            html += `
                <div class="user-mobile-card">
                    <div class="user-card-top-row">
                        <div>
                            <strong style="font-size: 0.95rem; color: var(--text-primary);">${escapeHtml(u.username)}</strong>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.3rem;">#${u.id}</span>
                            ${u.full_name ? `<div style="font-size: 0.8rem; color: var(--text-secondary);">${escapeHtml(u.full_name)}</div>` : ''}
                        </div>
                        <div style="display: flex; gap: 0.3rem; align-items: center;">
                            <span class="status-badge ${statusClass}">${escapeHtml(u.status)}</span>
                            <span class="role-badge role-${escapeHtml(u.role)}">${escapeHtml(u.role)}</span>
                        </div>
                    </div>

                    <div style="font-size: 0.82rem; color: var(--text-secondary);">
                        <div>✉️ ${escapeHtml(u.email)}</div>
                        ${u.contact ? `<div>📞 ${escapeHtml(u.contact)}</div>` : ''}
                    </div>

                    ${reqBadge ? `<div style="margin-top: 0.2rem;">${reqBadge}</div>` : ''}

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 0.5rem; margin-top: 0.25rem;">
                        <select onchange="updateRole(${u.id}, this.value)" class="form-control" style="width: auto; padding: 0.2rem 0.4rem; font-size: 0.78rem; border-radius: 0;">
                            <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                            <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                            <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                        <div style="display: flex; gap: 0.3rem;">
                            ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" type="button" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; background: var(--success); border-color: var(--success); border-radius: 2px;">Approve</button>` : ''}
                            <button onclick="openUserProfile(${u.id})" type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 2px;">
                                ${u.req_status === 'pending' ? '💬 Reply' : 'Inspect'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function openUserProfile(userId) {
        activeModalUserId = userId;
        const u = usersList.find(item => item.id == userId);
        if (!u) return;

        document.getElementById('profileModalTitle').innerHTML = `<span>👤</span> User Identity & Chat: <strong>${escapeHtml(u.username)}</strong>`;

        document.getElementById('profileModalBody').innerHTML = `
            <!-- Info Identity Banner -->
            <div style="background: var(--surface-subtle); border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; margin-bottom: 0.75rem; font-size: 0.82rem; border-radius: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.35rem; flex-wrap: wrap; gap: 0.4rem;">
                    <div>
                        <strong style="font-size: 0.9rem; color: var(--text-primary);">${escapeHtml(u.username)}</strong>
                        <span style="color: var(--text-muted); font-size: 0.78rem; margin-left: 0.25rem;">(ID #${u.id})</span>
                        ${u.full_name ? `<span style="color: var(--text-secondary); margin-left: 0.35rem; font-weight: 500;">• ${escapeHtml(u.full_name)}</span>` : ''}
                    </div>
                    <div style="display: flex; gap: 0.35rem; align-items: center;">
                        <span class="status-badge status-${u.status}">${u.status}</span>
                        <select onchange="updateRole(${u.id}, this.value)" class="form-control" style="width: auto; padding: 0.12rem 0.35rem; font-size: 0.75rem; border-radius: 0;">
                            <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                            <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                            <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem; font-size: 0.78rem; color: var(--text-secondary);">
                    <div>✉️ ${escapeHtml(u.email)} ${u.contact ? `| 📞 ${escapeHtml(u.contact)}` : ''}</div>
                    <div style="display: flex; gap: 0.3rem;">
                        ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" type="button" class="btn btn-primary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; background: var(--success); border-color: var(--success); border-radius: 2px;">Approve</button>` : ''}
                        ${u.status !== 'rejected' ? `<button onclick="rejectUser(${u.id})" type="button" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; border-radius: 2px; color: var(--danger);">Reject</button>` : ''}
                        <button onclick="deleteUser(${u.id})" type="button" class="btn btn-danger btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; border-radius: 2px;">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Single Consultation Chat Stream -->
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <strong style="font-size: 0.88rem; color: var(--text-primary);">💬 Consultation Dialogue History</strong>
                    <button onclick="clearChatHistory(${u.id})" type="button" class="btn btn-danger btn-sm" style="font-size: 0.7rem; padding: 0.1rem 0.4rem; border-radius: 2px;">🗑️ Clear Thread</button>
                </div>
                <div id="adminChatContainer" class="admin-chat-thread-box">
                    <div style="text-align: center; color: var(--text-muted); padding: 0.75rem; font-size: 0.82rem;">Loading dialogue...</div>
                </div>
            </div>

            <!-- Admin Message Composer with Built-in Keyboard -->
            <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.65rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0;">
                        Send Administrative Reply:
                    </label>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAdminKeyboard(${u.id})" style="font-size: 0.72rem; padding: 0.12rem 0.45rem; border-radius: 2px;">
                        ⌨️ Urdu Keyboard
                    </button>
                </div>
                <textarea id="replyMsg_${u.id}" class="form-control" rows="2" placeholder="Type your response or advice to ${escapeHtml(u.username)}..." style="width: 100%; box-sizing: border-box; resize: vertical; min-height: 60px; max-height: 120px; font-size: 0.88rem; border-radius: 0;"></textarea>

                <!-- Admin Virtual Urdu Keyboard Drawer -->
                <div id="adminKbDrawer_${u.id}" class="admin-kb-drawer" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; font-weight: 600; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.25rem;">
                        <span>⌨️ Virtual Urdu Keyboard (Admin Reply)</span>
                        <div style="display: flex; gap: 0.25rem;">
                            <button type="button" onclick="adminInsertChar(' ', ${u.id})" class="btn btn-secondary btn-sm" style="padding: 0.05rem 0.35rem; font-size: 0.68rem; border-radius: 2px;">Space ␣</button>
                            <button type="button" onclick="adminBackspaceChar(${u.id})" class="btn btn-danger btn-sm" style="padding: 0.05rem 0.35rem; font-size: 0.68rem; border-radius: 2px;">⌫</button>
                        </div>
                    </div>
                    <div class="admin-kb-grid">
                        ${arabicLetters.map(ch => `<button type="button" class="admin-kb-tile" onclick="adminInsertChar('${ch}', ${u.id})">${ch}</button>`).join('')}
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.4rem; margin-top: 0.5rem;">
                    <button onclick="sendAdminReply(${u.id})" type="button" class="btn btn-primary btn-sm" style="padding: 0.35rem 0.75rem; border-radius: 2px;">✉️ Send Reply</button>
                </div>
            </div>
        `;

        document.getElementById('userProfileModal').style.display = 'flex';
        loadAdminChatThread(userId);
    }

    function toggleAdminKeyboard(userId) {
        const drawer = document.getElementById('adminKbDrawer_' + userId);
        if (drawer) {
            drawer.style.display = drawer.style.display === 'none' ? 'flex' : 'none';
        }
    }

    function adminInsertChar(ch, userId) {
        const textarea = document.getElementById('replyMsg_' + userId);
        if (!textarea) return;
        const start = textarea.selectionStart || textarea.value.length;
        const end = textarea.selectionEnd || textarea.value.length;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + ch + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + ch.length;
        textarea.focus();
    }

    function adminBackspaceChar(userId) {
        const textarea = document.getElementById('replyMsg_' + userId);
        if (!textarea) return;
        const start = textarea.selectionStart || textarea.value.length;
        const end = textarea.selectionEnd || textarea.value.length;
        const text = textarea.value;
        if (start === end) {
            if (start > 0) {
                textarea.value = text.substring(0, start - 1) + text.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start - 1;
            }
        } else {
            textarea.value = text.substring(0, start) + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start;
        }
        textarea.focus();
    }

    function loadAdminChatThread(userId) {
        fetch('api.php?action=get_user_chats&user_id=' + userId)
        .then(res => res.json())
        .then(messages => {
            const container = document.getElementById('adminChatContainer');
            if (!container) return;
            if (!Array.isArray(messages) || messages.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 1.5rem;">No consultation messages recorded yet.</div>';
                return;
            }

            let html = '';
            messages.forEach(m => {
                const isUser = m.sender === 'user';
                const bubbleClass = isUser ? 'stream-bubble-user' : 'stream-bubble-admin';
                const senderTitle = isUser ? '👤 User Inquiry' : '🛡️ Admin Response';
                const details = m.name_lookup ? `
                    <div style="font-size: 0.76rem; background: rgba(0,0,0,0.03); padding: 0.25rem 0.45rem; margin-bottom: 0.35rem; border-radius: 0;">
                        <strong>Target Name:</strong> ${escapeHtml(m.name_lookup)} | <strong>Relation:</strong> ${escapeHtml(m.relationship || 'N/A')}
                    </div>
                ` : '';

                html += `
                    <div class="stream-bubble ${bubbleClass}">
                        <div class="bubble-meta-header">
                            <strong style="color: var(--text-primary);">${senderTitle}</strong>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span>${m.created_at || ''}</span>
                                <button onclick="deleteSingleMessage(${m.id}, ${m.user_id})" type="button" class="btn btn-danger btn-sm" style="font-size: 0.65rem; padding: 0.05rem 0.3rem; border-radius: 2px;">Del</button>
                            </div>
                        </div>
                        ${details}
                        <div style="white-space: pre-wrap;">${escapeHtml(m.message)}</div>
                    </div>
                `;
            });
            container.innerHTML = html;
            container.scrollTop = container.scrollHeight;
        });
    }

    function sendAdminReply(userId) {
        const replyText = document.getElementById('replyMsg_' + userId).value.trim();
        if (!replyText) {
            alert('Please enter a response message.');
            return;
        }

        fetch('api.php?action=send_admin_reply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, reply: replyText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('replyMsg_' + userId).value = '';
                loadAdminChatThread(userId);
                loadUsers();
            } else {
                alert('Failed to send reply: ' + (data.error || ''));
            }
        });
    }

    function deleteSingleMessage(chatId, userId) {
        if (!confirm('Are you sure you want to delete this message?')) return;
        fetch('api.php?action=delete_chat_message', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ chat_id: chatId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadAdminChatThread(userId);
            } else {
                alert('Failed to delete message: ' + (data.error || ''));
            }
        });
    }

    function clearChatHistory(userId) {
        if (!confirm('Are you sure you want to permanently clear the consultation chat history for this user?')) return;
        fetch('api.php?action=clear_user_chat_history', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadAdminChatThread(userId);
                loadUsers();
            } else alert('Failed to clear chat history');
        });
    }

    function closeProfileModal() {
        document.getElementById('userProfileModal').style.display = 'none';
        activeModalUserId = null;
    }

    function approveUser(id) {
        fetch('api.php?action=approve_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeProfileModal();
                loadUsers();
            } else alert('Failed to approve user: ' + (data.error || ''));
        });
    }

    function rejectUser(id) {
        if (!confirm('Reject this user account?')) return;
        fetch('api.php?action=reject_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeProfileModal();
                loadUsers();
            } else alert('Failed to reject user: ' + (data.error || ''));
        });
    }

    function deleteUser(id) {
        if (!confirm('Are you sure you want to permanently delete this user account? This cannot be undone.')) return;
        fetch('api.php?action=delete_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeProfileModal();
                loadUsers();
            } else alert('Failed to delete user: ' + (data.error || ''));
        });
    }

    function updateRole(id, role) {
        fetch('api.php?action=update_role', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, role })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadUsers();
            } else alert('Failed to update role: ' + (data.error || ''));
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    // Element Theme Color Settings Logic
    function bindColorPickers() {
        const elemModal = document.getElementById('elemSettingsModal');
        document.getElementById('btnOpenElemSettings')?.addEventListener('click', () => {
            if (elemModal) elemModal.style.display = 'flex';
        });
        document.getElementById('btnCloseElemSettings')?.addEventListener('click', () => {
            if (elemModal) elemModal.style.display = 'none';
        });

        const elements = ['Fire', 'Air', 'Water', 'Earth'];
        elements.forEach(elem => {
            const picker = document.getElementById('elemPicker' + elem);
            const hexInput = document.getElementById('elemHex' + elem);
            const badge = document.getElementById('previewBadge' + elem);

            if (picker && hexInput) {
                picker.addEventListener('input', () => {
                    hexInput.value = picker.value;
                    if (badge) badge.style.backgroundColor = picker.value;
                    document.documentElement.style.setProperty('--' + elem.toLowerCase() + '-color', picker.value);
                });

                hexInput.addEventListener('input', () => {
                    const val = hexInput.value.trim();
                    if (/^#[0-9A-F]{6}$/i.test(val)) {
                        picker.value = val;
                        if (badge) badge.style.backgroundColor = val;
                        document.documentElement.style.setProperty('--' + elem.toLowerCase() + '-color', val);
                    }
                });
            }
        });

        // Save Element Colors
        document.getElementById('btnSaveElemColors')?.addEventListener('click', () => {
            const fire = document.getElementById('elemHexFire').value.trim();
            const air = document.getElementById('elemHexAir').value.trim();
            const water = document.getElementById('elemHexWater').value.trim();
            const earth = document.getElementById('elemHexEarth').value.trim();
            const alertDiv = document.getElementById('elemColorAlert');

            fetch('api.php?action=save_element_settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fire, air, water, earth })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Elemental colors saved successfully!');
                    alertDiv.style.display = 'flex';
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Failed to save colors';
                    alertDiv.style.display = 'flex';
                }
            })
            .catch(() => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'Network error saving element colors.';
                alertDiv.style.display = 'flex';
            });
        });

        // Reset Element Colors to Defaults (Fire=Yellow, Air=Red, Water=Blue, Earth=Black)
        document.getElementById('btnResetElemColors')?.addEventListener('click', () => {
            if (!confirm('Reset all 4 elemental colors to default values (Fire=Yellow, Air=Red, Water=Blue, Earth=Black)?')) return;
            const alertDiv = document.getElementById('elemColorAlert');

            fetch('api.php?action=reset_element_settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.colors) {
                    const c = data.colors;
                    document.getElementById('elemPickerFire').value = c.fire;
                    document.getElementById('elemHexFire').value = c.fire;
                    document.getElementById('previewBadgeFire').style.backgroundColor = c.fire;
                    document.documentElement.style.setProperty('--fire-color', c.fire);

                    document.getElementById('elemPickerAir').value = c.air;
                    document.getElementById('elemHexAir').value = c.air;
                    document.getElementById('previewBadgeAir').style.backgroundColor = c.air;
                    document.documentElement.style.setProperty('--air-color', c.air);

                    document.getElementById('elemPickerWater').value = c.water;
                    document.getElementById('elemHexWater').value = c.water;
                    document.getElementById('previewBadgeWater').style.backgroundColor = c.water;
                    document.documentElement.style.setProperty('--water-color', c.water);

                    document.getElementById('elemPickerEarth').value = c.earth;
                    document.getElementById('elemHexEarth').value = c.earth;
                    document.getElementById('previewBadgeEarth').style.backgroundColor = c.earth;
                    document.documentElement.style.setProperty('--earth-color', c.earth);

                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Colors reset to defaults successfully!');
                    alertDiv.style.display = 'flex';
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Failed to reset colors';
                    alertDiv.style.display = 'flex';
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadUsers();
        bindColorPickers();

        document.getElementById('adminSearchInput')?.addEventListener('input', renderUserViews);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

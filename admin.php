<?php
// admin.php
$pageTitle = 'Admin Portal - Executive Dashboard & User Management';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    echo '<main class="container" style="text-align: center; padding: 4rem 1.5rem;">
            <div style="background: #ffffff; border: 1px solid var(--border-subtle); padding: 2.5rem; border-radius: var(--radius-lg); max-width: 500px; margin: 0 auto; box-shadow: var(--shadow-md);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🛡️</div>
                <h2 style="color: var(--danger); font-size: 1.35rem; margin-bottom: 0.5rem;">Administrator Access Restricted</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">You must possess verified Administrator role privileges to view the management console.</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
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
        gap: 1.75rem;
    }

    /* Executive KPI Stats */
    .admin-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    @media (max-width: 900px) {
        .admin-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .admin-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .kpi-icon-box {
        width: 46px;
        height: 46px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .kpi-text-col {
        display: flex;
        flex-direction: column;
    }

    .kpi-metric-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }

    .kpi-metric-count {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
        margin-top: 0.15rem;
    }

    /* Admin Management Card */
    .admin-card-panel {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .admin-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 1rem;
    }

    .admin-title-group {
        display: flex;
        flex-direction: column;
    }

    .admin-main-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .admin-sub-text {
        font-size: 0.84rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    /* Modern Table */
    .user-table-wrapper {
        overflow-x: auto;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
    }

    .user-data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.88rem;
    }

    .user-data-table th {
        background: var(--surface-subtle);
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-subtle);
    }

    .user-data-table td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
    }

    .user-data-table tbody tr:hover {
        background: var(--surface-subtle);
    }

    .user-profile-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--primary);
        cursor: pointer;
        text-decoration: none;
    }

    .user-profile-link:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }

    .notification-chip {
        background: var(--danger-bg);
        color: var(--danger);
        border: 1px solid var(--danger-border);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: var(--radius-full);
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Single Chat Thread Stream in Admin */
    .admin-chat-thread-box {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        padding: 1rem;
        max-height: 380px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .stream-bubble {
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.88rem;
        line-height: 1.5;
        position: relative;
        max-width: 85%;
    }

    .stream-bubble-user {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid var(--border-medium);
        color: var(--text-primary);
        box-shadow: var(--shadow-xs);
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
        font-size: 0.72rem;
        margin-bottom: 0.35rem;
        color: var(--text-muted);
        gap: 1rem;
    }
</style>

<main class="container">
    <div class="admin-dashboard-wrapper">
        <!-- Executive KPI Cards -->
        <div class="admin-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon-box" style="background: var(--primary-light); color: var(--primary);">👥</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Total Registered</span>
                    <div class="kpi-metric-count" id="kpiTotalUsers">--</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-box" style="background: var(--accent-gold-light); color: var(--accent-gold);">⏳</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Pending Approvals</span>
                    <div class="kpi-metric-count" id="kpiPendingApprovals">--</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-box" style="background: #fdf2f8; color: #db2777;">💬</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">New Inquiries</span>
                    <div class="kpi-metric-count" id="kpiPendingChats">--</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-box" style="background: #f0fdf4; color: var(--success);">🛡️</div>
                <div class="kpi-text-col">
                    <span class="kpi-metric-title">Staff & Admins</span>
                    <div class="kpi-metric-count" id="kpiStaffCount">--</div>
                </div>
            </div>
        </div>

        <!-- Main User Management Panel -->
        <div class="admin-card-panel">
            <div class="admin-header-row">
                <div class="admin-title-group">
                    <h2 class="admin-main-title">
                        <span>🛡️</span> User Directory & Consultation Console
                    </h2>
                    <p class="admin-sub-text">Inspect user profiles, approve new registrations, configure authorization roles, and reply to circumstance consultations.</p>
                </div>
                <button onclick="loadUsers()" class="btn btn-secondary btn-sm">🔄 Refresh Directory</button>
            </div>

            <div class="user-table-wrapper">
                <table class="user-data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role Access</th>
                            <th>Account Status</th>
                            <th>Consultation Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">Loading user records...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Profile & Single Chat Thread Modal -->
<div id="userProfileModal" class="modal-overlay-custom">
    <div class="modal-card-box" style="max-width: 720px; text-align: left; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 id="profileModalTitle" style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                <span>👤</span> User Profile & Consultation Thread
            </h3>
            <button onclick="closeProfileModal()" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem;">✕ Close</button>
        </div>

        <div id="profileModalBody">
            <!-- Populated dynamically via JS -->
        </div>
    </div>
</div>

<script>
    let usersList = [];
    let activeModalUserId = null;

    function loadUsers() {
        fetch('api.php?action=list_users')
        .then(res => res.json())
        .then(users => {
            usersList = Array.isArray(users) ? users : [];
            updateKPIs();
            renderUserTable();
        })
        .catch(() => {
            document.getElementById('userTableBody').innerHTML = '<tr><td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--danger);">Failed to load user directory.</td></tr>';
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
    }

    function renderUserTable() {
        const tbody = document.getElementById('userTableBody');
        if (!usersList.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">No users found.</td></tr>';
            return;
        }

        let html = '';
        usersList.forEach(u => {
            const statusClass = 'status-' + u.status;
            let reqBadge = '<span style="color:var(--text-muted); font-size:0.75rem;">None</span>';
            if (u.req_status === 'pending') {
                reqBadge = '<span class="notification-chip">🔔 New Question</span>';
            } else if (u.req_status === 'replied') {
                reqBadge = '<span style="color:var(--success); font-weight:700; font-size:0.75rem;">✓ Replied</span>';
            }

            html += `
                <tr>
                    <td style="font-weight: 600; color: var(--text-muted);">#${u.id}</td>
                    <td>
                        <a class="user-profile-link" onclick="openUserProfile(${u.id})">
                            <span class="user-avatar-circle" style="width: 22px; height: 22px; font-size: 0.68rem;">${escapeHtml(u.username.charAt(0).toUpperCase())}</span>
                            <span>${escapeHtml(u.username)}</span>
                        </a>
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem;">${escapeHtml(u.email)}</td>
                    <td>
                        <select onchange="updateRole(${u.id}, this.value)" class="form-control" style="width: auto; padding: 0.2rem 0.45rem; font-size: 0.78rem;">
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
                        ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.75rem; background: var(--success); border-color: var(--success);">Approve</button>` : ''}
                        <button onclick="openUserProfile(${u.id})" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.75rem;">
                            ${u.req_status === 'pending' ? '💬 Reply' : 'Inspect'}
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function openUserProfile(userId) {
        activeModalUserId = userId;
        const u = usersList.find(item => item.id == userId);
        if (!u) return;

        document.getElementById('profileModalTitle').innerHTML = `<span>👤</span> Profile & Chat Thread: <strong>${escapeHtml(u.username)}</strong>`;

        document.getElementById('profileModalBody').innerHTML = `
            <!-- Info Card -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; font-size: 0.85rem; background: var(--surface-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div><strong>User ID:</strong> #${u.id}</div>
                <div><strong>Email:</strong> ${escapeHtml(u.email)}</div>
                <div><strong>Joined:</strong> ${u.created_at || '—'}</div>
                <div>
                    <strong>Role:</strong> 
                    <select onchange="updateRole(${u.id}, this.value)" class="form-control" style="width: auto; padding: 0.15rem 0.35rem; font-size: 0.78rem; display: inline-block; margin-left: 0.3rem;">
                        <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                        <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </div>
                <div>
                    <strong>Status:</strong> <span class="status-badge status-${u.status}">${u.status}</span>
                </div>
                <div>
                    ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" class="btn btn-primary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; background: var(--success); border-color: var(--success);">Approve Account Now</button>` : ''}
                </div>
            </div>

            <!-- Single Consultation Chat Stream -->
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <strong style="font-size: 0.95rem; color: var(--text-primary);">💬 Consultation Stream</strong>
                    <button onclick="clearChatHistory(${u.id})" class="btn btn-danger btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">🗑️ Clear Thread History</button>
                </div>
                <div id="adminChatContainer" class="admin-chat-thread-box">
                    <div style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading messages...</div>
                </div>
            </div>

            <!-- Admin Message Composer -->
            <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.35rem;">
                    Send Consultation Reply:
                </label>
                <textarea id="replyMsg_${u.id}" class="form-control" rows="3" placeholder="Type your response to ${escapeHtml(u.username)}..."></textarea>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.6rem;">
                    <button onclick="sendAdminReply(${u.id})" class="btn btn-primary btn-sm">✉️ Send Reply</button>
                </div>
            </div>
        `;

        document.getElementById('userProfileModal').style.display = 'flex';
        loadAdminChatThread(userId);
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
                    <div style="font-size: 0.76rem; background: rgba(0,0,0,0.03); border-radius: 4px; padding: 0.25rem 0.45rem; margin-bottom: 0.35rem;">
                        <strong>Target Name:</strong> ${escapeHtml(m.name_lookup)} | <strong>Relation:</strong> ${escapeHtml(m.relationship || 'N/A')}
                    </div>
                ` : '';

                html += `
                    <div class="stream-bubble ${bubbleClass}">
                        <div class="bubble-meta-header">
                            <strong style="color: var(--text-primary);">${senderTitle}</strong>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span>${m.created_at || ''}</span>
                                <button onclick="deleteSingleMessage(${m.id}, ${m.user_id})" class="btn btn-danger btn-sm" style="font-size: 0.65rem; padding: 0.05rem 0.3rem;">Del</button>
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

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
// admin.php
$pageTitle = 'Admin Panel - User Profiles & Single Chat Thread';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    echo '<main class="container" style="text-align: center; padding: 3rem 1rem;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 2rem; border-radius: 8px; max-width: 500px; margin: 0 auto;">
                <h2 style="color: #dc2626; margin-bottom: 0.5rem;">Access Denied</h2>
                <p style="color: #64748b; margin-bottom: 1rem;">You must have Administrator role privileges to view this management page.</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<style>
    .user-name-link {
        color: #2563eb;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
    }
    .user-name-link:hover {
        color: #1d4ed8;
    }
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }
    .modal-content-profile {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        width: 100%;
        max-width: 720px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-height: 90vh;
        overflow-y: auto;
    }
    .notification-pill {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }

    /* Single Chat Thread in Admin View */
    .admin-chat-thread {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.85rem;
        max-height: 350px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .admin-chat-bubble {
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        font-size: 0.85rem;
        position: relative;
    }

    .bubble-user {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
    }

    .bubble-admin {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
    }
</style>

<main class="container">
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
            <div>
                <h2 style="font-size: 1.25rem; color: #0f172a;">Admin User Management & Chat History</h2>
                <p style="font-size: 0.85rem; color: #64748b;">Click on any username to open their profile, view complete single chat history, send replies, or manage message deletion.</p>
            </div>
            <button onclick="loadUsers()" class="btn btn-sm">Refresh List 🔄</button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.6rem;">ID</th>
                        <th style="padding: 0.6rem;">Username (Click Profile)</th>
                        <th style="padding: 0.6rem;">Email</th>
                        <th style="padding: 0.6rem;">Role</th>
                        <th style="padding: 0.6rem;">Status</th>
                        <th style="padding: 0.6rem;">Chat Request Status</th>
                        <th style="padding: 0.6rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr><td colspan="7" style="padding: 1rem; text-align: center; color: #64748b;">Loading registered users...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Single User Profile & Chat Thread Modal View -->
<div id="userProfileModal" class="modal-overlay">
    <div class="modal-content-profile">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
            <h3 id="profileModalTitle" style="font-size: 1.15rem; color: #0f172a;">User Profile & Chat History</h3>
            <button onclick="closeProfileModal()" class="btn btn-sm" style="color: #dc2626;">✕ Close</button>
        </div>

        <div id="profileModalBody">
            <!-- Dynamically populated -->
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
            usersList = users;
            const tbody = document.getElementById('userTableBody');
            if (!Array.isArray(users) || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="padding: 1rem; text-align: center;">No users found.</td></tr>';
                return;
            }
            let html = '';
            users.forEach(u => {
                const statusClass = 'status-' + u.status;
                let reqStatusBadge = '<span style="color:#94a3b8; font-size:0.75rem;">None</span>';
                if (u.req_status === 'pending') {
                    reqStatusBadge = '<span class="notification-pill">🔔 New Question</span>';
                } else if (u.req_status === 'replied') {
                    reqStatusBadge = '<span style="color:#16a34a; font-weight:600; font-size:0.75rem;">✅ Replied</span>';
                }

                html += `
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 0.6rem;">#${u.id}</td>
                        <td style="padding: 0.6rem;">
                            <span class="user-name-link" onclick="openUserProfile(${u.id})">👤 ${escapeHtml(u.username)}</span>
                            ${u.req_status === 'pending' ? '<span style="display:inline-block; width:8px; height:8px; background:#dc2626; border-radius:50%; margin-left:4px;" title="New question submitted"></span>' : ''}
                        </td>
                        <td style="padding: 0.6rem; color: #64748b;">${escapeHtml(u.email)}</td>
                        <td style="padding: 0.6rem;">
                            <select onchange="updateRole(${u.id}, this.value)" style="padding: 0.15rem 0.35rem; font-size: 0.75rem; border-radius: 4px; border: 1px solid #cbd5e1;">
                                <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                                <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                                <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                            </select>
                        </td>
                        <td style="padding: 0.6rem;">
                            <span class="status-badge ${statusClass}">${escapeHtml(u.status)}</span>
                        </td>
                        <td style="padding: 0.6rem;">
                            ${reqStatusBadge}
                        </td>
                        <td style="padding: 0.6rem; text-align: right;">
                            <button onclick="openUserProfile(${u.id})" class="btn btn-sm ${u.req_status === 'pending' ? 'btn-primary' : ''}" style="font-size:0.7rem; padding: 0.15rem 0.45rem;">
                                ${u.req_status === 'pending' ? 'Reply Chat 💬' : 'View Profile'}
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            document.getElementById('userTableBody').innerHTML = '<tr><td colspan="7" style="padding: 1rem; text-align: center; color: #dc2626;">Failed to load user list.</td></tr>';
        });
    }

    function openUserProfile(userId) {
        activeModalUserId = userId;
        const u = usersList.find(item => item.id == userId);
        if (!u) return;

        document.getElementById('profileModalTitle').innerText = '👤 Profile & Chat Thread: ' + u.username;

        document.getElementById('profileModalBody').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem; font-size: 0.85rem; background: #f8fafc; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <div><strong>User ID:</strong> #${u.id}</div>
                <div><strong>Username:</strong> ${escapeHtml(u.username)}</div>
                <div><strong>Email:</strong> ${escapeHtml(u.email)}</div>
                <div><strong>Joined:</strong> ${u.created_at || '-'}</div>
                <div>
                    <strong>Role:</strong> 
                    <select onchange="updateRole(${u.id}, this.value)" style="padding: 0.15rem 0.3rem; font-size: 0.75rem;">
                        <option value="public" ${u.role === 'public' ? 'selected' : ''}>Public</option>
                        <option value="staff" ${u.role === 'staff' ? 'selected' : ''}>Staff</option>
                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </div>
                <div>
                    <strong>Status:</strong> <span class="status-badge status-${u.status}">${u.status}</span>
                </div>
            </div>

            <!-- Single Chat Thread Box -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <h4 style="font-size: 0.95rem; color: #0f172a;">💬 Single Chat History Thread</h4>
                    <button onclick="clearChatHistory(${u.id})" class="btn btn-danger btn-sm" style="font-size: 0.7rem;">Clear Full Chat History 🗑️</button>
                </div>
                <div id="adminChatContainer" class="admin-chat-thread">
                    <div style="text-align: center; color: #64748b;">Loading chat messages...</div>
                </div>
            </div>

            <!-- Admin Reply Box -->
            <div style="border-top: 1px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.5rem;">
                <h4 style="font-size: 0.9rem; color: #0f172a; margin-bottom: 0.35rem;">Send Reply to ${escapeHtml(u.username)}</h4>
                <textarea id="replyMsg_${u.id}" rows="3" style="width: 100%; padding: 0.5rem; font-size: 0.85rem; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit;" placeholder="Type your reply message..."></textarea>
                <div style="display: flex; gap: 0.4rem; justify-content: flex-end; margin-top: 0.4rem;">
                    ${u.status === 'pending' ? `<button onclick="approveUser(${u.id})" class="btn btn-sm btn-primary" style="background:#16a34a; border-color:#16a34a;">Approve Account</button>` : ''}
                    <button onclick="sendAdminReply(${u.id})" class="btn btn-sm btn-primary">Send Reply ✉️</button>
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
                container.innerHTML = '<div style="text-align: center; color: #64748b; font-size: 0.85rem; padding: 1rem;">No chat messages yet.</div>';
                return;
            }

            let html = '';
            messages.forEach(m => {
                const isUser = m.sender === 'user';
                const bubbleClass = isUser ? 'bubble-user' : 'bubble-admin';
                const senderTitle = isUser ? '👤 User Question' : '🛡️ Admin Reply';
                const details = m.name_lookup ? `<div style="font-size: 0.75rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.2rem; margin-bottom: 0.2rem;"><strong>Lookup:</strong> ${escapeHtml(m.name_lookup)} | <strong>Rel:</strong> ${escapeHtml(m.relationship)} | <strong>Name:</strong> ${escapeHtml(m.name)}</div>` : '';

                html += `
                    <div class="admin-chat-bubble ${bubbleClass}">
                        <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #64748b; margin-bottom: 0.2rem;">
                            <strong>${senderTitle}</strong>
                            <div>
                                <span>${m.created_at || ''}</span>
                                <button onclick="deleteSingleMessage(${m.id}, ${m.user_id})" class="btn btn-danger btn-sm" style="font-size: 0.65rem; padding: 0 0.25rem; margin-left: 0.4rem;">Del</button>
                            </div>
                        </div>
                        ${details}
                        <div>${nl2br(escapeHtml(m.message))}</div>
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
            alert('Please type a reply message first.');
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
            } else alert('Failed to send reply: ' + (data.error || ''));
        });
    }

    function deleteSingleMessage(chatId, userId) {
        if (!confirm('Admin Privilege: Are you sure you want to delete this message?')) return;
        fetch('api.php?action=delete_chat_message', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ chat_id: chatId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadAdminChatThread(userId);
            else alert('Failed to delete message');
        });
    }

    function clearChatHistory(userId) {
        if (!confirm('Admin Privilege: Are you sure you want to CLEAR ALL CHAT HISTORY for this user?')) return;
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
            } else alert('Failed to approve user');
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
            if (data.success) alert('User role updated to ' + role);
            else alert('Failed to update role');
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function nl2br(str) {
        return (str || '').replace(/\n/g, "<br>");
    }

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

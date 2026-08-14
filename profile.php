<?php
// profile.php
$pageTitle = 'Member Profile & Consultation Stream';
require_once __DIR__ . '/includes/header.php';

requireLogin();
?>

<style>
    .profile-dashboard-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.75rem;
        align-items: flex-start;
    }

    @media (max-width: 860px) {
        .profile-dashboard-layout {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
    }

    .profile-card-panel {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .user-profile-header-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-subtle);
    }

    .large-user-avatar {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #ffffff;
        font-size: 1.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        box-shadow: var(--shadow-sm);
    }

    .profile-meta-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .meta-data-group {
        display: flex;
        flex-direction: column;
    }

    .meta-data-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }

    .meta-data-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 0.15rem;
    }

    /* Consultation Stream */
    .chat-stream-container {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        padding: 1.15rem;
        max-height: 420px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .chat-bubble-stream {
        max-width: 82%;
        padding: 0.85rem 1.1rem;
        border-radius: var(--radius-md);
        font-size: 0.9rem;
        line-height: 1.5;
        position: relative;
    }

    .user-stream-bubble {
        align-self: flex-end;
        background: var(--primary-light);
        border: 1px solid var(--primary-border);
        color: #1e3a8a;
        border-bottom-right-radius: 2px;
    }

    .admin-stream-bubble {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid var(--border-medium);
        color: var(--text-primary);
        box-shadow: var(--shadow-xs);
        border-bottom-left-radius: 2px;
    }

    .stream-bubble-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.72rem;
        margin-bottom: 0.35rem;
        color: var(--text-muted);
        gap: 1rem;
    }

    /* Query Form */
    .query-form-card {
        border-top: 1px solid var(--border-subtle);
        padding-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }
</style>

<main class="container">
    <div class="profile-dashboard-layout">
        <!-- Left: Account & Identity Card -->
        <div class="profile-card-panel">
            <div class="user-profile-header-card">
                <div class="large-user-avatar">
                    <?php echo strtoupper(substr($currentUser['username'], 0, 1)); ?>
                </div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                    <?php echo htmlspecialchars($currentUser['username']); ?>
                </h2>
                <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 0.4rem;">
                    <span class="role-badge role-<?php echo htmlspecialchars($currentUser['role']); ?>">
                        <?php echo htmlspecialchars($currentUser['role']); ?>
                    </span>
                    <span class="status-badge status-<?php echo htmlspecialchars($currentUser['status']); ?>">
                        <?php echo htmlspecialchars($currentUser['status']); ?>
                    </span>
                </div>
            </div>

            <div class="profile-meta-list">
                <div class="meta-data-group">
                    <span class="meta-data-label">Account Email</span>
                    <span class="meta-data-value"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
                <div class="meta-data-group">
                    <span class="meta-data-label">Membership Type</span>
                    <span class="meta-data-value">
                        <?php echo ucfirst(htmlspecialchars($currentUser['role'])); ?> Access
                    </span>
                </div>
            </div>

            <div style="background: var(--surface-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.85rem; font-size: 0.82rem; color: var(--text-secondary); line-height: 1.5;">
                <strong>💡 Consultation Guidance:</strong> You can submit specific inquiries to our administration team. All questions and replies remain organized in your persistent consultation stream.
            </div>
        </div>

        <!-- Right: Consultation Stream & Question Composer -->
        <div class="profile-card-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.85rem;">
                <div>
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.45rem;">
                        <span>💬</span> Consultation Dialogue & Inquiries
                    </h2>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.15rem;">Private direct thread between your account and the administrative scholars.</p>
                </div>
                <button id="btnToggleKeyboard" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                    ⌨️ Urdu Keyboard
                </button>
            </div>

            <!-- Messages Stream Box -->
            <div id="chatBoxThread" class="chat-stream-container">
                <div style="text-align: center; color: var(--text-muted); font-size: 0.88rem; padding: 2rem;">Loading consultation history...</div>
            </div>

            <div id="requestAlert" style="display: none;"></div>

            <!-- Ask New Question / Submit Consultation Query -->
            <form id="circumstanceRequestForm" class="query-form-card">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">
                    Ask a New Consultation Question
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label class="form-label" for="nameLookup">Target Name to Analyze *</label>
                        <input type="text" id="nameLookup" class="form-control" required placeholder="e.g. احمد / فاطمہ" style="font-family: var(--font-arabic); font-size: 1.1rem; direction: rtl;">
                    </div>

                    <div>
                        <label class="form-label" for="relationship">Relationship / Connection *</label>
                        <input type="text" id="relationship" class="form-control" required placeholder="e.g. Self, Spouse, Business Partner">
                    </div>
                </div>

                <div>
                    <label class="form-label" for="fullName">Your Full Name (for verification) *</label>
                    <input type="text" id="fullName" class="form-control" required placeholder="Enter your full registered name">
                </div>

                <div>
                    <label class="form-label" for="question">Detailed Question / Specific Circumstance *</label>
                    <textarea id="question" rows="3" class="form-control" required placeholder="Type your question or circumstance details here for the admin..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 0.65rem;">
                    ✉️ Submit Question to Admin
                </button>
            </form>

            <!-- Virtual Urdu Keyboard Drawer (Default Hidden) -->
            <div class="keyboard-drawer-card" id="keyboardContainer" style="display: none; margin-top: 1rem;">
                <div class="kb-header-row">
                    <span class="kb-title">⌨️ Virtual Urdu Keyboard (Target: <span id="activeFieldLabel" style="color: var(--primary);">Target Name</span>)</span>
                    <button id="btnCloseKeyboard" class="btn btn-secondary btn-sm" style="color: var(--danger);">✕ Close</button>
                </div>
                <div class="kb-special-keys-row">
                    <button id="btnSpaceBar" class="btn btn-secondary" style="flex: 2; padding: 0.5rem;">Space Bar ␣</button>
                    <button id="btnBackspace" class="btn btn-danger" style="flex: 1; padding: 0.5rem;">Backspace ⌫</button>
                </div>
                <div class="kb-grid-matrix" id="lettersGrid"></div>
            </div>

        </div>
    </div>
</main>

<script>
    const letterData = [
        { char: 'ا' }, { char: 'آ' }, { char: 'ب' }, { char: 'پ' }, { char: 'ج' }, { char: 'چ' },
        { char: 'د' }, { char: 'ڈ' }, { char: 'ہ' }, { char: 'ھ' }, { char: 'و' }, { char: 'ز' },
        { char: 'ژ' }, { char: 'ح' }, { char: 'ط' }, { char: 'ی' }, { char: 'ے' }, { char: 'ک' },
        { char: 'گ' }, { char: 'ل' }, { char: 'م' }, { char: 'ن' }, { char: 'ں' }, { char: 'س' },
        { char: 'ع' }, { char: 'ف' }, { char: 'ص' }, { char: 'ق' }, { char: 'ر' }, { char: 'ڑ' },
        { char: 'ش' }, { char: 'ت' }, { char: 'ٹ' }, { char: 'ث' }, { char: 'خ' }, { char: 'ذ' },
        { char: 'ض' }, { char: 'ظ' }, { char: 'غ' }
    ];

    let activeInputField = document.getElementById('nameLookup');

    function renderVirtualKeyboard() {
        const grid = document.getElementById('lettersGrid');
        if (!grid) return;
        grid.innerHTML = '';
        letterData.forEach(item => {
            const card = document.createElement('div');
            card.className = 'kb-key-tile';
            card.innerHTML = `<span class="kb-arabic-glyph">${item.char}</span>`;
            card.onclick = () => insertChar(item.char);
            grid.appendChild(card);
        });
    }

    function insertChar(char) {
        if (!activeInputField) activeInputField = document.getElementById('nameLookup');
        const start = activeInputField.selectionStart || activeInputField.value.length;
        const end = activeInputField.selectionEnd || activeInputField.value.length;
        const val = activeInputField.value;
        activeInputField.value = val.substring(0, start) + char + val.substring(end);
        activeInputField.selectionStart = activeInputField.selectionEnd = start + char.length;
        activeInputField.focus();
    }

    function backspaceChar() {
        if (!activeInputField) activeInputField = document.getElementById('nameLookup');
        const start = activeInputField.selectionStart;
        const end = activeInputField.selectionEnd;
        const val = activeInputField.value;
        if (start === end && start > 0) {
            activeInputField.value = val.substring(0, start - 1) + val.substring(end);
            activeInputField.selectionStart = activeInputField.selectionEnd = start - 1;
        } else if (start !== end) {
            activeInputField.value = val.substring(0, start) + val.substring(end);
            activeInputField.selectionStart = activeInputField.selectionEnd = start;
        }
        activeInputField.focus();
    }

    function loadUserChats() {
        fetch('api.php?action=get_user_chats')
        .then(res => res.json())
        .then(messages => {
            const chatBox = document.getElementById('chatBoxThread');
            if (!Array.isArray(messages) || messages.length === 0) {
                chatBox.innerHTML = '<div style="text-align: center; color: var(--text-muted); font-size: 0.88rem; padding: 2rem;">No consultation messages yet. Submit your first inquiry below!</div>';
                return;
            }

            let html = '';
            messages.forEach(m => {
                const isUser = m.sender === 'user';
                const bubbleClass = isUser ? 'user-stream-bubble' : 'admin-stream-bubble';
                const senderTitle = isUser ? '👤 You (Inquiry)' : '🛡️ Admin Response';
                const details = m.name_lookup ? `
                    <div style="font-size: 0.75rem; background: rgba(0,0,0,0.03); border-radius: 4px; padding: 0.25rem 0.45rem; margin-bottom: 0.35rem;">
                        <strong>Target:</strong> ${escapeHtml(m.name_lookup)} | <strong>Rel:</strong> ${escapeHtml(m.relationship)} | <strong>Name:</strong> ${escapeHtml(m.name)}
                    </div>
                ` : '';

                html += `
                    <div class="chat-bubble-stream ${bubbleClass}">
                        <div class="stream-bubble-header">
                            <strong style="color: var(--text-primary);">${senderTitle}</strong>
                            <span>${m.created_at || ''}</span>
                        </div>
                        ${details}
                        <div style="white-space: pre-wrap;">${escapeHtml(m.message)}</div>
                    </div>
                `;
            });
            chatBox.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(() => {
            document.getElementById('chatBoxThread').innerHTML = '<div style="color:var(--danger); text-align:center; padding:1rem;">Failed to load chat history.</div>';
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderVirtualKeyboard();
        loadUserChats();

        const allInputs = document.querySelectorAll('input[type="text"], input[type="search"], textarea');
        allInputs.forEach(el => {
            el.addEventListener('focus', () => {
                activeInputField = el;
                const label = el.placeholder || el.id || 'Input';
                const lblEl = document.getElementById('activeFieldLabel');
                if (lblEl) lblEl.innerText = label;
            });
        });

        const kbContainer = document.getElementById('keyboardContainer');
        document.getElementById('btnToggleKeyboard').addEventListener('click', () => {
            kbContainer.style.display = (kbContainer.style.display === 'none' || !kbContainer.style.display) ? 'flex' : 'none';
        });
        document.getElementById('btnCloseKeyboard').addEventListener('click', () => {
            kbContainer.style.display = 'none';
        });

        document.getElementById('btnSpaceBar').addEventListener('click', () => insertChar(' '));
        document.getElementById('btnBackspace').addEventListener('click', backspaceChar);

        const form = document.getElementById('circumstanceRequestForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const name_lookup = document.getElementById('nameLookup').value.trim();
            const relationship = document.getElementById('relationship').value.trim();
            const name = document.getElementById('fullName').value.trim();
            const question = document.getElementById('question').value.trim();
            const alertDiv = document.getElementById('requestAlert');

            fetch('api.php?action=send_user_chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name_lookup, relationship, name, question })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Question submitted successfully!');
                    alertDiv.style.display = 'flex';

                    // Clear form inputs
                    form.reset();

                    // Reload stream
                    loadUserChats();

                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Submission failed.';
                    alertDiv.style.display = 'flex';
                }
            })
            .catch(() => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'Network error submitting request.';
                alertDiv.style.display = 'flex';
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

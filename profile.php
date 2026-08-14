<?php
// profile.php
$pageTitle = 'Member Profile & Consultation Stream';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser) {
    header('Location: index.php?auth=login');
    exit;
}
?>

<style>
    .profile-dashboard-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.5rem;
        align-items: flex-start;
    }

    @media (max-width: 840px) {
        .profile-dashboard-layout {
            grid-template-columns: 1fr;
        }
    }

    .profile-card-panel {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* Left Account Card */
    .user-profile-header-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-subtle);
    }

    .large-user-avatar {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #ffffff;
        font-size: 1.8rem;
        font-weight: 800;
        border-radius: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.85rem;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .profile-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        font-size: 0.88rem;
    }

    .info-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
        border-bottom: 1px dashed var(--border-subtle);
    }

    .info-item-label {
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .info-item-val {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Chat / Consultation Stream */
    .chat-stream-container {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        border-radius: 0;
        padding: 1rem;
        max-height: 350px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .stream-bubble {
        padding: 0.75rem 1rem;
        border-radius: 0;
        font-size: 0.88rem;
        line-height: 1.5;
        max-width: 85%;
    }

    .user-stream-bubble {
        align-self: flex-end;
        background: var(--primary-light);
        border: 1px solid var(--primary-border);
        color: #1e3a8a;
    }

    .admin-stream-bubble {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid var(--border-medium);
        color: var(--text-primary);
        box-shadow: var(--shadow-xs);
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

    /* Keyboard drawer inside profile: zero border radius */
    .keyboard-drawer-card {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        padding: 1.25rem;
        box-shadow: var(--shadow-md);
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        animation: fadeIn 0.15s ease-in-out;
    }

    .kb-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 0.6rem;
    }

    .kb-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .kb-special-keys-row {
        display: flex;
        gap: 0.5rem;
    }

    .kb-grid-matrix {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(56px, 1fr));
        gap: 0.45rem;
        direction: rtl;
    }

    .kb-key-tile {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        height: 58px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 0.25rem 0.35rem;
        cursor: pointer;
        user-select: none;
        transition: all 0.12s ease;
        box-shadow: var(--shadow-xs);
    }

    .kb-key-tile:hover {
        background: var(--surface-subtle);
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .kb-arabic-glyph {
        font-family: var(--font-arabic);
        font-size: 1.85rem;
        line-height: 1;
        color: var(--text-primary);
    }

    .kb-val-num {
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--accent-gold);
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

            <div class="profile-info-list">
                <div class="info-list-item">
                    <span class="info-item-label">Account ID</span>
                    <span class="info-item-val">#<?php echo $currentUser['id']; ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Email Address</span>
                    <span class="info-item-val" style="font-size: 0.82rem; word-break: break-all;"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Member Since</span>
                    <span class="info-item-val" style="font-size: 0.8rem;"><?php echo htmlspecialchars($currentUser['created_at'] ?? '—'); ?></span>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                <h3 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.04em;">
                    Current Circumstance
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                    <?php echo !empty($currentUser['circumstance']) ? nl2br(htmlspecialchars($currentUser['circumstance'])) : '<em style="color: var(--text-muted);">No circumstance recorded. Submit below.</em>'; ?>
                </p>
            </div>
        </div>

        <!-- Right: Consultation Stream & Question Composer -->
        <div class="profile-card-panel">
            <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.85rem;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.45rem;">
                    <span>💬</span> Consultation Dialogue & Inquiries
                </h2>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.15rem;">Private direct thread between your account and the administrative scholars.</p>
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
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label class="form-label" for="nameLookup" style="margin-bottom: 0;">Target Name *</label>
                            <button id="btnToggleKeyboard" type="button" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 2px;">
                                ⌨️ Urdu Keyboard
                            </button>
                        </div>
                        <input type="text" id="nameLookup" class="form-control" required placeholder="e.g. احمد / فاطمہ" style="font-family: var(--font-arabic); font-size: 1.1rem; direction: rtl; border-radius: 0;">
                    </div>

                    <div>
                        <label class="form-label" for="relationship">Relationship / Connection *</label>
                        <input type="text" id="relationship" class="form-control" required placeholder="e.g. Self, Spouse, Business Partner" style="border-radius: 0;">
                    </div>
                </div>

                <!-- Virtual Urdu Keyboard Drawer (Directly under nameLookup, default hidden) -->
                <div class="keyboard-drawer-card" id="keyboardContainer" style="display: none; margin-top: 0.5rem;">
                    <div class="kb-header-row">
                        <span class="kb-title">⌨️ Virtual Urdu Keyboard (Target: <span id="activeFieldLabel" style="color: var(--primary);">Target Name</span>)</span>
                        <button id="btnCloseKeyboard" type="button" class="btn btn-secondary btn-sm" style="color: var(--danger); border-radius: 2px;">✕ Close</button>
                    </div>
                    <div class="kb-special-keys-row">
                        <button id="btnSpaceBar" type="button" class="btn btn-secondary" style="flex: 2; padding: 0.5rem; border-radius: 2px;">Space Bar ␣</button>
                        <button id="btnBackspace" type="button" class="btn btn-danger" style="flex: 1; padding: 0.5rem; border-radius: 2px;">Backspace ⌫</button>
                    </div>
                    <div class="kb-grid-matrix" id="lettersGrid"></div>
                </div>

                <div>
                    <label class="form-label" for="fullName">Your Full Name (for verification) *</label>
                    <input type="text" id="fullName" class="form-control" required placeholder="Enter your full registered name" style="border-radius: 0;">
                </div>

                <div>
                    <label class="form-label" for="question">Detailed Question / Specific Circumstance *</label>
                    <textarea id="question" rows="3" class="form-control" required placeholder="Type your question or circumstance details here for the admin..." style="border-radius: 0;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 0.65rem; border-radius: 2px;">
                    ✉️ Submit Question to Admin
                </button>
            </form>

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

    let activeInput = document.getElementById('nameLookup');

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
        if (!activeInput) activeInput = document.getElementById('nameLookup');
        const start = activeInput.selectionStart || activeInput.value.length;
        const end = activeInput.selectionEnd || activeInput.value.length;
        const val = activeInput.value;
        activeInput.value = val.substring(0, start) + char + val.substring(end);
        activeInput.selectionStart = activeInput.selectionEnd = start + char.length;
        activeInput.focus();
    }

    function backspaceChar() {
        if (!activeInput) activeInput = document.getElementById('nameLookup');
        const start = activeInput.selectionStart;
        const end = activeInput.selectionEnd;
        const val = activeInput.value;
        if (start === end && start > 0) {
            activeInput.value = val.substring(0, start - 1) + val.substring(end);
            activeInput.selectionStart = activeInput.selectionEnd = start - 1;
        } else if (start !== end) {
            activeInput.value = val.substring(0, start) + val.substring(end);
            activeInput.selectionStart = activeInput.selectionEnd = start;
        }
        activeInput.focus();
    }

    function loadUserChats() {
        fetch('api.php?action=get_user_chats')
        .then(res => res.json())
        .then(messages => {
            const chatBox = document.getElementById('chatBoxThread');
            if (!chatBox) return;

            if (!Array.isArray(messages) || messages.length === 0) {
                chatBox.innerHTML = '<div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2rem;">No consultation dialogues submitted yet. Ask your question below.</div>';
                return;
            }

            let html = '';
            messages.forEach(m => {
                const isUser = m.sender === 'user';
                const bubbleClass = isUser ? 'user-stream-bubble' : 'admin-stream-bubble';
                const senderLabel = isUser ? '👤 You (Inquiry)' : '🛡️ Admin Scholar Reply';
                const details = m.name_lookup ? `
                    <div style="font-size: 0.76rem; background: rgba(0,0,0,0.03); border-radius: 0; padding: 0.25rem 0.45rem; margin-bottom: 0.35rem;">
                        <strong>Target Name:</strong> ${escapeHtml(m.name_lookup)} | <strong>Relation:</strong> ${escapeHtml(m.relationship || 'N/A')}
                    </div>
                ` : '';

                html += `
                    <div class="stream-bubble ${bubbleClass}">
                        <div class="stream-bubble-header">
                            <strong style="color: var(--text-primary);">${senderLabel}</strong>
                            <span>${m.created_at || ''}</span>
                        </div>
                        ${details}
                        <div style="white-space: pre-wrap;">${escapeHtml(m.message)}</div>
                    </div>
                `;
            });
            chatBox.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderVirtualKeyboard();
        loadUserChats();

        const allInputs = document.querySelectorAll('input[type="text"], textarea');
        allInputs.forEach(el => {
            el.addEventListener('focus', () => {
                activeInput = el;
                const lbl = el.previousElementSibling ? el.previousElementSibling.innerText : el.id;
                const fieldLbl = document.getElementById('activeFieldLabel');
                if (fieldLbl) fieldLbl.innerText = lbl.replace('*', '').trim();
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

        // Submit form
        document.getElementById('circumstanceRequestForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value.trim();
            const nameLookup = document.getElementById('nameLookup').value.trim();
            const relationship = document.getElementById('relationship').value.trim();
            const question = document.getElementById('question').value.trim();
            const alertDiv = document.getElementById('requestAlert');

            fetch('api.php?action=circumstance_request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fullName, nameLookup, relationship, question })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Consultation inquiry submitted to admin successfully!');
                    alertDiv.style.display = 'flex';
                    document.getElementById('circumstanceRequestForm').reset();
                    loadUserChats();
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Failed to submit inquiry';
                    alertDiv.style.display = 'flex';
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

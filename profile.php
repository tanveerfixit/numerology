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
        grid-template-columns: 340px 1fr;
        gap: 1.5rem;
        align-items: flex-start;
    }

    @media (max-width: 880px) {
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
        text-align: right;
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

    /* Form Card */
    .query-form-card {
        border-top: 1px solid var(--border-subtle);
        padding-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    /* Built-in Virtual Keyboard Drawer: zero border-radius */
    .keyboard-drawer-card {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        padding: 1.15rem;
        box-shadow: var(--shadow-md);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        animation: fadeIn 0.15s ease-in-out;
    }

    .kb-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 0.5rem;
    }

    .kb-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .kb-special-keys-row {
        display: flex;
        gap: 0.5rem;
    }

    .kb-grid-matrix {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(52px, 1fr));
        gap: 0.4rem;
        direction: rtl;
    }

    .kb-key-tile {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        height: 54px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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

    .kb-key-tile:active {
        transform: translateY(0);
        background: var(--primary-light);
    }

    .kb-arabic-glyph {
        font-family: var(--font-arabic);
        font-size: 1.75rem;
        line-height: 1;
        color: var(--text-primary);
    }
</style>

<main class="container">
    <?php if (isset($_GET['submitted'])): ?>
        <div class="alert alert-success" style="border-radius: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.85rem; padding: 1rem 1.25rem;">
            <span style="font-size: 1.6rem;">✨</span>
            <div>
                <strong style="font-size: 0.95rem;">Consultation Question Sent for Administrative Review!</strong><br>
                <span style="font-size: 0.85rem; color: var(--text-secondary);">Your initial numerology question has been forwarded to the admin panel. Your account is currently in pending status; once an administrator reviews and approves your account, you will receive replies and will be able to submit follow-up questions.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="profile-dashboard-layout">
        <!-- Left: Account, Identity & Profile Information Form -->
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

            <!-- Profile Summary -->
            <div class="profile-info-list">
                <div class="info-list-item">
                    <span class="info-item-label">Account ID</span>
                    <span class="info-item-val">#<?php echo $currentUser['id']; ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Full Name</span>
                    <span class="info-item-val" id="summaryFullName"><?php echo !empty($currentUser['full_name']) ? htmlspecialchars($currentUser['full_name']) : '<em style="color:var(--text-muted); font-weight:400;">Not set</em>'; ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Contact</span>
                    <span class="info-item-val" id="summaryContact"><?php echo !empty($currentUser['contact']) ? htmlspecialchars($currentUser['contact']) : '<em style="color:var(--text-muted); font-weight:400;">Not set</em>'; ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Email</span>
                    <span class="info-item-val" style="font-size: 0.82rem; word-break: break-all;"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
                <div class="info-list-item">
                    <span class="info-item-label">Member Since</span>
                    <span class="info-item-val" style="font-size: 0.8rem;"><?php echo htmlspecialchars($currentUser['created_at'] ?? '—'); ?></span>
                </div>
            </div>

            <!-- Edit Personal Profile (Name & Contact) Form -->
            <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.15rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h3 style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.35rem;">
                        <span>✏️</span> Edit Profile Details
                    </h3>
                </div>

                <div id="profileUpdateAlert" style="display: none; margin-bottom: 0.75rem;"></div>

                <form id="editProfileDetailsForm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <label class="form-label" for="profileFullName" style="margin-bottom: 0; font-size: 0.78rem;">Full Name</label>
                            <button type="button" class="btn-urdu-kb btn-open-kb" data-target="profileFullName">
                                Urdu Keyboard
                            </button>
                        </div>
                        <input type="text" id="profileFullName" class="form-control" placeholder="e.g. محمد طارق / Tariq Ali" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" style="border-radius: 0;">
                    </div>

                    <div>
                        <label class="form-label" for="profileContact" style="font-size: 0.78rem; margin-bottom: 0.25rem;">Contact / Phone / WhatsApp</label>
                        <input type="text" id="profileContact" class="form-control" placeholder="e.g. +92 300 1234567" value="<?php echo htmlspecialchars($currentUser['contact'] ?? ''); ?>" style="border-radius: 0;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 0.25rem; padding: 0.5rem; justify-content: center; border-radius: 2px;">
                        💾 Save Profile Details
                    </button>
                </form>
            </div>

            <!-- Active Circumstance -->
            <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                <h3 style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.04em;">
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

            <?php if ($currentUser['status'] === 'approved'): ?>
                <!-- Ask New Question / Submit Consultation Query (Approved Users) -->
                <form id="circumstanceRequestForm" class="query-form-card">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">
                            Send Consultation Question / Reply
                        </h3>
                        <button type="button" class="btn-urdu-kb btn-open-kb" data-target="question">
                            Urdu Keyboard
                        </button>
                    </div>
                    
                    <div>
                        <label class="form-label" for="question" style="font-weight: 700; margin-bottom: 0.35rem;">
                            Question / Message *
                        </label>
                        <textarea id="question" rows="3" class="form-control" required placeholder="Type your consultation question, follow-up, or circumstance message here..." style="border-radius: 0;"></textarea>
                    </div>

                    <!-- Optional Context Details Accordion / Sub-fields -->
                    <details style="background: var(--surface-subtle); border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: 0; font-size: 0.85rem;">
                        <summary style="cursor: pointer; font-weight: 600; color: var(--text-secondary); user-select: none;">
                            <span>⚙️ Optional Context Details (Target Name, Relationship, Contact)</span>
                        </summary>
                        <div style="display: flex; flex-direction: column; gap: 0.65rem; margin-top: 0.75rem; padding-top: 0.65rem; border-top: 1px solid var(--border-subtle);">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                        <label class="form-label" for="nameLookup" style="margin-bottom: 0; font-size: 0.76rem;">Target Name (Optional)</label>
                                        <button type="button" class="btn btn-secondary btn-sm btn-open-kb" data-target="nameLookup" style="padding: 0.1rem 0.35rem; font-size: 0.68rem; border-radius: 2px;">
                                            ⌨️
                                        </button>
                                    </div>
                                    <input type="text" id="nameLookup" class="form-control" placeholder="e.g. احمد / فاطمہ" value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>" style="font-family: var(--font-arabic); font-size: 1.1rem; direction: rtl; border-radius: 0;">
                                </div>

                                <div>
                                    <label class="form-label" for="relationship" style="font-size: 0.76rem; margin-bottom: 0.25rem;">Relationship (Optional)</label>
                                    <input type="text" id="relationship" class="form-control" placeholder="e.g. Self, Spouse, Business" style="border-radius: 0;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
                                <div>
                                    <label class="form-label" for="fullName" style="font-size: 0.76rem; margin-bottom: 0.25rem;">Your Name (Optional)</label>
                                    <input type="text" id="fullName" class="form-control" placeholder="Enter your name" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? $currentUser['username']); ?>" style="border-radius: 0;">
                                </div>

                                <div>
                                    <label class="form-label" for="contactNumber" style="font-size: 0.76rem; margin-bottom: 0.25rem;">Contact / Phone (Optional)</label>
                                    <input type="text" id="contactNumber" class="form-control" placeholder="e.g. +92 300 1234567" value="<?php echo htmlspecialchars($currentUser['contact'] ?? ''); ?>" style="border-radius: 0;">
                                </div>
                            </div>
                        </div>
                    </details>

                    <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 0.65rem; border-radius: 2px;">
                        ✉️ Send Message to Admin
                    </button>
                </form>
            <?php else: ?>
                <!-- Pending Approval State Message -->
                <div style="background: var(--surface-subtle); border: 1px solid var(--border-medium); padding: 1.25rem; text-align: center; border-radius: 0;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.4rem;">⏳</div>
                    <strong style="font-size: 0.95rem; color: var(--text-primary); display: block; margin-bottom: 0.35rem;">
                        Account Pending Manual Admin Approval
                    </strong>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); max-width: 480px; margin: 0 auto; line-height: 1.5;">
                        Your initial question has been submitted to the admin scholars. An administrator will manually review your account and consultation request. Once approved, you will be able to send follow-up questions in this thread.
                    </p>
                    <div style="margin-top: 0.75rem;">
                        <span class="status-badge status-pending" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">Status: Pending Manual Approval</span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Virtual Urdu Keyboard Drawer (Shared, interactive for all input fields) -->
            <div class="keyboard-drawer-card" id="keyboardContainer" style="display: none; margin-top: 0.75rem;">
                <div class="kb-header-row">
                    <span class="kb-title">⌨️ Virtual Urdu Keyboard (Active Target: <span id="activeFieldLabel" style="color: var(--primary); font-weight: 700;">Target Name</span>)</span>
                    <button id="btnCloseKeyboard" type="button" class="btn btn-secondary btn-sm" style="color: var(--danger); border-radius: 2px;">✕ Close Keyboard</button>
                </div>
                <div class="kb-special-keys-row">
                    <button id="btnSpaceBar" type="button" class="btn btn-secondary" style="flex: 2; padding: 0.45rem; border-radius: 2px;">Space Bar ␣</button>
                    <button id="btnBackspace" type="button" class="btn btn-danger" style="flex: 1; padding: 0.45rem; border-radius: 2px;">Backspace ⌫</button>
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
        if (!activeInput) activeInput = document.getElementById('nameLookup') || document.getElementById('profileFullName');
        const start = activeInput.selectionStart ?? activeInput.value.length;
        const end = activeInput.selectionEnd ?? activeInput.value.length;
        const val = activeInput.value;
        activeInput.value = val.substring(0, start) + char + val.substring(end);
        activeInput.selectionStart = activeInput.selectionEnd = start + char.length;
        activeInput.focus();
        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function backspaceChar() {
        if (!activeInput) activeInput = document.getElementById('nameLookup') || document.getElementById('profileFullName');
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
        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function setActiveInput(el) {
        if (!el) return;
        activeInput = el;
        const label = el.getAttribute('placeholder') || el.id || 'Input';
        const fieldLbl = document.getElementById('activeFieldLabel');
        if (fieldLbl) fieldLbl.innerText = label;
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

        const kbContainer = document.getElementById('keyboardContainer');

        // Track focus on ALL input fields and textareas
        const allInputs = document.querySelectorAll('input[type="text"], input[type="tel"], textarea');
        allInputs.forEach(el => {
            el.addEventListener('focus', () => {
                setActiveInput(el);
            });
        });

        // Quick toggle buttons for keyboard
        document.querySelectorAll('.btn-open-kb').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = btn.getAttribute('data-target');
                const targetEl = document.getElementById(targetId);
                if (targetEl) {
                    setActiveInput(targetEl);
                    targetEl.focus();
                }
                kbContainer.style.display = 'flex';
                kbContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });

        document.getElementById('btnCloseKeyboard')?.addEventListener('click', () => {
            kbContainer.style.display = 'none';
        });

        document.getElementById('btnSpaceBar')?.addEventListener('click', () => insertChar(' '));
        document.getElementById('btnBackspace')?.addEventListener('click', backspaceChar);

        // Edit Profile Details Form Submit
        document.getElementById('editProfileDetailsForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const fullName = document.getElementById('profileFullName').value.trim();
            const contact = document.getElementById('profileContact').value.trim();
            const alertDiv = document.getElementById('profileUpdateAlert');

            fetch('api.php?action=update_profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ full_name: fullName, contact: contact })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Profile details saved successfully!');
                    alertDiv.style.display = 'flex';

                    // Update summary fields and question form sync
                    document.getElementById('summaryFullName').innerText = fullName || 'Not set';
                    document.getElementById('summaryContact').innerText = contact || 'Not set';
                    if (document.getElementById('fullName')) document.getElementById('fullName').value = fullName;
                    if (document.getElementById('contactNumber')) document.getElementById('contactNumber').value = contact;

                    setTimeout(() => { alertDiv.style.display = 'none'; }, 3500);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Failed to update profile';
                    alertDiv.style.display = 'flex';
                }
            })
            .catch(() => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'Network error updating profile.';
                alertDiv.style.display = 'flex';
            });
        });

        // Submit Consultation Request Form
        document.getElementById('circumstanceRequestForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value.trim();
            const contactNumber = document.getElementById('contactNumber').value.trim();
            const nameLookup = document.getElementById('nameLookup').value.trim();
            const relationship = document.getElementById('relationship').value.trim();
            const question = document.getElementById('question').value.trim();
            const alertDiv = document.getElementById('requestAlert');

            fetch('api.php?action=circumstance_request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fullName, contactNumber, nameLookup, relationship, question })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = '✓ ' + (data.message || 'Consultation inquiry submitted to admin successfully!');
                    alertDiv.style.display = 'flex';
                    document.getElementById('nameLookup').value = '';
                    document.getElementById('relationship').value = '';
                    document.getElementById('question').value = '';
                    loadUserChats();
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Failed to submit inquiry';
                    alertDiv.style.display = 'flex';
                }
            })
            .catch(() => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'Network error submitting inquiry.';
                alertDiv.style.display = 'flex';
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

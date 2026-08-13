<?php
// profile.php
$pageTitle = 'My Profile & Circumstance Chat History';
require_once __DIR__ . '/includes/header.php';

requireLogin();
?>

<style>
    .profile-container {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 1.5rem;
        max-width: 1050px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }

    .profile-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .info-group {
        margin-bottom: 0.75rem;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.04em;
    }

    .info-val {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 500;
    }

    /* Single Chat Thread Styles */
    .chat-box-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .chat-bubble {
        max-width: 85%;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 0.88rem;
        line-height: 1.5;
        position: relative;
    }

    .chat-user {
        align-self: flex-end;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        border-bottom-right-radius: 2px;
    }

    .chat-admin {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
        border-bottom-left-radius: 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .chat-meta {
        font-size: 0.7rem;
        color: #64748b;
        margin-bottom: 0.25rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 0.85rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
    }

    /* Built-in Urdu Keyboard */
    .chart-section {
        margin-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        max-width: 1050px;
        margin-left: auto;
        margin-right: auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .letters-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.45rem;
        direction: rtl;
    }

    .letter-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        user-select: none;
        width: 54px;
        height: 54px;
        transition: all 0.12s ease;
    }

    .letter-card:hover {
        background: #f1f5f9;
        border-color: #2563eb;
    }

    .letter-arabic {
        position: absolute;
        font-family: 'Amiri', serif;
        font-size: 1.7rem;
        color: #0f172a;
        line-height: 1;
    }
</style>

<main class="container">
    <div class="profile-container">
        <!-- Account Overview Card -->
        <div class="profile-card">
            <h2 class="profile-title">
                <span>👤 Account Overview</span>
            </h2>
            
            <div class="info-group">
                <div class="info-label">Username</div>
                <div class="info-val"><?php echo htmlspecialchars($currentUser['username']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">Email Address</div>
                <div class="info-val"><?php echo htmlspecialchars($currentUser['email']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">Account Role & Status</div>
                <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 0.2rem;">
                    <span class="user-badge" style="text-transform: capitalize; font-weight: 600; color: #0f172a;">
                        Role: <?php echo htmlspecialchars($currentUser['role']); ?>
                    </span>
                    <span class="status-badge status-<?php echo htmlspecialchars($currentUser['status']); ?>">
                        <?php echo htmlspecialchars($currentUser['status']); ?>
                    </span>
                </div>
            </div>

            <!-- Quick Instructions -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; margin-top: 1rem; font-size: 0.8rem; color: #475569; line-height: 1.5;">
                ℹ️ <strong>Circumstance Q&A Chat:</strong> You can ask questions to the admin at any time using the form. All your past questions and admin replies are safely stored in your chat history log.
            </div>
        </div>

        <!-- Single Chat History & Request Form Card -->
        <div class="profile-card">
            <div class="profile-title">
                <span>💬 Circumstance Chat History & Request</span>
                <button id="btnToggleKeyboard" class="btn btn-sm" style="border-color: #2563eb; color: #2563eb; font-weight: 600;">Urdu Keyboard ⌨️</button>
            </div>

            <!-- Chat History Thread Box -->
            <div id="chatBoxThread" class="chat-box-wrapper">
                <div style="text-align: center; color: #64748b; font-size: 0.85rem;">Loading chat history...</div>
            </div>

            <div id="requestAlert" style="display: none;"></div>

            <!-- Ask New Question Form -->
            <form id="circumstanceRequestForm" style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                <h4 style="font-size: 0.95rem; color: #0f172a; margin-bottom: 0.75rem;">Ask a New Question / Circumstance Query</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div class="form-group">
                        <label for="nameLookup">Name Lookup *</label>
                        <input type="text" id="nameLookup" class="form-control" required placeholder="Target name to lookup">
                    </div>

                    <div class="form-group">
                        <label for="relationship">Relationship *</label>
                        <input type="text" id="relationship" class="form-control" required placeholder="e.g. Self, Spouse, Partner">
                    </div>
                </div>

                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" class="form-control" required placeholder="Enter full name for verification">
                </div>

                <div class="form-group">
                    <label for="question">Question / Query Details *</label>
                    <textarea id="question" rows="3" class="form-control" required placeholder="State your question for the admin..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.55rem;">Send Question to Admin ✉️</button>
            </form>
        </div>
    </div>

    <!-- Built-in Urdu Keyboard (DEFAULT HIDDEN) -->
    <div class="chart-section" id="keyboardContainer" style="display: none; margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem;">
            <span style="font-size: 0.85rem; font-weight: 600; color: #0f172a;">Built-in Urdu Keyboard ⌨️ (Active Target: <span id="activeFieldLabel" style="color: #2563eb;">Name Lookup</span>)</span>
            <button id="btnCloseKeyboard" class="btn btn-sm" style="color: #dc2626;">✕ Hide</button>
        </div>
        <div id="keyboardActionRow" style="display: flex; gap: 0.5rem; width: 100%;">
            <button id="btnSpaceBar" class="btn btn-primary" style="flex: 1; padding: 0.4rem 0;">Space Bar ␣</button>
            <button id="btnBackspace" class="btn btn-danger" style="flex: 1; padding: 0.4rem 0;">Backspace ⌫</button>
        </div>
        <div class="letters-grid" id="lettersGrid"></div>
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

    function renderMainGrid() {
        const grid = document.getElementById('lettersGrid');
        if (!grid) return;
        grid.innerHTML = '';
        letterData.forEach(item => {
            const card = document.createElement('div');
            card.className = 'letter-card';
            card.innerHTML = `<span class="letter-arabic">${item.char}</span>`;
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
                chatBox.innerHTML = '<div style="text-align: center; color: #64748b; font-size: 0.85rem; padding: 1rem;">No chat history yet. Ask your first question below!</div>';
                return;
            }

            let html = '';
            messages.forEach(m => {
                const isUser = m.sender === 'user';
                const bubbleClass = isUser ? 'chat-user' : 'chat-admin';
                const senderTitle = isUser ? '👤 You' : '🛡️ Admin Reply';
                const details = m.name_lookup ? `<div style="font-size: 0.75rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.25rem; margin-bottom: 0.25rem;"><strong>Target:</strong> ${escapeHtml(m.name_lookup)} | <strong>Rel:</strong> ${escapeHtml(m.relationship)} | <strong>Name:</strong> ${escapeHtml(m.name)}</div>` : '';

                html += `
                    <div class="chat-bubble ${bubbleClass}">
                        <div class="chat-meta">
                            <strong>${senderTitle}</strong>
                            <span>${m.created_at || ''}</span>
                        </div>
                        ${details}
                        <div>${nl2br(escapeHtml(m.message))}</div>
                    </div>
                `;
            });
            chatBox.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(() => {
            document.getElementById('chatBoxThread').innerHTML = '<div style="color:#dc2626; text-align:center;">Failed to load chat history.</div>';
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function nl2br(str) {
        return (str || '').replace(/\n/g, "<br>");
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderMainGrid();
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
            if (kbContainer.style.display === 'none' || !kbContainer.style.display) {
                kbContainer.style.display = 'flex';
            } else {
                kbContainer.style.display = 'none';
            }
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
                    alertDiv.innerText = data.message || 'Question sent successfully!';
                    alertDiv.style.display = 'block';

                    // CLEAR ALL INPUT FIELDS so user can ask again cleanly
                    document.getElementById('nameLookup').value = '';
                    document.getElementById('relationship').value = '';
                    document.getElementById('fullName').value = '';
                    document.getElementById('question').value = '';

                    // Reload chat history thread
                    loadUserChats();

                    setTimeout(() => { alertDiv.style.display = 'none'; }, 4000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = data.error || 'Submission failed.';
                    alertDiv.style.display = 'block';
                }
            })
            .catch(() => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'Network error submitting request.';
                alertDiv.style.display = 'block';
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

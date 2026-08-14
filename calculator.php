<?php
// calculator.php
$pageTitle = 'Interactive Abjad Calculator & Telemetry Workbench';
require_once __DIR__ . '/includes/header.php';

$isAuthorized = ($currentUser && $currentUser['status'] === 'approved');
$isStaffOrAdmin = ($currentUser && in_array($currentUser['role'], ['staff', 'admin']) && $currentUser['status'] === 'approved');
$userCircumstance = $currentUser ? ($currentUser['circumstance'] ?? '') : '';
?>

<style>
    /* Calculator Page Layout: Zero border-radius on inputs, divs, cards; 2px on buttons */
    .calc-page-wrapper {
        max-width: 980px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .workbench-card {
        background: var(--surface-card);
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* Main Text Input Area */
    .input-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .input-header-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .input-stats-badge {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        border-radius: 0;
    }

    .main-calc-input {
        width: 100%;
        padding: 0.9rem 1.15rem;
        background: #ffffff;
        border: 2px solid var(--border-medium);
        border-radius: 0 !important;
        color: var(--text-primary);
        font-family: var(--font-arabic);
        font-size: 1.85rem;
        line-height: 1.4;
        direction: rtl;
        transition: all 0.15s ease;
    }

    .main-calc-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        background: #ffffff;
    }

    /* Elemental Distribution Telemetry Bar */
    .elements-telemetry-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.85rem;
        direction: rtl;
    }

    @media (max-width: 640px) {
        .elements-telemetry-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .element-telemetry-item {
        background: var(--surface-subtle);
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 0.65rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .elem-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .elem-bar-track-custom {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 0;
        overflow: hidden;
    }

    .elem-bar-fill-custom {
        height: 100%;
        border-radius: 0;
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Action Toolbar */
    .calc-toolbar-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding-top: 0.5rem;
        border-top: 1px solid var(--border-subtle);
    }

    .toolbar-left-buttons {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .metadata-inputs-group {
        display: flex;
        gap: 0.5rem;
        flex: 1;
        min-width: 260px;
    }

    .meta-field-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .meta-field-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
    }

    .meta-input-box {
        width: 100%;
        padding: 0.38rem 0.65rem;
        border: 1px solid var(--border-medium);
        border-radius: 0 !important;
        font-size: 0.85rem;
        font-family: inherit;
    }

    .meta-input-box:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* Live Results Dashboard Card */
    .results-display-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .results-display-card {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
    }

    .kpi-values-column {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: center;
        border-right: 1px solid var(--border-subtle);
        padding-right: 1.5rem;
    }

    @media (max-width: 768px) {
        .kpi-values-column {
            border-right: none;
            border-bottom: 1px solid var(--border-subtle);
            padding-right: 0;
            padding-bottom: 1rem;
            flex-direction: row;
            justify-content: space-around;
        }
    }

    .kpi-stat-block {
        display: flex;
        flex-direction: column;
    }

    .kpi-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .kpi-stat-number {
        font-size: 2.3rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    /* Letter Breakdown Matrix */
    .breakdown-section-col {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .breakdown-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .breakdown-flow-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        direction: rtl;
        align-items: center;
        min-height: 52px;
    }

    .letter-chip {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        padding: 0.35rem 0.65rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 44px;
        box-shadow: var(--shadow-xs);
        transition: transform 0.1s ease;
    }

    .letter-chip:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
    }

    .chip-char {
        font-family: var(--font-arabic);
        font-size: 1.55rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .chip-val {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--accent-gold);
    }

    .chip-elem-dot {
        width: 6px;
        height: 6px;
        border-radius: 0;
        margin-top: 2px;
    }

    .chip-separator {
        width: 1rem;
        height: 2rem;
        border-left: 2px dashed var(--border-medium);
        margin: 0 0.25rem;
        display: inline-block;
    }

    /* Built-in Urdu Keyboard Section */
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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
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

    .kb-key-tile:active {
        transform: translateY(0);
        background: var(--primary-light);
    }

    .kb-key-tile.active-in-text {
        background: var(--primary-light);
        border-color: var(--primary);
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

    /* Modal styles */
    .modal-overlay-custom {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }

    .modal-card-box {
        background: #ffffff;
        border-radius: 0;
        border: 1px solid var(--border-subtle);
        max-width: 480px;
        width: 100%;
        padding: 1.75rem;
        box-shadow: var(--shadow-xl);
        text-align: center;
        animation: modalScale 0.15s ease-out;
    }

    @keyframes modalScale {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<main class="container">
    <div class="calc-page-wrapper">

        <?php if ($currentUser && !empty($userCircumstance)): ?>
            <div class="alert alert-info" style="border-radius: 0;">
                <span style="font-size: 1.2rem;">📌</span>
                <div>
                    <strong>Your Circumstance Note:</strong>
                    <div style="font-size: 0.85rem; color: #1e3a8a; margin-top: 0.2rem;">
                        <?php echo nl2br(htmlspecialchars($userCircumstance)); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Calculator Main Workbench -->
        <div class="workbench-card">
            <!-- Top Input Header with Direct Keyboard Button -->
            <div class="input-header-row">
                <span class="input-header-label">
                    <span>🔤</span> Name or Text Input (Arabic / Urdu / Persian)
                </span>
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <button id="btnToggleKeyboard" type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border-color: var(--primary-border); font-weight: 600; padding: 0.28rem 0.75rem; border-radius: 2px;">
                        ⌨️ Urdu Keyboard
                    </button>
                    <span class="input-stats-badge" id="charCountLabel">0 characters</span>
                </div>
            </div>

            <!-- Calligraphic Text Input -->
            <input type="text" id="calcInput" class="main-calc-input" placeholder="Type name or use the Urdu Keyboard..." autocomplete="off">

            <!-- Built-in Urdu Keyboard Drawer (Directly under input field, default hidden) -->
            <div class="keyboard-drawer-card" id="keyboardContainer" style="display: none;">
                <div class="kb-header-row">
                    <span class="kb-title">
                        ⌨️ Urdu / Arabic Virtual Keyboard (Target: <span id="activeFieldLabel" style="color: var(--primary);">Main Input</span>)
                    </span>
                    <button id="btnCloseKeyboard" type="button" class="btn btn-sm btn-secondary" style="color: var(--danger); border-radius: 2px;">✕ Close Keyboard</button>
                </div>

                <div class="kb-special-keys-row">
                    <button id="btnSpaceBar" type="button" class="btn btn-secondary" style="flex: 2; padding: 0.5rem; border-radius: 2px;">Space Bar ␣</button>
                    <button id="btnBackspace" type="button" class="btn btn-danger" style="flex: 1; padding: 0.5rem; border-radius: 2px;">Backspace ⌫</button>
                </div>

                <div class="kb-grid-matrix" id="lettersGrid"></div>
            </div>

            <!-- Four Elements Telemetry Distribution -->
            <div class="elements-telemetry-grid">
                <div class="element-telemetry-item">
                    <div class="elem-meta-row" style="color: var(--fire-color);">
                        <span>🔥 Fire (آتشی)</span>
                        <span id="val-fire">0%</span>
                    </div>
                    <div class="elem-bar-track-custom">
                        <div id="bar-fire" class="elem-bar-fill-custom" style="width: 0%; background: var(--fire-color);"></div>
                    </div>
                </div>

                <div class="element-telemetry-item">
                    <div class="elem-meta-row" style="color: var(--air-color);">
                        <span>💨 Air (بادی)</span>
                        <span id="val-air">0%</span>
                    </div>
                    <div class="elem-bar-track-custom">
                        <div id="bar-air" class="elem-bar-fill-custom" style="width: 0%; background: var(--air-color);"></div>
                    </div>
                </div>

                <div class="element-telemetry-item">
                    <div class="elem-meta-row" style="color: var(--water-color);">
                        <span>💧 Water (آبی)</span>
                        <span id="val-water">0%</span>
                    </div>
                    <div class="elem-bar-track-custom">
                        <div id="bar-water" class="elem-bar-fill-custom" style="width: 0%; background: var(--water-color);"></div>
                    </div>
                </div>

                <div class="element-telemetry-item">
                    <div class="elem-meta-row" style="color: var(--earth-color);">
                        <span>🪨 Earth (خاکی)</span>
                        <span id="val-earth">0%</span>
                    </div>
                    <div class="elem-bar-track-custom">
                        <div id="bar-earth" class="elem-bar-fill-custom" style="width: 0%; background: var(--earth-color);"></div>
                    </div>
                </div>
            </div>

            <!-- Action Toolbar & Metadata Inputs -->
            <div class="calc-toolbar-row">
                <div class="toolbar-left-buttons">
                    <button id="btnClear" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;" title="Clear input fields">✕ Clear</button>
                    <button id="btnCopyResult" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;" title="Copy breakdown to clipboard">📋 Copy</button>
                    <button id="btnSave" type="button" class="btn btn-primary btn-sm" style="border-radius: 2px;">💾 Save Record</button>
                    <button id="btnMemo" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;">📜 Saved History</button>
                </div>

                <div class="metadata-inputs-group">
                    <div class="meta-field-wrapper" style="max-width: 130px;">
                        <label class="meta-field-label" for="originInput">Origin</label>
                        <input type="text" id="originInput" class="meta-input-box" placeholder="e.g. Arabic">
                    </div>
                    <div class="meta-field-wrapper">
                        <label class="meta-field-label" for="meaningInput">Meanings</label>
                        <input type="text" id="meaningInput" class="meta-input-box" placeholder="e.g. Praised, Exalted">
                    </div>
                </div>
            </div>
        </div>

        <!-- Realtime KPI & Letter Breakdown Card -->
        <div class="results-display-card">
            <!-- Left KPI Column -->
            <div class="kpi-values-column">
                <div class="kpi-stat-block">
                    <span class="kpi-stat-label">Total Abjad Sum</span>
                    <div class="kpi-stat-number" id="totalValue" style="color: var(--accent-gold);">0</div>
                </div>
                <div class="kpi-stat-block">
                    <span class="kpi-stat-label">Single Digital Root (1-9)</span>
                    <div class="kpi-stat-number" id="singleValue" style="color: var(--primary);">0</div>
                </div>
            </div>

            <!-- Right Breakdown Matrix -->
            <div class="breakdown-section-col">
                <div class="breakdown-header-row">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary);">
                        Letter-by-Letter Breakdown Matrix
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                        Decimal value & element
                    </span>
                </div>
                <div class="breakdown-flow-container" id="breakdownFlow">
                    <span style="color: var(--text-muted); font-size: 0.88rem; font-style: italic;">
                        Type in the box above or click Urdu Keyboard to inspect letters...
                    </span>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Auth / Authorization Prompt Modal -->
<div id="loginPromptModal" class="modal-overlay-custom">
    <div class="modal-card-box">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
        <h3 id="promptTitle" style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
            Staff & Admin Authorization Required
        </h3>
        <p id="promptMsg" style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
            Saving calculations or managing the saved names history database is strictly reserved for Staff and Admin accounts.
        </p>
        <div id="promptAuthButtons" style="display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 0.75rem;">
            <a href="index.php?auth=login" class="btn btn-primary" style="flex: 1; padding: 0.55rem; border-radius: 2px;">Sign In</a>
            <a href="index.php?auth=signup" class="btn btn-secondary" style="flex: 1; padding: 0.55rem; border-radius: 2px;">Create Account</a>
        </div>
        <button id="btnClosePrompt" type="button" class="btn btn-sm btn-secondary" style="width: 100%; border-radius: 2px;">Dismiss</button>
    </div>
</div>

<script>
    const isLoggedInUser = <?php echo json_encode(isLoggedIn()); ?>;
    const isStaffOrAdminUser = <?php echo json_encode($isStaffOrAdmin); ?>;

    const letterData = [
        { char: 'ا', value: 1, name: 'Alif', elem: 'fire' }, { char: 'آ', value: 1, name: 'Alif Mad', elem: 'fire' },
        { char: 'ب', value: 2, name: 'Be', elem: 'air' }, { char: 'پ', value: 2, name: 'Pe', elem: 'air' },
        { char: 'ج', value: 3, name: 'Jeem', elem: 'water' }, { char: 'چ', value: 3, name: 'Che', elem: 'water' },
        { char: 'د', value: 4, name: 'Dal', elem: 'earth' }, { char: 'ڈ', value: 4, name: 'Ddal', elem: 'earth' },
        { char: 'ہ', value: 5, name: 'Gol He', elem: 'fire' }, { char: 'ھ', value: 5, name: 'Do-chashmi He', elem: 'fire' },
        { char: 'و', value: 6, name: 'Wao', elem: 'air' }, { char: 'ز', value: 7, name: 'Ze', elem: 'water' },
        { char: 'ژ', value: 7, name: 'Zhe', elem: 'water' }, { char: 'ح', value: 8, name: 'He (Halqi)', elem: 'earth' },
        { char: 'ط', value: 9, name: 'To\'ey', elem: 'fire' }, { char: 'ی', value: 10, name: 'Ye', elem: 'air' },
        { char: 'ے', value: 10, name: 'Bari Ye', elem: 'air' }, { char: 'ک', value: 20, name: 'Kaf', elem: 'water' },
        { char: 'گ', value: 20, name: 'Gaf', elem: 'water' }, { char: 'ل', value: 30, name: 'Laam', elem: 'earth' },
        { char: 'م', value: 40, name: 'Meem', elem: 'fire' }, { char: 'ن', value: 50, name: 'Noon', elem: 'air' },
        { char: 'ں', value: 50, name: 'Noon Ghunna', elem: 'air' }, { char: 'س', value: 60, name: 'Seen', elem: 'water' },
        { char: 'ع', value: 70, name: 'Ain', elem: 'earth' }, { char: 'ف', value: 80, name: 'Fe', elem: 'fire' },
        { char: 'ص', value: 90, name: 'Suad', elem: 'air' }, { char: 'ق', value: 100, name: 'Qaf', elem: 'water' },
        { char: 'ر', value: 200, name: 'Re', elem: 'earth' }, { char: 'ڑ', value: 200, name: 'Rre', elem: 'earth' },
        { char: 'ش', value: 300, name: 'Sheen', elem: 'fire' }, { char: 'ت', value: 400, name: 'Te', elem: 'air' },
        { char: 'ٹ', value: 400, name: 'Tte', elem: 'air' }, { char: 'ث', value: 500, name: 'Se', elem: 'water' },
        { char: 'خ', value: 600, name: 'Khe', elem: 'earth' }, { char: 'ذ', value: 700, name: 'Zal', elem: 'fire' },
        { char: 'ض', value: 800, name: 'Zuad', elem: 'air' }, { char: 'ظ', value: 900, name: 'Zo\'ey', elem: 'water' },
        { char: 'غ', value: 1000, name: 'Ghain', elem: 'earth' }
    ];

    const letterMap = {};
    const elementMap = {};
    const elemColorMap = {
        'fire': 'var(--fire-color)',
        'air': 'var(--air-color)',
        'water': 'var(--water-color)',
        'earth': 'var(--earth-color)'
    };

    letterData.forEach(item => {
        letterMap[item.char] = item.value;
        elementMap[item.char] = item.elem;
    });

    let activeInputField = document.getElementById('calcInput');

    function getElementPercentages(text) {
        let counts = { fire: 0, air: 0, water: 0, earth: 0 };
        let totalVal = 0;
        for (let char of text) {
            let elem = elementMap[char];
            let val = letterMap[char] || 0;
            if (elem && val > 0) {
                counts[elem] += val;
                totalVal += val;
            }
        }
        if (totalVal === 0) return { fire: 0, air: 0, water: 0, earth: 0 };
        return {
            fire: Math.round((counts.fire / totalVal) * 100),
            air: Math.round((counts.air / totalVal) * 100),
            water: Math.round((counts.water / totalVal) * 100),
            earth: Math.round((counts.earth / totalVal) * 100)
        };
    }

    function calculateDigitalRoot(num) {
        if (!num || num === 0) return 0;
        return (num % 9 === 0) ? 9 : num % 9;
    }

    function calculateAbjad() {
        const text = document.getElementById('calcInput').value;
        let total = 0;
        let breakdownHTML = '';
        let charsInText = [];

        document.getElementById('charCountLabel').innerText = `${text.length} character${text.length === 1 ? '' : 's'}`;

        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            if (char === ' ') {
                breakdownHTML += `<span class="chip-separator"></span>`;
                continue;
            }
            const val = letterMap[char] || 0;
            const elem = elementMap[char] || 'earth';
            if (val > 0) {
                total += val;
                charsInText.push(char);
                const dotColor = elemColorMap[elem] || 'var(--text-muted)';
                breakdownHTML += `
                    <div class="letter-chip">
                        <span class="chip-char">${char}</span>
                        <span class="chip-val">${val}</span>
                        <span class="chip-elem-dot" style="background: ${dotColor};" title="Element: ${elem}"></span>
                    </div>
                `;
            }
        }

        const single = calculateDigitalRoot(total);
        document.getElementById('totalValue').innerText = total;
        document.getElementById('singleValue').innerText = single;
        document.getElementById('breakdownFlow').innerHTML = breakdownHTML || `<span style="color: var(--text-muted); font-size: 0.88rem; font-style: italic;">Type in the box above or click Urdu Keyboard to inspect letters...</span>`;

        const elems = getElementPercentages(text);
        document.getElementById('val-fire').innerText = elems.fire + '%';
        document.getElementById('bar-fire').style.width = elems.fire + '%';
        document.getElementById('val-air').innerText = elems.air + '%';
        document.getElementById('bar-air').style.width = elems.air + '%';
        document.getElementById('val-water').innerText = elems.water + '%';
        document.getElementById('bar-water').style.width = elems.water + '%';
        document.getElementById('val-earth').innerText = elems.earth + '%';
        document.getElementById('bar-earth').style.width = elems.earth + '%';

        highlightLetters(charsInText);
    }

    function highlightLetters(chars) {
        document.querySelectorAll('.kb-key-tile').forEach(card => card.classList.remove('active-in-text'));
        chars.forEach(c => {
            document.querySelectorAll(`.kb-key-tile[data-char="${c}"]`).forEach(card => card.classList.add('active-in-text'));
        });
    }

    function renderVirtualKeyboard() {
        const grid = document.getElementById('lettersGrid');
        if (!grid) return;
        grid.innerHTML = '';
        letterData.forEach(item => {
            const card = document.createElement('div');
            card.className = 'kb-key-tile';
            card.setAttribute('data-char', item.char);
            card.title = `${item.name} (${item.char}) = ${item.value} | Element: ${item.elem}`;
            card.innerHTML = `
                <span class="kb-arabic-glyph">${item.char}</span>
                <span class="kb-val-num">${item.value}</span>
            `;
            card.onclick = () => insertChar(item.char);
            grid.appendChild(card);
        });
    }

    function insertChar(char) {
        if (!activeInputField) activeInputField = document.getElementById('calcInput');
        const start = activeInputField.selectionStart || activeInputField.value.length;
        const end = activeInputField.selectionEnd || activeInputField.value.length;
        const val = activeInputField.value;
        activeInputField.value = val.substring(0, start) + char + val.substring(end);
        activeInputField.selectionStart = activeInputField.selectionEnd = start + char.length;
        activeInputField.focus();
        if (activeInputField.id === 'calcInput') calculateAbjad();
    }

    function backspaceChar() {
        if (!activeInputField) activeInputField = document.getElementById('calcInput');
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
        if (activeInputField.id === 'calcInput') calculateAbjad();
    }

    function handleProtectedAction(actionCallback) {
        if (!isLoggedInUser) {
            document.getElementById('promptTitle').innerText = 'Account Sign In Required';
            document.getElementById('promptMsg').innerText = 'To save calculations or view saved history records, please sign in to your account.';
            document.getElementById('promptAuthButtons').style.display = 'flex';
            document.getElementById('loginPromptModal').style.display = 'flex';
        } else if (!isStaffOrAdminUser) {
            document.getElementById('promptTitle').innerText = 'Staff & Admin Privileges Reserved';
            document.getElementById('promptMsg').innerText = 'Access rights to saved names history, saving, and editing records are reserved exclusively for Staff and Admin accounts. Public users can calculate freely and submit consultation lookup requests with follow-up questions in their profile.';
            document.getElementById('promptAuthButtons').style.display = 'none';
            document.getElementById('loginPromptModal').style.display = 'flex';
        } else {
            actionCallback();
        }
    }

    function saveCalculation() {
        handleProtectedAction(() => {
            const name = document.getElementById('calcInput').value.trim();
            const total = parseInt(document.getElementById('totalValue').innerText) || 0;
            const single = parseInt(document.getElementById('singleValue').innerText) || 0;
            const origin = document.getElementById('originInput').value.trim();
            const meanings = document.getElementById('meaningInput').value.trim();

            if (!name) { alert('Please enter text first.'); return; }

            fetch('api.php?action=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, total, single, origin, meanings })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Calculation saved successfully to history log!');
                } else {
                    alert('Error saving: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(() => alert('Network error occurred while saving.'));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderVirtualKeyboard();

        const allInputs = document.querySelectorAll('input[type="text"], input[type="search"], textarea');
        allInputs.forEach(el => {
            el.addEventListener('focus', () => {
                activeInputField = el;
                const label = el.placeholder || el.id || 'Input';
                const lblEl = document.getElementById('activeFieldLabel');
                if (lblEl) lblEl.innerText = label;
            });
        });

        document.getElementById('calcInput').addEventListener('input', calculateAbjad);

        document.getElementById('btnClear').addEventListener('click', () => {
            document.getElementById('calcInput').value = '';
            document.getElementById('originInput').value = '';
            document.getElementById('meaningInput').value = '';
            calculateAbjad();
        });

        document.getElementById('btnCopyResult').addEventListener('click', () => {
            const name = document.getElementById('calcInput').value;
            const total = document.getElementById('totalValue').innerText;
            const single = document.getElementById('singleValue').innerText;
            const copyText = `Name: ${name} | Total Abjad: ${total} | Single Root: ${single}`;
            navigator.clipboard.writeText(copyText).then(() => {
                alert('Copied calculation summary to clipboard!');
            });
        });

        document.getElementById('btnSave').addEventListener('click', saveCalculation);
        document.getElementById('btnMemo').addEventListener('click', () => {
            handleProtectedAction(() => {
                window.location.href = 'saved.php';
            });
        });

        const kbContainer = document.getElementById('keyboardContainer');
        document.getElementById('btnToggleKeyboard').addEventListener('click', () => {
            kbContainer.style.display = (kbContainer.style.display === 'none' || !kbContainer.style.display) ? 'flex' : 'none';
        });
        document.getElementById('btnCloseKeyboard').addEventListener('click', () => {
            kbContainer.style.display = 'none';
        });

        document.getElementById('btnClosePrompt').addEventListener('click', () => {
            document.getElementById('loginPromptModal').style.display = 'none';
        });

        document.getElementById('btnSpaceBar').addEventListener('click', () => insertChar(' '));
        document.getElementById('btnBackspace').addEventListener('click', backspaceChar);

        // Run initial empty calculation
        calculateAbjad();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

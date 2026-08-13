<?php
// calculator.php
$pageTitle = 'Abjad Calculator & Saved History';
require_once __DIR__ . '/includes/header.php';

$isAuthorized = ($currentUser && $currentUser['status'] === 'approved');
$isStaffOrAdmin = ($currentUser && in_array($currentUser['role'], ['staff', 'admin']) && $currentUser['status'] === 'approved');
$userCircumstance = $currentUser ? ($currentUser['circumstance'] ?? '') : '';
?>

<style>
    .calculator-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .calc-input {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        color: #0f172a;
        font-family: 'Amiri', serif;
        font-size: 1.4rem;
        direction: rtl;
    }

    .calc-input:focus {
        outline: none;
        border-color: #2563eb;
        background: #ffffff;
    }

    .detail-input {
        direction: auto;
        text-align: right;
    }

    .elements-line-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        direction: rtl;
    }

    .element-item {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .element-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .element-bar-track {
        width: 100%;
        height: 5px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .action-fields-row {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .action-buttons-group {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .origin-input-wrapper {
        width: 110px;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .meaning-input-wrapper {
        flex: 1;
        min-width: 140px;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .calc-results {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .values-wrapper {
        display: flex;
        gap: 1.25rem;
        align-items: center;
    }

    .total-value-container {
        display: flex;
        flex-direction: column;
    }

    .total-value-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
    }

    .total-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #d97706;
        line-height: 1;
    }

    .breakdown-container {
        flex: 1;
        min-width: 160px;
    }

    .breakdown-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.3rem;
    }

    .breakdown-flow {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        direction: rtl;
        align-items: center;
    }

    .breakdown-item {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 36px;
    }

    .breakdown-letter {
        font-family: 'Amiri', serif;
        font-size: 1.25rem;
        font-weight: bold;
        color: #0f172a;
    }

    .breakdown-val {
        font-size: 0.65rem;
        color: #64748b;
    }

    .breakdown-space-separator {
        width: 0.85rem;
        height: 1.6rem;
        border-left: 2px dashed #cbd5e1;
        margin: 0 0.25rem;
        display: inline-block;
    }

    /* Built-in Urdu Keyboard Section */
    .chart-section {
        margin-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        max-width: 900px;
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
        width: 60px;
        height: 60px;
        transition: all 0.12s ease;
    }

    .letter-card:hover {
        background: #f1f5f9;
        border-color: #2563eb;
    }

    .letter-card.highlighted {
        background: #eff6ff;
        border-color: #2563eb;
    }

    .letter-arabic {
        position: absolute;
        top: 5px;
        right: 7px;
        font-family: 'Amiri', serif;
        font-size: 1.85rem;
        color: #0f172a;
        line-height: 1;
    }

    .letter-value {
        position: absolute;
        bottom: 5px;
        left: 7px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #d97706;
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        display: none;
        flex-direction: column;
        z-index: 1000;
        padding: 1rem;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        width: 100%;
        max-width: 1100px;
        max-height: 90vh;
        margin: auto;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .modal-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.6rem;
    }

    .history-table-wrapper {
        overflow-x: auto;
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        direction: rtl;
        text-align: right;
        font-size: 0.88rem;
    }

    .history-table th, .history-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid #e2e8f0;
    }

    .history-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #0f172a;
    }

    .history-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    .history-table td.arabic-cell {
        font-family: 'Amiri', serif;
        font-size: 1.3rem;
    }

    .table-search-input {
        width: 100%;
        padding: 0.3rem 0.5rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 0.8rem;
        direction: rtl;
    }

    .circumstance-card {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        max-width: 900px;
        margin: 0 auto 1.25rem auto;
        color: #1e3a8a;
    }
</style>

<main class="container">
    <?php if ($currentUser && !empty($userCircumstance)): ?>
        <!-- User Personal Circumstance Card -->
        <div class="circumstance-card">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                <span style="font-size: 1.1rem;">📌</span>
                <strong style="font-size: 0.95rem; color: #1e40af;">Your Update & Circumstance Note:</strong>
            </div>
            <p style="font-size: 0.9rem; color: #1e3a8a; line-height: 1.5; margin-left: 1.6rem;">
                <?php echo nl2br(htmlspecialchars($userCircumstance)); ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Interactive Calculator -->
    <div class="calculator-card">
        <!-- Main Input -->
        <div class="input-group" style="position: relative;">
            <div class="input-wrapper">
                <input type="text" id="calcInput" class="calc-input" placeholder="Type or click Urdu Keyboard ⌨️ button to type..." autocomplete="off">
            </div>
            <div id="suggestionsBox" class="suggestions-box" style="display: none;"></div>
        </div>

        <!-- Elemental Temperament -->
        <div class="elements-line-container">
            <div class="element-item">
                <div class="element-header" style="color: #d97706;">
                    <span>🔥 Fire (آتشی)</span>
                    <span id="val-fire">0%</span>
                </div>
                <div class="element-bar-track">
                    <div id="bar-fire" style="width: 0%; height: 100%; background: #d97706; transition: width 0.3s ease;"></div>
                </div>
            </div>
            <div class="element-item">
                <div class="element-header" style="color: #dc2626;">
                    <span>💨 Air (بادی)</span>
                    <span id="val-air">0%</span>
                </div>
                <div class="element-bar-track">
                    <div id="bar-air" style="width: 0%; height: 100%; background: #dc2626; transition: width 0.3s ease;"></div>
                </div>
            </div>
            <div class="element-item">
                <div class="element-header" style="color: #2563eb;">
                    <span>💧 Water (آبی)</span>
                    <span id="val-water">0%</span>
                </div>
                <div class="element-bar-track">
                    <div id="bar-water" style="width: 0%; height: 100%; background: #2563eb; transition: width 0.3s ease;"></div>
                </div>
            </div>
            <div class="element-item">
                <div class="element-header" style="color: #16a34a;">
                    <span>🪨 Earth (خاکی)</span>
                    <span id="val-earth">0%</span>
                </div>
                <div class="element-bar-track">
                    <div id="bar-earth" style="width: 0%; height: 100%; background: #16a34a; transition: width 0.3s ease;"></div>
                </div>
            </div>
        </div>

        <!-- Action Row -->
        <div class="action-fields-row">
            <div class="action-buttons-group">
                <button id="btnClear" class="btn">Clear</button>
                <button id="btnToggleKeyboard" class="btn" style="border-color: #2563eb; color: #2563eb; font-weight: 600;">Urdu Keyboard ⌨️</button>
                <button id="btnSave" class="btn btn-primary">Save to History</button>
                <button id="btnMemo" class="btn">Saved History Log</button>
            </div>

            <div class="origin-input-wrapper">
                <label style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Origin</label>
                <input type="text" id="originInput" class="calc-input detail-input" placeholder="Origin..." style="font-size: 0.85rem; height: 34px; padding: 0.2rem 0.4rem;">
            </div>

            <div class="meaning-input-wrapper">
                <label style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Meanings (Optional)</label>
                <input type="text" id="meaningInput" class="calc-input detail-input" placeholder="e.g. Gracious, Noble..." style="font-size: 0.85rem; height: 34px; padding: 0.2rem 0.4rem;">
            </div>
        </div>

        <!-- Calculation Results -->
        <div class="calc-results">
            <div class="values-wrapper">
                <div class="total-value-container">
                    <div class="total-value-label">Single Root</div>
                    <div class="total-value" id="singleValue" style="color: #2563eb;">0</div>
                </div>
                <div class="total-value-container" style="border-left: 1px solid #cbd5e1; padding-left: 1.25rem;">
                    <div class="total-value-label">Total Abjad</div>
                    <div class="total-value" id="totalValue">0</div>
                </div>
            </div>

            <div class="breakdown-container">
                <div class="breakdown-label">Letter Breakdown</div>
                <div class="breakdown-flow" id="breakdownFlow">
                    <span style="color: #64748b; font-size: 0.85rem; font-style: italic;">Start typing or click Urdu Keyboard ⌨️ to input...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- On-screen Built-in Urdu Keyboard (DEFAULT HIDDEN) -->
    <div class="chart-section" id="keyboardContainer" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem;">
            <span style="font-size: 0.85rem; font-weight: 600; color: #0f172a;">Built-in Urdu Keyboard ⌨️ (Active Target: <span id="activeFieldLabel" style="color: #2563eb;">Main Input</span>)</span>
            <button id="btnCloseKeyboard" class="btn btn-sm" style="color: #dc2626;">✕ Hide</button>
        </div>
        <div id="keyboardActionRow" style="display: flex; gap: 0.5rem; width: 100%;">
            <button id="btnSpaceBar" class="btn btn-primary" style="flex: 1; padding: 0.4rem 0;">Space Bar ␣</button>
            <button id="btnBackspace" class="btn btn-danger" style="flex: 1; padding: 0.4rem 0;">Backspace ⌫</button>
        </div>
        <div class="letters-grid" id="lettersGrid"></div>
    </div>
</main>

<!-- Login or Privilege Required Modal -->
<div id="loginPromptModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 480px; height: auto; text-align: center; gap: 1rem;">
        <div style="font-size: 2.2rem;">🔒</div>
        <h3 id="promptTitle" style="font-size: 1.2rem; color: #0f172a;">Staff & Admin Authorization Required</h3>
        <p id="promptMsg" style="font-size: 0.9rem; color: #475569; line-height: 1.5;">
            Access to save calculation entries or view the saved names history database is reserved exclusively for Staff and Admin members.
        </p>
        <div id="promptAuthButtons" style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.5rem;">
            <a href="index.php?auth=login" class="btn btn-primary" style="flex: 1; justify-content: center; padding: 0.5rem;">Login</a>
            <a href="index.php?auth=signup" class="btn" style="flex: 1; justify-content: center; padding: 0.5rem;">Sign Up</a>
        </div>
        <button id="btnClosePrompt" class="btn btn-sm" style="margin-top: 0.5rem;">Close</button>
    </div>
</div>

<!-- Full Screen Saved History & Memo Modal (Staff & Admin Only) -->
<div id="memoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header-top">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button id="btnBackToCalc" class="btn btn-sm">← Back to Calculator</button>
                <strong style="font-size: 1rem; color: #0f172a;">Saved Names History Log (Staff & Admin Access)</strong>
            </div>
            <div style="display: flex; gap: 0.4rem;">
                <button id="btnAddNew" class="btn btn-primary btn-sm">+ Add Record</button>
                <button id="btnCloseModal" class="btn btn-sm" style="color: #dc2626;">✕ Close</button>
            </div>
        </div>

        <!-- Add/Edit Record Form Overlay -->
        <div id="addEditRecordForm" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.75rem; border-radius: 6px;">
            <h4 id="formTitle" style="margin-bottom: 0.5rem; font-size: 0.9rem;">Add New Calculation Record</h4>
            <input type="hidden" id="editRecordId">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.5rem; margin-bottom: 0.5rem;">
                <div>
                    <label style="font-size: 0.7rem; color: #64748b;">Name *</label>
                    <input type="text" id="formName" class="calc-input" style="font-size: 1rem; height: 32px; padding: 0.2rem 0.4rem;">
                </div>
                <div>
                    <label style="font-size: 0.7rem; color: #64748b;">Total *</label>
                    <input type="number" id="formTotal" class="calc-input" style="font-size: 1rem; height: 32px; padding: 0.2rem 0.4rem; direction: ltr;">
                </div>
                <div>
                    <label style="font-size: 0.7rem; color: #64748b;">Single Root *</label>
                    <input type="number" id="formSingle" class="calc-input" style="font-size: 1rem; height: 32px; padding: 0.2rem 0.4rem; direction: ltr;">
                </div>
                <div>
                    <label style="font-size: 0.7rem; color: #64748b;">Origin</label>
                    <input type="text" id="formOrigin" class="calc-input" style="font-size: 1rem; height: 32px; padding: 0.2rem 0.4rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-size: 0.7rem; color: #64748b;">Meanings</label>
                    <input type="text" id="formMeanings" class="calc-input" style="font-size: 1rem; height: 32px; padding: 0.2rem 0.4rem;">
                </div>
            </div>
            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                <button id="btnCancelForm" class="btn btn-sm">Cancel</button>
                <button id="btnSubmitForm" class="btn btn-primary btn-sm">Save Record</button>
            </div>
        </div>

        <!-- History Table -->
        <div class="history-table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th class="sortable" data-col="name">Name ↕</th>
                        <th class="sortable" data-col="total">Total ↕</th>
                        <th class="sortable" data-col="single">Single ↕</th>
                        <th class="sortable" data-col="origin">Origin ↕</th>
                        <th>Meanings</th>
                        <th>Temperament</th>
                    </tr>
                    <tr>
                        <td><button id="btnClearFilters" class="btn btn-sm" style="font-size: 0.7rem;">Clear</button></td>
                        <td><input type="text" id="search-name" class="table-search-input" placeholder="Search..."></td>
                        <td><input type="text" id="search-total" class="table-search-input" placeholder="Search..."></td>
                        <td><input type="text" id="search-single" class="table-search-input" placeholder="Search..."></td>
                        <td><input type="text" id="search-origin" class="table-search-input" placeholder="Search..."></td>
                        <td><input type="text" id="search-meanings" class="table-search-input" placeholder="Search..."></td>
                        <td>
                            <select id="search-temperament" class="table-search-input" style="font-size: 0.75rem;">
                                <option value="">All</option>
                                <option value="Fire">Fire</option>
                                <option value="Air">Air</option>
                                <option value="Water">Water</option>
                                <option value="Earth">Earth</option>
                            </select>
                        </td>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr><td colspan="7" style="text-align: center; color: #64748b;">Loading history...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #64748b;">
            <div>
                Show: 
                <select id="pageSizeSelect" style="padding: 0.15rem; font-size: 0.8rem; border-radius: 4px; border: 1px solid #cbd5e1;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                records per page
            </div>
            <div id="pageInfoText">Page 1 of 1</div>
            <div style="display: flex; gap: 0.3rem;">
                <button id="btnPrevPage" class="btn btn-sm">Previous</button>
                <button id="btnNextPage" class="btn btn-sm">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    const isLoggedInUser = <?php echo json_encode(isLoggedIn()); ?>;
    const isStaffOrAdminUser = <?php echo json_encode($isStaffOrAdmin); ?>;

    const letterData = [
        { char: 'ا', value: 1, name: 'Alif' }, { char: 'آ', value: 1, name: 'Alif Mad' },
        { char: 'ب', value: 2, name: 'Be' }, { char: 'پ', value: 2, name: 'Pe' },
        { char: 'ج', value: 3, name: 'Jeem' }, { char: 'چ', value: 3, name: 'Che' },
        { char: 'د', value: 4, name: 'Dal' }, { char: 'ڈ', value: 4, name: 'Ddal' },
        { char: 'ہ', value: 5, name: 'Gol He' }, { char: 'ھ', value: 5, name: 'Do-chashmi He' },
        { char: 'و', value: 6, name: 'Wao' }, { char: 'ز', value: 7, name: 'Ze' },
        { char: 'ژ', value: 7, name: 'Zhe' }, { char: 'ح', value: 8, name: 'He (Halqi)' },
        { char: 'ط', value: 9, name: 'To\'ey' }, { char: 'ی', value: 10, name: 'Ye' },
        { char: 'ے', value: 10, name: 'Bari Ye' }, { char: 'ک', value: 20, name: 'Kaf' },
        { char: 'گ', value: 20, name: 'Gaf' }, { char: 'ل', value: 30, name: 'Laam' },
        { char: 'م', value: 40, name: 'Meem' }, { char: 'ن', value: 50, name: 'Noon' },
        { char: 'ں', value: 50, name: 'Noon Ghunna' }, { char: 'س', value: 60, name: 'Seen' },
        { char: 'ع', value: 70, name: 'Ain' }, { char: 'ف', value: 80, name: 'Fe' },
        { char: 'ص', value: 90, name: 'Suad' }, { char: 'ق', value: 100, name: 'Qaf' },
        { char: 'ر', value: 200, name: 'Re' }, { char: 'ڑ', value: 200, name: 'Rre' },
        { char: 'ش', value: 300, name: 'Sheen' }, { char: 'ت', value: 400, name: 'Te' },
        { char: 'ٹ', value: 400, name: 'Tte' }, { char: 'ث', value: 500, name: 'Se' },
        { char: 'خ', value: 600, name: 'Khe' }, { char: 'ذ', value: 700, name: 'Zal' },
        { char: 'ض', value: 800, name: 'Zuad' }, { char: 'ظ', value: 900, name: 'Zo\'ey' },
        { char: 'غ', value: 1000, name: 'Ghain' }
    ];

    const letterMap = {};
    letterData.forEach(item => letterMap[item.char] = item.value);

    const elementMap = {
        'ا': 'fire', 'آ': 'fire', 'ہ': 'fire', 'ھ': 'fire', 'ط': 'fire', 'م': 'fire', 'ف': 'fire', 'ش': 'fire', 'ذ': 'fire',
        'ب': 'air', 'پ': 'air', 'و': 'air', 'ی': 'air', 'ے': 'air', 'ن': 'air', 'ں': 'air', 'ص': 'air', 'ت': 'air', 'ٹ': 'air', 'ض': 'air',
        'ج': 'water', 'چ': 'water', 'ز': 'water', 'ژ': 'water', 'ک': 'water', 'گ': 'water', 'س': 'water', 'ق': 'water', 'ث': 'water', 'ظ': 'water',
        'د': 'earth', 'ڈ': 'earth', 'ح': 'earth', 'ل': 'earth', 'ع': 'earth', 'ر': 'earth', 'ڑ': 'earth', 'خ': 'earth', 'غ': 'earth'
    };

    let activeInputField = document.getElementById('calcInput');
    let calculationsHistory = [];

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

        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            if (char === ' ') {
                breakdownHTML += `<span class="breakdown-space-separator"></span>`;
                continue;
            }
            const val = letterMap[char] || 0;
            if (val > 0) {
                total += val;
                charsInText.push(char);
                breakdownHTML += `
                    <div class="breakdown-item">
                        <span class="breakdown-letter">${char}</span>
                        <span class="breakdown-val">${val}</span>
                    </div>
                `;
            }
        }

        const single = calculateDigitalRoot(total);
        document.getElementById('totalValue').innerText = total;
        document.getElementById('singleValue').innerText = single;
        document.getElementById('breakdownFlow').innerHTML = breakdownHTML || `<span style="color: #64748b; font-size: 0.85rem; font-style: italic;">Start typing...</span>`;

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
        document.querySelectorAll('.letter-card').forEach(card => card.classList.remove('highlighted'));
        chars.forEach(c => {
            document.querySelectorAll(`.letter-card[data-char="${c}"]`).forEach(card => card.classList.add('highlighted'));
        });
    }

    function renderMainGrid() {
        const grid = document.getElementById('lettersGrid');
        grid.innerHTML = '';
        letterData.forEach(item => {
            const card = document.createElement('div');
            card.className = 'letter-card';
            card.setAttribute('data-char', item.char);
            card.innerHTML = `<span class="letter-arabic">${item.char}</span><span class="letter-value">${item.value}</span>`;
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
            document.getElementById('promptTitle').innerText = 'Account Login Required';
            document.getElementById('promptMsg').innerText = 'To save calculations or view saved history, please log in to your account.';
            document.getElementById('promptAuthButtons').style.display = 'flex';
            document.getElementById('loginPromptModal').style.display = 'flex';
        } else if (!isStaffOrAdminUser) {
            document.getElementById('promptTitle').innerText = 'Staff & Admin Privilege Required';
            document.getElementById('promptMsg').innerText = 'Access rights to saved names history and saving calculations are reserved exclusively for Staff and Admin accounts. Public accounts may use the interactive calculator.';
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
                    alert('Calculation saved to history database!');
                    loadHistory();
                } else alert('Error saving: ' + (data.error || 'Unknown error'));
            });
        });
    }

    function openMemoModal() {
        handleProtectedAction(() => {
            window.location.href = 'saved.php';
        });
    }

    function loadHistory() {
        if (!isStaffOrAdminUser) return;
        fetch('api.php?action=history')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                calculationsHistory = data;
                renderTable();
            }
        });
    }

    function renderTable() {
        const tbody = document.getElementById('historyTableBody');
        if (!tbody) return;

        let filtered = calculationsHistory.filter(item => {
            const sName = (document.getElementById('search-name')?.value || '').toLowerCase();
            const sTotal = (document.getElementById('search-total')?.value || '').toLowerCase();
            const sSingle = (document.getElementById('search-single')?.value || '').toLowerCase();
            const sOrigin = (document.getElementById('search-origin')?.value || '').toLowerCase();
            const sMeanings = (document.getElementById('search-meanings')?.value || '').toLowerCase();

            if (sName && !item.name.toLowerCase().includes(sName)) return false;
            if (sTotal && !String(item.total).includes(sTotal)) return false;
            if (sSingle && !String(item.single).includes(sSingle)) return false;
            if (sOrigin && !(item.origin || '').toLowerCase().includes(sOrigin)) return false;
            if (sMeanings && !(item.meanings || '').toLowerCase().includes(sMeanings)) return false;
            return true;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #64748b;">No matching records found.</td></tr>`;
            return;
        }

        let html = '';
        filtered.forEach(item => {
            html += `
                <tr>
                    <td>
                        <button onclick="editRecord(${item.id})" class="btn btn-sm" style="font-size:0.7rem; padding: 0.1rem 0.3rem;">Edit</button>
                        <button onclick="deleteRecord(${item.id})" class="btn btn-danger btn-sm" style="font-size:0.7rem; padding: 0.1rem 0.3rem;">Del</button>
                    </td>
                    <td class="arabic-cell">${item.name}</td>
                    <td><strong>${item.total}</strong></td>
                    <td><span style="color:#2563eb; font-weight:bold;">${item.single}</span></td>
                    <td>${item.origin || '-'}</td>
                    <td>${item.meanings || '-'}</td>
                    <td><span style="font-size:0.75rem; color:#64748b;">Calc</span></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function deleteRecord(id) {
        if (!confirm('Are you sure you want to delete this record?')) return;
        fetch('api.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadHistory();
            else alert('Error deleting record.');
        });
    }

    function editRecord(id) {
        const item = calculationsHistory.find(r => r.id == id);
        if (!item) return;
        document.getElementById('editRecordId').value = item.id;
        document.getElementById('formName').value = item.name;
        document.getElementById('formTotal').value = item.total;
        document.getElementById('formSingle').value = item.single;
        document.getElementById('formOrigin').value = item.origin || '';
        document.getElementById('formMeanings').value = item.meanings || '';
        document.getElementById('addEditRecordForm').style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderMainGrid();

        // Track focus on all inputs/textareas to target built-in keyboard typing
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

        document.getElementById('btnSave').addEventListener('click', saveCalculation);
        document.getElementById('btnMemo').addEventListener('click', openMemoModal);

        // Toggle Keyboard button (Default Hide)
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

        document.getElementById('btnCloseModal').addEventListener('click', () => document.getElementById('memoModal').style.display = 'none');
        document.getElementById('btnBackToCalc').addEventListener('click', () => document.getElementById('memoModal').style.display = 'none');
        document.getElementById('btnClosePrompt').addEventListener('click', () => document.getElementById('loginPromptModal').style.display = 'none');

        document.getElementById('btnSpaceBar').addEventListener('click', () => insertChar(' '));
        document.getElementById('btnBackspace').addEventListener('click', backspaceChar);

        if (isStaffOrAdminUser) loadHistory();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

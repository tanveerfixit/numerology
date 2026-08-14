<?php
// saved.php
$pageTitle = 'Saved Names History Log & Database';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['status'] !== 'approved') {
    echo '<main class="container" style="text-align: center; padding: 3rem 1.5rem;">
            <div style="background: #ffffff; border: 1px solid var(--border-subtle); padding: 2.5rem; border-radius: var(--radius-lg); max-width: 500px; margin: 0 auto; box-shadow: var(--shadow-md);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🔒</div>
                <h2 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 0.5rem;">Account Approval Required</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">Your account must be approved by an administrator to view and manage saved calculation history records.</p>
                <a href="calculator.php" class="btn btn-primary">Return to Calculator</a>
            </div>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<style>
    .history-card-wrapper {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .history-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 1rem;
    }

    .history-title-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .history-title-text {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }

    /* Filter Controls Bar */
    .filter-controls-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        min-width: 240px;
        max-width: 400px;
    }

    .search-icon-pos {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.85rem;
        pointer-events: none;
    }

    .modern-search-input {
        width: 100%;
        padding: 0.48rem 0.75rem 0.48rem 2.2rem;
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        font-size: 0.88rem;
        font-family: inherit;
        background: var(--surface-subtle);
        color: var(--text-primary);
        transition: all 0.15s ease;
    }

    .modern-search-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .element-filter-pills {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .elem-filter-btn {
        background: var(--surface-subtle);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.32rem 0.65rem;
        border-radius: var(--radius-full);
        cursor: pointer;
        transition: all 0.12s ease;
    }

    .elem-filter-btn:hover {
        background: var(--surface-active);
        color: var(--text-primary);
    }

    .elem-filter-btn.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    /* Modern Table Grid */
    .table-container-modern {
        overflow-x: auto;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
    }

    .data-grid-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.88rem;
    }

    .data-grid-table th {
        background: var(--surface-subtle);
        padding: 0.7rem 0.85rem;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-subtle);
    }

    .data-grid-table td {
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-primary);
        vertical-align: middle;
    }

    .data-grid-table tbody tr {
        transition: background 0.12s ease;
    }

    .data-grid-table tbody tr:hover {
        background: var(--surface-subtle);
    }

    .arabic-grid-cell {
        font-family: var(--font-arabic);
        font-size: 1.55rem;
        font-weight: 700;
        color: var(--text-primary);
        direction: rtl;
        text-align: right;
    }

    .arabic-grid-link {
        color: var(--text-primary);
        text-decoration: none;
        transition: color 0.12s ease;
    }

    .arabic-grid-link:hover {
        color: var(--primary);
    }

    .root-pill-badge {
        background: var(--primary-light);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.15rem 0.5rem;
        border-radius: var(--radius-full);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
    }

    .elem-metric-quad {
        display: inline-flex;
        gap: 0.5rem;
        align-items: center;
        font-size: 0.82rem;
        font-weight: 700;
        direction: ltr;
    }

    /* Modal / Form overlay */
    .form-drawer-card {
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        display: none;
        animation: fadeIn 0.15s ease-in-out;
    }

    .pagination-bar-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 0.5rem;
        font-size: 0.82rem;
        color: var(--text-secondary);
        flex-wrap: wrap;
        gap: 0.5rem;
    }
</style>

<main class="container">
    <div class="history-card-wrapper">
        <!-- Top Title Bar -->
        <div class="history-header-bar">
            <div class="history-title-group">
                <a href="calculator.php" class="btn btn-secondary btn-sm" title="Return to Calculator">← Calculator</a>
                <h1 class="history-title-text">Saved Calculation Records</h1>
            </div>
            <div style="display: flex; gap: 0.45rem;">
                <button id="btnAddNew" class="btn btn-primary btn-sm">+ Add New Record</button>
                <button onclick="loadHistory()" class="btn btn-secondary btn-sm" title="Refresh list">🔄 Refresh</button>
            </div>
        </div>

        <!-- Add / Edit Record Inline Drawer -->
        <div id="addEditRecordForm" class="form-drawer-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                <strong id="formTitle" style="font-size: 1rem; color: var(--text-primary);">Add Calculation Record</strong>
                <button id="btnCancelForm" class="btn btn-sm btn-secondary" style="padding: 0.15rem 0.5rem;">✕ Close</button>
            </div>
            <input type="hidden" id="editRecordId">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-bottom: 0.85rem;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.25rem;">Name (Arabic/Urdu) *</label>
                    <input type="text" id="formName" class="form-control" style="font-family: var(--font-arabic); font-size: 1.15rem; direction: rtl;">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.25rem;">Total Abjad *</label>
                    <input type="number" id="formTotal" class="form-control">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.25rem;">Single Root (1-9) *</label>
                    <input type="number" id="formSingle" class="form-control">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.25rem;">Origin</label>
                    <input type="text" id="formOrigin" class="form-control" placeholder="e.g. Arabic, Persian">
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 0.25rem;">Meanings</label>
                    <input type="text" id="formMeanings" class="form-control" placeholder="e.g. The Praised One, Exalted">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button id="btnCancelFormBottom" class="btn btn-secondary btn-sm">Cancel</button>
                <button id="btnSubmitForm" class="btn btn-primary btn-sm">Save Record</button>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="filter-controls-row">
            <div class="search-input-wrapper">
                <span class="search-icon-pos">🔍</span>
                <input type="text" id="globalSearchInput" class="modern-search-input" placeholder="Search name, meaning, root, origin...">
            </div>

            <div class="element-filter-pills">
                <button class="elem-filter-btn active" data-elem="">All Elements</button>
                <button class="elem-filter-btn" data-elem="Fire">🔥 Fire</button>
                <button class="elem-filter-btn" data-elem="Air">💨 Air</button>
                <button class="elem-filter-btn" data-elem="Water">💧 Water</button>
                <button class="elem-filter-btn" data-elem="Earth">🪨 Earth</button>
            </div>
        </div>

        <!-- Data Grid Table -->
        <div class="table-container-modern">
            <table class="data-grid-table">
                <thead>
                    <tr>
                        <th style="text-align: right;">Name (Click to Inspect)</th>
                        <th>Total Abjad</th>
                        <th>Single Root</th>
                        <th>Origin</th>
                        <th>Meanings</th>
                        <th style="text-align: center;">Elements (🔥 / 💨 / 💧 / 🪨)</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Loading history records...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-bar-row">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span>Show:</span>
                <select id="pageSizeSelect" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>records per page</span>
            </div>
            <div id="pageInfoText" style="font-weight: 500;">Page 1 of 1</div>
            <div style="display: flex; gap: 0.35rem;">
                <button id="btnPrevPage" class="btn btn-secondary btn-sm">Previous</button>
                <button id="btnNextPage" class="btn btn-secondary btn-sm">Next</button>
            </div>
        </div>
    </div>
</main>

<script>
    let calculationsHistory = [];
    let currentPage = 1;
    let pageSize = 25;
    let activeElemFilter = '';

    const letterMap = {
        'ا': 1, 'آ': 1, 'ب': 2, 'پ': 2, 'ج': 3, 'چ': 3, 'د': 4, 'ڈ': 4, 'ہ': 5, 'ھ': 5,
        'و': 6, 'ز': 7, 'ژ': 7, 'ح': 8, 'ط': 9, 'ی': 10, 'ے': 10, 'ک': 20, 'گ': 20,
        'ل': 30, 'م': 40, 'ن': 50, 'ں': 50, 'س': 60, 'ع': 70, 'ف': 80, 'ص': 90,
        'ق': 100, 'ر': 200, 'ڑ': 200, 'ش': 300, 'ت': 400, 'ٹ': 400, 'ث': 500,
        'خ': 600, 'ذ': 700, 'ض': 800, 'ظ': 900, 'غ': 1000
    };

    const elementMap = {
        'ا': 'Fire', 'آ': 'Fire', 'ہ': 'Fire', 'ھ': 'Fire', 'ط': 'Fire', 'م': 'Fire', 'ف': 'Fire', 'ش': 'Fire', 'ذ': 'Fire',
        'ب': 'Air', 'پ': 'Air', 'و': 'Air', 'ی': 'Air', 'ے': 'Air', 'ن': 'Air', 'ں': 'Air', 'ص': 'Air', 'ت': 'Air', 'ٹ': 'Air', 'ض': 'Air',
        'ج': 'Water', 'چ': 'Water', 'ز': 'Water', 'ژ': 'Water', 'ک': 'Water', 'گ': 'Water', 'س': 'Water', 'ق': 'Water', 'ث': 'Water', 'ظ': 'Water',
        'د': 'Earth', 'ڈ': 'Earth', 'ح': 'Earth', 'ل': 'Earth', 'ع': 'Earth', 'ر': 'Earth', 'ڑ': 'Earth', 'خ': 'Earth', 'غ': 'Earth'
    };

    function getElementPercentages(text) {
        let counts = { Fire: 0, Air: 0, Water: 0, Earth: 0 };
        let totalVal = 0;
        for (let char of (text || '')) {
            let elem = elementMap[char];
            let val = letterMap[char] || 0;
            if (elem && val > 0) {
                counts[elem] += val;
                totalVal += val;
            }
        }

        if (totalVal === 0) {
            return {
                Fire: 0, Air: 0, Water: 0, Earth: 0,
                html: '<span style="color:var(--text-muted); font-size:0.75rem;">N/A</span>'
            };
        }

        const pFire = Math.round((counts.Fire / totalVal) * 100);
        const pAir = Math.round((counts.Air / totalVal) * 100);
        const pWater = Math.round((counts.Water / totalVal) * 100);
        const pEarth = Math.round((counts.Earth / totalVal) * 100);

        const html = `
            <div class="elem-metric-quad">
                <span style="color: var(--fire-color);" title="Fire: ${pFire}%">${pFire}%</span>
                <span style="color: var(--air-color);" title="Air: ${pAir}%">${pAir}%</span>
                <span style="color: var(--water-color);" title="Water: ${pWater}%">${pWater}%</span>
                <span style="color: var(--earth-color);" title="Earth: ${pEarth}%">${pEarth}%</span>
            </div>
        `;

        return { Fire: pFire, Air: pAir, Water: pWater, Earth: pEarth, html };
    }

    function loadHistory() {
        const tbody = document.getElementById('historyTableBody');
        fetch('api.php?action=history')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                calculationsHistory = data;
                renderTable();
            } else if (data && data.error) {
                if (tbody) tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: var(--danger); padding: 1.5rem;">${escapeHtml(data.error)}</td></tr>`;
            }
        })
        .catch(() => {
            if (tbody) tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: var(--danger); padding: 1.5rem;">Network error loading records.</td></tr>`;
        });
    }

    function renderTable() {
        const tbody = document.getElementById('historyTableBody');
        if (!tbody) return;

        const query = (document.getElementById('globalSearchInput')?.value || '').toLowerCase().trim();

        let filtered = calculationsHistory.filter(item => {
            const elemInfo = getElementPercentages(item.name);

            if (activeElemFilter) {
                if (activeElemFilter === 'Fire' && elemInfo.Fire === 0) return false;
                if (activeElemFilter === 'Air' && elemInfo.Air === 0) return false;
                if (activeElemFilter === 'Water' && elemInfo.Water === 0) return false;
                if (activeElemFilter === 'Earth' && elemInfo.Earth === 0) return false;
            }

            if (query) {
                const matchName = (item.name || '').toLowerCase().includes(query);
                const matchTotal = String(item.total).includes(query);
                const matchSingle = String(item.single).includes(query);
                const matchOrigin = (item.origin || '').toLowerCase().includes(query);
                const matchMeanings = (item.meanings || '').toLowerCase().includes(query);
                if (!matchName && !matchTotal && !matchSingle && !matchOrigin && !matchMeanings) return false;
            }

            return true;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No calculation records match your search filter.</td></tr>`;
            const pageInfo = document.getElementById('pageInfoText');
            if (pageInfo) pageInfo.innerText = `0 records found`;
            return;
        }

        const totalPages = Math.ceil(filtered.length / pageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        const startIdx = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(startIdx, startIdx + pageSize);

        const pageInfo = document.getElementById('pageInfoText');
        if (pageInfo) pageInfo.innerText = `Page ${currentPage} of ${totalPages} (${filtered.length} total records)`;

        let html = '';
        pageItems.forEach(item => {
            const elemInfo = getElementPercentages(item.name);
            html += `
                <tr>
                    <td class="arabic-grid-cell">
                        <a href="view_name.php?id=${item.id}" class="arabic-grid-link" title="Click to inspect notes & breakdown">${escapeHtml(item.name)}</a>
                    </td>
                    <td><strong style="color: var(--accent-gold); font-size: 1.05rem;">${item.total}</strong></td>
                    <td><span class="root-pill-badge">${item.single}</span></td>
                    <td><span style="color: var(--text-secondary); font-size: 0.85rem;">${escapeHtml(item.origin || '—')}</span></td>
                    <td><span style="color: var(--text-secondary); font-size: 0.85rem;">${escapeHtml(item.meanings || '—')}</span></td>
                    <td style="text-align: center;">${elemInfo.html}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="view_name.php?id=${item.id}" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Inspect</a>
                        <button onclick="editRecord(${item.id})" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Edit</button>
                        <button onclick="deleteRecord(${item.id})" class="btn btn-danger btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Del</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
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
        document.getElementById('formTitle').innerText = 'Edit Calculation Record';
        document.getElementById('addEditRecordForm').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function deleteRecord(id) {
        if (!confirm('Are you sure you want to permanently delete this record?')) return;
        fetch('api.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadHistory();
            } else {
                alert('Error deleting: ' + (data.error || ''));
            }
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadHistory();

        // Element filter buttons
        document.querySelectorAll('.elem-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.elem-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeElemFilter = btn.getAttribute('data-elem');
                currentPage = 1;
                renderTable();
            });
        });

        // Search Input
        document.getElementById('globalSearchInput')?.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });

        // Add Record Drawer Toggle
        document.getElementById('btnAddNew')?.addEventListener('click', () => {
            document.getElementById('editRecordId').value = '';
            document.getElementById('formName').value = '';
            document.getElementById('formTotal').value = '';
            document.getElementById('formSingle').value = '';
            document.getElementById('formOrigin').value = '';
            document.getElementById('formMeanings').value = '';
            document.getElementById('formTitle').innerText = 'Add Calculation Record';
            document.getElementById('addEditRecordForm').style.display = 'block';
        });

        document.getElementById('btnCancelForm')?.addEventListener('click', () => {
            document.getElementById('addEditRecordForm').style.display = 'none';
        });
        document.getElementById('btnCancelFormBottom')?.addEventListener('click', () => {
            document.getElementById('addEditRecordForm').style.display = 'none';
        });

        // Form submit
        document.getElementById('btnSubmitForm')?.addEventListener('click', () => {
            const id = document.getElementById('editRecordId').value;
            const name = document.getElementById('formName').value.trim();
            const total = parseInt(document.getElementById('formTotal').value);
            const single = parseInt(document.getElementById('formSingle').value);
            const origin = document.getElementById('formOrigin').value.trim();
            const meanings = document.getElementById('formMeanings').value.trim();

            if (!name || isNaN(total) || isNaN(single)) {
                alert('Please fill out Name, Total Abjad, and Single Root.');
                return;
            }

            const action = id ? 'edit' : 'save';
            const payload = id ? { id, name, total, single, origin, meanings } : { name, total, single, origin, meanings };

            fetch('api.php?action=' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('addEditRecordForm').style.display = 'none';
                    loadHistory();
                } else {
                    alert('Error saving record: ' + (data.error || ''));
                }
            });
        });

        // Page size & navigation
        document.getElementById('pageSizeSelect')?.addEventListener('change', (e) => {
            pageSize = parseInt(e.target.value) || 25;
            currentPage = 1;
            renderTable();
        });

        document.getElementById('btnPrevPage')?.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        document.getElementById('btnNextPage')?.addEventListener('click', () => {
            currentPage++;
            renderTable();
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
// saved.php
$pageTitle = 'Saved Names History Log';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['status'] !== 'approved') {
    echo '<main style="text-align: center; padding: 2rem 1rem;">
            <h2>Account Approval Required</h2>
            <p>Your account must be logged in and approved by an administrator to view saved names history.</p>
            <a href="calculator.php" class="btn btn-primary" style="border-radius: 2px;">Return to Calculator</a>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<style>
    /* Yesterday's layout: Zero unnecessary padding, no shadows, no nested cards */
    main.container-saved {
        width: 100%;
        padding: 0.25rem 0.5rem;
        flex: 1;
    }

    .action-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0.25rem;
        margin-bottom: 0.4rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        direction: rtl;
        text-align: right;
        font-size: 1rem;
    }

    .history-table th, .history-table td {
        padding: 0.15rem 0.35rem;
        border: 1px solid #cbd5e1;
    }

    .history-table th {
        background: #e2e8f0;
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
    }

    /* Alternating slightly grey rows */
    .history-table tbody tr:nth-child(even) {
        background-color: #f1f5f9;
    }

    .history-table tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    /* Interactive mouse hover effect */
    .history-table tbody tr:hover {
        background-color: #cbd5e1 !important;
        transition: background 0.1s ease;
    }

    .history-table td.arabic-cell {
        font-family: 'Amiri', serif;
        font-size: 1.45rem;
        font-weight: bold;
    }

    .table-search-input {
        width: 100%;
        padding: 0.25rem 0.4rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 0;
        font-size: 0.95rem;
        font-weight: 500;
        direction: rtl;
    }
</style>

<main class="container-saved">
    <!-- Action Line -->
    <div class="action-header-row">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <a href="calculator.php" class="btn btn-sm" style="border-radius: 2px;">← Calculator</a>
            <strong style="font-size: 1rem; color: #0f172a;">Saved Names History Log</strong>
        </div>
        <div style="display: flex; gap: 0.4rem;">
            <button id="btnAddNew" type="button" class="btn btn-primary btn-sm" style="border-radius: 2px;">+ Add Record</button>
            <button onclick="loadHistory()" type="button" class="btn btn-sm" style="border-radius: 2px;">Refresh 🔄</button>
        </div>
    </div>

    <!-- Add/Edit Record Form (Inline, hidden by default) -->
    <div id="addEditRecordForm" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.75rem; margin-bottom: 0.6rem; border-radius: 0;">
        <h4 id="formTitle" style="margin-bottom: 0.5rem; font-size: 1.05rem; font-weight: 700; color: #0f172a;">Add Calculation Record</h4>
        <input type="hidden" id="editRecordId">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-bottom: 0.6rem;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.15rem;">Name *</label>
                <input type="text" id="formName" class="calc-input" style="width:100%; font-size: 1.05rem; height: 34px; padding: 0.25rem 0.4rem; border:1px solid #cbd5e1; border-radius: 0; direction: rtl; font-family: var(--font-arabic);">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.15rem;">Total *</label>
                <input type="number" id="formTotal" class="calc-input" style="width:100%; font-size: 1rem; height: 34px; padding: 0.25rem 0.4rem; border:1px solid #cbd5e1; border-radius: 0; direction: ltr;">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.15rem;">Single Root *</label>
                <input type="number" id="formSingle" class="calc-input" style="width:100%; font-size: 1rem; height: 34px; padding: 0.25rem 0.4rem; border:1px solid #cbd5e1; border-radius: 0; direction: ltr;">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.15rem;">Origin</label>
                <input type="text" id="formOrigin" class="calc-input" style="width:100%; font-size: 0.95rem; height: 34px; padding: 0.25rem 0.4rem; border:1px solid #cbd5e1; border-radius: 0;">
            </div>
            <div style="grid-column: span 2;">
                <label style="font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.15rem;">Meanings</label>
                <input type="text" id="formMeanings" class="calc-input" style="width:100%; font-size: 0.95rem; height: 34px; padding: 0.25rem 0.4rem; border:1px solid #cbd5e1; border-radius: 0;">
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button id="btnCancelForm" type="button" class="btn btn-sm" style="font-size: 0.9rem; padding: 0.3rem 0.7rem; border-radius: 2px;">Cancel</button>
            <button id="btnSubmitForm" type="button" class="btn btn-primary btn-sm" style="font-size: 0.9rem; padding: 0.3rem 0.7rem; border-radius: 2px;">Save Record</button>
        </div>
    </div>

    <!-- Bare History Table Direct View -->
    <div style="overflow-x: auto;">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Name (Click for Notes/Edit) ↕</th>
                    <th>Total ↕</th>
                    <th>Single ↕</th>
                    <th>Origin ↕</th>
                    <th>Meanings</th>
                    <th>Element Status</th>
                </tr>
                <tr>
                    <td><input type="text" id="search-name" class="table-search-input" placeholder="Search name..."></td>
                    <td><input type="text" id="search-total" class="table-search-input" placeholder="Search total..."></td>
                    <td><input type="text" id="search-single" class="table-search-input" placeholder="Search single..."></td>
                    <td><input type="text" id="search-origin" class="table-search-input" placeholder="Search origin..."></td>
                    <td><input type="text" id="search-meanings" class="table-search-input" placeholder="Search meanings..."></td>
                    <td style="display: flex; gap: 0.2rem; align-items: center;">
                        <button id="btnClearFilters" type="button" class="btn btn-sm" style="font-size: 0.75rem; padding: 0.15rem 0.35rem; font-weight: 600; border-radius: 2px;">Clear</button>
                        <select id="search-temperament" class="table-search-input" style="font-size: 0.85rem; font-weight: 500; border-radius: 0;">
                            <option value="">All Elements</option>
                            <option value="Fire">Fire</option>
                            <option value="Air">Air</option>
                            <option value="Water">Water</option>
                            <option value="Earth">Earth</option>
                        </select>
                    </td>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                <tr><td colspan="6" style="text-align: center; color: #64748b;">Loading history records...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Line -->
    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #64748b; padding: 0.4rem 0;">
        <div>
            Show: 
            <select id="pageSizeSelect" style="padding: 0.1rem; font-size: 0.78rem; border: 1px solid #cbd5e1; border-radius: 0;">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div id="pageInfoText">Page 1 of 1</div>
        <div style="display: flex; gap: 0.25rem;">
            <button id="btnPrevPage" type="button" class="btn btn-sm" style="border-radius: 2px;">Prev</button>
            <button id="btnNextPage" type="button" class="btn btn-sm" style="border-radius: 2px;">Next</button>
        </div>
    </div>
</main>

<script>
    let calculationsHistory = [];
    let currentPage = 1;
    let pageSize = 25;

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
                html: '<span style="color:#d97706; font-weight:700;">0</span> <span style="color:#dc2626; font-weight:700;">0</span> <span style="color:#2563eb; font-weight:700;">0</span> <span style="color:#16a34a; font-weight:700;">0</span>'
            };
        }

        const pFire = Math.round((counts.Fire / totalVal) * 100);
        const pAir = Math.round((counts.Air / totalVal) * 100);
        const pWater = Math.round((counts.Water / totalVal) * 100);
        const pEarth = Math.round((counts.Earth / totalVal) * 100);

        const html = `
            <div style="display: inline-flex; gap: 0.45rem; align-items: center; justify-content: center; direction: ltr; font-weight: 700; font-size: 0.95rem;">
                <span style="color: var(--fire-color);" title="Fire (آتشی)">${pFire}</span>
                <span style="color: var(--air-color);" title="Air (بادی)">${pAir}</span>
                <span style="color: var(--water-color);" title="Water (آبی)">${pWater}</span>
                <span style="color: var(--earth-color);" title="Earth (خاکی)">${pEarth}</span>
            </div>
        `;

        return { Fire: pFire, Air: pAir, Water: pWater, Earth: pEarth, html };
    }

    function loadHistory() {
        const tbody = document.getElementById('historyTableBody');
        if (tbody && calculationsHistory.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #64748b;">Loading history records...</td></tr>`;
        }

        fetch('api.php?action=history')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                calculationsHistory = data;
                renderTable();
            } else if (data && data.error) {
                if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #dc2626;">${escapeHtml(data.error)}</td></tr>`;
            }
        })
        .catch(err => {
            console.error('History fetch error:', err);
            if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #dc2626;">Network error loading records.</td></tr>`;
        });
    }

    function renderTable() {
        const tbody = document.getElementById('historyTableBody');
        if (!tbody) return;

        const sTemp = (document.getElementById('search-temperament')?.value || '').toLowerCase();

        let filtered = calculationsHistory.filter(item => {
            const elemInfo = getElementPercentages(item.name);

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
            if (sTemp) {
                if (sTemp === 'fire' && elemInfo.Fire === 0) return false;
                if (sTemp === 'air' && elemInfo.Air === 0) return false;
                if (sTemp === 'water' && elemInfo.Water === 0) return false;
                if (sTemp === 'earth' && elemInfo.Earth === 0) return false;
            }
            return true;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #64748b;">No matching records found.</td></tr>`;
            const pageInfo = document.getElementById('pageInfoText');
            if (pageInfo) pageInfo.innerText = `Page 0 of 0 (0 records)`;
            return;
        }

        const totalPages = Math.ceil(filtered.length / pageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        const startIdx = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(startIdx, startIdx + pageSize);

        const pageInfo = document.getElementById('pageInfoText');
        if (pageInfo) pageInfo.innerText = `Page ${currentPage} of ${totalPages} (${filtered.length} records)`;

        let html = '';
        pageItems.forEach(item => {
            const elemInfo = getElementPercentages(item.name);
            html += `
                <tr onclick="window.location.href='view_name.php?id=${item.id}'" style="cursor: pointer;" title="Click to view details & notes for ${escapeHtml(item.name)}">
                    <td class="arabic-cell">
                        <a href="view_name.php?id=${item.id}" style="color: #0f172a; text-decoration: none; font-weight: bold;">${escapeHtml(item.name)}</a>
                    </td>
                    <td><strong>${item.total}</strong></td>
                    <td><span style="color:#2563eb; font-weight:bold;">${item.single}</span></td>
                    <td>${escapeHtml(item.origin || '-')}</td>
                    <td>${escapeHtml(item.meanings || '-')}</td>
                    <td style="text-align: center;">${elemInfo.html}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    // Immediate load invocation
    loadHistory();

    document.addEventListener('DOMContentLoaded', () => {
        loadHistory();

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

        document.getElementById('btnSubmitForm')?.addEventListener('click', () => {
            const id = document.getElementById('editRecordId').value;
            const name = document.getElementById('formName').value.trim();
            const total = parseInt(document.getElementById('formTotal').value);
            const single = parseInt(document.getElementById('formSingle').value);
            const origin = document.getElementById('formOrigin').value.trim();
            const meanings = document.getElementById('formMeanings').value.trim();

            if (!name || isNaN(total) || isNaN(single)) {
                alert('Please fill Name, Total, and Single Root.');
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
                } else alert('Error: ' + (data.error || ''));
            });
        });

        ['search-name', 'search-total', 'search-single', 'search-origin', 'search-meanings', 'search-temperament'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', renderTable);
        });

        document.getElementById('btnClearFilters')?.addEventListener('click', () => {
            ['search-name', 'search-total', 'search-single', 'search-origin', 'search-meanings', 'search-temperament'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            renderTable();
        });

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

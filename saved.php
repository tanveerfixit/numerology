<?php
// saved.php
$pageTitle = 'Saved Names History Log';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!isStaffOrAdmin($currentUser)) {
    echo '<main style="text-align: center; padding: 1rem;">
            <h2>Staff & Admin Privilege Required</h2>
            <p>Access to saved names history database is reserved for Staff and Admin accounts.</p>
            <a href="calculator.php" class="btn btn-primary">Return to Calculator</a>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<style>
    /* Simplest layout: Zero unnecessary padding, no shadows, no nested cards */
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
        border-radius: 4px;
        font-size: 0.95rem;
        font-weight: 500;
        direction: rtl;
    }
</style>

<main class="container-saved">
    <!-- Action Line -->
    <div class="action-header-row">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <a href="calculator.php" class="btn btn-sm">← Calculator</a>
            <strong style="font-size: 1rem; color: #0f172a;">Saved Names History Log</strong>
        </div>
        <div style="display: flex; gap: 0.4rem;">
            <button id="btnAddNew" class="btn btn-primary btn-sm">+ Add Record</button>
            <button onclick="loadHistory()" class="btn btn-sm">Refresh 🔄</button>
        </div>
    </div>

    <!-- Add/Edit Record Form (Inline, hidden by default) -->
    <div id="addEditRecordForm" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.5rem; margin-bottom: 0.5rem;">
        <h4 id="formTitle" style="margin-bottom: 0.4rem; font-size: 0.85rem;">Add Calculation Record</h4>
        <input type="hidden" id="editRecordId">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.4rem; margin-bottom: 0.4rem;">
            <div>
                <label style="font-size: 0.7rem; color: #64748b;">Name *</label>
                <input type="text" id="formName" class="calc-input" style="width:100%; font-size: 0.9rem; height: 28px; padding: 0.15rem 0.3rem; border:1px solid #cbd5e1;">
            </div>
            <div>
                <label style="font-size: 0.7rem; color: #64748b;">Total *</label>
                <input type="number" id="formTotal" class="calc-input" style="width:100%; font-size: 0.9rem; height: 28px; padding: 0.15rem 0.3rem; border:1px solid #cbd5e1; direction: ltr;">
            </div>
            <div>
                <label style="font-size: 0.7rem; color: #64748b;">Single Root *</label>
                <input type="number" id="formSingle" class="calc-input" style="width:100%; font-size: 0.9rem; height: 28px; padding: 0.15rem 0.3rem; border:1px solid #cbd5e1; direction: ltr;">
            </div>
            <div>
                <label style="font-size: 0.7rem; color: #64748b;">Origin</label>
                <input type="text" id="formOrigin" class="calc-input" style="width:100%; font-size: 0.9rem; height: 28px; padding: 0.15rem 0.3rem; border:1px solid #cbd5e1;">
            </div>
            <div style="grid-column: span 2;">
                <label style="font-size: 0.7rem; color: #64748b;">Meanings</label>
                <input type="text" id="formMeanings" class="calc-input" style="width:100%; font-size: 0.9rem; height: 28px; padding: 0.15rem 0.3rem; border:1px solid #cbd5e1;">
            </div>
        </div>
        <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
            <button id="btnCancelForm" class="btn btn-sm">Cancel</button>
            <button id="btnSubmitForm" class="btn btn-primary btn-sm">Save</button>
        </div>
    </div>

    <!-- Bare History Table Direct View -->
    <div style="overflow-x: auto;">
        <table class="history-table">
            <thead>
                <tr>
                    <th style="text-align: center;">Actions</th>
                    <th>Name ↕</th>
                    <th>Total ↕</th>
                    <th>Single ↕</th>
                    <th>Origin ↕</th>
                    <th>Meanings</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td style="text-align: center;"><button id="btnClearFilters" class="btn btn-sm" style="font-size: 0.85rem; padding: 0.2rem 0.5rem; font-weight: 600;">Clear</button></td>
                    <td><input type="text" id="search-name" class="table-search-input" placeholder="Search name..."></td>
                    <td><input type="text" id="search-total" class="table-search-input" placeholder="Search total..."></td>
                    <td><input type="text" id="search-single" class="table-search-input" placeholder="Search single..."></td>
                    <td><input type="text" id="search-origin" class="table-search-input" placeholder="Search origin..."></td>
                    <td><input type="text" id="search-meanings" class="table-search-input" placeholder="Search meanings..."></td>
                    <td>
                        <select id="search-temperament" class="table-search-input" style="font-size: 0.9rem; font-weight: 500;">
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
                <tr><td colspan="7" style="text-align: center; color: #64748b;">Loading history records...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Line -->
    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #64748b; padding: 0.4rem 0;">
        <div>
            Show: 
            <select id="pageSizeSelect" style="padding: 0.1rem; font-size: 0.78rem; border: 1px solid #cbd5e1;">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div id="pageInfoText">Page 1 of 1</div>
        <div style="display: flex; gap: 0.25rem;">
            <button id="btnPrevPage" class="btn btn-sm">Prev</button>
            <button id="btnNextPage" class="btn btn-sm">Next</button>
        </div>
    </div>
</main>

<script>
    let calculationsHistory = [];
    let currentPage = 1;
    let pageSize = 25;

    function loadHistory() {
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
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #64748b;">No matching records.</td></tr>`;
            return;
        }

        const totalPages = Math.ceil(filtered.length / pageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        const startIdx = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(startIdx, startIdx + pageSize);

        document.getElementById('pageInfoText').innerText = `Page ${currentPage} of ${totalPages} (${filtered.length} records)`;

        let html = '';
        pageItems.forEach(item => {
            html += `
                <tr>
                    <td style="text-align: center;">
                        <button onclick="editRecord(${item.id})" class="btn btn-sm" style="font-size:0.68rem; padding: 0.05rem 0.25rem;">Edit</button>
                        <button onclick="deleteRecord(${item.id})" class="btn btn-danger btn-sm" style="font-size:0.68rem; padding: 0.05rem 0.25rem;">Del</button>
                    </td>
                    <td class="arabic-cell">${escapeHtml(item.name)}</td>
                    <td><strong>${item.total}</strong></td>
                    <td><span style="color:#2563eb; font-weight:bold;">${item.single}</span></td>
                    <td>${escapeHtml(item.origin || '-')}</td>
                    <td>${escapeHtml(item.meanings || '-')}</td>
                    <td><span style="font-size:0.72rem; color:#16a34a;">Saved</span></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function deleteRecord(id) {
        if (!confirm('Delete record?')) return;
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
        document.getElementById('formTitle').innerText = 'Edit Record #' + item.id;
        document.getElementById('addEditRecordForm').style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadHistory();

        document.getElementById('btnAddNew').addEventListener('click', () => {
            document.getElementById('editRecordId').value = '';
            document.getElementById('formName').value = '';
            document.getElementById('formTotal').value = '';
            document.getElementById('formSingle').value = '';
            document.getElementById('formOrigin').value = '';
            document.getElementById('formMeanings').value = '';
            document.getElementById('formTitle').innerText = 'Add Calculation Record';
            document.getElementById('addEditRecordForm').style.display = 'block';
        });

        document.getElementById('btnCancelForm').addEventListener('click', () => {
            document.getElementById('addEditRecordForm').style.display = 'none';
        });

        document.getElementById('btnSubmitForm').addEventListener('click', () => {
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

        document.getElementById('btnClearFilters').addEventListener('click', () => {
            ['search-name', 'search-total', 'search-single', 'search-origin', 'search-meanings', 'search-temperament'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            renderTable();
        });

        document.getElementById('pageSizeSelect').addEventListener('change', (e) => {
            pageSize = parseInt(e.target.value) || 25;
            currentPage = 1;
            renderTable();
        });

        document.getElementById('btnPrevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        document.getElementById('btnNextPage').addEventListener('click', () => {
            currentPage++;
            renderTable();
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

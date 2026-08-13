<?php
// view_name.php
$pageTitle = 'Single Name Details & Notes';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || $currentUser['status'] !== 'approved') {
    echo '<main style="text-align: center; padding: 2rem 1rem;">
            <h2>Account Approval Required</h2>
            <p>Your account must be logged in and approved by an administrator to view name record details.</p>
            <a href="calculator.php" class="btn btn-primary">Return to Calculator</a>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$recordId = $_GET['id'] ?? null;
?>

<style>
    main.container-name-detail {
        width: 100%;
        max-width: 950px;
        margin: 0 auto;
        padding: 0.75rem 1rem;
        flex: 1;
    }

    .detail-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .name-title-arabic {
        font-family: 'Amiri', serif;
        font-size: 2.8rem;
        color: #0f172a;
        direction: rtl;
        line-height: 1.2;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 0.75rem;
        margin: 1rem 0;
        background: #f8fafc;
        padding: 0.85rem;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    .metric-item {
        display: flex;
        flex-direction: column;
    }

    .metric-label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .metric-val {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
    }

    .elements-box {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .elem-pill {
        flex: 1;
        min-width: 100px;
        padding: 0.5rem;
        border-radius: 6px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .elem-fire { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .elem-air { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .elem-water { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .elem-earth { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
</style>

<main class="container-name-detail">
    <!-- Top Action Line -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
        <a href="saved.php" class="btn btn-sm">← Back to Saved List</a>
        <div style="display: flex; gap: 0.4rem;">
            <button id="btnEditName" class="btn btn-sm">Edit Entry ✏️</button>
            <button id="btnDeleteName" class="btn btn-danger btn-sm">Delete Entry 🗑️</button>
        </div>
    </div>

    <div id="detailLoading" style="text-align: center; padding: 2rem; color: #64748b;">Loading name record details...</div>

    <div id="detailContent" style="display: none;">
        <!-- Header Name Card -->
        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">RECORD ID #<span id="nameIdDisplay"></span></span>
                    <h1 id="nameArabicDisplay" class="name-title-arabic">--</h1>
                </div>
                <div style="text-align: right; font-size: 0.75rem; color: #64748b;">
                    Saved on: <span id="createdAtDisplay"></span>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="metrics-grid">
                <div class="metric-item">
                    <span class="metric-label">Total Abjad Sum</span>
                    <span id="totalDisplay" class="metric-val" style="color: #d97706;">0</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Single Root</span>
                    <span id="singleDisplay" class="metric-val" style="color: #2563eb;">0</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Origin</span>
                    <span id="originDisplay" class="metric-val" style="font-size: 1.05rem;">-</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Meanings</span>
                    <span id="meaningsDisplay" class="metric-val" style="font-size: 1.05rem;">-</span>
                </div>
            </div>

            <!-- Elemental Breakdown -->
            <div style="margin-top: 1rem;">
                <strong style="font-size: 0.85rem; color: #475569;">Elemental Temperament Breakdown:</strong>
                <div id="elementsContainer" class="elements-box"></div>
            </div>
        </div>

        <!-- Specific Custom Notes Card -->
        <div class="detail-card">
            <h3 style="font-size: 1.05rem; color: #0f172a; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                <span>📌 Specific Notes for this Name</span>
            </h3>
            <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 0.75rem;">Write custom observations, spiritual notes, or analysis specifically for this name entry.</p>

            <div id="notesAlert" style="display: none;"></div>

            <textarea id="nameNotesInput" rows="5" style="width: 100%; padding: 0.65rem; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;" placeholder="Write custom notes for this name entry..."></textarea>
            
            <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                <button id="btnSaveNotes" class="btn btn-primary">Save Specific Notes 💾</button>
            </div>
        </div>

        <!-- Inline Edit Modal Form -->
        <div id="editModalOverlay" style="display: none; position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(15,23,42,0.4); align-items: center; justify-content: center; z-index:1000; padding: 1rem;">
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; width: 100%; max-width: 500px; padding: 1.25rem;">
                <h3 style="margin-bottom: 0.75rem; font-size: 1.05rem; color: #0f172a;">Edit Name Record</h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <div>
                        <label style="font-size: 0.75rem; color: #64748b;">Name *</label>
                        <input type="text" id="editName" class="calc-input" style="width: 100%; font-size: 1.1rem; padding: 0.3rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label style="font-size: 0.75rem; color: #64748b;">Total *</label>
                            <input type="number" id="editTotal" class="calc-input" style="width: 100%; font-size: 1rem; padding: 0.3rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; direction: ltr;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; color: #64748b;">Single Root *</label>
                            <input type="number" id="editSingle" class="calc-input" style="width: 100%; font-size: 1rem; padding: 0.3rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; direction: ltr;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: #64748b;">Origin</label>
                        <input type="text" id="editOrigin" class="calc-input" style="width: 100%; font-size: 0.9rem; padding: 0.3rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: #64748b;">Meanings</label>
                        <input type="text" id="editMeanings" class="calc-input" style="width: 100%; font-size: 0.9rem; padding: 0.3rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.4rem; margin-top: 1rem;">
                    <button id="btnCancelEdit" class="btn btn-sm">Cancel</button>
                    <button id="btnSubmitEdit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const recordId = <?php echo json_encode($recordId); ?>;
    let nameRecord = null;

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

    function loadNameDetail() {
        if (!recordId) {
            document.getElementById('detailLoading').innerText = 'No record ID specified.';
            return;
        }

        fetch('api.php?action=get_name_detail&id=' + recordId)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                document.getElementById('detailLoading').innerText = 'Error: ' + data.error;
                return;
            }

            nameRecord = data;
            document.getElementById('nameIdDisplay').innerText = data.id;
            document.getElementById('nameArabicDisplay').innerText = data.name;
            document.getElementById('totalDisplay').innerText = data.total;
            document.getElementById('singleDisplay').innerText = data.single;
            document.getElementById('originDisplay').innerText = data.origin || '-';
            document.getElementById('meaningsDisplay').innerText = data.meanings || '-';
            document.getElementById('createdAtDisplay').innerText = data.created_at || '-';
            document.getElementById('nameNotesInput').value = data.notes || '';

            renderElementBreakdown(data.name);

            document.getElementById('detailLoading').style.display = 'none';
            document.getElementById('detailContent').style.display = 'block';
        })
        .catch(() => {
            document.getElementById('detailLoading').innerText = 'Failed to load record details.';
        });
    }

    function renderElementBreakdown(text) {
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

        let pFire = totalVal ? Math.round((counts.Fire / totalVal) * 100) : 0;
        let pAir = totalVal ? Math.round((counts.Air / totalVal) * 100) : 0;
        let pWater = totalVal ? Math.round((counts.Water / totalVal) * 100) : 0;
        let pEarth = totalVal ? Math.round((counts.Earth / totalVal) * 100) : 0;

        document.getElementById('elementsContainer').innerHTML = `
            <div class="elem-pill elem-fire">🔥 Fire (آتشی): ${pFire}%</div>
            <div class="elem-pill elem-air">💨 Air (بادی): ${pAir}%</div>
            <div class="elem-pill elem-water">💧 Water (آبی): ${pWater}%</div>
            <div class="elem-pill elem-earth">🪨 Earth (خاکی): ${pEarth}%</div>
        `;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadNameDetail();

        // Save Notes Action
        document.getElementById('btnSaveNotes').addEventListener('click', () => {
            const notes = document.getElementById('nameNotesInput').value;
            const alertDiv = document.getElementById('notesAlert');

            fetch('api.php?action=update_name_notes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: recordId, notes })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = data.message || 'Notes saved successfully!';
                    alertDiv.style.display = 'block';
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 3000);
                } else alert('Failed to save notes: ' + (data.error || ''));
            });
        });

        // Edit Entry Action
        document.getElementById('btnEditName').addEventListener('click', () => {
            if (!nameRecord) return;
            document.getElementById('editName').value = nameRecord.name;
            document.getElementById('editTotal').value = nameRecord.total;
            document.getElementById('editSingle').value = nameRecord.single;
            document.getElementById('editOrigin').value = nameRecord.origin || '';
            document.getElementById('editMeanings').value = nameRecord.meanings || '';
            document.getElementById('editModalOverlay').style.display = 'flex';
        });

        document.getElementById('btnCancelEdit').addEventListener('click', () => {
            document.getElementById('editModalOverlay').style.display = 'none';
        });

        document.getElementById('btnSubmitEdit').addEventListener('click', () => {
            const name = document.getElementById('editName').value.trim();
            const total = parseInt(document.getElementById('editTotal').value);
            const single = parseInt(document.getElementById('editSingle').value);
            const origin = document.getElementById('editOrigin').value.trim();
            const meanings = document.getElementById('editMeanings').value.trim();

            if (!name || isNaN(total) || isNaN(single)) {
                alert('Please fill Name, Total, and Single Root.');
                return;
            }

            fetch('api.php?action=edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: recordId, name, total, single, origin, meanings })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editModalOverlay').style.display = 'none';
                    loadNameDetail();
                } else alert('Failed to edit record');
            });
        });

        // Delete Entry Action
        document.getElementById('btnDeleteName').addEventListener('click', () => {
            if (!confirm('Are you sure you want to delete this name record?')) return;
            fetch('api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: recordId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'saved.php';
                } else alert('Failed to delete record');
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

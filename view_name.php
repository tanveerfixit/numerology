<?php
// view_name.php
$pageTitle = 'Name Inspection & Specific Notes';
require_once __DIR__ . '/includes/header.php';

requireLogin();
if (!$currentUser || !in_array($currentUser['role'], ['staff', 'admin']) || $currentUser['status'] !== 'approved') {
    echo '<main class="container" style="text-align: center; padding: 3rem 1.5rem;">
            <div style="background: #ffffff; border: 1px solid var(--border-subtle); padding: 2.5rem; border-radius: 0; max-width: 500px; margin: 0 auto; box-shadow: var(--shadow-sm);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🔒</div>
                <h2 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 0.5rem;">Staff & Admin Access Only</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">Access to individual saved record details and notes is reserved for Staff and Admin accounts. Public users can use the calculator workbench and submit consultation requests in their profile.</p>
                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="calculator.php" class="btn btn-primary" style="border-radius: 2px;">Go to Calculator</a>
                    <a href="profile.php" class="btn btn-secondary" style="border-radius: 2px;">My Profile & Chat</a>
                </div>
            </div>
          </main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$recordId = $_GET['id'] ?? null;
?>

<style>
    .name-detail-page {
        max-width: 920px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .detail-top-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .detail-card-panel {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .name-calligraphy-banner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 1.25rem;
    }

    .name-arabic-hero {
        font-family: var(--font-arabic);
        font-size: 3.2rem;
        font-weight: 700;
        color: var(--text-primary);
        direction: rtl;
        line-height: 1.15;
    }

    .metrics-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    @media (max-width: 640px) {
        .metrics-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .metric-panel-item {
        background: var(--surface-subtle);
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
    }

    .metric-panel-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }

    .metric-panel-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 0.2rem;
    }

    /* Elements Breakdown */
    .elements-status-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }

    @media (max-width: 640px) {
        .elements-status-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .elem-status-box {
        border-radius: 0;
        padding: 0.75rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        border: 1px solid transparent;
    }

    .elem-box-fire { background: var(--fire-bg); color: var(--fire-color); border-color: var(--fire-border); }
    .elem-box-air { background: var(--air-bg); color: var(--air-color); border-color: var(--air-border); }
    .elem-box-water { background: var(--water-bg); color: var(--water-color); border-color: var(--water-border); }
    .elem-box-earth { background: var(--earth-bg); color: var(--earth-color); border-color: var(--earth-border); }

    .elem-box-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.82rem;
        font-weight: 700;
    }

    /* Notes section */
    .notes-textarea-control {
        width: 100%;
        padding: 0.85rem 1rem;
        border: 1px solid var(--border-medium);
        border-radius: 0 !important;
        font-size: 0.92rem;
        font-family: inherit;
        line-height: 1.6;
        color: var(--text-primary);
        resize: vertical;
    }

    .notes-textarea-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }
</style>

<main class="container">
    <div class="name-detail-page">
        <!-- Breadcrumb / Actions -->
        <div class="detail-top-nav">
            <a href="saved.php" class="btn btn-secondary btn-sm" style="border-radius: 2px;">← Back to Saved Records</a>
            <div style="display: flex; gap: 0.45rem;">
                <button id="btnEditName" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;">✏️ Edit Details</button>
                <button id="btnDeleteName" type="button" class="btn btn-danger btn-sm" style="border-radius: 2px;">🗑️ Delete Entry</button>
            </div>
        </div>

        <div id="detailLoading" style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <span>⏳ Loading calculation record details...</span>
        </div>

        <div id="detailContent" style="display: none; flex-direction: column; gap: 1.5rem;">
            <!-- Main Name Information Card -->
            <div class="detail-card-panel">
                <div class="name-calligraphy-banner">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <span class="role-badge role-public">RECORD #<span id="nameIdDisplay"></span></span>
                        </div>
                        <h1 id="nameArabicDisplay" class="name-arabic-hero">--</h1>
                    </div>
                    <div style="text-align: right; font-size: 0.78rem; color: var(--text-muted);">
                        <span>Saved on:</span>
                        <div id="createdAtDisplay" style="font-weight: 600; color: var(--text-secondary); margin-top: 0.15rem;">--</div>
                    </div>
                </div>

                <!-- 4 Metrics KPI -->
                <div class="metrics-stats-grid">
                    <div class="metric-panel-item">
                        <span class="metric-panel-label">Total Abjad Sum</span>
                        <span id="totalDisplay" class="metric-panel-value" style="color: var(--accent-gold);">0</span>
                    </div>
                    <div class="metric-panel-item">
                        <span class="metric-panel-label">Single Digital Root</span>
                        <span id="singleDisplay" class="metric-panel-value" style="color: var(--primary);">0</span>
                    </div>
                    <div class="metric-panel-item">
                        <span class="metric-panel-label">Origin</span>
                        <span id="originDisplay" class="metric-panel-value" style="font-size: 1.15rem; font-weight: 600;">—</span>
                    </div>
                    <div class="metric-panel-item">
                        <span class="metric-panel-label">Meanings</span>
                        <span id="meaningsDisplay" class="metric-panel-value" style="font-size: 1.15rem; font-weight: 600;">—</span>
                    </div>
                </div>

                <!-- 4 Elements Breakdown Meters -->
                <div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 0.6rem; text-transform: uppercase; letter-spacing: 0.04em;">
                        Elemental Temperament Breakdown
                    </span>
                    <div id="elementsContainer" class="elements-status-grid"></div>
                </div>
            </div>

            <!-- Notes & Observations Card -->
            <div class="detail-card-panel">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                            <span>📝</span> Specialized Analysis & Personal Notes
                        </h2>
                        <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.15rem;">Record historical chronogram context, personal spiritual observations, or client consultations for this name.</p>
                    </div>
                </div>

                <div id="notesAlert" style="display: none;"></div>

                <textarea id="nameNotesInput" class="notes-textarea-control" rows="5" placeholder="Enter personalized notes, spiritual insights, or consultation records for this specific name..."></textarea>
                
                <div style="display: flex; justify-content: flex-end;">
                    <button id="btnSaveNotes" type="button" class="btn btn-primary" style="border-radius: 2px;">💾 Save Specific Notes</button>
                </div>
            </div>
        </div>

        <!-- Inline Edit Modal -->
        <div id="editModalOverlay" class="modal-overlay-custom">
            <div class="modal-card-box" style="text-align: left; max-width: 520px; border-radius: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Edit Name Record</h3>
                    <button id="btnCancelEditTop" type="button" class="btn btn-sm btn-secondary" style="padding: 0.15rem 0.45rem; border-radius: 2px;">✕</button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label class="form-label" for="editName">Name (Arabic/Urdu) *</label>
                        <input type="text" id="editName" class="form-control" style="font-family: var(--font-arabic); font-size: 1.25rem; direction: rtl; border-radius: 0;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label class="form-label" for="editTotal">Total Abjad *</label>
                            <input type="number" id="editTotal" class="form-control" style="border-radius: 0;">
                        </div>
                        <div>
                            <label class="form-label" for="editSingle">Single Root *</label>
                            <input type="number" id="editSingle" class="form-control" style="border-radius: 0;">
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="editOrigin">Origin</label>
                        <input type="text" id="editOrigin" class="form-control" style="border-radius: 0;">
                    </div>
                    <div>
                        <label class="form-label" for="editMeanings">Meanings</label>
                        <input type="text" id="editMeanings" class="form-control" style="border-radius: 0;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem;">
                    <button id="btnCancelEdit" type="button" class="btn btn-secondary btn-sm" style="border-radius: 2px;">Cancel</button>
                    <button id="btnSubmitEdit" type="button" class="btn btn-primary btn-sm" style="border-radius: 2px;">Save Changes</button>
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
            document.getElementById('originDisplay').innerText = data.origin || '—';
            document.getElementById('meaningsDisplay').innerText = data.meanings || '—';
            document.getElementById('createdAtDisplay').innerText = data.created_at || '—';
            document.getElementById('nameNotesInput').value = data.notes || '';

            renderElementBreakdown(data.name);

            document.getElementById('detailLoading').style.display = 'none';
            document.getElementById('detailContent').style.display = 'flex';
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
            <div class="elem-status-box elem-box-fire">
                <div class="elem-box-header"><span>🔥 Fire (آتشی)</span><span>${pFire}%</span></div>
                <div style="height: 4px; background: rgba(234,88,12,0.15); border-radius: 0; overflow: hidden;">
                    <div style="width: ${pFire}%; height: 100%; background: var(--fire-color); border-radius: 0;"></div>
                </div>
            </div>
            <div class="elem-status-box elem-box-air">
                <div class="elem-box-header"><span>💨 Air (بادی)</span><span>${pAir}%</span></div>
                <div style="height: 4px; background: rgba(2,132,199,0.15); border-radius: 0; overflow: hidden;">
                    <div style="width: ${pAir}%; height: 100%; background: var(--air-color); border-radius: 0;"></div>
                </div>
            </div>
            <div class="elem-status-box elem-box-water">
                <div class="elem-box-header"><span>💧 Water (آبی)</span><span>${pWater}%</span></div>
                <div style="height: 4px; background: rgba(37,99,235,0.15); border-radius: 0; overflow: hidden;">
                    <div style="width: ${pWater}%; height: 100%; background: var(--water-color); border-radius: 0;"></div>
                </div>
            </div>
            <div class="elem-status-box elem-box-earth">
                <div class="elem-box-header"><span>🪨 Earth (خاکی)</span><span>${pEarth}%</span></div>
                <div style="height: 4px; background: rgba(22,163,74,0.15); border-radius: 0; overflow: hidden;">
                    <div style="width: ${pEarth}%; height: 100%; background: var(--earth-color); border-radius: 0;"></div>
                </div>
            </div>
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
                    alertDiv.innerText = '✓ ' + (data.message || 'Notes updated successfully!');
                    alertDiv.style.display = 'flex';
                    setTimeout(() => { alertDiv.style.display = 'none'; }, 3000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = 'Failed to save notes: ' + (data.error || '');
                    alertDiv.style.display = 'flex';
                }
            });
        });

        // Edit Modal Handlers
        document.getElementById('btnEditName').addEventListener('click', () => {
            if (!nameRecord) return;
            document.getElementById('editName').value = nameRecord.name;
            document.getElementById('editTotal').value = nameRecord.total;
            document.getElementById('editSingle').value = nameRecord.single;
            document.getElementById('editOrigin').value = nameRecord.origin || '';
            document.getElementById('editMeanings').value = nameRecord.meanings || '';
            document.getElementById('editModalOverlay').style.display = 'flex';
        });

        const closeEdit = () => { document.getElementById('editModalOverlay').style.display = 'none'; };
        document.getElementById('btnCancelEdit').addEventListener('click', closeEdit);
        document.getElementById('btnCancelEditTop').addEventListener('click', closeEdit);

        document.getElementById('btnSubmitEdit').addEventListener('click', () => {
            const name = document.getElementById('editName').value.trim();
            const total = parseInt(document.getElementById('editTotal').value);
            const single = parseInt(document.getElementById('editSingle').value);
            const origin = document.getElementById('editOrigin').value.trim();
            const meanings = document.getElementById('editMeanings').value.trim();

            if (!name || isNaN(total) || isNaN(single)) {
                alert('Please fill out Name, Total Abjad, and Single Root.');
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
                    closeEdit();
                    loadNameDetail();
                } else alert('Failed to edit record: ' + (data.error || ''));
            });
        });

        // Delete Record Action
        document.getElementById('btnDeleteName').addEventListener('click', () => {
            if (!confirm('Are you sure you want to permanently delete this calculation record?')) return;
            fetch('api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: recordId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'saved.php';
                } else alert('Failed to delete record: ' + (data.error || ''));
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

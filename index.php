<?php
// index.php
$pageTitle = 'Huroof-e-Abjad & Geomancy - Ancient Science of Letters & Numbers';
require_once __DIR__ . '/includes/header.php';

$authMode = $_GET['auth'] ?? '';
$errorMsg = $_GET['error'] ?? '';
?>

<style>
    /* Landing Page Section Header */
    .landing-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }

    .landing-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        margin-bottom: 0.75rem;
    }

    .landing-title {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
    }

    .landing-subtitle {
        font-size: 1.05rem;
        color: #64748b;
        max-width: 720px;
        margin: 0 auto;
        line-height: 1.5;
    }

    /* Modern Fluid Grid */
    .insights-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        width: 100%;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 1024px) {
        .insights-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .landing-title {
            font-size: 1.7rem;
        }
    }

    @media (max-width: 640px) {
        .insights-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .landing-title {
            font-size: 1.4rem;
        }
        .landing-subtitle {
            font-size: 0.95rem;
        }
    }

    /* Modern Card Component */
    .insight-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .insight-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--card-accent, #2563eb);
        opacity: 0.85;
    }

    .insight-card:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
    }

    .card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: var(--icon-bg, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .tag-badge {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .insight-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .insight-desc {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
    }

    /* Auth Forms Styling */
    .auth-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 1.75rem;
        max-width: 440px;
        margin: 2rem auto;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.06);
    }

    .auth-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        text-align: center;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.3rem;
    }

    .form-control {
        width: 100%;
        padding: 0.55rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
    }

    .alert {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
</style>

<main class="container">
    <?php if ($currentUser && !empty($currentUser['circumstance'])): ?>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #1e3a8a;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                <span style="font-size: 1.1rem;">📌</span>
                <strong style="font-size: 0.95rem; color: #1e40af;">Your Update & Circumstance Note:</strong>
            </div>
            <p style="font-size: 0.9rem; color: #1e3a8a; line-height: 1.5; margin-left: 1.6rem;">
                <?php echo nl2br(htmlspecialchars($currentUser['circumstance'])); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg === 'pending'): ?>
        <div class="alert alert-warning" style="text-align: center;">
            ⏳ Your account is currently <strong>awaiting administrator approval</strong>. Once verified, your access to calculations and saved history will be unlocked.
        </div>
    <?php elseif ($errorMsg === 'unauthorized'): ?>
        <div class="alert alert-danger" style="text-align: center;">
            ⚠️ You do not have permission to access that area.
        </div>
    <?php endif; ?>

    <?php if ($authMode === 'login' && !$currentUser): ?>
        <!-- Login Card -->
        <div class="auth-card">
            <h3 class="auth-title">Account Login</h3>
            <div id="loginAlert" style="display: none;"></div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="loginUsername">Username or Email</label>
                    <input type="text" id="loginUsername" class="form-control" required placeholder="Enter username or email">
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.6rem; font-size: 0.95rem;">Login</button>
            </form>
            <p style="text-align: center; font-size: 0.85rem; color: #64748b; margin-top: 1rem;">
                Don't have an account? <a href="index.php?auth=signup" style="color: #2563eb; font-weight: 500;">Sign Up here</a>
            </p>
        </div>
    <?php elseif ($authMode === 'signup' && !$currentUser): ?>
        <!-- Signup Card -->
        <div class="auth-card">
            <h3 class="auth-title">Create Account</h3>
            <div id="signupAlert" style="display: none;"></div>
            <form id="signupForm">
                <div class="form-group">
                    <label for="signupUsername">Username</label>
                    <input type="text" id="signupUsername" class="form-control" required placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label for="signupEmail">Email Address</label>
                    <input type="email" id="signupEmail" class="form-control" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.6rem; font-size: 0.95rem;">Sign Up</button>
            </form>
            <p style="text-align: center; font-size: 0.85rem; color: #64748b; margin-top: 1rem;">
                Already registered? <a href="index.php?auth=login" style="color: #2563eb; font-weight: 500;">Login here</a>
            </p>
        </div>
    <?php else: ?>
        <!-- Section Header -->
        <div class="landing-header">
            <div class="landing-badge">✨ Numerical Wisdom & Divination</div>
            <h1 class="landing-title">Fascinating Insights: Abjad & Geomancy</h1>
            <p class="landing-subtitle">Discover how numerical values, elemental temperaments, and binary geomantic figures reveal deeper structural patterns in language and nature.</p>
        </div>

        <!-- Modern Insights Cards Grid -->
        <section class="insights-grid">
            <!-- Card 1 -->
            <div class="insight-card" style="--card-accent: #d97706; --icon-bg: #fef3c7;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">📜</div>
                        <span class="tag-badge">Historical System</span>
                    </div>
                    <h3 class="insight-title">3,000-Year Historical Legacy</h3>
                    <p class="insight-desc">The Abjad order follows the ancient Semitic alphabet sequence. Each letter carries a specific numerical value ranging from 1 to 1000, used historically for poetry, name compatibility, and sacred chronograms.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="insight-card" style="--card-accent: #2563eb; --icon-bg: #eff6ff;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">🔢</div>
                        <span class="tag-badge">Numerical Root</span>
                    </div>
                    <h3 class="insight-title">The Single Root Essence</h3>
                    <p class="insight-desc">By reducing any word's total numerical sum to its core single digit (1 through 9), practitioners distill complex names and expressions down to fundamental spiritual archetypes.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="insight-card" style="--card-accent: #dc2626; --icon-bg: #fef2f2;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">🔥</div>
                        <span class="tag-badge">Natural Elements</span>
                    </div>
                    <h3 class="insight-title">Four Elemental Temperaments</h3>
                    <p class="insight-desc">Every alphabet letter belongs to one of four natural elements: Fire (آتشی), Air (بادی), Water (آبی), or Earth (خاکی). Analyzing their distribution reveals unique temperament profiles.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="insight-card" style="--card-accent: #059669; --icon-bg: #ecfdf5;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">🏜️</div>
                        <span class="tag-badge">Binary Science</span>
                    </div>
                    <h3 class="insight-title">Geomancy (Ilm-ul-Raml)</h3>
                    <p class="insight-desc">Known as "the science of sand," Geomancy generates 16 distinct figures composed of single dots (active) and double dots (passive), forming an intuitive ancient binary code.</p>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="insight-card" style="--card-accent: #7c3aed; --icon-bg: #f5f3ff;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">🪐</div>
                        <span class="tag-badge">Cosmic Order</span>
                    </div>
                    <h3 class="insight-title">Planetary & Zodiac Harmony</h3>
                    <p class="insight-desc">Each geomantic figure corresponds to celestial bodies, zodiac signs, and Abjad values, connecting spoken language directly with cosmic cycles.</p>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="insight-card" style="--card-accent: #0284c7; --icon-bg: #f0f9ff;">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">🔒</div>
                        <span class="tag-badge">Data Privacy</span>
                    </div>
                    <h3 class="insight-title">Protected History Log</h3>
                    <p class="insight-desc">All calculations, letter breakdowns, and personal history records are safely saved in your private account workspace, restricted strictly to authorized users.</p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const username = document.getElementById('loginUsername').value;
                const password = document.getElementById('loginPassword').value;
                const alertDiv = document.getElementById('loginAlert');

                fetch('api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.user.status === 'approved') {
                            window.location.href = 'calculator.php';
                        } else {
                            window.location.href = 'index.php?error=pending';
                        }
                    } else {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerText = data.error || 'Login failed';
                        alertDiv.style.display = 'block';
                    }
                })
                .catch(() => {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = 'Network error during login';
                    alertDiv.style.display = 'block';
                });
            });
        }

        const signupForm = document.getElementById('signupForm');
        if (signupForm) {
            signupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const username = document.getElementById('signupUsername').value;
                const email = document.getElementById('signupEmail').value;
                const password = document.getElementById('signupPassword').value;
                const alertDiv = document.getElementById('signupAlert');

                fetch('api.php?action=signup', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, password })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alertDiv.className = 'alert alert-success';
                        alertDiv.innerText = data.message || 'Account created! Pending approval.';
                        alertDiv.style.display = 'block';
                        signupForm.reset();
                    } else {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerText = data.error || 'Signup failed';
                        alertDiv.style.display = 'block';
                    }
                })
                .catch(() => {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = 'Network error during signup';
                    alertDiv.style.display = 'block';
                });
            });
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

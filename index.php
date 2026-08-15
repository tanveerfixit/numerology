<?php
// index.php
$pageTitle = 'Huroof-e-Abjad & Geomancy - Classical Science of Letters & Numbers';
require_once __DIR__ . '/includes/header.php';

$authMode = $_GET['auth'] ?? '';
$errorMsg = $_GET['error'] ?? '';
$reqNameParam = trim($_GET['name'] ?? '');
$isRequestMode = isset($_GET['request']) || $authMode === 'request' || !empty($reqNameParam);
?>

<style>
    /* Hero Section */
    .hero-container {
        padding: 2.5rem 0 3.5rem;
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 2.5rem;
        align-items: center;
    }

    @media (max-width: 900px) {
        .hero-container {
            grid-template-columns: 1fr;
            padding: 1rem 0 2rem;
            gap: 1.5rem;
        }
        .hero-text-col {
            display: none !important;
        }
    }

    .hero-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.25;
        margin-bottom: 0.85rem;
    }

    .hero-title span.highlight {
        color: var(--primary);
        background: linear-gradient(120deg, #1d4ed8, #2563eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        font-size: 1.1rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.75rem;
        max-width: 540px;
    }

    .hero-cta-group {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
    }

    /* Live Interactive Hero Playground Card */
    .hero-playground-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: var(--shadow-lg);
        position: relative;
    }

    .playground-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-subtle);
        border-radius: 0;
    }

    .playground-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        color: var(--success);
        font-weight: 600;
        background: var(--success-bg);
        border: 1px solid var(--success-border);
        padding: 0.15rem 0.5rem;
        border-radius: 0;
    }

    .live-dot {
        width: 6px;
        height: 6px;
        background: var(--success);
        border-radius: 0;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }

    .hero-input {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--surface-subtle);
        border: 1px solid var(--border-medium);
        border-radius: 0 !important;
        color: var(--text-primary);
        font-family: var(--font-arabic);
        font-size: 1.5rem;
        direction: rtl;
        transition: all 0.15s ease;
    }

    .hero-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    .hero-metrics-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
        margin: 1rem 0;
    }

    .hero-metric-box {
        background: var(--surface-subtle);
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 0.75rem 1rem;
        text-align: center;
    }

    .hero-metric-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hero-metric-val {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 0.15rem;
    }

    /* Elements Micro Circles */
    .hero-elements-meters {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-top: 0.85rem;
        justify-items: center;
        align-items: center;
        direction: rtl;
    }

    .micro-elem-circle-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .micro-elem-circle {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: conic-gradient(var(--elem-col) 0%, var(--surface-subtle) 0% 100%);
        transition: background 0.3s ease;
        padding: 3.5px;
    }

    .micro-circle-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.76rem;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    /* Section Title */
    .section-header-block {
        text-align: center;
        max-width: 680px;
        margin: 3rem auto 2rem;
    }

    .section-tag {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
        display: block;
    }

    .section-heading {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
    }

    .section-subtext {
        font-size: 0.98rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Elements Grid Section */
    .elements-overview-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 3.5rem;
        direction: rtl;
    }

    @media (max-width: 900px) {
        .elements-overview-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 520px) {
        .elements-overview-grid {
            grid-template-columns: 1fr;
        }
    }

    .element-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .element-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--elem-color);
    }

    .element-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--border-medium);
    }

    .elem-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .elem-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 0;
        background: var(--elem-bg);
        color: var(--elem-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .elem-nature-tag {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--surface-subtle);
        padding: 0.2rem 0.5rem;
        border-radius: 0;
    }

    .elem-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .elem-arabic-set {
        font-family: var(--font-arabic);
        font-size: 1.25rem;
        color: var(--text-primary);
        direction: rtl;
        padding: 0.5rem 0.65rem;
        background: var(--surface-subtle);
        border-radius: 0;
        margin: 0.65rem 0;
        letter-spacing: 0.25rem;
        text-align: center;
    }

    .elem-desc-text {
        font-size: 0.84rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* Knowledge Cards Grid */
    .knowledge-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 3.5rem;
    }

    @media (max-width: 900px) {
        .knowledge-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 600px) {
        .knowledge-grid {
            grid-template-columns: 1fr;
        }
    }

    .knowledge-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 0;
        padding: 1.4rem;
        display: flex;
        flex-direction: column;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .knowledge-card:hover {
        transform: translateY(-3px);
        border-color: var(--border-medium);
        box-shadow: var(--shadow-md);
    }

    .k-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1rem;
    }

    .k-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .k-card-desc {
        font-size: 0.88rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Auth Box */
    .auth-wrapper-card {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: 0;
        padding: 2rem;
        max-width: 440px;
        margin: 2.5rem auto;
        box-shadow: var(--shadow-xl);
    }

    .auth-tab-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.35rem;
        background: var(--surface-subtle);
        padding: 0.25rem;
        border-radius: 0;
        margin-bottom: 1.5rem;
    }

    .auth-tab-btn {
        background: transparent;
        border: none;
        padding: 0.45rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-secondary);
        border-radius: 2px !important;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .auth-tab-btn.active {
        background: #ffffff;
        color: var(--primary);
        box-shadow: var(--shadow-xs);
    }

    .form-group-item {
        margin-bottom: 1.1rem;
    }

    .form-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }
</style>

<main class="container">
    <?php if ($currentUser && !empty($currentUser['circumstance'])): ?>
        <div class="alert alert-info" style="border-radius: 0;">
            <span style="font-size: 1.2rem;">📌</span>
            <div>
                <strong>Active Circumstance Consultation:</strong>
                <p style="margin-top: 0.2rem; font-size: 0.85rem; color: #1e3a8a;">
                    <?php echo nl2br(htmlspecialchars($currentUser['circumstance'])); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg === 'pending'): ?>
        <div class="alert alert-warning" style="border-radius: 0;">
            <span>⏳</span>
            <div>
                <strong>Account Pending Approval:</strong> Your registration has been received and is awaiting administrator verification. Basic calculator access is open.
            </div>
        </div>
    <?php elseif ($errorMsg === 'unauthorized'): ?>
        <div class="alert alert-danger" style="border-radius: 0;">
            <span>⚠️</span>
            <div><strong>Access Restricted:</strong> You do not possess the required privilege role to view that area.</div>
        </div>
    <?php endif; ?>

    <?php if ($authMode === 'login' || $authMode === 'signup' || $authMode === 'request'): ?>
        <!-- Dedicated Tabbed Auth Card -->
        <div class="auth-wrapper-card" style="max-width: 580px;">
            <div class="auth-tab-buttons">
                <a href="index.php?auth=login" class="auth-tab-btn <?php echo $authMode === 'login' ? 'active' : ''; ?>" style="text-align: center; text-decoration: none; border-radius: 2px;">Account Login</a>
                <a href="index.php?auth=signup<?php echo $reqNameParam ? '&name='.urlencode($reqNameParam).'&request=1' : ''; ?>" class="auth-tab-btn <?php echo ($authMode === 'signup' || $authMode === 'request') ? 'active' : ''; ?>" style="text-align: center; text-decoration: none; border-radius: 2px;">
                    <?php echo (isset($_GET['request']) || !empty($reqNameParam)) ? '✨ Request Info & Register' : 'Create Account'; ?>
                </a>
            </div>

            <?php if ($authMode === 'login'): ?>
                <div id="loginAlert" style="display: none;"></div>
                <form id="loginForm">
                    <div class="form-group-item">
                        <label class="form-label" for="loginUsername">Username or Email</label>
                        <input type="text" id="loginUsername" class="form-control" placeholder="Enter username or email" required autocomplete="username">
                    </div>
                    <div class="form-group-item">
                        <label class="form-label" for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.65rem; margin-top: 0.5rem; border-radius: 2px;">
                        Sign In →
                    </button>
                </form>
                <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 1.25rem;">
                    Don't have an account? <a href="index.php?auth=signup" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign Up</a>
                </p>
            <?php else: ?>
                <div id="signupAlert" style="display: none;"></div>

                <?php if (isset($_GET['request']) || !empty($reqNameParam)): ?>
                    <div style="background: var(--surface-subtle); border-left: 3px solid var(--primary); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; color: var(--text-secondary);">
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 0.2rem;">✨ Numerology Consultation & Inquiry Request</strong>
                        Register your account to send a request for in-depth numerology inspection (Abjad total, digital root, elemental temperament & recommendations) from our administrative scholars.
                    </div>
                <?php endif; ?>

                <form id="signupForm">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group-item">
                            <label class="form-label" for="signupUsername">Desired Username *</label>
                            <input type="text" id="signupUsername" class="form-control" placeholder="e.g. tariq_ali" required autocomplete="username">
                        </div>
                        <div class="form-group-item">
                            <label class="form-label" for="signupEmail">Email Address *</label>
                            <input type="email" id="signupEmail" class="form-control" placeholder="tariq@example.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group-item">
                        <label class="form-label" for="signupPassword">Password *</label>
                        <input type="password" id="signupPassword" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                    </div>

                    <!-- Consultation Request Section -->
                    <div style="background: #ffffff; border: 1px solid var(--border-medium); padding: 1rem; margin: 1rem 0; border-radius: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">
                                📜 Numerology Information & Target Name Request
                            </span>
                            <button type="button" id="btnToggleUrduKb" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem; font-size: 0.75rem; border-radius: 2px;">
                                ⌨️ Urdu Keyboard
                            </button>
                        </div>

                        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div>
                                <label class="form-label" for="signupReqName" style="font-size: 0.78rem;">Target Name (Urdu / Arabic)</label>
                                <input type="text" id="signupReqName" class="form-control" placeholder="e.g. محمد / علی / فاطمہ..." value="<?php echo htmlspecialchars($reqNameParam); ?>" style="font-family: var(--font-arabic); font-size: 1.15rem; direction: rtl;">
                            </div>
                            <div>
                                <label class="form-label" for="signupRelationship" style="font-size: 0.78rem;">Relationship</label>
                                <input type="text" id="signupRelationship" class="form-control" placeholder="e.g. Self, Child, Spouse">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div>
                                <label class="form-label" for="signupFullName" style="font-size: 0.78rem;">Your Full Name (Optional)</label>
                                <input type="text" id="signupFullName" class="form-control" placeholder="e.g. Tariq Mahmood">
                            </div>
                            <div>
                                <label class="form-label" for="signupContact" style="font-size: 0.78rem;">Contact / Phone (Optional)</label>
                                <input type="text" id="signupContact" class="form-control" placeholder="e.g. +92 300 1234567">
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="signupQuestion" style="font-size: 0.78rem;">Consultation Question / Information to Inspect</label>
                            <textarea id="signupQuestion" class="form-control" rows="2" placeholder="Please inspect this name according to numerology for temperament, digital root, compatibility, and guidance..."></textarea>
                        </div>

                        <!-- Virtual Urdu Keyboard Drawer in Signup -->
                        <div id="signupKbContainer" style="display: none; margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px dashed var(--border-subtle);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-primary);">⌨️ Virtual Urdu Keyboard</span>
                                <div style="display: flex; gap: 0.35rem;">
                                    <button id="signupKbSpace" type="button" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.5rem; font-size: 0.72rem; border-radius: 2px;">Space</button>
                                    <button id="signupKbBackspace" type="button" class="btn btn-danger btn-sm" style="padding: 0.15rem 0.5rem; font-size: 0.72rem; border-radius: 2px;">⌫</button>
                                </div>
                            </div>
                            <div id="signupKbGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)); gap: 0.3rem; direction: rtl;"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; margin-top: 0.25rem; font-weight: 700; border-radius: 2px; box-shadow: var(--shadow-sm);">
                        <span>✨ Submit Request & Register Account</span>
                        <span>→</span>
                    </button>
                </form>
                <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 1.25rem;">
                    Already registered? <a href="index.php?auth=login" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign In</a>
                </p>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- Hero Section with Interactive Quick-Playground -->
        <section class="hero-container">
            <div class="hero-text-col">
                <h1 class="hero-title">
                    Unlock Hidden Numerical Wisdom in <span class="highlight">Every Word</span>.
                </h1>
                <p class="hero-desc">
                    Discover the ancient Semitic numerical values of letters, four elemental temperaments (Fire, Air, Water, Earth), and classical geomantic figure alignments.
                </p>
                <div class="hero-cta-group">
                    <a href="calculator.php" class="btn btn-primary btn-lg" style="border-radius: 2px;">
                        <span>🧮 Full Calculator Workbench</span>
                        <span>→</span>
                    </a>
                    <a href="#elementsSection" class="btn btn-secondary btn-lg" style="border-radius: 2px;">
                        <span>Learn 4 Elements</span>
                    </a>
                </div>
            </div>

            <!-- Interactive Live Playground in Hero -->
            <div class="hero-playground-card">
                <div class="playground-header">
                    <div class="playground-title">
                        <span>⚡ Live Quick Calculation</span>
                    </div>
                    <div class="live-indicator">
                        <span class="live-dot"></span>
                        <span>Realtime</span>
                    </div>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem; display: block;">Type any Urdu / Arabic Name:</label>
                    <input type="text" id="heroQuickInput" class="hero-input" placeholder="محمد / علی / فاطمہ..." value="" autocomplete="off">
                </div>

                <div class="hero-metrics-row">
                    <div class="hero-metric-box">
                        <span class="hero-metric-label">Total Abjad Value</span>
                        <div class="hero-metric-val" id="heroTotalVal" style="color: var(--accent-gold);">0</div>
                    </div>
                    <div class="hero-metric-box">
                        <span class="hero-metric-label">Single Digital Root</span>
                        <div class="hero-metric-val" id="heroSingleVal" style="color: var(--primary);">0</div>
                    </div>
                </div>

                <div class="hero-elements-meters">
                    <div class="micro-elem-circle-wrap" title="Fire">
                        <div id="hero-circle-fire" class="micro-elem-circle" style="--elem-col: var(--fire-color);">
                            <div class="micro-circle-inner">
                                <span id="hero-pct-fire" style="color: var(--fire-color);">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="micro-elem-circle-wrap" title="Air">
                        <div id="hero-circle-air" class="micro-elem-circle" style="--elem-col: var(--air-color);">
                            <div class="micro-circle-inner">
                                <span id="hero-pct-air" style="color: var(--air-color);">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="micro-elem-circle-wrap" title="Water">
                        <div id="hero-circle-water" class="micro-elem-circle" style="--elem-col: var(--water-color);">
                            <div class="micro-circle-inner">
                                <span id="hero-pct-water" style="color: var(--water-color);">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="micro-elem-circle-wrap" title="Earth">
                        <div id="hero-circle-earth" class="micro-elem-circle" style="--elem-col: var(--earth-color);">
                            <div class="micro-circle-inner">
                                <span id="hero-pct-earth" style="color: var(--earth-color);">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.55rem; text-align: center;">
                    <button type="button" id="btnHeroRequestInfo" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.65rem 1rem; font-weight: 700; border-radius: 2px; box-shadow: var(--shadow-sm); cursor: pointer;">
                        <span>✨ Request info</span>
                        <span>→</span>
                    </button>
                    <a href="calculator.php" style="font-size: 0.82rem; font-weight: 600; color: var(--primary); text-decoration: none; padding: 0.15rem 0;">
                        Open full workbench with Urdu on-screen keyboard & breakdown →
                    </a>
                </div>
            </div>
        </section>

        <!-- Four Elements Matrix Section -->
        <div id="elementsSection" class="section-header-block">
            <span class="section-tag">Four Elemental Temperaments</span>
            <h2 class="section-heading">The Four Cosmic Elements (Taba'i)</h2>
            <p class="section-subtext">Classical numerology categorizes the 28 Arabic letters across four natural elements, shaping distinct personality and temperament profiles.</p>
        </div>

        <section class="elements-overview-grid">
            <!-- Fire -->
            <div class="element-card" style="--elem-color: var(--fire-color); --elem-bg: var(--fire-bg);">
                <div>
                    <div class="elem-top-row">
                        <div class="elem-icon-badge">🔥</div>
                        <span class="elem-nature-tag">Hot & Dry (گرم خشک)</span>
                    </div>
                    <h3 class="elem-title">Fire (آتشی)</h3>
                    <p class="elem-desc-text">Governs leadership, vital warmth, initiative, spiritual drive, and dynamic courage.</p>
                </div>
                <div>
                    <div class="elem-arabic-set">ا ہ ط م ف ش ذ</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-align: center;">7 Corresponding Letters</div>
                </div>
            </div>

            <!-- Air -->
            <div class="element-card" style="--elem-color: var(--air-color); --elem-bg: var(--air-bg);">
                <div>
                    <div class="elem-top-row">
                        <div class="elem-icon-badge">💨</div>
                        <span class="elem-nature-tag">Hot & Moist (گرم تر)</span>
                    </div>
                    <h3 class="elem-title">Air (بادی)</h3>
                    <p class="elem-desc-text">Governs communication, eloquence, intellect, adaptability, and social connectivity.</p>
                </div>
                <div>
                    <div class="elem-arabic-set">ب و ی ن ص ت ض</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-align: center;">7 Corresponding Letters</div>
                </div>
            </div>

            <!-- Water -->
            <div class="element-card" style="--elem-color: var(--water-color); --elem-bg: var(--water-bg);">
                <div>
                    <div class="elem-top-row">
                        <div class="elem-icon-badge">💧</div>
                        <span class="elem-nature-tag">Cold & Moist (سرد تر)</span>
                    </div>
                    <h3 class="elem-title">Water (آبی)</h3>
                    <p class="elem-desc-text">Governs intuition, emotional depth, healing, flexibility, and subconscious insight.</p>
                </div>
                <div>
                    <div class="elem-arabic-set">ج ز ک س ق ث ظ</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-align: center;">7 Corresponding Letters</div>
                </div>
            </div>

            <!-- Earth -->
            <div class="element-card" style="--elem-color: var(--earth-color); --elem-bg: var(--earth-bg);">
                <div>
                    <div class="elem-top-row">
                        <div class="elem-icon-badge">🪨</div>
                        <span class="elem-nature-tag">Cold & Dry (سرد خشک)</span>
                    </div>
                    <h3 class="elem-title">Earth (خاکی)</h3>
                    <p class="elem-desc-text">Governs stability, endurance, structure, practical manifestation, and grounded loyalty.</p>
                </div>
                <div>
                    <div class="elem-arabic-set">د ح ل ع ر خ غ</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); text-align: center;">7 Corresponding Letters</div>
                </div>
            </div>
        </section>

        <!-- Deep Knowledge Bento Grid -->
        <div class="section-header-block">
            <span class="section-tag">System Principles</span>
            <h2 class="section-heading">Core Scientific Archetypes</h2>
            <p class="section-subtext">How numerical values, elemental weights, and binary geomantic figures connect to create holistic analysis.</p>
        </div>

        <section class="knowledge-grid">
            <!-- Card 1 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: var(--accent-gold-light); color: var(--accent-gold);">📜</div>
                <h3 class="k-card-title">3,000-Year Semitic Legacy</h3>
                <p class="k-card-desc">The Abjad decimal hierarchy assigns exact values from 1 to 1000 across 28 alphabets, used for historical chronograms, poetry metrics, and sacred literature.</p>
            </div>

            <!-- Card 2 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: var(--primary-light); color: var(--primary);">🔢</div>
                <h3 class="k-card-title">Single Digital Root Distillation</h3>
                <p class="k-card-desc">By reducing any cumulative numerical sum modulo 9, the single root value (1 through 9) reveals the fundamental archetypal baseline of the name.</p>
            </div>

            <!-- Card 3 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: #fdf2f8; color: #db2777;">🏜️</div>
                <h3 class="k-card-title">Geomancy (Ilm-ul-Raml)</h3>
                <p class="k-card-desc">The ancient binary science of sand. 16 archetypal figures generated via active (odd) and passive (even) points, reflecting elemental polarity.</p>
            </div>

            <!-- Card 4 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: #f5f3ff; color: #7c3aed;">🪐</div>
                <h3 class="k-card-title">Planetary & Zodiac Correspondence</h3>
                <p class="k-card-desc">Each figure and elemental dominance aligns with celestial bodies (Sun, Moon, Mars, Jupiter) and seasonal astrological houses.</p>
            </div>

            <!-- Card 5 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">💬</div>
                <h3 class="k-card-title">Consultation & Q&A Thread</h3>
                <p class="k-card-desc">Submit your personal circumstance or question to the admin expert team and receive personalized numerical guidance in your private stream.</p>
            </div>

            <!-- Card 6 -->
            <div class="knowledge-card">
                <div class="k-icon-wrapper" style="background: var(--surface-subtle); color: var(--text-primary);">🔒</div>
                <h3 class="k-card-title">Protected Historical Archive</h3>
                <p class="k-card-desc">Authorized accounts can save calculation entries, record origins and meanings, add rich notes, and maintain an organized history log.</p>
            </div>
        </section>

    <?php endif; ?>
</main>

<script>
    // Embedded Map for Realtime Hero Quick Calculator
    const quickLetterMap = {
        'ا': 1, 'آ': 1, 'ب': 2, 'پ': 2, 'ج': 3, 'چ': 3, 'د': 4, 'ڈ': 4, 'ہ': 5, 'ھ': 5,
        'و': 6, 'ز': 7, 'ژ': 7, 'ح': 8, 'ط': 9, 'ی': 10, 'ے': 10, 'ک': 20, 'گ': 20,
        'ل': 30, 'م': 40, 'ن': 50, 'ں': 50, 'س': 60, 'ع': 70, 'ف': 80, 'ص': 90,
        'ق': 100, 'ر': 200, 'ڑ': 200, 'ش': 300, 'ت': 400, 'ٹ': 400, 'ث': 500,
        'خ': 600, 'ذ': 700, 'ض': 800, 'ظ': 900, 'غ': 1000
    };

    const quickElementMap = {
        'ا': 'fire', 'آ': 'fire', 'ہ': 'fire', 'ھ': 'fire', 'ط': 'fire', 'م': 'fire', 'ف': 'fire', 'ش': 'fire', 'ذ': 'fire',
        'ب': 'air', 'پ': 'air', 'و': 'air', 'ی': 'air', 'ے': 'air', 'ن': 'air', 'ں': 'air', 'ص': 'air', 'ت': 'air', 'ٹ': 'air', 'ض': 'air',
        'ج': 'water', 'چ': 'water', 'ز': 'water', 'ژ': 'water', 'ک': 'water', 'گ': 'water', 'س': 'water', 'ق': 'water', 'ث': 'water', 'ظ': 'water',
        'د': 'earth', 'ڈ': 'earth', 'ح': 'earth', 'ل': 'earth', 'ع': 'earth', 'ر': 'earth', 'ڑ': 'earth', 'خ': 'earth', 'غ': 'earth'
    };

    function updateHeroQuickCalc() {
        const inputEl = document.getElementById('heroQuickInput');
        if (!inputEl) return;
        const text = inputEl.value;
        let total = 0;
        let counts = { fire: 0, air: 0, water: 0, earth: 0 };
        let elemTotal = 0;

        for (let char of text) {
            const val = quickLetterMap[char] || 0;
            if (val > 0) {
                total += val;
                const elem = quickElementMap[char];
                if (elem) {
                    counts[elem] += val;
                    elemTotal += val;
                }
            }
        }

        const single = (total === 0) ? 0 : ((total % 9 === 0) ? 9 : total % 9);
        document.getElementById('heroTotalVal').innerText = total;
        document.getElementById('heroSingleVal').innerText = single;

        const pFire = elemTotal ? Math.round((counts.fire / elemTotal) * 100) : 0;
        const pAir = elemTotal ? Math.round((counts.air / elemTotal) * 100) : 0;
        const pWater = elemTotal ? Math.round((counts.water / elemTotal) * 100) : 0;
        const pEarth = elemTotal ? Math.round((counts.earth / elemTotal) * 100) : 0;

        const pctFireEl = document.getElementById('hero-pct-fire');
        const pctAirEl = document.getElementById('hero-pct-air');
        const pctWaterEl = document.getElementById('hero-pct-water');
        const pctEarthEl = document.getElementById('hero-pct-earth');

        if (pctFireEl) pctFireEl.innerText = pFire + '%';
        if (pctAirEl) pctAirEl.innerText = pAir + '%';
        if (pctWaterEl) pctWaterEl.innerText = pWater + '%';
        if (pctEarthEl) pctEarthEl.innerText = pEarth + '%';

        const circleFire = document.getElementById('hero-circle-fire');
        const circleAir = document.getElementById('hero-circle-air');
        const circleWater = document.getElementById('hero-circle-water');
        const circleEarth = document.getElementById('hero-circle-earth');

        if (circleFire) circleFire.style.background = `conic-gradient(var(--fire-color) ${pFire}%, var(--surface-subtle) ${pFire}% 100%)`;
        if (circleAir) circleAir.style.background = `conic-gradient(var(--air-color) ${pAir}%, var(--surface-subtle) ${pAir}% 100%)`;
        if (circleWater) circleWater.style.background = `conic-gradient(var(--water-color) ${pWater}%, var(--surface-subtle) ${pWater}% 100%)`;
        if (circleEarth) circleEarth.style.background = `conic-gradient(var(--earth-color) ${pEarth}%, var(--surface-subtle) ${pEarth}% 100%)`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const quickInput = document.getElementById('heroQuickInput');
        if (quickInput) {
            quickInput.addEventListener('input', updateHeroQuickCalc);
            updateHeroQuickCalc();
        }

        // Auth Form handlers
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = document.getElementById('loginUsername').value.trim();
                const password = document.getElementById('loginPassword').value;
                const alertDiv = document.getElementById('loginAlert');
                const submitBtn = loginForm.querySelector('button[type="submit"]');

                if (submitBtn) submitBtn.disabled = true;
                alertDiv.style.display = 'none';

                try {
                    const res = await fetch('api.php?action=login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ username, password })
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch (err) {
                        throw new Error('Server returned an unexpected response (Status ' + res.status + ')');
                    }

                    if (res.ok && data.success) {
                        if (data.user && data.user.role === 'admin') {
                            window.location.href = 'admin.php';
                        } else if (data.user && data.user.role === 'staff') {
                            window.location.href = 'saved.php';
                        } else {
                            window.location.href = 'calculator.php';
                        }
                    } else {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerText = data.error || 'Invalid username or password';
                        alertDiv.style.display = 'block';
                    }
                } catch (err) {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = err.message || 'Network error during login';
                    alertDiv.style.display = 'block';
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        }

        // Hero Request Info Button Handler
        const btnHeroReq = document.getElementById('btnHeroRequestInfo');
        if (btnHeroReq) {
            btnHeroReq.addEventListener('click', () => {
                const nameVal = document.getElementById('heroQuickInput') ? document.getElementById('heroQuickInput').value.trim() : '';
                <?php if ($currentUser): ?>
                    window.location.href = 'profile.php' + (nameVal ? '?name=' + encodeURIComponent(nameVal) : '');
                <?php else: ?>
                    window.location.href = 'index.php?auth=signup&request=1' + (nameVal ? '&name=' + encodeURIComponent(nameVal) : '');
                <?php endif; ?>
            });
        }

        // Virtual Urdu Keyboard for Signup Request Target Name
        const btnToggleKb = document.getElementById('btnToggleUrduKb');
        const kbContainer = document.getElementById('signupKbContainer');
        const targetInput = document.getElementById('signupReqName');
        const kbGrid = document.getElementById('signupKbGrid');

        if (btnToggleKb && kbContainer && kbGrid && targetInput) {
            const kbLetters = [
                'ا', 'آ', 'ب', 'پ', 'ت', 'ٹ', 'ث', 'ج', 'چ', 'ح', 'خ',
                'د', 'ڈ', 'ذ', 'ر', 'ڑ', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض',
                'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن',
                'ں', 'و', 'ہ', 'ھ', 'ء', 'ی', 'ے'
            ];

            kbLetters.forEach(ch => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-secondary';
                btn.style.cssText = 'padding: 0.35rem 0.2rem; font-family: var(--font-arabic); font-size: 1.15rem; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 0;';
                btn.innerText = ch;
                btn.onclick = () => {
                    const start = targetInput.selectionStart ?? targetInput.value.length;
                    const end = targetInput.selectionEnd ?? targetInput.value.length;
                    const val = targetInput.value;
                    targetInput.value = val.substring(0, start) + ch + val.substring(end);
                    targetInput.selectionStart = targetInput.selectionEnd = start + ch.length;
                    targetInput.focus();
                };
                kbGrid.appendChild(btn);
            });

            btnToggleKb.onclick = () => {
                kbContainer.style.display = (kbContainer.style.display === 'none' || !kbContainer.style.display) ? 'block' : 'none';
            };

            const kbSpace = document.getElementById('signupKbSpace');
            if (kbSpace) {
                kbSpace.onclick = () => {
                    const start = targetInput.selectionStart ?? targetInput.value.length;
                    const end = targetInput.selectionEnd ?? targetInput.value.length;
                    const val = targetInput.value;
                    targetInput.value = val.substring(0, start) + ' ' + val.substring(end);
                    targetInput.selectionStart = targetInput.selectionEnd = start + 1;
                    targetInput.focus();
                };
            }

            const kbBack = document.getElementById('signupKbBackspace');
            if (kbBack) {
                kbBack.onclick = () => {
                    const start = targetInput.selectionStart;
                    const end = targetInput.selectionEnd;
                    const val = targetInput.value;
                    if (start === end && start > 0) {
                        targetInput.value = val.substring(0, start - 1) + val.substring(end);
                        targetInput.selectionStart = targetInput.selectionEnd = start - 1;
                    } else if (start !== end) {
                        targetInput.value = val.substring(0, start) + val.substring(end);
                        targetInput.selectionStart = targetInput.selectionEnd = start;
                    }
                    targetInput.focus();
                };
            }
        }

        // Signup form handler with Consultation Request submission
        const signupForm = document.getElementById('signupForm');
        if (signupForm) {
            signupForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = document.getElementById('signupUsername').value.trim();
                const email = document.getElementById('signupEmail').value.trim();
                const password = document.getElementById('signupPassword').value;
                const fullName = document.getElementById('signupFullName') ? document.getElementById('signupFullName').value.trim() : '';
                const contact = document.getElementById('signupContact') ? document.getElementById('signupContact').value.trim() : '';
                const reqNameLookup = document.getElementById('signupReqName') ? document.getElementById('signupReqName').value.trim() : '';
                const reqRelationship = document.getElementById('signupRelationship') ? document.getElementById('signupRelationship').value.trim() : '';
                const reqQuestion = document.getElementById('signupQuestion') ? document.getElementById('signupQuestion').value.trim() : '';
                const alertDiv = document.getElementById('signupAlert');
                const submitBtn = signupForm.querySelector('button[type="submit"]');

                if (submitBtn) submitBtn.disabled = true;
                alertDiv.style.display = 'none';

                try {
                    const res = await fetch('api.php?action=signup', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            username,
                            email,
                            password,
                            full_name: fullName,
                            contact,
                            req_name_lookup: reqNameLookup,
                            req_relationship: reqRelationship,
                            req_question: reqQuestion,
                            request_info: true
                        })
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch (err) {
                        throw new Error('Server returned an unexpected response (Status ' + res.status + ')');
                    }

                    if (res.ok && data.success) {
                        alertDiv.className = 'alert alert-success';
                        alertDiv.innerText = '✓ ' + (data.message || 'Account registered and consultation request submitted!');
                        alertDiv.style.display = 'block';
                        signupForm.reset();
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 500);
                        }
                    } else {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerText = data.error || 'Signup failed';
                        alertDiv.style.display = 'block';
                    }
                } catch (err) {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerText = err.message || 'Network error during signup';
                    alertDiv.style.display = 'block';
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
$page_title = "Payment Gateway Settings (Razorpay)";
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// Handle POST Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = [
        'razorpay_enabled',
        'razorpay_mode',
        'razorpay_test_key_id',
        'razorpay_test_key_secret',
        'razorpay_live_key_id',
        'razorpay_live_key_secret',
        'seat_booking_fee',
        'razorpay_company_name'
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        foreach ($keys as $k) {
            $val = trim($_POST[$k] ?? '');
            // default fallback for razorpay_enabled if unchecked
            if ($k === 'razorpay_enabled' && !isset($_POST['razorpay_enabled'])) {
                $val = '0';
            }
            $stmt->execute([$k, $val, $val]);
        }
        $msg = "Razorpay payment settings saved successfully!";
    } catch (Exception $e) {
        $err = "Failed to save settings: " . $e->getMessage();
    }
}

// Fetch current settings
$settings = [];
$s_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'razorpay_%' OR setting_key = 'seat_booking_fee'");
while ($row = $s_stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$enabled          = ($settings['razorpay_enabled'] ?? '1') === '1';
$mode             = $settings['razorpay_mode'] ?? 'test';
$test_key_id      = $settings['razorpay_test_key_id'] ?? 'rzp_test_1DP5mmOlF5G5ag';
$test_key_secret  = $settings['razorpay_test_key_secret'] ?? '';
$live_key_id      = $settings['razorpay_live_key_id'] ?? '';
$live_key_secret  = $settings['razorpay_live_key_secret'] ?? '';
$fee_amount       = $settings['seat_booking_fee'] ?? '999';
$company_name     = $settings['razorpay_company_name'] ?? 'Finchskills Institute';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Razorpay Payment Gateway Setup</h4>
        <p class="text-muted small mb-0">Configure online registration fee, test & live API keys, and payment checkout branding</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge <?= $mode === 'live' ? 'bg-success' : 'bg-warning text-dark' ?> px-3 py-2 fs-6">
            <i class="fa-solid <?= $mode === 'live' ? 'fa-bolt' : 'fa-vial' ?> me-1"></i>
            Active Mode: <?= strtoupper($mode) ?>
        </span>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fa-solid fa-circle-check fs-4 me-2"></i>
        <div><?= htmlspecialchars($msg) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($err)): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fa-solid fa-circle-exclamation fs-4 me-2"></i>
        <div><?= htmlspecialchars($err) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" action="payment-settings.php">
    <div class="row g-4">
        <!-- Main Configuration Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> General Gateway Settings
                    </h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="razorpay_enabled" name="razorpay_enabled" value="1" <?= $enabled ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold small" for="razorpay_enabled">Enable Gateway</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Mode Selector -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Environment Mode <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check p-3 border rounded-3 flex-fill <?= $mode === 'test' ? 'border-primary bg-primary-subtle' : 'bg-light' ?>">
                                    <input class="form-check-input" type="radio" name="razorpay_mode" id="modeTest" value="test" <?= $mode === 'test' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold d-block cursor-pointer" for="modeTest">
                                        <i class="fa-solid fa-vial text-warning me-1"></i> Test Sandbox
                                        <div class="small fw-normal text-muted mt-1">Test payments without actual money deduction</div>
                                    </label>
                                </div>
                                <div class="form-check p-3 border rounded-3 flex-fill <?= $mode === 'live' ? 'border-success bg-success-subtle' : 'bg-light' ?>">
                                    <input class="form-check-input" type="radio" name="razorpay_mode" id="modeLive" value="live" <?= $mode === 'live' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold d-block cursor-pointer" for="modeLive">
                                        <i class="fa-solid fa-bolt text-success me-1"></i> Live Production
                                        <div class="small fw-normal text-muted mt-1">Accept real payments from candidates</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Seat Booking Fee -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="seat_booking_fee">Seat Reservation Fee (₹ INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                <input type="number" step="1" min="0" class="form-control form-control-lg fw-bold text-dark" id="seat_booking_fee" name="seat_booking_fee" value="<?= htmlspecialchars($fee_amount) ?>" required>
                            </div>
                            <small class="text-muted">Charged to candidates upon registration for provisional seat confirmation.</small>
                        </div>

                        <!-- Brand Name on Checkout -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold" for="razorpay_company_name">Company / Institute Name (Displayed on Razorpay Modal)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-building text-muted"></i></span>
                                <input type="text" class="form-control" id="razorpay_company_name" name="razorpay_company_name" value="<?= htmlspecialchars($company_name) ?>" placeholder="Finchskills Institute">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Keys Settings (Test & Live) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-key text-primary me-2"></i> Razorpay API Credentials
                    </h6>
                </div>
                <div class="card-body p-4">
                    <!-- TEST KEYS -->
                    <div class="p-3 mb-4 rounded-3 border bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="fw-bold text-dark"><i class="fa-solid fa-vial text-warning me-2"></i> Test Mode Keys</div>
                            <span class="badge bg-secondary-subtle text-secondary">Sandbox Only</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Test Key ID</label>
                                <input type="text" class="form-control font-monospace" name="razorpay_test_key_id" value="<?= htmlspecialchars($test_key_id) ?>" placeholder="rzp_test_...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Test Key Secret</label>
                                <input type="password" class="form-control font-monospace" name="razorpay_test_key_secret" value="<?= htmlspecialchars($test_key_secret) ?>" placeholder="Optional for standard frontend popup">
                            </div>
                        </div>
                    </div>

                    <!-- LIVE KEYS -->
                    <div class="p-3 rounded-3 border" style="background: #fcfdfd;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="fw-bold text-dark"><i class="fa-solid fa-bolt text-success me-2"></i> Live Production Keys</div>
                            <span class="badge bg-success-subtle text-success">Real Transactions</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Live Key ID</label>
                                <input type="text" class="form-control font-monospace" name="razorpay_live_key_id" value="<?= htmlspecialchars($live_key_id) ?>" placeholder="rzp_live_...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Live Key Secret</label>
                                <input type="password" class="form-control font-monospace" name="razorpay_live_key_secret" value="<?= htmlspecialchars($live_key_secret) ?>" placeholder="Live secret key from Razorpay dashboard">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-top text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Gateway Configuration
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Info & Instructions -->
        <div class="col-lg-4">
            <!-- How to get keys card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-circle-info text-info me-2"></i> How to Get Keys
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                        <li>Log in to your <a href="https://dashboard.razorpay.com" target="_blank" class="fw-semibold text-primary">Razorpay Dashboard</a>.</li>
                        <li>Switch between <strong>Test</strong> or <strong>Live</strong> mode from the top-left toggle.</li>
                        <li>Navigate to <strong>Account & Settings</strong> &rarr; <strong>API Keys</strong>.</li>
                        <li>Click <strong>Generate Key</strong> to view your <em>Key ID</em> and <em>Key Secret</em>.</li>
                        <li>Copy and paste them into the respective fields on the left.</li>
                    </ol>
                </div>
            </div>

            <!-- Current Active Status Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-circle-check text-success me-2"></i> Current Status
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Gateway Status:</span>
                        <span class="fw-bold <?= $enabled ? 'text-success' : 'text-danger' ?>"><?= $enabled ? 'Active' : 'Disabled' ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Environment:</span>
                        <span class="fw-bold text-dark text-capitalize"><?= htmlspecialchars($mode) ?> Mode</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Seat Fee Amount:</span>
                        <span class="fw-bold text-primary">₹<?= number_format((float)$fee_amount, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted">Active Key ID:</span>
                        <span class="fw-semibold font-monospace text-truncate ms-2" style="max-width: 140px;">
                            <?= htmlspecialchars(($mode === 'live') ? ($live_key_id ?: 'Not Set') : ($test_key_id ?: 'Not Set')) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
$page_title = "Email & SMTP Settings (PHPMailer)";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../mail-helper.php';

$msg = '';
$err = '';
$test_result = null;

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Save Settings
    if (isset($_POST['action_save_smtp'])) {
        $keys = [
            'smtp_enabled',
            'smtp_host',
            'smtp_port',
            'smtp_secure',
            'smtp_username',
            'smtp_password',
            'smtp_from_name',
            'smtp_from_email',
            'smtp_reply_to'
        ];

        try {
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            foreach ($keys as $k) {
                $val = trim($_POST[$k] ?? '');
                if ($k === 'smtp_enabled' && !isset($_POST['smtp_enabled'])) {
                    $val = '0';
                }
                $stmt->execute([$k, $val, $val]);
            }
            $msg = "SMTP & Email configuration saved successfully!";
        } catch (Exception $e) {
            $err = "Failed to save settings: " . $e->getMessage();
        }
    }

    // 2. Test Connection & Send Live Test Email
    if (isset($_POST['action_test_smtp'])) {
        $test_email = trim($_POST['test_email'] ?? '');
        if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
            $err = "Please enter a valid email address to send the test message.";
        } else {
            $test_subject = "SMTP Test Verification - Finchskills Institute";
            $test_html = '
            <!DOCTYPE html>
            <html>
            <head><meta charset="utf-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f8fafc;padding:20px;color:#334155;}.card{max-width:500px;margin:0 auto;background:#fff;border-radius:10px;border:1px solid #e2e8f0;padding:25px;}.badge{background:#22c55e;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;}</style></head>
            <body>
                <div class="card">
                    <h3 style="color:#0e1e2e;margin-top:0;">Finchskills SMTP Configuration Test</h3>
                    <p>This is a live test email sent from the Finchskills Institute Admin Portal.</p>
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;padding:15px;border-radius:8px;margin:15px 0;">
                        <span class="badge">SUCCESS</span>
                        <p style="margin:8px 0 0;font-size:14px;color:#166534;">Your SMTP host, credentials, and port are 100% properly configured!</p>
                    </div>
                    <p style="font-size:12px;color:#64748b;">Timestamp: ' . date('d M Y, h:i:s A') . '</p>
                </div>
            </body>
            </html>';

            $res = sendFinchMail($test_email, $test_subject, $test_html, ['throw_error' => true]);
            if ($res['success']) {
                $test_result = [
                    'status'  => 'success',
                    'message' => "Success! Test email was successfully delivered to <strong>" . htmlspecialchars($test_email) . "</strong>."
                ];
            } else {
                $test_result = [
                    'status'  => 'danger',
                    'message' => "SMTP Error: " . htmlspecialchars($res['message'])
                ];
            }
        }
    }
}

// Fetch current SMTP Settings
$cfg = getSmtpConfig();
$enabled    = ($cfg['smtp_enabled'] ?? '1') === '1';
$host       = $cfg['smtp_host'] ?? 'smtp.gmail.com';
$port       = $cfg['smtp_port'] ?? '587';
$secure     = strtolower($cfg['smtp_secure'] ?? 'tls');
$username   = $cfg['smtp_username'] ?? '';
$password   = $cfg['smtp_password'] ?? '';
$from_name  = $cfg['smtp_from_name'] ?? 'Finchskills Institute';
$from_email = $cfg['smtp_from_email'] ?? '';
$reply_to   = $cfg['smtp_reply_to'] ?? '';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Email & SMTP Setup (PHPMailer)</h4>
        <p class="text-muted small mb-0">Manage Webmail / Gmail credentials used for sending candidate OTPs, Student IDs, and Payment receipts</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge <?= $enabled ? 'bg-success' : 'bg-secondary' ?> px-3 py-2 fs-6">
            <i class="fa-solid <?= $enabled ? 'fa-circle-check' : 'fa-circle-pause' ?> me-1"></i>
            Mailer: <?= $enabled ? 'Active (PHPMailer)' : 'Disabled (Fallback)' ?>
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

<?php if ($test_result): ?>
    <div class="alert alert-<?= $test_result['status'] ?> alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fa-solid <?= $test_result['status'] === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> fs-4 me-2"></i>
        <div><?= $test_result['message'] ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Main Configuration Form -->
    <div class="col-lg-8">
        <form method="POST" action="smtp-settings.php">
            <input type="hidden" name="action_save_smtp" value="1">
            
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-server text-primary me-2"></i> SMTP Server Settings
                    </h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="smtp_enabled" name="smtp_enabled" value="1" <?= $enabled ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold small" for="smtp_enabled">Enable SMTP Sending</label>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Quick Presets -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <span class="small fw-bold text-secondary text-uppercase d-block mb-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Quick Presets (Auto-Fill Server & Port):
                        </span>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" onclick="applyPreset('gmail')">
                                <i class="fa-brands fa-google text-danger me-1"></i> Gmail (smtp.gmail.com)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" onclick="applyPreset('hostinger')">
                                <i class="fa-solid fa-cloud text-primary me-1"></i> Hostinger Webmail
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white" onclick="applyPreset('cpanel')">
                                <i class="fa-solid fa-globe text-success me-1"></i> cPanel / Custom Webmail
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- SMTP Host -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="smtp_host">SMTP Host / Server <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($host) ?>" placeholder="e.g. mail.finchskills.com or smtp.gmail.com" required>
                            <small class="text-muted">Webmail address or SMTP server domain</small>
                        </div>

                        <!-- Port -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="smtp_port">Port <span class="text-danger">*</span></label>
                            <input type="number" class="form-control font-monospace" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($port) ?>" placeholder="587 / 465" required>
                            <small class="text-muted">Common: 587 (TLS) or 465 (SSL)</small>
                        </div>

                        <!-- Encryption -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="smtp_secure">Security <span class="text-danger">*</span></label>
                            <select class="form-select font-monospace" id="smtp_secure" name="smtp_secure">
                                <option value="tls" <?= $secure === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                                <option value="ssl" <?= $secure === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                                <option value="none" <?= $secure === 'none' ? 'selected' : '' ?>>None (Port 25/587)</option>
                            </select>
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="smtp_username">SMTP Username / Webmail Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-regular fa-envelope text-muted"></i></span>
                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?= htmlspecialchars($username) ?>" placeholder="e.g. admission@finchskills.com" required>
                            </div>
                            <small class="text-muted">Full email address used for authenticating with the mail server</small>
                        </div>

                        <!-- Password with Eye Toggle -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="smtp_password">SMTP Password / App Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-key text-muted"></i></span>
                                <input type="password" class="form-control font-monospace" id="smtp_password" name="smtp_password" value="<?= htmlspecialchars($password) ?>" placeholder="Enter email password or app password" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePasswordBtn" onclick="togglePasswordVisibility()">
                                    <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Webmail password, or Google App Password (if using Gmail)</small>
                        </div>

                        <div class="col-12"><hr class="my-2 text-muted"></div>

                        <!-- Sender Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="smtp_from_name">Sender "From" Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user-tag text-muted"></i></span>
                                <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="<?= htmlspecialchars($from_name) ?>" placeholder="Finchskills Institute">
                            </div>
                            <small class="text-muted">Displayed as the sender in candidate's inbox</small>
                        </div>

                        <!-- Sender Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="smtp_from_email">Sender "From" Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-paper-plane text-muted"></i></span>
                                <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" value="<?= htmlspecialchars($from_email) ?>" placeholder="e.g. admission@finchskills.com">
                            </div>
                            <small class="text-muted">Must match or be allowed by your SMTP server domain</small>
                        </div>

                        <!-- Reply-To Email -->
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="smtp_reply_to">Reply-To Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-reply text-muted"></i></span>
                                <input type="email" class="form-control" id="smtp_reply_to" name="smtp_reply_to" value="<?= htmlspecialchars($reply_to) ?>" placeholder="e.g. hello@finchskills.com">
                            </div>
                            <small class="text-muted">Where candidate replies will be sent</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white py-3 border-top text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save SMTP Configuration
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Sidebar: Live Test Tool & Help Guide -->
    <div class="col-lg-4">
        <!-- Live Test Tool -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-paper-plane text-success me-2"></i> Send Live Test Email
                </h6>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-3">
                    Enter an email address below to test the connection and verify that emails are reaching real inboxes.
                </p>
                <form method="POST" action="smtp-settings.php">
                    <input type="hidden" name="action_test_smtp" value="1">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Recipient Email Address</label>
                        <input type="email" name="test_email" class="form-control" placeholder="your-email@gmail.com" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="fa-solid fa-bolt me-1"></i> Test Connection & Send
                    </button>
                </form>
            </div>
        </div>

        <!-- Where these emails are used -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Emails Dispatched Through SMTP
                </h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-key text-warning mt-1"></i>
                        <div><strong>Candidate Registration OTP:</strong> 6-digit verification code.</div>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-id-card text-success mt-1"></i>
                        <div><strong>Welcome & Student ID:</strong> Auto-generated ID (<code>FS-2026-XXXX</code>).</div>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-lock text-danger mt-1"></i>
                        <div><strong>Forgot Password Reset:</strong> OTP for account recovery.</div>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-receipt text-info mt-1"></i>
                        <div><strong>Seat Payment Receipt:</strong> Razorpay fee confirmation.</div>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-code text-secondary mt-1"></i>
                        <div><strong>Reusable API:</strong> Ready for Admission & Contact forms.</div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Setup Tips -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-circle-info text-info me-2"></i> Webmail Setup Guide
                </h6>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-2">
                    <strong>For cPanel / Custom Webmail:</strong>
                </p>
                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.6;">
                    <li>Host: <code>mail.yourdomain.com</code></li>
                    <li>Port: <code>465</code> with <strong>SSL</strong>, or <code>587</code> with <strong>TLS</strong></li>
                    <li>Username: Full email (e.g. <code>info@yourdomain.com</code>)</li>
                    <li>Password: Your webmail password</li>
                </ul>
                <p class="small text-muted mb-2">
                    <strong>For Gmail:</strong>
                </p>
                <p class="small text-muted mb-0" style="line-height: 1.5;">
                    Must use a 16-character <strong>Google App Password</strong> (generated via Google Account &rarr; Security &rarr; 2-Step Verification &rarr; App Passwords).
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function applyPreset(type) {
    const hostInput = document.getElementById('smtp_host');
    const portInput = document.getElementById('smtp_port');
    const secureSelect = document.getElementById('smtp_secure');

    if (type === 'gmail') {
        hostInput.value = 'smtp.gmail.com';
        portInput.value = '587';
        secureSelect.value = 'tls';
    } else if (type === 'hostinger') {
        hostInput.value = 'smtp.hostinger.com';
        portInput.value = '465';
        secureSelect.value = 'ssl';
    } else if (type === 'cpanel') {
        const domain = window.location.hostname;
        hostInput.value = (domain && domain !== 'localhost') ? ('mail.' + domain) : 'mail.finchskills.com';
        portInput.value = '465';
        secureSelect.value = 'ssl';
    }
}

function togglePasswordVisibility() {
    const passField = document.getElementById('smtp_password');
    const icon = document.getElementById('togglePasswordIcon');
    if (passField.type === 'password') {
        passField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

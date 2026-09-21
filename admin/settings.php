<?php
$page_title = "Site & Brand Settings";
require_once __DIR__ . '/includes/header.php';

$msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_keys = [
        'phone_1', 'phone_2', 'email_1', 'email_2', 
        'address', 'timing_mon_fri', 'timing_sat',
        'facebook_url', 'instagram_url', 'linkedin_url', 
        'twitter_url', 'youtube_url', 'whatsapp_number',
        'map_iframe'
    ];

    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    
    foreach ($settings_keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $stmt->execute([$key, $val, $val]);
    }

    // Handle Logo & Favicon Uploads
    $upload_dir = __DIR__ . '/../uploads/site/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Site Main Logo
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
        $file_name = 'logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir . $file_name)) {
            $logo_path = 'uploads/site/' . $file_name;
            $stmt->execute(['site_logo', $logo_path, $logo_path]);
        }
    }

    // Site Footer Logo
    if (isset($_FILES['footer_logo']) && $_FILES['footer_logo']['error'] == 0) {
        $ext = pathinfo($_FILES['footer_logo']['name'], PATHINFO_EXTENSION);
        $file_name = 'footer_logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $upload_dir . $file_name)) {
            $footer_logo_path = 'uploads/site/' . $file_name;
            $stmt->execute(['footer_logo', $footer_logo_path, $footer_logo_path]);
        }
    }

    // Site Favicon
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] == 0) {
        $ext = pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION);
        $file_name = 'favicon_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $upload_dir . $file_name)) {
            $favicon_path = 'uploads/site/' . $file_name;
            $stmt->execute(['site_favicon', $favicon_path, $favicon_path]);
        }
    }

    $msg = 'Settings and logos updated successfully!';
}

// Reload fresh settings
$settings = getSiteSettings($pdo);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Website & Brand Settings</h4>
        <p class="text-muted small mb-0">Manage website logos, contact details, social links, and physical address</p>
    </div>
    <div class="d-flex gap-2">
        <a href="smtp-settings.php" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa-solid fa-envelope-circle-check me-1"></i> Email & SMTP Settings
        </a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4">
        <i class="fa-solid fa-circle-check fs-4 me-2"></i>
        <div><?= htmlspecialchars($msg) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" action="settings.php" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- LOGO & BRANDING CARD -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image text-primary me-2"></i> Website Logos & Favicon</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Main Header Logo -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Header Main Logo</label>
                            <div class="p-3 border rounded-3 bg-light text-center mb-2" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img src="../<?= htmlspecialchars($settings['site_logo'] ?? 'img/logo.png') ?>" alt="Header Logo" style="max-height: 60px; max-width: 100%;">
                            </div>
                            <input type="file" name="site_logo" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: PNG format (transparent)</small>
                        </div>

                        <!-- Footer Logo -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Footer Logo</label>
                            <div class="p-3 border rounded-3 bg-dark text-center mb-2" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img src="../<?= htmlspecialchars($settings['footer_logo'] ?? $settings['site_logo'] ?? 'img/footer-logo.png') ?>" alt="Footer Logo" style="max-height: 60px; max-width: 100%;">
                            </div>
                            <input type="file" name="footer_logo" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: Light/White logo on dark</small>
                        </div>

                        <!-- Favicon -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Browser Favicon</label>
                            <div class="p-3 border rounded-3 bg-light text-center mb-2" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img src="../<?= htmlspecialchars($settings['site_favicon'] ?? 'img/favicon.png') ?>" alt="Favicon" style="max-height: 48px; max-width: 48px;">
                            </div>
                            <input type="file" name="site_favicon" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: 32x32 or 64x64 PNG / ICO</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACT NUMBERS & EMAILS -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-phone text-primary me-2"></i> Contact Numbers & Emails</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Primary Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" name="phone_1" class="form-control" value="<?= htmlspecialchars($settings['phone_1'] ?? '') ?>" placeholder="+91 9650386711">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Secondary / Alternate Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" name="phone_2" class="form-control" value="<?= htmlspecialchars($settings['phone_2'] ?? '') ?>" placeholder="+91 9876543210">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Primary Support Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email_1" class="form-control" value="<?= htmlspecialchars($settings['email_1'] ?? '') ?>" placeholder="hello@finchskills.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Admission / Alternate Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email_2" class="form-control" value="<?= htmlspecialchars($settings['email_2'] ?? '') ?>" placeholder="admission@finchskills.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS & TIMINGS -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-map-location-dot text-danger me-2"></i> Location & Office Timings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Institute Physical Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter full institute address..."><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mon - Fri Timing</label>
                        <input type="text" name="timing_mon_fri" class="form-control" value="<?= htmlspecialchars($settings['timing_mon_fri'] ?? '') ?>" placeholder="Monday - Friday: 10:00 - 05:00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Saturday Timing</label>
                        <input type="text" name="timing_sat" class="form-control" value="<?= htmlspecialchars($settings['timing_sat'] ?? '') ?>" placeholder="Saturday: 10:00 - 02:00">
                    </div>
                </div>
            </div>
        </div>

        <!-- SOCIAL MEDIA LINKS -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-share-nodes text-success me-2"></i> Social Media & WhatsApp</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">WhatsApp Number (with country code)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-whatsapp text-success"></i></span>
                            <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>" placeholder="919650386711">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facebook URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-facebook text-primary"></i></span>
                            <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instagram URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-instagram text-danger"></i></span>
                            <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">LinkedIn URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-linkedin text-info"></i></span>
                            <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Twitter / X URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-x-twitter text-dark"></i></span>
                            <input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">YouTube URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-brands fa-youtube text-danger"></i></span>
                            <input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GOOGLE MAP -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-map text-warning me-2"></i> Google Map Embed URL</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Google Map Embed Link (src attribute)</label>
                        <textarea name="map_iframe" class="form-control" rows="5" placeholder="Paste embed link..."><?= htmlspecialchars($settings['map_iframe'] ?? '') ?></textarea>
                        <small class="text-muted">Paste either the full iframe src link or URL from Google Maps Embed feature.</small>
                    </div>

                    <?php if (!empty($settings['map_iframe'])): ?>
                    <div class="mt-3">
                        <label class="form-label fw-semibold d-block">Map Preview</label>
                        <div class="rounded-3 overflow-hidden border" style="height: 180px;">
                            <iframe src="<?= htmlspecialchars($settings['map_iframe']) ?>" width="100%" height="100%" style="border:0;" loading="lazy"></iframe>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="col-12 text-end mb-4">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save All Settings
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

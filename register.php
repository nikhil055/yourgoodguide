<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';

// If already logged in, redirect to profile
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header("Location: student-profile.php");
    exit;
}

$error = '';
$success = '';
$show_otp_modal = false;
$temp_email = $_SESSION['reg_temp_email'] ?? '';

// Handle Registration POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $aadhaar = preg_replace('/[^0-9]/', '', trim($_POST['aadhaar'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validations
    if (empty($name) || empty($email) || empty($phone) || empty($aadhaar) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (strlen($aadhaar) !== 12) {
        $error = "Aadhaar number must be exactly 12 digits.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Password and confirm password do not match.";
    } else {
        // Check existing verified user
        $chk = $pdo->prepare("SELECT id, is_verified FROM students WHERE email = ?");
        $chk->execute([$email]);
        $existing = $chk->fetch();

        if ($existing && $existing['is_verified'] == 1) {
            $error = "An account with this email already exists. Please <a href='student-login.php' class='alert-link'>log in here</a>.";
        } else {
            // Handle Passport Photo Upload
            $photo_path = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/uploads/students/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed)) {
                    $photo_filename = 'std_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_filename)) {
                        $photo_path = 'uploads/students/' . $photo_filename;
                    }
                } else {
                    $error = "Invalid photo format. Please upload JPG, PNG, or WEBP.";
                }
            }

            if (empty($error)) {
                // Generate 6-digit OTP
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                $otp_expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Generate Unique Student ID: FS-YYYY-XXXX
                do {
                    $generated_id = 'FS-' . date('Y') . '-' . mt_rand(1000, 9999);
                    $id_chk = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
                    $id_chk->execute([$generated_id]);
                } while ($id_chk->fetch());

                if ($existing && $existing['is_verified'] == 0) {
                    // Update unverified record
                    $stmt = $pdo->prepare("UPDATE students SET 
                        student_id = ?, name = ?, phone = ?, aadhaar = ?, photo = IFNULL(?, photo), 
                        password = ?, otp_code = ?, otp_expires_at = ?, is_verified = 0 
                        WHERE id = ?");
                    $stmt->execute([$generated_id, $name, $phone, $aadhaar, $photo_path, $hashed_password, $otp, $otp_expires, $existing['id']]);
                    $student_db_id = $existing['id'];
                } else {
                    // Insert new record
                    $stmt = $pdo->prepare("INSERT INTO students 
                        (student_id, name, email, phone, aadhaar, photo, password, otp_code, otp_expires_at, is_verified) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$generated_id, $name, $email, $phone, $aadhaar, $photo_path, $hashed_password, $otp, $otp_expires]);
                    $student_db_id = $pdo->lastInsertId();
                }

                // Send OTP Email
                sendRegistrationOtp($email, $name, $otp);

                // Store in session for OTP verification
                $_SESSION['reg_temp_email'] = $email;
                $_SESSION['reg_student_id'] = $student_db_id;
                $show_otp_modal = true;
                $temp_email = $email;
            }
        }
    }
}

// Handle OTP Verification POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_verify_otp'])) {
    $entered_otp = trim($_POST['otp'] ?? '');
    $verify_email = trim($_POST['verify_email'] ?? '');

    if (empty($entered_otp) || strlen($entered_otp) !== 6) {
        $error = "Please enter a valid 6-digit OTP.";
        $show_otp_modal = true;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND is_verified = 0 ORDER BY id DESC LIMIT 1");
        $stmt->execute([$verify_email]);
        $student = $stmt->fetch();

        if ($student) {
            if ($student['otp_code'] === $entered_otp && strtotime($student['otp_expires_at']) >= time()) {
                // Verified successfully!
                $upd = $pdo->prepare("UPDATE students SET is_verified = 1, otp_code = NULL WHERE id = ?");
                $upd->execute([$student['id']]);

                // Send Welcome & Credentials Email
                sendWelcomeEmail($student['email'], $student['name'], $student['student_id']);

                // Auto login student
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_db_id'] = $student['id'];
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['student_name'] = $student['name'];
                $_SESSION['student_email'] = $student['email'];
                $_SESSION['student_photo'] = $student['photo'];

                unset($_SESSION['reg_temp_email']);
                unset($_SESSION['reg_student_id']);

                // Redirect to Seat Reservation / Fees step
                header("Location: seat-reservation.php?new=1");
                exit;
            } else {
                $error = "Invalid or expired OTP. Please try again.";
                $show_otp_modal = true;
            }
        } else {
            $error = "Registration record not found or already verified.";
        }
    }
}

// Handle Resend OTP POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_resend_otp'])) {
    $resend_email = trim($_POST['resend_email'] ?? '');
    if (!empty($resend_email)) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND is_verified = 0 LIMIT 1");
        $stmt->execute([$resend_email]);
        $student = $stmt->fetch();
        if ($student) {
            $new_otp = sprintf("%06d", mt_rand(100000, 999999));
            $new_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $upd = $pdo->prepare("UPDATE students SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
            $upd->execute([$new_otp, $new_expiry, $student['id']]);

            sendRegistrationOtp($student['email'], $student['name'], $new_otp);
            $success = "A new OTP has been sent to your email.";
            $show_otp_modal = true;
        }
    }
}

$page_title = "Candidate Registration - Finchskills Institute";
include "header.php";
?>

<style>
.reg-section {
    padding: 50px 0 80px;
    background: #fafbfc;
}
.reg-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 36px 32px;
    box-shadow: none !important;
}
.reg-banner-card {
    background: linear-gradient(145deg, #0e1e2e 0%, #162a3f 100%);
    border-radius: 16px;
    padding: 38px 32px;
    color: #ffffff;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.reg-title {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
}
.reg-label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}
.reg-input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}
.reg-input:focus {
    border-color: #fe7c03;
    box-shadow: 0 0 0 3px rgba(254, 124, 3, 0.1);
}
.password-wrap {
    position: relative;
}
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    font-size: 15px;
}
.password-toggle:hover {
    color: #0f172a;
}
.photo-upload-box {
    display: flex;
    align-items: center;
    gap: 18px;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    padding: 14px 18px;
}
.photo-avatar-preview {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid #fe7c03;
    object-fit: cover;
    background: #ffffff;
    flex-shrink: 0;
}
.reg-submit-btn {
    background: #fe7c03;
    color: #ffffff;
    border: none;
    border-radius: 9px;
    padding: 13px 20px;
    font-size: 15px;
    font-weight: 700;
    width: 100%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.reg-submit-btn:hover {
    background: #e66f00;
    color: #ffffff;
}

/* Benefit Chips */
.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 20px;
}
.benefit-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(254, 124, 3, 0.15);
    color: #fe7c03;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
}

/* OTP Modal */
.otp-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}
.otp-card {
    background: #ffffff;
    border-radius: 16px;
    max-width: 440px;
    width: 100%;
    padding: 32px 28px;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}
.otp-input-field {
    letter-spacing: 12px;
    font-size: 26px;
    font-weight: 800;
    text-align: center;
    color: #0f172a;
    border: 2px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px;
    width: 100%;
    max-width: 280px;
    margin: 16px auto;
    display: block;
}
.otp-input-field:focus {
    border-color: #fe7c03;
    outline: none;
}
</style>

<!-- ================= REGISTRATION SECTION ================= -->
<section class="reg-section">
    <div class="container">

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-secondary"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Candidate Registration</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5 align-items-stretch">

            <!-- ================= LEFT: BRAND INFO & TRUST PILLARS ================= -->
            <div class="col-lg-5">
                <div class="reg-banner-card">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(254, 124, 3, 0.2); color: #fe7c03; font-size: 12px; font-weight: 700;">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>STUDENT ENROLLMENT PORTAL</span>
                        </div>
                        <h2 class="fw-bold text-white mb-3" style="font-size: 28px; line-height: 1.3;">
                            Start Your Career in Aviation, Hospitality &amp; Cruise Lines
                        </h2>
                        <p class="text-white-50 small mb-4" style="line-height: 1.7;">
                            Register as a verified candidate to receive your official Finchskills Student ID, reserve your preferred batch timing, and access practical job-oriented training.
                        </p>

                        <div class="benefit-item">
                            <div class="benefit-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div>
                                <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">Instant Unique Student ID</h6>
                                <p class="text-white-50 small mb-0">Get your recognized registration number immediately upon email verification.</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon"><i class="fa-solid fa-chair"></i></div>
                            <div>
                                <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">Direct Batch Seat Reservation</h6>
                                <p class="text-white-50 small mb-0">Lock your batch seat with our seamless Razorpay gateway or choose to pay later.</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon"><i class="fa-solid fa-user-shield"></i></div>
                            <div>
                                <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">Lifetime Portal Access</h6>
                                <p class="text-white-50 small mb-0">Access interview alerts, course materials, and verified credentials at any time.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-top border-secondary border-opacity-25 mt-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="fa-solid fa-headset text-dark fs-5"></i>
                            </div>
                            <div>
                                <span class="d-block small text-white-50">Need Help Registering?</span>
                                <strong class="text-white small">Call Counselor: <a href="tel:+918750860860" class="text-white text-decoration-none">+91 87508 60860</a></strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================= RIGHT: REGISTRATION FORM ================= -->
            <div class="col-lg-7">
                <div class="reg-card">

                    <div class="mb-4">
                        <h3 class="reg-title mb-1">Candidate Registration</h3>
                        <p class="text-muted small mb-0">Create your candidate profile to book batch seats and track your admissions.</p>
                    </div>

                    <?php if (!empty($error) && !$show_otp_modal): ?>
                        <div class="alert alert-danger py-2.5 px-3 small d-flex align-items-center mb-4" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6"></i>
                            <div><?= $error ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success py-2.5 px-3 small d-flex align-items-center mb-4" role="alert">
                            <i class="fa-solid fa-circle-check me-2 fs-6"></i>
                            <div><?= htmlspecialchars($success) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php" enctype="multipart/form-data">
                        <input type="hidden" name="action_register" value="1">

                        <!-- NAME & MOBILE -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="reg-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control reg-input" placeholder="e.g. Rahul Sharma" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="reg-label">Mobile Number (WhatsApp) <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control reg-input" placeholder="e.g. 9876543210" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                            </div>
                        </div>

                        <!-- EMAIL & AADHAAR -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="reg-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control reg-input" placeholder="e.g. rahul@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <div class="form-text small" style="font-size: 11px;">Verification OTP will be sent to this email.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="reg-label">Aadhaar Number (12 Digits) <span class="text-danger">*</span></label>
                                <input type="text" name="aadhaar" maxlength="12" class="form-control reg-input" placeholder="12-digit Aadhaar number" value="<?= htmlspecialchars($_POST['aadhaar'] ?? '') ?>" required>
                            </div>
                        </div>

                        <!-- PASSWORD & CONFIRM PASSWORD WITH EYE TOGGLE -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="reg-label">Create Password <span class="text-danger">*</span></label>
                                <div class="password-wrap">
                                    <input type="password" name="password" id="regPassword" class="form-control reg-input pe-5" placeholder="Minimum 6 characters" required>
                                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('regPassword', this)" title="Show/Hide Password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="reg-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="password-wrap">
                                    <input type="password" name="confirm_password" id="regConfirmPassword" class="form-control reg-input pe-5" placeholder="Re-enter your password" required>
                                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('regConfirmPassword', this)" title="Show/Hide Password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PASSPORT PHOTO UPLOAD -->
                        <div class="mb-4">
                            <label class="reg-label">Passport Size Photograph <span class="text-danger">*</span></label>
                            <div class="photo-upload-box">
                                <img src="img/avatar-placeholder.png" alt="Avatar Preview" class="photo-avatar-preview" id="avatarPreview" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23cbd5e1\'%3E%3Cpath d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/%3E%3C/svg%3E';">
                                <div class="flex-grow-1">
                                    <input type="file" name="photo" id="photoInput" class="form-control form-control-sm" accept="image/*" required>
                                    <div class="form-text small mt-1" style="font-size: 11px;">
                                        Upload a clear front-facing passport style photo (JPG/PNG/WEBP, Max 2MB).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TERMS CHECKBOX -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" required checked>
                            <label class="form-check-label small text-secondary" for="agreeTerms">
                                I confirm that all details provided are accurate and agree to Finchskills <a href="terms-conditions.php" class="text-decoration-none" style="color: #fe7c03;">Terms &amp; Conditions</a>.
                            </label>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit" class="reg-submit-btn">
                            <span>Continue to Email Verification</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <div class="text-center mt-3 small text-muted">
                            Already have an account? <a href="student-login.php" class="fw-bold text-decoration-none" style="color: #fe7c03;">Log In Here &rarr;</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= OTP VERIFICATION MODAL ================= -->
<?php if ($show_otp_modal): ?>
<div class="otp-modal-overlay">
    <div class="otp-card">
        
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: #fff7ed; color: #fe7c03;">
            <i class="fa-solid fa-envelope-circle-check fs-3"></i>
        </div>

        <h4 class="fw-bold text-dark mb-1">Verify Your Email</h4>
        <p class="small text-muted mb-2">We sent a 6-digit verification code to:</p>
        <div class="badge bg-light text-dark border px-3 py-1.5 rounded-pill mb-3 font-monospace">
            <?= htmlspecialchars($temp_email) ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 small mb-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['debug_latest_otp'])): ?>
            <!-- Helpful local dev helper note -->
            <div class="alert alert-warning py-1.5 px-2 small mb-3 text-start" style="font-size: 11px;">
                <i class="fa-solid fa-code me-1"></i> <strong>Local Testing OTP:</strong> <?= htmlspecialchars($_SESSION['debug_latest_otp']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="hidden" name="action_verify_otp" value="1">
            <input type="hidden" name="verify_email" value="<?= htmlspecialchars($temp_email) ?>">

            <input type="text" name="otp" maxlength="6" class="otp-input-field" placeholder="------" autofocus required>

            <button type="submit" class="reg-submit-btn mb-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Verify &amp; Create Account</span>
            </button>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top small">
            <form method="POST" action="register.php" class="d-inline">
                <input type="hidden" name="action_resend_otp" value="1">
                <input type="hidden" name="resend_email" value="<?= htmlspecialchars($temp_email) ?>">
                <button type="submit" class="border-0 bg-transparent text-primary p-0 fw-semibold">
                    <i class="fa-solid fa-rotate-right me-1"></i> Resend OTP
                </button>
            </form>
            <a href="register.php" class="text-secondary text-decoration-none">Edit Details</a>
        </div>

    </div>
</div>
<?php endif; ?>

<script>
// Eye Icon Password Toggle
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Passport Photo Instant Live Preview
const photoInput = document.getElementById('photoInput');
const avatarPreview = document.getElementById('avatarPreview');

if (photoInput) {
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
}
</script>

<?php include "footer.php"; ?>

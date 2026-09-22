<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

// Check if candidate is logged in
$isStudentLoggedIn = isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true;
$student = null;

if ($isStudentLoggedIn && !empty($_SESSION['student_db_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_SESSION['student_db_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all active courses for the dropdown
$courses_stmt = $pdo->query("SELECT id, title, slug, duration, study_mode FROM courses WHERE status = 'active' ORDER BY title ASC");
$all_courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Preselected course from query string (?course=...)
$preselected_course = trim($_GET['course'] ?? '');

$page_title = "Online Course Admission Application - Finchskills Institute";
include "header.php";
?>

<style>
/* ===================================================
   MULTI-STEP APPLICATION WIZARD STYLING (FLAT & CLEAN)
=================================================== */
.wizard-section {
    padding: 50px 0 90px;
    background: #f8fafc;
    min-height: 85vh;
}

.wizard-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: none !important;
    overflow: hidden;
}

/* WIZARD PROGRESS BAR */
.wizard-progress-header {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 24px 20px 20px;
}

.wizard-stepper {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 760px;
    margin: 0 auto;
}

.wizard-stepper::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}

.wizard-stepper-progress {
    position: absolute;
    top: 20px;
    left: 40px;
    height: 3px;
    background: #fe7c03;
    z-index: 2;
    transition: width 0.3s ease;
    width: 0%;
}

.step-item {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    background: transparent;
    border: none;
    padding: 0;
    width: 100px;
}

.step-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ffffff;
    border: 2.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    color: #64748b;
    transition: all 0.25s ease;
}

.step-item.active .step-circle {
    border-color: #fe7c03;
    background: #fe7c03;
    color: #ffffff;
    transform: scale(1.08);
}

.step-item.completed .step-circle {
    border-color: #10b981;
    background: #10b981;
    color: #ffffff;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-top: 8px;
    text-align: center;
    transition: color 0.2s ease;
}

.step-item.active .step-label {
    color: #fe7c03;
    font-weight: 700;
}

.step-item.completed .step-label {
    color: #0f172a;
}

/* WIZARD CONTENT */
.wizard-body {
    padding: 36px 32px 40px;
}

@media (max-width: 767px) {
    .wizard-body {
        padding: 24px 18px;
    }
    .wizard-stepper {
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .step-item {
        width: 70px;
    }
    .step-circle {
        width: 34px;
        height: 34px;
        font-size: 12px;
    }
    .step-label {
        font-size: 10.5px;
    }
}

.wizard-step-pane {
    display: none;
}

.wizard-step-pane.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-heading {
    font-size: 20px;
    font-weight: 800;
    color: #0e1e2e;
    letter-spacing: -0.3px;
    margin-bottom: 6px;
}

.step-subheading {
    font-size: 13.5px;
    color: #64748b;
    margin-bottom: 24px;
}

/* FORM FIELDS */
.form-label-custom {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.form-label-custom .req {
    color: #ef4444;
}

.form-control-custom,
.form-select-custom {
    width: 100%;
    padding: 11px 14px;
    font-size: 14px;
    color: #0f172a;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    transition: all 0.2s ease;
    box-shadow: none !important;
}

.form-control-custom:focus,
.form-select-custom:focus {
    border-color: #fe7c03;
    background: #ffffff;
}

.form-control-custom[readonly] {
    background: #f1f5f9;
    color: #475569;
    cursor: not-allowed;
}

/* UPLOAD BOXES */
.file-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.file-dropzone:hover {
    border-color: #fe7c03;
    background: #fff8f3;
}

.file-dropzone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}

.file-dropzone-icon {
    font-size: 28px;
    color: #94a3b8;
    margin-bottom: 8px;
    transition: color 0.2s ease;
}

.file-dropzone:hover .file-dropzone-icon {
    color: #fe7c03;
}

.file-chosen-name {
    font-size: 12px;
    font-weight: 600;
    color: #059669;
    margin-top: 6px;
    display: none;
}

/* WIZARD ACTION BUTTONS */
.wizard-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 36px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.btn-wizard-prev {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #475569;
    font-weight: 600;
    font-size: 13.5px;
    padding: 10px 22px;
    border-radius: 8px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.btn-wizard-prev:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

.btn-wizard-next,
.btn-wizard-submit {
    background: linear-gradient(135deg, #fe7c03 0%, #ea6c00 100%);
    border: 1.5px solid transparent;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    padding: 11px 26px;
    border-radius: 8px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(254, 124, 3, 0.2);
}

.btn-wizard-next:hover,
.btn-wizard-submit:hover {
    background: #ea6c00;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(254, 124, 3, 0.28);
}

/* SUMMARY / REVIEW TILES */
.review-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 16px;
}

.review-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0e1e2e;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.review-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 13px;
    border-bottom: 1px dashed #e2e8f0;
}

.review-row:last-child {
    border-bottom: none;
}

.review-label {
    color: #64748b;
}

.review-val {
    color: #0f172a;
    font-weight: 600;
    text-align: right;
}
</style>

<!-- SECTION WRAPPER -->
<section class="wizard-section">
    <div class="container">
        
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">

                <!-- TOP GUEST OR CANDIDATE BADGE -->
                <?php if ($isStudentLoggedIn && $student): ?>
                    <div class="alert alert-light border border-slate-200 rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                                <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Photo" class="rounded-circle border border-warning" style="width: 46px; height: 46px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 16px;">
                                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                    <span><?= htmlspecialchars($student['name']) ?></span>
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;">
                                        <?= htmlspecialchars($student['student_id']) ?>
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i> Logged In - Your personal info and passport photo have been auto-filled!
                                </div>
                            </div>
                        </div>
                        <a href="student-profile.php" class="btn btn-sm btn-outline-secondary">My Profile</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border border-slate-200 rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="fa-solid fa-circle-info text-primary fs-5"></i>
                            <span>Already registered with us? Log in to auto-fill your profile details instantly.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="student-login.php?redirect=form-submission.php<?= !empty($preselected_course) ? '?course=' . urlencode($preselected_course) : '' ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Log In
                            </a>
                            <a href="register.php" class="btn btn-sm btn-primary" style="background:#fe7c03; border-color:#fe7c03;">
                                <i class="fa-solid fa-user-plus me-1"></i> Register Yourself
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- WIZARD CONTAINER -->
                <div class="wizard-card">
                    
                    <!-- STEPPER HEADER -->
                    <div class="wizard-progress-header">
                        <div class="wizard-stepper">
                            <div class="wizard-stepper-progress" id="stepperProgressBar"></div>

                            <!-- STEP 1 -->
                            <div class="step-item active" id="stepIndicator1" onclick="jumpToStep(1)">
                                <div class="step-circle"><i class="fa-solid fa-user"></i></div>
                                <span class="step-label">1. Candidate</span>
                            </div>

                            <!-- STEP 2 -->
                            <div class="step-item" id="stepIndicator2" onclick="jumpToStep(2)">
                                <div class="step-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                                <span class="step-label">2. Course</span>
                            </div>

                            <!-- STEP 3 -->
                            <div class="step-item" id="stepIndicator3" onclick="jumpToStep(3)">
                                <div class="step-circle"><i class="fa-solid fa-map-location-dot"></i></div>
                                <span class="step-label">3. Address</span>
                            </div>

                            <!-- STEP 4 -->
                            <div class="step-item" id="stepIndicator4" onclick="jumpToStep(4)">
                                <div class="step-circle"><i class="fa-solid fa-file-arrow-up"></i></div>
                                <span class="step-label">4. Documents</span>
                            </div>

                            <!-- STEP 5 -->
                            <div class="step-item" id="stepIndicator5" onclick="jumpToStep(5)">
                                <div class="step-circle"><i class="fa-solid fa-clipboard-check"></i></div>
                                <span class="step-label">5. Review</span>
                            </div>
                        </div>
                    </div>

                    <!-- WIZARD BODY -->
                    <div class="wizard-body">
                        
                        <form id="admissionForm" action="send-admission.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- Hidden Student Reference -->
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id'] ?? '') ?>">
                            <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($student['photo'] ?? '') ?>">

                            <!-- ============================================ -->
                            <!-- STEP 1: CANDIDATE PERSONAL DETAILS (AUTO-FILLED) -->
                            <!-- ============================================ -->
                            <div class="wizard-step-pane active" id="stepPane1">
                                <h3 class="step-heading">Step 1: Personal & Identity Information</h3>
                                <p class="step-subheading">Please review your primary candidate information. Fields marked with <span class="text-danger">*</span> are mandatory.</p>

                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Full Name <span class="req">*</span></span>
                                            <?php if ($isStudentLoggedIn): ?><span class="badge bg-success-subtle text-success py-0" style="font-size:10px;">Autofilled</span><?php endif; ?>
                                        </label>
                                        <input type="text" name="name" id="f_name" class="form-control-custom" value="<?= htmlspecialchars($student['name'] ?? '') ?>" placeholder="Your complete official name" required>
                                    </div>

                                    <!-- Father's Name -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Father's / Guardian's Name <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="father_name" id="f_father_name" class="form-control-custom" placeholder="Father's or Guardian's full name" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Email Address <span class="req">*</span></span>
                                            <?php if ($isStudentLoggedIn): ?><span class="badge bg-success-subtle text-success py-0" style="font-size:10px;">Verified</span><?php endif; ?>
                                        </label>
                                        <input type="email" name="email" id="f_email" class="form-control-custom" value="<?= htmlspecialchars($student['email'] ?? '') ?>" placeholder="name@example.com" required <?= $isStudentLoggedIn ? 'readonly' : '' ?>>
                                    </div>

                                    <!-- Mobile -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>WhatsApp / Mobile Number <span class="req">*</span></span>
                                            <?php if ($isStudentLoggedIn): ?><span class="badge bg-success-subtle text-success py-0" style="font-size:10px;">Autofilled</span><?php endif; ?>
                                        </label>
                                        <input type="tel" name="mobile" id="f_mobile" class="form-control-custom" value="<?= htmlspecialchars($student['phone'] ?? '') ?>" placeholder="+91 98765 43210" required>
                                    </div>

                                    <!-- Aadhaar Number -->
                                    <div class="col-md-4">
                                        <label class="form-label-custom">
                                            <span>12-Digit Aadhaar Number <span class="req">*</span></span>
                                            <?php if ($isStudentLoggedIn): ?><span class="badge bg-success-subtle text-success py-0" style="font-size:10px;">Autofilled</span><?php endif; ?>
                                        </label>
                                        <input type="text" name="aadhaar" id="f_aadhaar" class="form-control-custom font-monospace" value="<?= htmlspecialchars($student['aadhaar'] ?? '') ?>" placeholder="XXXX XXXX XXXX" maxlength="14" required>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="col-md-4">
                                        <label class="form-label-custom">
                                            <span>Date of Birth <span class="req">*</span></span>
                                        </label>
                                        <input type="date" name="dob" id="f_dob" class="form-control-custom" required>
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-4">
                                        <label class="form-label-custom">
                                            <span>Gender <span class="req">*</span></span>
                                        </label>
                                        <select name="gender" id="f_gender" class="form-select-custom" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- STEP 1 BUTTONS -->
                                <div class="wizard-footer justify-content-end">
                                    <button type="button" class="btn-wizard-next" onclick="goToStep(2)">
                                        <span>Next: Select Course &amp; Academics</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- ============================================ -->
                            <!-- STEP 2: COURSE & ACADEMIC BACKGROUND -->
                            <!-- ============================================ -->
                            <div class="wizard-step-pane" id="stepPane2">
                                <h3 class="step-heading">Step 2: Program Selection &amp; Academics</h3>
                                <p class="step-subheading">Choose your preferred training course and provide your academic qualifications.</p>

                                <div class="row g-3">
                                    <!-- Course Selection -->
                                    <div class="col-md-12">
                                        <label class="form-label-custom">
                                            <span>Desired Training Program / Course <span class="req">*</span></span>
                                        </label>
                                        <select name="course" id="f_course" class="form-select-custom" required onchange="updateCoursePreview()">
                                            <option value="">-- Choose Course Program --</option>
                                            <?php foreach ($all_courses as $ac): ?>
                                                <?php
                                                $is_selected = (!empty($preselected_course) && (
                                                    strtolower($ac['title']) === strtolower($preselected_course) || 
                                                    strtolower($ac['slug']) === strtolower($preselected_course)
                                                ));
                                                ?>
                                                <option value="<?= htmlspecialchars($ac['title']) ?>" 
                                                        data-duration="<?= htmlspecialchars($ac['duration']) ?>"
                                                        data-mode="<?= htmlspecialchars($ac['study_mode']) ?>"
                                                        <?= $is_selected ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($ac['title']) ?> (<?= htmlspecialchars($ac['duration']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Course Preview Badge -->
                                    <div class="col-md-12">
                                        <div id="courseInfoBadge" class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-award text-warning fs-4"></i>
                                                <div>
                                                    <div class="fw-bold text-dark" id="previewCourseName">
                                                        <?= !empty($preselected_course) ? htmlspecialchars($preselected_course) : 'Please select a course above' ?>
                                                    </div>
                                                    <small class="text-muted" id="previewCourseMeta">Duration: Industry standard certification &bull; 100% Placement Support</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2.5">Batch Intake Open</span>
                                        </div>
                                    </div>

                                    <!-- Highest Qualification -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Highest Academic Qualification <span class="req">*</span></span>
                                        </label>
                                        <select name="education" id="f_education" class="form-select-custom" required>
                                            <option value="">Select Qualification</option>
                                            <option value="10th Pass (Matriculation)">10th Pass (Matriculation)</option>
                                            <option value="12th Pass (Intermediate)">12th Pass (Intermediate)</option>
                                            <option value="Diploma Holder">Diploma Holder</option>
                                            <option value="Graduate (Bachelor's Degree)">Graduate (Bachelor's Degree)</option>
                                            <option value="Post Graduate (Master's Degree)">Post Graduate (Master's Degree)</option>
                                            <option value="Pursuing Graduation">Pursuing Graduation</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Board / University -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Board / University Name <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="board_university" id="f_board" class="form-control-custom" placeholder="e.g. CBSE, ICSE, UP Board, Delhi University" required>
                                    </div>

                                    <!-- Passing Year -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Passing Year <span class="req">*</span></span>
                                        </label>
                                        <select name="passing_year" id="f_passing_year" class="form-select-custom" required>
                                            <option value="">Select Year</option>
                                            <?php for ($y = date('Y'); $y >= 2012; $y--): ?>
                                                <option value="<?= $y ?>"><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <!-- Percentage -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Percentage / CGPA / Grade <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="percentage" id="f_percentage" class="form-control-custom" placeholder="e.g. 78.5% or 8.2 CGPA" required>
                                    </div>
                                </div>

                                <!-- STEP 2 BUTTONS -->
                                <div class="wizard-footer">
                                    <button type="button" class="btn-wizard-prev" onclick="goToStep(1)">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>Previous Step</span>
                                    </button>
                                    <button type="button" class="btn-wizard-next" onclick="goToStep(3)">
                                        <span>Next: Address Details</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- ============================================ -->
                            <!-- STEP 3: CONTACT & PERMANENT ADDRESS -->
                            <!-- ============================================ -->
                            <div class="wizard-step-pane" id="stepPane3">
                                <h3 class="step-heading">Step 3: Residential &amp; Contact Address</h3>
                                <p class="step-subheading">Enter your complete permanent communication and residential address details.</p>

                                <div class="row g-3">
                                    <!-- Street / Address -->
                                    <div class="col-md-12">
                                        <label class="form-label-custom">
                                            <span>Full Street Address / House No. / Landmark <span class="req">*</span></span>
                                        </label>
                                        <textarea name="address" id="f_address" class="form-control-custom" rows="2" placeholder="House/Flat No., Street, Landmark, Area" required></textarea>
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>City / District <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="city" id="f_city" class="form-control-custom" placeholder="e.g. Ghaziabad / Noida / New Delhi" required>
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>State / Union Territory <span class="req">*</span></span>
                                        </label>
                                        <select name="state" id="f_state" class="form-select-custom" required>
                                            <option value="">Select State</option>
                                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                                            <option value="Delhi">Delhi</option>
                                            <option value="Haryana">Haryana</option>
                                            <option value="Punjab">Punjab</option>
                                            <option value="Rajasthan">Rajasthan</option>
                                            <option value="Bihar">Bihar</option>
                                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                                            <option value="Uttarakhand">Uttarakhand</option>
                                            <option value="Maharashtra">Maharashtra</option>
                                            <option value="West Bengal">West Bengal</option>
                                            <option value="Gujarat">Gujarat</option>
                                            <option value="Other">Other State / UT</option>
                                        </select>
                                    </div>

                                    <!-- Post Office -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Post Office <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="post_office" id="f_post_office" class="form-control-custom" placeholder="Local Post Office name" required>
                                    </div>

                                    <!-- Pin Code -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>6-Digit Pin Code <span class="req">*</span></span>
                                        </label>
                                        <input type="text" name="pincode" id="f_pincode" class="form-control-custom font-monospace" placeholder="e.g. 201016" maxlength="6" required>
                                    </div>
                                </div>

                                <!-- STEP 3 BUTTONS -->
                                <div class="wizard-footer">
                                    <button type="button" class="btn-wizard-prev" onclick="goToStep(2)">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>Previous Step</span>
                                    </button>
                                    <button type="button" class="btn-wizard-next" onclick="goToStep(4)">
                                        <span>Next: Upload Documents</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- ============================================ -->
                            <!-- STEP 4: DOCUMENT UPLOADS & ATTACHMENTS -->
                            <!-- ============================================ -->
                            <div class="wizard-step-pane" id="stepPane4">
                                <h3 class="step-heading">Step 4: Academic &amp; Identity Documents</h3>
                                <p class="step-subheading">Attach clear copies of your documents. Supported formats: JPG, PNG, PDF (Max: 5MB per file).</p>

                                <div class="row g-4">
                                    <!-- Passport Photo -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Passport Size Photo <span class="req">*</span></span>
                                        </label>
                                        
                                        <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                                            <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Photo" class="rounded border" style="width: 50px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <span class="badge bg-success-subtle text-success py-1"><i class="fa-solid fa-check me-1"></i> Profile Photo Verified</span>
                                                        <div class="small text-muted mt-1">Auto-loaded from your candidate registration.</div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('photoInputGroup').classList.toggle('d-none')">
                                                    Change
                                                </button>
                                            </div>
                                            <div id="photoInputGroup" class="d-none">
                                                <div class="file-dropzone">
                                                    <i class="fa-solid fa-camera file-dropzone-icon"></i>
                                                    <div class="small fw-semibold text-dark">Upload New Photo</div>
                                                    <div class="small text-muted">Click or drag image here</div>
                                                    <input type="file" name="photo" id="f_photo" accept="image/*" onchange="previewFileName(this)">
                                                    <div class="file-chosen-name"></div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="file-dropzone">
                                                <i class="fa-solid fa-camera file-dropzone-icon"></i>
                                                <div class="small fw-semibold text-dark">Upload Recent Passport Photo <span class="text-danger">*</span></div>
                                                <div class="small text-muted">JPG or PNG format</div>
                                                <input type="file" name="photo" id="f_photo" accept="image/*" required onchange="previewFileName(this)">
                                                <div class="file-chosen-name"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- 10th Marksheet -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Class 10th Marksheet / Certificate <span class="req">*</span></span>
                                        </label>
                                        <div class="file-dropzone">
                                            <i class="fa-solid fa-file-pdf file-dropzone-icon"></i>
                                            <div class="small fw-semibold text-dark">Upload 10th Marksheet <span class="text-danger">*</span></div>
                                            <div class="small text-muted">PDF or Image scan</div>
                                            <input type="file" name="marksheet10" id="f_m10" accept="image/*,.pdf" required onchange="previewFileName(this)">
                                            <div class="file-chosen-name"></div>
                                        </div>
                                    </div>

                                    <!-- 12th / Highest Marksheet -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Class 12th / Graduation Marksheet</span>
                                            <span class="text-muted small">Optional</span>
                                        </label>
                                        <div class="file-dropzone">
                                            <i class="fa-solid fa-file-lines file-dropzone-icon"></i>
                                            <div class="small fw-semibold text-dark">Upload 12th / Degree Marksheet</div>
                                            <div class="small text-muted">PDF or Image scan</div>
                                            <input type="file" name="marksheet12" id="f_m12" accept="image/*,.pdf" onchange="previewFileName(this)">
                                            <div class="file-chosen-name"></div>
                                        </div>
                                    </div>

                                    <!-- Aadhaar Card Document -->
                                    <div class="col-md-6">
                                        <label class="form-label-custom">
                                            <span>Aadhaar Card Copy (Front &amp; Back) <span class="req">*</span></span>
                                        </label>
                                        <div class="file-dropzone">
                                            <i class="fa-solid fa-id-card file-dropzone-icon"></i>
                                            <div class="small fw-semibold text-dark">Upload Aadhaar Card Document <span class="text-danger">*</span></div>
                                            <div class="small text-muted">PDF or Clear photo copy</div>
                                            <input type="file" name="aadhaar_card" id="f_aadhaar_file" accept="image/*,.pdf" required onchange="previewFileName(this)">
                                            <div class="file-chosen-name"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 4 BUTTONS -->
                                <div class="wizard-footer">
                                    <button type="button" class="btn-wizard-prev" onclick="goToStep(3)">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>Previous Step</span>
                                    </button>
                                    <button type="button" class="btn-wizard-next" onclick="prepareReviewAndGo(5)">
                                        <span>Next: Review &amp; Confirm</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- ============================================ -->
                            <!-- STEP 5: REVIEW & FINAL SUBMISSION -->
                            <!-- ============================================ -->
                            <div class="wizard-step-pane" id="stepPane5">
                                <h3 class="step-heading">Step 5: Review &amp; Submit Application</h3>
                                <p class="step-subheading">Please review all provided information carefully before submitting your online application.</p>

                                <div class="row g-3">
                                    <!-- Candidate Bio Review -->
                                    <div class="col-md-6">
                                        <div class="review-card">
                                            <div class="review-card-title">
                                                <span><i class="fa-solid fa-user text-primary me-1.5"></i> Candidate Identity</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="goToStep(1)">Edit</button>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Candidate Name</span>
                                                <span class="review-val" id="r_name">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Father's Name</span>
                                                <span class="review-val" id="r_father">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Date of Birth &amp; Gender</span>
                                                <span class="review-val" id="r_dob_gender">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Aadhaar No.</span>
                                                <span class="review-val font-monospace" id="r_aadhaar">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Course & Academic Review -->
                                    <div class="col-md-6">
                                        <div class="review-card">
                                            <div class="review-card-title">
                                                <span><i class="fa-solid fa-graduation-cap text-warning me-1.5"></i> Course &amp; Academics</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="goToStep(2)">Edit</button>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Selected Course</span>
                                                <span class="review-val text-primary fw-bold" id="r_course">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Highest Qualification</span>
                                                <span class="review-val" id="r_education">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Board / University</span>
                                                <span class="review-val" id="r_board">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Passing Year &amp; Marks</span>
                                                <span class="review-val" id="r_passing_marks">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact & Address Review -->
                                    <div class="col-md-6">
                                        <div class="review-card">
                                            <div class="review-card-title">
                                                <span><i class="fa-solid fa-envelope text-info me-1.5"></i> Contact &amp; Communication</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="goToStep(1)">Edit</button>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Mobile (WhatsApp)</span>
                                                <span class="review-val" id="r_mobile">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Email Address</span>
                                                <span class="review-val" id="r_email">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Review -->
                                    <div class="col-md-6">
                                        <div class="review-card">
                                            <div class="review-card-title">
                                                <span><i class="fa-solid fa-location-dot text-danger me-1.5"></i> Address Details</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="goToStep(3)">Edit</button>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">Full Address</span>
                                                <span class="review-val text-truncate" style="max-width: 200px;" id="r_address">-</span>
                                            </div>
                                            <div class="review-row">
                                                <span class="review-label">City, State &amp; Pin</span>
                                                <span class="review-val" id="r_location">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Self Declaration -->
                                <div class="p-3 bg-light rounded-3 border mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="declarationCheck" required>
                                        <label class="form-check-label small text-dark fw-semibold" for="declarationCheck">
                                            I hereby declare that all details submitted above are true and complete. I agree to comply with Finchskills Institute's admission guidelines and terms.
                                        </label>
                                    </div>
                                </div>

                                <!-- STEP 5 BUTTONS -->
                                <div class="wizard-footer">
                                    <button type="button" class="btn-wizard-prev" onclick="goToStep(4)">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>Back to Documents</span>
                                    </button>
                                    <button type="submit" class="btn-wizard-submit" id="submitBtn">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Submit Online Application</span>
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

<!-- WIZARD STEPPER JAVASCRIPT -->
<script>
let currentStep = 1;
const totalSteps = 5;

function updateProgressBar(step) {
    const percentage = ((step - 1) / (totalSteps - 1)) * 100;
    const bar = document.getElementById('stepperProgressBar');
    if (bar) bar.style.width = percentage + '%';

    for (let i = 1; i <= totalSteps; i++) {
        const item = document.getElementById('stepIndicator' + i);
        if (item) {
            item.classList.remove('active', 'completed');
            if (i < step) {
                item.classList.add('completed');
                item.querySelector('.step-circle').innerHTML = '<i class="fa-solid fa-check"></i>';
            } else if (i === step) {
                item.classList.add('active');
                // Restore icon for active step
                const icons = ['fa-user', 'fa-graduation-cap', 'fa-map-location-dot', 'fa-file-arrow-up', 'fa-clipboard-check'];
                item.querySelector('.step-circle').innerHTML = '<i class="fa-solid ' + icons[i - 1] + '"></i>';
            } else {
                const icons = ['fa-user', 'fa-graduation-cap', 'fa-map-location-dot', 'fa-file-arrow-up', 'fa-clipboard-check'];
                item.querySelector('.step-circle').innerHTML = '<i class="fa-solid ' + icons[i - 1] + '"></i>';
            }
        }
    }
}

function validateCurrentStep(step) {
    const pane = document.getElementById('stepPane' + step);
    if (!pane) return true;

    // Find all required visible inputs in this pane
    const inputs = pane.querySelectorAll('input[required], select[required], textarea[required]');
    for (let input of inputs) {
        if (!input.checkValidity() || input.value.trim() === '') {
            input.focus();
            input.classList.add('border-danger');
            setTimeout(() => input.classList.remove('border-danger'), 3000);
            return false;
        }
    }
    return true;
}

function goToStep(step) {
    if (step > currentStep) {
        // Validate current step before moving forward
        if (!validateCurrentStep(currentStep)) return;
    }

    currentStep = step;

    // Hide all panes
    document.querySelectorAll('.wizard-step-pane').forEach(p => p.classList.remove('active'));

    // Show target pane
    const targetPane = document.getElementById('stepPane' + step);
    if (targetPane) {
        targetPane.classList.add('active');
    }

    updateProgressBar(step);

    // Scroll gently to top of wizard
    const card = document.querySelector('.wizard-card');
    if (card) {
        const offset = card.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ top: offset, behavior: 'smooth' });
    }
}

function jumpToStep(step) {
    // Only allow clicking to steps that have already been visited / validated
    if (step < currentStep) {
        goToStep(step);
    }
}

function prepareReviewAndGo(step) {
    if (!validateCurrentStep(currentStep)) return;

    // Populate Review Fields
    document.getElementById('r_name').textContent = document.getElementById('f_name').value || '-';
    document.getElementById('r_father').textContent = document.getElementById('f_father_name').value || '-';
    document.getElementById('r_dob_gender').textContent = (document.getElementById('f_dob').value || '-') + ' (' + (document.getElementById('f_gender').value || '-') + ')';
    document.getElementById('r_aadhaar').textContent = document.getElementById('f_aadhaar').value || '-';
    document.getElementById('r_mobile').textContent = document.getElementById('f_mobile').value || '-';
    document.getElementById('r_email').textContent = document.getElementById('f_email').value || '-';

    document.getElementById('r_course').textContent = document.getElementById('f_course').value || '-';
    document.getElementById('r_education').textContent = document.getElementById('f_education').value || '-';
    document.getElementById('r_board').textContent = document.getElementById('f_board').value || '-';
    document.getElementById('r_passing_marks').textContent = (document.getElementById('f_passing_year').value || '-') + ' | ' + (document.getElementById('f_percentage').value || '-');

    document.getElementById('r_address').textContent = document.getElementById('f_address').value || '-';
    document.getElementById('r_location').textContent = (document.getElementById('f_city').value || '-') + ', ' + (document.getElementById('f_state').value || '-') + ' - ' + (document.getElementById('f_pincode').value || '-');

    goToStep(step);
}

function updateCoursePreview() {
    const select = document.getElementById('f_course');
    const selectedOption = select.options[select.selectedIndex];
    const previewName = document.getElementById('previewCourseName');
    const previewMeta = document.getElementById('previewCourseMeta');

    if (select.value) {
        previewName.textContent = select.value;
        const dur = selectedOption.dataset.duration || 'Flexible';
        const mode = selectedOption.dataset.mode || 'Classroom & Practical';
        previewMeta.textContent = 'Duration: ' + dur + ' • Mode: ' + mode + ' • 100% Placement Support';
    } else {
        previewName.textContent = 'Please select a course above';
        previewMeta.textContent = 'Duration: Industry standard certification • 100% Placement Support';
    }
}

function previewFileName(input) {
    const chosenDiv = input.parentElement.querySelector('.file-chosen-name');
    if (input.files && input.files[0]) {
        chosenDiv.textContent = '✓ ' + input.files[0].name;
        chosenDiv.style.display = 'block';
    }
}

// Prevent double submission
document.getElementById('admissionForm').addEventListener('submit', function(e) {
    const dec = document.getElementById('declarationCheck');
    if (!dec.checked) {
        e.preventDefault();
        alert('Please accept the declaration before submitting.');
        dec.focus();
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting Application...';
});
</script>

<?php include "footer.php"; ?>
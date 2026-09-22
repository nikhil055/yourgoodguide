<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

// Require Student Login
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: student-login.php");
    exit;
}

$student_id = (int)($_SESSION['student_db_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: student-logout.php");
    exit;
}

// Fetch Enrolled Admissions
$adm_stmt = $pdo->prepare("SELECT * FROM admissions WHERE student_id = ? OR email = ? ORDER BY id DESC");
$adm_stmt->execute([$student['student_id'], $student['email']]);
$my_admissions = $adm_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Installments and Payment History
$ins_stmt = $pdo->prepare("SELECT * FROM student_installments WHERE student_db_id = ? OR student_id = ? ORDER BY due_date ASC, id ASC");
$ins_stmt->execute([$student_id, $student['student_id']]);
$my_installments = $ins_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Next Due Installment
$next_due_stmt = $pdo->prepare("SELECT * FROM student_installments WHERE (student_db_id = ? OR student_id = ?) AND status = 'Pending' ORDER BY due_date ASC LIMIT 1");
$next_due_stmt->execute([$student_id, $student['student_id']]);
$next_due = $next_due_stmt->fetch(PDO::FETCH_ASSOC);

// Razorpay Settings
$s_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'razorpay_%'");
$gateway_settings = $s_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$razorpay_mode = $gateway_settings['razorpay_mode'] ?? 'test';
$razorpay_key = ($razorpay_mode === 'live') ? ($gateway_settings['razorpay_live_key_id'] ?? '') : ($gateway_settings['razorpay_test_key_id'] ?? 'rzp_test_1DP5mmOlF5G5ag');

$page_title = "Student Dashboard - " . htmlspecialchars($student['name']);
include "header.php";
?>

<style>
.profile-section {
    padding: 35px 0 70px;
    background: #f8fafc;
    min-height: 85vh;
}
.profile-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 24px;
    box-shadow: none !important;
}
.profile-header-strip {
    background: #0f172a;
    border-radius: 6px;
    padding: 20px;
    color: #ffffff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
}
.profile-avatar-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fe7c03;
    background: #ffffff;
}
.id-chip {
    background: rgba(254, 124, 3, 0.15);
    color: #fe7c03;
    border: 1px solid rgba(254, 124, 3, 0.35);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.info-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px 14px;
    height: 100%;
}
.info-tile-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #64748b;
    margin-bottom: 2px;
    display: block;
}
.info-tile-val {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
}
.due-alert-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 14px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

@media print {
    body * { visibility: hidden; }
    #printableSlip, #printableSlip * { visibility: visible; }
    #printableSlip { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>

<section class="profile-section">
    <div class="container">

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3 no-print">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-secondary"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Student Dashboard</li>
            </ol>
        </nav>

        <!-- SUCCESS NOTIFICATION ALERTS -->
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-2 shadow-none border border-success-subtle no-print" role="alert">
                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                <div class="small">
                    <strong>Payment Confirmed!</strong> Your course admission fee payment has been successfully verified. An official invoice and receipt have been sent to your email.
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- UPCOMING DUE DATE ALERT NOTIFICATION -->
        <?php if ($next_due): ?>
            <?php
            $today = new DateTime();
            $due_dt = new DateTime($next_due['due_date']);
            $days_left = (int)$today->diff($due_dt)->format("%r%a");
            $is_overdue = ($days_left < 0);
            ?>
            <div class="due-alert-box no-print" style="<?= $is_overdue ? 'background:#fef2f2; border-color:#fecaca;' : '' ?>">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid <?= $is_overdue ? 'fa-triangle-exclamation text-danger' : 'fa-bell text-warning' ?> fs-4"></i>
                    <div>
                        <strong class="<?= $is_overdue ? 'text-danger' : 'text-dark' ?>" style="font-size: 13.5px;">
                            <?= $is_overdue ? 'Overdue Installment Reminder' : 'Upcoming Fee Installment Alert' ?>: <?= htmlspecialchars($next_due['installment_title']) ?>
                        </strong>
                        <div class="text-muted" style="font-size: 12px;">
                            Course: <strong><?= htmlspecialchars($next_due['course_title']) ?></strong> &bull; Amount: <strong class="text-dark font-monospace">₹<?= number_format($next_due['installment_amount'], 2) ?></strong> &bull; 
                            Due Date: <strong class="<?= $is_overdue ? 'text-danger' : 'text-dark' ?>"><?= date('d M Y', strtotime($next_due['due_date'])) ?></strong> 
                            (<?= $is_overdue ? abs($days_left) . ' days overdue' : ($days_left === 0 ? 'Due Today' : 'in ' . $days_left . ' days') ?>)
                        </div>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-success px-3 py-1.5 fw-semibold" onclick="paySingleInstallment(<?= $next_due['id'] ?>, <?= $next_due['installment_amount'] ?>, '<?= addslashes($next_due['installment_title']) ?>', '<?= addslashes($next_due['course_title']) ?>')">
                        <i class="fa-solid fa-lock me-1"></i> Pay ₹<?= number_format($next_due['installment_amount'], 0) ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-card">

            <!-- HEADER BANNER -->
            <div class="profile-header-strip">
                <div class="d-flex align-items-center gap-3">
                    <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                        <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Passport Photo" class="profile-avatar-lg">
                    <?php else: ?>
                        <div class="profile-avatar-lg d-flex align-items-center justify-content-center text-dark bg-white">
                            <i class="fa-solid fa-user fs-4"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="fw-bold text-white mb-0.5" style="font-size: 18px;"><?= htmlspecialchars($student['name']) ?></h4>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="id-chip font-monospace">
                                <i class="fa-solid fa-id-badge"></i> <?= htmlspecialchars($student['student_id']) ?>
                            </span>
                            <span class="badge bg-success text-white px-2 py-0.5 rounded-1" style="font-size:10px;">
                                <i class="fa-solid fa-circle-check me-0.5"></i> Verified Student
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-1.5 no-print">
                    <button type="button" onclick="window.print()" class="btn btn-light btn-sm px-2.5 py-1" style="font-size: 12px;">
                        <i class="fa-solid fa-print me-1"></i> Print ID Slip
                    </button>
                    <a href="student-logout.php" class="btn btn-outline-light btn-sm px-2.5 py-1" style="font-size: 12px;">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
                    </a>
                </div>
            </div>

            <!-- KEY INFO TILES -->
            <div class="row g-2.5 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="info-tile">
                        <span class="info-tile-label"><i class="fa-solid fa-phone me-1"></i> Mobile / WhatsApp</span>
                        <div class="info-tile-val"><?= htmlspecialchars($student['phone']) ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-tile">
                        <span class="info-tile-label"><i class="fa-solid fa-envelope me-1"></i> Email Address</span>
                        <div class="info-tile-val text-truncate" title="<?= htmlspecialchars($student['email']) ?>"><?= htmlspecialchars($student['email']) ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-tile">
                        <span class="info-tile-label"><i class="fa-solid fa-fingerprint me-1"></i> Aadhaar Number</span>
                        <div class="info-tile-val font-monospace">XXXX-XXXX-<?= substr($student['aadhaar'], -4) ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-tile">
                        <span class="info-tile-label"><i class="fa-solid fa-calendar me-1"></i> Registered On</span>
                        <div class="info-tile-val"><?= date('d M Y', strtotime($student['created_at'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- 1. ENROLLED ADMISSIONS & COURSES SECTION -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 15px;">
                        <i class="fa-solid fa-graduation-cap text-primary me-1.5"></i> My Enrolled Courses &amp; Applications
                    </h5>
                    <a href="form-submission.php" class="btn btn-sm btn-outline-primary py-1 px-2.5 no-print" style="font-size: 11.5px;">
                        <i class="fa-solid fa-plus me-1"></i> Apply for New Course
                    </a>
                </div>

                <?php if (!empty($my_admissions)): ?>
                    <div class="table-responsive border rounded-2">
                        <table class="table table-hover table-sm mb-0 align-middle" style="font-size: 12.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref / Course</th>
                                    <th>Fee Total</th>
                                    <th>Paid Amount</th>
                                    <th>Balance</th>
                                    <th>Admission Status</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($my_admissions as $adm): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark d-block"><?= htmlspecialchars($adm['course']) ?></strong>
                                            <span class="text-muted font-monospace" style="font-size: 11px;">Ref: ADM-<?= str_pad($adm['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td class="font-monospace">₹<?= number_format((float)$adm['fee_total'], 2) ?></td>
                                        <td class="font-monospace text-success fw-bold">₹<?= number_format((float)$adm['fee_paid'], 2) ?></td>
                                        <td class="font-monospace <?= ((float)$adm['fee_pending'] > 0) ? 'text-danger fw-semibold' : 'text-success' ?>">
                                            ₹<?= number_format((float)$adm['fee_pending'], 2) ?>
                                        </td>
                                        <td>
                                            <?php if ($adm['status'] === 'Approved'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">Enrolled / Confirmed</span>
                                            <?php elseif ($adm['status'] === 'Pending'): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5">Pending Payment</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary px-2 py-0.5"><?= htmlspecialchars($adm['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?= date('d M Y', strtotime($adm['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-light rounded-2 border text-center text-muted small">
                        No active admission applications found. <a href="form-submission.php" class="text-primary fw-semibold">Click here to apply for a course</a>.
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. FEE INSTALLMENT SCHEDULE & PAYMENT HISTORY -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-2.5" style="font-size: 15px;">
                    <i class="fa-solid fa-receipt text-success me-1.5"></i> Fee Installments Schedule &amp; Receipts
                </h5>

                <?php if (!empty($my_installments)): ?>
                    <div class="table-responsive border rounded-2">
                        <table class="table table-hover table-sm mb-0 align-middle" style="font-size: 12.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Installment Title</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Paid Date</th>
                                    <th>Status</th>
                                    <th>Receipt / Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($my_installments as $ins): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark"><?= htmlspecialchars($ins['installment_title']) ?></strong>
                                            <?php if (!empty($ins['receipt_no'])): ?>
                                                <span class="d-block text-muted font-monospace" style="font-size: 10.5px;">#<?= htmlspecialchars($ins['receipt_no']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($ins['course_title']) ?></td>
                                        <td class="font-monospace fw-bold">₹<?= number_format($ins['installment_amount'], 2) ?></td>
                                        <td class="text-muted"><?= date('d M Y', strtotime($ins['due_date'])) ?></td>
                                        <td class="text-muted"><?= $ins['paid_date'] ? date('d M Y, h:i A', strtotime($ins['paid_date'])) : '-' ?></td>
                                        <td>
                                            <?php if ($ins['status'] === 'Paid'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">Paid</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ins['status'] === 'Paid'): ?>
                                                <span class="text-success small fw-semibold"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-primary py-0.5 px-2 no-print" style="font-size: 11.5px; background: #fe7c03; border-color: #fe7c03;" onclick="paySingleInstallment(<?= $ins['id'] ?>, <?= $ins['installment_amount'] ?>, '<?= addslashes($ins['installment_title']) ?>', '<?= addslashes($ins['course_title']) ?>')">
                                                    Pay Now
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-light rounded-2 border text-center text-muted small">
                        No installment records yet. Complete your course application to view scheduled fee installments.
                    </div>
                <?php endif; ?>
            </div>

            <!-- 3. PRINTABLE PROVISIONAL STUDENT CARD -->
            <div class="print-slip-box p-3 border rounded-2 bg-light" id="printableSlip">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="img/logo.png" alt="Finchskills" style="height: 32px;" onerror="this.style.display='none';">
                        <div>
                            <strong class="text-dark d-block" style="font-size: 14.5px;">Finchskills Institute</strong>
                            <small class="text-muted" style="font-size: 10.5px;">Student Provisional ID &amp; Enrollment Slip</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark text-white font-monospace px-2.5 py-1 rounded-1" style="font-size: 11.5px;">
                            <?= htmlspecialchars($student['student_id']) ?>
                        </span>
                    </div>
                </div>

                <div class="row align-items-center g-2.5">
                    <div class="col-sm-3 text-center">
                        <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                            <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Photo" class="rounded-2 border" style="width: 85px; height: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-2 border bg-white d-flex align-items-center justify-content-center text-muted mx-auto" style="width: 85px; height: 100px;">
                                <i class="fa-solid fa-user fa-2x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-9">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 12.5px;">
                            <tr>
                                <td class="text-muted" style="width: 130px;">Student Name:</td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($student['name']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Contact Phone:</td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($student['phone']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email Address:</td>
                                <td class="text-dark"><?= htmlspecialchars($student['email']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Aadhaar (Last 4):</td>
                                <td class="font-monospace text-dark">XXXX-XXXX-<?= substr($student['aadhaar'], -4) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                    <span>Orbit Plaza, Crossing Republik, Ghaziabad &bull; Helpline: +91 96503 86711</span>
                    <span>System Generated Record</span>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Razorpay Script for dashboard single installment payments -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function paySingleInstallment(installmentId, amount, title, courseTitle) {
    const amountPaise = Math.round(Number(amount) * 100);

    const options = {
        "key": "<?= htmlspecialchars($razorpay_key) ?>",
        "amount": amountPaise,
        "currency": "INR",
        "name": "Finchskills Institute",
        "description": title + " - " + courseTitle,
        "image": "img/favicon.png",
        "prefill": {
            "name": "<?= htmlspecialchars($student['name']) ?>",
            "email": "<?= htmlspecialchars($student['email']) ?>",
            "contact": "<?= htmlspecialchars($student['phone']) ?>"
        },
        "theme": {
            "color": "#fe7c03"
        },
        "handler": function (response) {
            fetch('process-fee-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'verify_course_fee',
                    admission_id: "<?= !empty($my_admissions[0]['id']) ? $my_admissions[0]['id'] : 0 ?>",
                    razorpay_payment_id: response.razorpay_payment_id,
                    plan_type: 'installment',
                    paid_amount: amount
                })
            })
            .then(res => res.json())
            .then(data => {
                window.location.href = 'student-profile.php?payment=success';
            })
            .catch(err => {
                window.location.href = 'student-profile.php?payment=success';
            });
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
}
</script>

<?php include "footer.php"; ?>

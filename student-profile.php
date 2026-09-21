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

$page_title = "Candidate Profile - " . htmlspecialchars($student['name']);
include "header.php";
?>

<style>
.profile-section {
    padding: 50px 0 80px;
    background: #fafbfc;
    min-height: 80vh;
}
.profile-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    box-shadow: none !important;
}
.profile-header-strip {
    background: linear-gradient(135deg, #0e1e2e 0%, #1a2f44 100%);
    border-radius: 14px;
    padding: 28px 24px;
    color: #ffffff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 28px;
}
.profile-avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fe7c03;
    background: #ffffff;
}
.id-chip {
    background: rgba(254, 124, 3, 0.2);
    color: #fe7c03;
    border: 1px solid rgba(254, 124, 3, 0.4);
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.info-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    height: 100%;
}
.info-tile-label {
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    margin-bottom: 4px;
    display: block;
}
.info-tile-val {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}
.print-slip-box {
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    padding: 24px;
    background: #ffffff;
    margin-top: 24px;
}

@media print {
    body * {
        visibility: hidden;
    }
    #printableSlip, #printableSlip * {
        visibility: visible;
    }
    #printableSlip {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<section class="profile-section">
    <div class="container">

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4 no-print">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-secondary"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Candidate Dashboard</li>
            </ol>
        </nav>

        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm no-print" role="alert">
                <i class="fa-solid fa-circle-check fs-4 text-success"></i>
                <div>
                    <strong>Payment Received!</strong> Your seat registration fee has been verified and your seat is locked. Receipt has been emailed to you.
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['skipped'])): ?>
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm no-print" role="alert">
                <i class="fa-solid fa-circle-info fs-4 text-info"></i>
                <div>
                    <strong>Welcome to Your Dashboard!</strong> You skipped seat reservation fee for now. You can reserve your seat at any time below.
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            <i class="fa-solid fa-user fs-2"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="fw-bold text-white mb-1" style="font-size: 22px;"><?= htmlspecialchars($student['name']) ?></h3>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="id-chip font-monospace">
                                <i class="fa-solid fa-id-card"></i>
                                <?= htmlspecialchars($student['student_id']) ?>
                            </span>
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill small">
                                <i class="fa-solid fa-circle-check me-1"></i> Verified Candidate
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 no-print">
                    <button type="button" onclick="window.print()" class="btn btn-light btn-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>Print Registration Card</span>
                    </button>
                    <a href="student-logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- KEY INFO TILES -->
            <div class="row g-3 mb-4">
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

            <!-- SEAT STATUS & FEE STATUS CARDS -->
            <div class="row g-4 mb-4">
                
                <!-- SEAT STATUS CARD -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chair text-primary me-2"></i>Batch Seat Status</h6>
                            <?php if ($student['seat_status'] === 'Confirmed'): ?>
                                <span class="badge bg-success px-2.5 py-1 rounded-pill">Confirmed</span>
                            <?php elseif ($student['seat_status'] === 'Reserved'): ?>
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">Reserved (Pending Fee)</span>
                            <?php else: ?>
                                <span class="badge bg-secondary px-2.5 py-1 rounded-pill">Pending</span>
                            <?php endif; ?>
                        </div>
                        <p class="small text-muted mb-3">
                            <?php if ($student['seat_status'] === 'Confirmed'): ?>
                                Your batch seat is officially locked. Our academic counselor will coordinate your batch timings and orientation induction kit.
                            <?php else: ?>
                                Your seat reservation is on hold. Complete your seat reservation fee to lock priority batch timings.
                            <?php endif; ?>
                        </p>
                        <?php if ($student['seat_status'] !== 'Confirmed'): ?>
                            <a href="seat-reservation.php" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2 no-print">
                                <i class="fa-solid fa-lock"></i>
                                <span>Lock Seat with Razorpay</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PAYMENT STATUS CARD -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i>Payment Receipt</h6>
                            <?php if ($student['payment_status'] === 'Paid'): ?>
                                <span class="badge bg-success px-2.5 py-1 rounded-pill">Paid &bull; ₹<?= number_format($student['payment_amount'], 2) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary px-2.5 py-1 rounded-pill">Unpaid / Skipped</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($student['payment_status'] === 'Paid'): ?>
                            <div class="small font-monospace mb-2 text-dark">
                                <div>Txn ID: <strong><?= htmlspecialchars($student['payment_id']) ?></strong></div>
                                <div>Date: <strong><?= date('d M Y, h:i A', strtotime($student['payment_date'])) ?></strong></div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                <i class="fa-solid fa-circle-check me-1"></i> Digital Invoice Valid
                            </span>
                        <?php else: ?>
                            <p class="small text-muted mb-3">You chose to skip the reservation fee during registration. You can pay via UPI/Card at any time.</p>
                            <a href="seat-reservation.php" class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-2 no-print">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>Pay Registration Fee</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- PRINTABLE REGISTRATION CARD SLIP -->
            <div class="print-slip-box" id="printableSlip">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="img/logo.png" alt="Finchskills" style="height: 38px;" onerror="this.style.display='none';">
                        <div>
                            <strong class="text-dark d-block" style="font-size: 16px;">Finchskills Institute</strong>
                            <small class="text-muted" style="font-size: 11px;">Candidate Registration Slip &amp; Provisional ID Card</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark text-white font-monospace px-3 py-1.5 rounded-pill" style="font-size: 13px;">
                            <?= htmlspecialchars($student['student_id']) ?>
                        </span>
                    </div>
                </div>

                <div class="row align-items-center g-3">
                    <div class="col-sm-3 text-center">
                        <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                            <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Photo" class="rounded-3 border" style="width: 100px; height: 115px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center text-muted mx-auto" style="width: 100px; height: 115px;">
                                <i class="fa-solid fa-user fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-9">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Candidate Name:</td>
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
                            <tr>
                                <td class="text-muted">Seat Booking:</td>
                                <td>
                                    <strong class="<?= $student['seat_status'] === 'Confirmed' ? 'text-success' : 'text-warning' ?>">
                                        <?= htmlspecialchars($student['seat_status']) ?> (<?= htmlspecialchars($student['payment_status']) ?>)
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center small text-muted">
                    <span>Finchskills Institute &bull; Orbit Plaza, Crossing Republik, Ghaziabad &bull; Helpline: +91 87508 60860</span>
                    <span>System Generated Slip</span>
                </div>
            </div>

            <!-- NEXT STEPS / LINKS -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top no-print">
                <div class="text-muted small">
                    Need to submit your detailed documents? Fill out the <a href="form-submission.php" class="text-primary text-decoration-none fw-semibold">Online Admission Form</a>
                </div>
                <div class="d-flex gap-2">
                    <a href="courses.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-graduation-cap me-1"></i> Browse Courses
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

<?php include "footer.php"; ?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

// Require Student Login
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: student-login.php?redirect=seat-reservation.php");
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

// Fetch Razorpay Settings
$settings = [];
$s_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'razorpay_%' OR setting_key = 'seat_booking_fee'");
while ($r = $s_stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$r['setting_key']] = $r['setting_value'];
}

$mode = $settings['razorpay_mode'] ?? 'test';
$key_id = ($mode === 'live') ? ($settings['razorpay_live_key_id'] ?? '') : ($settings['razorpay_test_key_id'] ?? 'rzp_test_1DP5mmOlF5G5ag');
$fee_amount = (float)($settings['seat_booking_fee'] ?? 999);
$amount_paise = $fee_amount * 100;

// Handle Skip Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_skip'])) {
    if ($student['payment_status'] === 'Unpaid') {
        $upd = $pdo->prepare("UPDATE students SET payment_status = 'Skipped', seat_status = 'Reserved' WHERE id = ?");
        $upd->execute([$student['id']]);
    }
    header("Location: student-profile.php?skipped=1");
    exit;
}

$page_title = "Reserve Your Seat - Finchskills Institute";
include "header.php";
?>

<style>
.seat-section {
    padding: 60px 0 90px;
    background: #fafbfc;
    min-height: 80vh;
}
.seat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 38px 32px;
    max-width: 620px;
    margin: 0 auto;
    box-shadow: none !important;
}
.seat-header-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    background: #fff7ed;
    border: 1px solid #ffedd5;
    color: #fe7c03;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}
.student-badge-strip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.student-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fe7c03;
    background: #ffffff;
    flex-shrink: 0;
}
.fee-box {
    background: #fafbfc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 24px;
}
.fee-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}
.fee-row:last-child {
    border-bottom: none;
    padding-top: 14px;
    margin-top: 6px;
    border-top: 2px dashed #e2e8f0;
}
.pay-btn {
    background: #047857;
    color: #ffffff;
    border: none;
    border-radius: 9px;
    padding: 13px 20px;
    font-size: 15px;
    font-weight: 700;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}
.pay-btn:hover {
    background: #065f46;
    color: #ffffff;
}
.skip-btn {
    background: #ffffff;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    padding: 11px 20px;
    font-size: 14px;
    font-weight: 600;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.skip-btn:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}
</style>

<section class="seat-section">
    <div class="container">
        <div class="seat-card">

            <div class="text-center mb-4">
                <div class="seat-header-icon">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 24px;">Reserve Your Program Seat</h2>
                <p class="text-muted small mb-0">Secure your seat for upcoming batch orientation &amp; practical labs.</p>
            </div>

            <!-- CANDIDATE BADGE STRIP -->
            <div class="student-badge-strip">
                <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/' . $student['photo'])): ?>
                    <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Avatar" class="student-avatar">
                <?php else: ?>
                    <div class="student-avatar d-flex align-items-center justify-content-center text-secondary bg-white">
                        <i class="fa-solid fa-user fs-4"></i>
                    </div>
                <?php endif; ?>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong class="text-dark" style="font-size: 15px;"><?= htmlspecialchars($student['name']) ?></strong>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                            <i class="fa-solid fa-circle-check me-1"></i> Verified
                        </span>
                    </div>
                    <div class="text-muted small mt-0.5 font-monospace">ID: <strong class="text-primary"><?= htmlspecialchars($student['student_id']) ?></strong> &bull; <?= htmlspecialchars($student['phone']) ?></div>
                </div>
            </div>

            <?php if ($student['payment_status'] === 'Paid'): ?>
                <!-- ALREADY PAID STATE -->
                <div class="alert alert-success d-flex align-items-center gap-3 p-3 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check fs-2 text-success"></i>
                    <div>
                        <h6 class="fw-bold text-success mb-1">Seat Already Confirmed!</h6>
                        <p class="mb-0 small text-secondary">Your payment (ID: <code><?= htmlspecialchars($student['payment_id']) ?></code>) has been received. Your seat is locked.</p>
                    </div>
                </div>
                <div class="text-center">
                    <a href="student-profile.php" class="pay-btn" style="background: #fe7c03;">
                        <span>Go to My Profile &amp; Registration Slip</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>

                <!-- FEE DETAILS & BENEFITS -->
                <div class="fee-box">
                    <div class="fee-row">
                        <span class="text-secondary">Registration &amp; Batch Reservation Fee</span>
                        <strong class="text-dark">₹<?= number_format($fee_amount, 2) ?></strong>
                    </div>
                    <div class="fee-row">
                        <span class="text-secondary">Study Material &amp; Induction Kit</span>
                        <span class="badge bg-light text-success border border-success-subtle">Included</span>
                    </div>
                    <div class="fee-row">
                        <span class="text-secondary">Dedicated Counselor Guidance</span>
                        <span class="badge bg-light text-success border border-success-subtle">Included</span>
                    </div>
                    <div class="fee-row">
                        <div>
                            <strong class="text-dark d-block">Total Payable Now</strong>
                            <small class="text-muted" style="font-size: 11.5px;">Secures guaranteed admission in next batch</small>
                        </div>
                        <h4 class="fw-bold mb-0" style="color: #047857;">₹<?= number_format($fee_amount, 2) ?></h4>
                    </div>
                </div>

                <!-- PAYMENT ACTIONS -->
                <div class="d-grid gap-2.5 mb-3">
                    <!-- Razorpay Button -->
                    <button type="button" id="payNowBtn" class="pay-btn">
                        <i class="fa-solid fa-lock"></i>
                        <span>Pay ₹<?= number_format($fee_amount, 0) ?> via Razorpay &amp; Confirm Seat</span>
                    </button>

                    <!-- Skip Form -->
                    <form method="POST" action="seat-reservation.php">
                        <input type="hidden" name="action_skip" value="1">
                        <button type="submit" class="skip-btn" title="You can pay this fee later from your dashboard">
                            <span>Skip for Now &amp; Go to Dashboard</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </form>
                </div>

                <div class="text-center">
                    <small class="text-muted" style="font-size: 11.5px;">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> 256-Bit Encrypted Secure Razorpay Payment (UPI, Cards, Netbanking)
                    </small>
                </div>

            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Razorpay Official Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payBtn = document.getElementById('payNowBtn');
    if (!payBtn) return;

    payBtn.addEventListener('click', function() {
        const options = {
            "key": "<?= htmlspecialchars($key_id) ?>",
            "amount": "<?= $amount_paise ?>",
            "currency": "INR",
            "name": "Finchskills Institute",
            "description": "Seat Reservation - <?= htmlspecialchars($student['student_id']) ?>",
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
                // Post payment verification to backend
                payBtn.disabled = true;
                payBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Verifying Payment...</span>';

                fetch('verify-payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        student_id: "<?= $student['id'] ?>",
                        amount: "<?= $fee_amount ?>"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'student-profile.php?payment=success';
                    } else {
                        alert('Payment recorded. ' + (data.message || 'Please check your profile.'));
                        window.location.href = 'student-profile.php';
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.location.href = 'student-profile.php?payment=done';
                });
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            alert("Payment failed: " + response.error.description);
        });
        rzp.open();
    });
});
</script>

<?php include "footer.php"; ?>

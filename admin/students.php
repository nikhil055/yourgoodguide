<?php
$page_title = "Registered Candidates Management";
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];

    // Delete
    if ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("SELECT photo FROM students WHERE id = ?");
        $stmt->execute([$target_id]);
        $cand = $stmt->fetch();
        if ($cand && !empty($cand['photo']) && file_exists(__DIR__ . '/../' . $cand['photo'])) {
            @unlink(__DIR__ . '/../' . $cand['photo']);
        }

        $del = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $del->execute([$target_id]);
        header("Location: students.php?msg=deleted");
        exit;
    }

    // Toggle Seat Status
    if ($_GET['action'] === 'toggle_seat') {
        $stmt = $pdo->prepare("SELECT seat_status FROM students WHERE id = ?");
        $stmt->execute([$target_id]);
        $cand = $stmt->fetch();
        if ($cand) {
            $new_seat = ($cand['seat_status'] === 'Confirmed') ? 'Pending' : 'Confirmed';
            $upd = $pdo->prepare("UPDATE students SET seat_status = ? WHERE id = ?");
            $upd->execute([$new_seat, $target_id]);
        }
        header("Location: students.php?msg=seat_updated");
        exit;
    }

    // Toggle Payment Status
    if ($_GET['action'] === 'toggle_payment') {
        $stmt = $pdo->prepare("SELECT payment_status FROM students WHERE id = ?");
        $stmt->execute([$target_id]);
        $cand = $stmt->fetch();
        if ($cand) {
            $new_pay = ($cand['payment_status'] === 'Paid') ? 'Unpaid' : 'Paid';
            $upd = $pdo->prepare("UPDATE students SET payment_status = ?, seat_status = CASE WHEN ? = 'Paid' THEN 'Confirmed' ELSE seat_status END WHERE id = ?");
            $upd->execute([$new_pay, $new_pay, $target_id]);
        }
        header("Location: students.php?msg=payment_updated");
        exit;
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') $msg = "Candidate record removed successfully.";
    if ($_GET['msg'] === 'seat_updated') $msg = "Seat confirmation status updated successfully.";
    if ($_GET['msg'] === 'payment_updated') $msg = "Payment status updated successfully.";
}

// Fetch Counts for KPI Cards
$kpi_stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified,
    SUM(CASE WHEN seat_status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN payment_status = 'Paid' THEN 1 ELSE 0 END) as paid,
    SUM(CASE WHEN payment_status = 'Unpaid' OR payment_status = 'Skipped' THEN 1 ELSE 0 END) as unpaid
FROM students");
$kpi = $kpi_stmt->fetch(PDO::FETCH_ASSOC);

// Search & Filter Query
$search = trim($_GET['search'] ?? '');
$filter_seat = trim($_GET['seat_status'] ?? '');
$filter_payment = trim($_GET['payment_status'] ?? '');

$query = "SELECT * FROM students WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR student_id LIKE ? OR email LIKE ? OR phone LIKE ? OR aadhaar LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}
if (!empty($filter_seat)) {
    $query .= " AND seat_status = ?";
    $params[] = $filter_seat;
}
if (!empty($filter_payment)) {
    $query .= " AND payment_status = ?";
    $params[] = $filter_payment;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Registered Candidates</h4>
        <p class="text-muted small mb-0">Total <?= count($students) ?> students found (with credentials, Aadhaar, and payment details)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="payment-settings.php" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa-solid fa-credit-card me-1"></i> Gateway Setup
        </a>
        <a href="../register.php" target="_blank" class="btn btn-primary btn-sm px-3">
            <i class="fa-solid fa-user-plus me-1"></i> New Registration Form
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

<!-- KPI STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold small">TOTAL CANDIDATES</span>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= (int)($kpi['total'] ?? 0) ?></h3>
            <span class="badge bg-light text-secondary border"><?= (int)($kpi['verified'] ?? 0) ?> Verified Emails</span>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold small">CONFIRMED SEATS</span>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-chair"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= (int)($kpi['confirmed'] ?? 0) ?></h3>
            <span class="badge bg-success-subtle text-success">Guaranteed Admission</span>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold small">FEES PAID (RAZORPAY)</span>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= (int)($kpi['paid'] ?? 0) ?></h3>
            <span class="badge bg-info-subtle text-info">Payment Verified</span>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold small">PENDING / UNPAID</span>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= (int)($kpi['unpaid'] ?? 0) ?></h3>
            <span class="badge bg-warning-subtle text-warning">Requires Follow-up</span>
        </div>
    </div>
</div>

<!-- FILTER & SEARCH CARD -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="students.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, student ID, email, phone, aadhaar..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="seat_status" class="form-select">
                    <option value="">All Seat Statuses</option>
                    <option value="Confirmed" <?= $filter_seat === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Reserved" <?= $filter_seat === 'Reserved' ? 'selected' : '' ?>>Reserved</option>
                    <option value="Pending" <?= $filter_seat === 'Pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">All Payment Statuses</option>
                    <option value="Paid" <?= $filter_payment === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Unpaid" <?= $filter_payment === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="Skipped" <?= $filter_payment === 'Skipped' ? 'selected' : '' ?>>Skipped (Pay Later)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="students.php" class="btn btn-light" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- STUDENTS TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Candidate</th>
                        <th>Student ID</th>
                        <th>Contact / Email</th>
                        <th>Aadhaar Number</th>
                        <th>Seat Status</th>
                        <th>Fee / Razorpay</th>
                        <th>Registered Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-2 mb-2 d-block"></i>
                                No candidate records found matching your filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $stu): ?>
                            <?php
                            $clean_phone = preg_replace('/[^0-9]/', '', $stu['phone']);
                            $wa_link = "https://wa.me/91" . substr($clean_phone, -10);
                            $photo_src = (!empty($stu['photo']) && file_exists(__DIR__ . '/../' . $stu['photo'])) ? '../' . $stu['photo'] : '';
                            ?>
                            <tr>
                                <!-- Candidate Info -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($photo_src): ?>
                                            <img src="<?= htmlspecialchars($photo_src) ?>" alt="Photo" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 14px;">
                                                <?= strtoupper(substr($stu['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($stu['name']) ?></div>
                                            <?php if ($stu['is_verified'] == 1): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-0" style="font-size: 10px;">
                                                    <i class="fa-solid fa-check me-1"></i>Email Verified
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-0" style="font-size: 10px;">
                                                    <i class="fa-solid fa-clock me-1"></i>Unverified
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Student ID -->
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12px;">
                                        <i class="fa-solid fa-id-badge text-warning me-1"></i><?= htmlspecialchars($stu['student_id']) ?>
                                    </span>
                                </td>

                                <!-- Contact / Email -->
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <a href="tel:<?= htmlspecialchars($stu['phone']) ?>" class="text-decoration-none text-dark">
                                            <i class="fa-solid fa-phone text-muted me-1"></i><?= htmlspecialchars($stu['phone']) ?>
                                        </a>
                                        <a href="<?= $wa_link ?>" target="_blank" class="text-success ms-2" title="Chat on WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        <a href="mailto:<?= htmlspecialchars($stu['email']) ?>" class="text-decoration-none text-muted">
                                            <i class="fa-regular fa-envelope me-1"></i><?= htmlspecialchars($stu['email']) ?>
                                        </a>
                                    </div>
                                </td>

                                <!-- Aadhaar -->
                                <td>
                                    <span class="font-monospace small text-secondary">
                                        <i class="fa-solid fa-address-card text-muted me-1"></i><?= htmlspecialchars($stu['aadhaar']) ?>
                                    </span>
                                </td>

                                <!-- Seat Status -->
                                <td>
                                    <?php if ($stu['seat_status'] === 'Confirmed'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i>Confirmed
                                        </span>
                                    <?php elseif ($stu['seat_status'] === 'Reserved'): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <i class="fa-solid fa-bookmark me-1"></i>Reserved
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="fa-solid fa-clock me-1"></i>Pending
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Payment Status -->
                                <td>
                                    <?php if ($stu['payment_status'] === 'Paid'): ?>
                                        <span class="badge bg-success text-white px-2 py-1">
                                            <i class="fa-solid fa-check me-1"></i>Paid ₹<?= number_format((float)($stu['payment_amount'] ?: 999), 0) ?>
                                        </span>
                                        <?php if (!empty($stu['payment_id'])): ?>
                                            <div class="text-muted font-monospace" style="font-size: 10.5px; margin-top: 2px;" title="<?= htmlspecialchars($stu['payment_id']) ?>">
                                                ID: <?= htmlspecialchars(substr($stu['payment_id'], 0, 14)) ?>...
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($stu['payment_status'] === 'Skipped'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                            Pay Later (Skipped)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            Unpaid
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Registration Date -->
                                <td class="small text-muted">
                                    <?= date('d M Y, h:i A', strtotime($stu['created_at'])) ?>
                                </td>

                                <!-- Actions -->
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <button class="dropdown-item view-student-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#studentDetailModal"
                                                        data-id="<?= htmlspecialchars($stu['id']) ?>"
                                                        data-studentid="<?= htmlspecialchars($stu['student_id']) ?>"
                                                        data-name="<?= htmlspecialchars($stu['name']) ?>"
                                                        data-email="<?= htmlspecialchars($stu['email']) ?>"
                                                        data-phone="<?= htmlspecialchars($stu['phone']) ?>"
                                                        data-aadhaar="<?= htmlspecialchars($stu['aadhaar']) ?>"
                                                        data-seat="<?= htmlspecialchars($stu['seat_status']) ?>"
                                                        data-payment="<?= htmlspecialchars($stu['payment_status']) ?>"
                                                        data-paymentid="<?= htmlspecialchars($stu['payment_id'] ?? '') ?>"
                                                        data-photo="<?= htmlspecialchars($photo_src) ?>"
                                                        data-date="<?= date('d M Y, h:i A', strtotime($stu['created_at'])) ?>">
                                                    <i class="fa-solid fa-eye text-primary me-2"></i> View Full Profile
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="students.php?action=toggle_seat&id=<?= $stu['id'] ?>">
                                                    <i class="fa-solid fa-chair text-warning me-2"></i> 
                                                    <?= ($stu['seat_status'] === 'Confirmed') ? 'Mark Seat Pending' : 'Confirm Seat' ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="students.php?action=toggle_payment&id=<?= $stu['id'] ?>">
                                                    <i class="fa-solid fa-credit-card text-success me-2"></i> 
                                                    <?= ($stu['payment_status'] === 'Paid') ? 'Mark as Unpaid' : 'Mark as Paid' ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="students.php?action=delete&id=<?= $stu['id'] ?>" onclick="return confirm('Are you sure you want to permanently delete this candidate account?')">
                                                    <i class="fa-solid fa-trash me-2"></i> Delete Candidate
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CANDIDATE DETAIL MODAL -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4" style="background: #0e1e2e; color: #fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-id-card text-warning fs-5"></i>
                    <h5 class="modal-title fw-bold mb-0">Candidate Full Details</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div id="mPhotoContainer">
                                <img id="mPhoto" src="" alt="Passport Photo" class="rounded-3 border shadow-sm" style="width: 110px; height: 130px; object-fit: cover; display: none;">
                                <div id="mPhotoPlaceholder" class="rounded-3 border d-flex align-items-center justify-content-center bg-light text-muted mx-auto" style="width: 110px; height: 130px; font-size: 32px;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-dark mb-0" id="mName">-</h4>
                                <span class="badge bg-warning text-dark font-monospace" id="mStudentId">-</span>
                            </div>
                            <p class="text-muted small mb-3"><i class="fa-solid fa-calendar-check me-1"></i> Registered on: <span id="mDate">-</span></p>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-success-subtle text-success border" id="mSeatStatus">Seat: -</span>
                                <span class="badge bg-primary-subtle text-primary border" id="mPaymentStatus">Payment: -</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa-solid fa-address-book text-primary me-2"></i> Contact Information
                            </h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Email Address</span>
                                <span class="fw-semibold text-dark" id="mEmail">-</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">WhatsApp / Mobile</span>
                                <span class="fw-semibold text-dark" id="mPhone">-</span>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Aadhaar Card Number</span>
                                <span class="fw-bold text-dark font-monospace" id="mAadhaar">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa-solid fa-credit-card text-success me-2"></i> Seat & Payment Status
                            </h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Seat Reservation</span>
                                <span class="fw-bold text-dark" id="mSeatDetail">-</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Fee Payment</span>
                                <span class="fw-bold text-dark" id="mPaymentDetail">-</span>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Razorpay Transaction ID</span>
                                <span class="fw-semibold font-monospace text-muted small text-break" id="mPaymentId">None</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-3 px-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-student-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('mName').textContent = this.dataset.name;
            document.getElementById('mStudentId').textContent = this.dataset.studentid;
            document.getElementById('mEmail').textContent = this.dataset.email;
            document.getElementById('mPhone').textContent = this.dataset.phone;
            document.getElementById('mAadhaar').textContent = this.dataset.aadhaar;
            document.getElementById('mDate').textContent = this.dataset.date;
            document.getElementById('mSeatStatus').textContent = 'Seat: ' + this.dataset.seat;
            document.getElementById('mPaymentStatus').textContent = 'Payment: ' + this.dataset.payment;
            document.getElementById('mSeatDetail').textContent = this.dataset.seat;
            document.getElementById('mPaymentDetail').textContent = this.dataset.payment;
            document.getElementById('mPaymentId').textContent = this.dataset.paymentid ? this.dataset.paymentid : 'Not paid via gateway';

            const photoImg = document.getElementById('mPhoto');
            const photoPlaceholder = document.getElementById('mPhotoPlaceholder');

            if (this.dataset.photo) {
                photoImg.src = this.dataset.photo;
                photoImg.style.display = 'block';
                photoPlaceholder.style.display = 'none';
            } else {
                photoImg.style.display = 'none';
                photoPlaceholder.style.display = 'flex';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

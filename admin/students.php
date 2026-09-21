<?php
$page_title = "Registered Students";
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

<div class="space-y-4">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-md border border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-[#0e1e2e]">Registered Students</h2>
                <span class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-600 rounded border border-slate-200">
                    <?= count($students) ?> Students Listed
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Manage candidate profiles, seat confirmations, and payment verifications</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="payment-settings.php" class="px-3 py-1.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-[#fe7c03] rounded-md transition-colors flex items-center gap-1.5 no-underline">
                <i class="fa-solid fa-credit-card text-slate-400"></i>
                <span>Gateway Setup</span>
            </a>
            <a href="../register.php" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#fe7c03] hover:bg-[#ea6c00] rounded-md transition-colors flex items-center gap-1.5 no-underline">
                <i class="fa-solid fa-user-plus text-[11px]"></i>
                <span>Public Form</span>
            </a>
        </div>
    </div>

    <!-- ALERTS -->
    <?php if (!empty($msg)): ?>
        <div class="p-3 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-md p-3.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold uppercase text-slate-400">Total Registered</span>
                <span class="w-7 h-7 rounded bg-orange-50 text-[#fe7c03] flex items-center justify-center text-xs"><i class="fa-solid fa-users"></i></span>
            </div>
            <h4 class="text-xl font-bold text-[#0e1e2e]"><?= (int)($kpi['total'] ?? 0) ?></h4>
            <p class="text-[10px] text-slate-400 mt-1"><?= (int)($kpi['verified'] ?? 0) ?> Emails Verified</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-md p-3.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold uppercase text-slate-400">Confirmed Seats</span>
                <span class="w-7 h-7 rounded bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs"><i class="fa-solid fa-chair"></i></span>
            </div>
            <h4 class="text-xl font-bold text-[#0e1e2e]"><?= (int)($kpi['confirmed'] ?? 0) ?></h4>
            <p class="text-[10px] text-emerald-600 font-medium mt-1">Guaranteed Admission</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-md p-3.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold uppercase text-slate-400">Fees Paid</span>
                <span class="w-7 h-7 rounded bg-sky-50 text-sky-600 flex items-center justify-center text-xs"><i class="fa-solid fa-receipt"></i></span>
            </div>
            <h4 class="text-xl font-bold text-[#0e1e2e]"><?= (int)($kpi['paid'] ?? 0) ?></h4>
            <p class="text-[10px] text-sky-600 font-medium mt-1">Razorpay Verified</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-md p-3.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold uppercase text-slate-400">Pending / Unpaid</span>
                <span class="w-7 h-7 rounded bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="fa-solid fa-hourglass-half"></i></span>
            </div>
            <h4 class="text-xl font-bold text-[#0e1e2e]"><?= (int)($kpi['unpaid'] ?? 0) ?></h4>
            <p class="text-[10px] text-amber-600 font-medium mt-1">Follow-up Required</p>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white border border-slate-200 rounded-md p-3">
        <form method="GET" action="students.php" class="flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Seat Status -->
                <select name="seat_status" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-[#fe7c03]">
                    <option value="">All Seat Statuses</option>
                    <option value="Confirmed" <?= $filter_seat === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Reserved" <?= $filter_seat === 'Reserved' ? 'selected' : '' ?>>Reserved</option>
                    <option value="Pending" <?= $filter_seat === 'Pending' ? 'selected' : '' ?>>Pending</option>
                </select>

                <!-- Payment Status -->
                <select name="payment_status" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-[#fe7c03]">
                    <option value="">All Payment Statuses</option>
                    <option value="Paid" <?= $filter_payment === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Unpaid" <?= $filter_payment === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="Skipped" <?= $filter_payment === 'Skipped' ? 'selected' : '' ?>>Skipped (Pay Later)</option>
                </select>

                <?php if (!empty($filter_seat) || !empty($filter_payment) || !empty($search)): ?>
                    <a href="students.php" class="px-2 py-1 text-xs text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded transition-colors flex items-center gap-1 no-underline">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Reset
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Input -->
            <div class="relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" placeholder="Search name, ID, phone, email..." value="<?= htmlspecialchars($search) ?>" class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-md focus:outline-none focus:border-[#fe7c03] text-slate-800">
            </div>
        </form>
    </div>

    <!-- STUDENTS TABLE -->
    <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5">Candidate</th>
                        <th class="px-4 py-2.5">Student ID</th>
                        <th class="px-4 py-2.5">Contact / Email</th>
                        <th class="px-4 py-2.5">Aadhaar</th>
                        <th class="px-4 py-2.5">Seat Status</th>
                        <th class="px-4 py-2.5">Payment</th>
                        <th class="px-4 py-2.5">Date</th>
                        <th class="px-4 py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-2xl mb-1 text-slate-300 block"></i>
                                <span class="font-semibold text-slate-700 block">No candidates found</span>
                                <span class="text-[11px]">No registered candidate records match your current filters.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $stu): ?>
                            <?php
                            $clean_phone = preg_replace('/[^0-9]/', '', $stu['phone']);
                            $wa_link = "https://wa.me/91" . substr($clean_phone, -10);
                            $photo_src = (!empty($stu['photo']) && file_exists(__DIR__ . '/../' . $stu['photo'])) ? '../' . $stu['photo'] : '';
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Candidate Info -->
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <?php if ($photo_src): ?>
                                            <img src="<?= htmlspecialchars($photo_src) ?>" alt="" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs shrink-0">
                                                <?= strtoupper(substr($stu['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-semibold text-[#0e1e2e] leading-tight"><?= htmlspecialchars($stu['name']) ?></div>
                                            <?php if ($stu['is_verified'] == 1): ?>
                                                <span class="text-[10px] text-emerald-600 font-medium inline-flex items-center gap-0.5">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Verified
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-amber-600 font-medium inline-flex items-center gap-0.5">
                                                    <i class="fa-regular fa-clock text-[9px]"></i> Unverified
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Student ID -->
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 text-[11px] font-mono font-medium rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        <?= htmlspecialchars($stu['student_id']) ?>
                                    </span>
                                </td>

                                <!-- Contact / Email -->
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-1.5 font-medium text-slate-800">
                                        <a href="tel:<?= htmlspecialchars($stu['phone']) ?>" class="hover:text-[#fe7c03] no-underline text-inherit">
                                            <?= htmlspecialchars($stu['phone']) ?>
                                        </a>
                                        <a href="<?= $wa_link ?>" target="_blank" class="text-emerald-600 hover:text-emerald-700" title="WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </a>
                                    </div>
                                    <a href="mailto:<?= htmlspecialchars($stu['email']) ?>" class="text-[11px] text-slate-400 hover:text-[#fe7c03] no-underline block">
                                        <?= htmlspecialchars($stu['email']) ?>
                                    </a>
                                </td>

                                <!-- Aadhaar -->
                                <td class="px-4 py-2.5 font-mono text-[11px] text-slate-600">
                                    <?= htmlspecialchars($stu['aadhaar']) ?>
                                </td>

                                <!-- Seat Status -->
                                <td class="px-4 py-2.5">
                                    <a href="students.php?action=toggle_seat&id=<?= $stu['id'] ?>" class="no-underline inline-block" title="Click to toggle seat status">
                                        <?php if ($stu['seat_status'] === 'Confirmed'): ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Confirmed
                                            </span>
                                        <?php elseif ($stu['seat_status'] === 'Reserved'): ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-sky-50 text-sky-700 border border-sky-200 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-bookmark text-[9px]"></i> Reserved
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                                <i class="fa-regular fa-clock text-[9px]"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                </td>

                                <!-- Payment Status -->
                                <td class="px-4 py-2.5">
                                    <a href="students.php?action=toggle_payment&id=<?= $stu['id'] ?>" class="no-underline inline-block" title="Click to toggle payment status">
                                        <?php if ($stu['payment_status'] === 'Paid'): ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-emerald-600 text-white inline-flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[9px]"></i> Paid ₹<?= number_format((float)($stu['payment_amount'] ?: 999), 0) ?>
                                            </span>
                                            <?php if (!empty($stu['payment_id'])): ?>
                                                <div class="text-[9px] font-mono text-slate-400 mt-0.5" title="<?= htmlspecialchars($stu['payment_id']) ?>">
                                                    <?= htmlspecialchars(substr($stu['payment_id'], 0, 12)) ?>...
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($stu['payment_status'] === 'Skipped'): ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                Pay Later
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-rose-50 text-rose-600 border border-rose-200">
                                                Unpaid
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-2.5 text-[11px] text-slate-400 whitespace-nowrap">
                                    <?= date('d M Y, h:i A', strtotime($stu['created_at'])) ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-2.5 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- View Modal -->
                                        <button type="button" class="w-7 h-7 rounded border border-slate-200 bg-white text-slate-600 hover:text-[#fe7c03] hover:border-orange-200 flex items-center justify-center transition-colors view-student-btn"
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
                                                data-date="<?= date('d M Y, h:i A', strtotime($stu['created_at'])) ?>"
                                                title="View Profile">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                        </button>

                                        <!-- Delete -->
                                        <a href="students.php?action=delete&id=<?= $stu['id'] ?>" onclick="return confirm('Permanently delete candidate \'<?= htmlspecialchars(addslashes($stu['name'])) ?>\'?');" class="w-7 h-7 rounded border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:border-rose-200 flex items-center justify-center transition-colors no-underline" title="Delete Candidate">
                                            <i class="fa-regular fa-trash-can text-[10px]"></i>
                                        </a>
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
        <div class="modal-content border border-slate-200 rounded-md shadow-none overflow-hidden">
            <div class="modal-header border-b border-slate-200 py-3 px-4 bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="w-6 text-center text-[#fe7c03]"><i class="fa-solid fa-id-card text-sm"></i></span>
                    <h5 class="modal-title text-xs font-bold text-[#0e1e2e] uppercase tracking-wider mb-0">Candidate Profile Details</h5>
                </div>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 space-y-4 bg-slate-50/50">
                
                <!-- Profile Header Card -->
                <div class="bg-white border border-slate-200 rounded-md p-4 flex flex-col sm:flex-row items-center gap-4">
                    <div id="mPhotoContainer" class="shrink-0">
                        <img id="mPhoto" src="" alt="Passport Photo" class="w-20 h-24 rounded object-cover border border-slate-200" style="display: none;">
                        <div id="mPhotoPlaceholder" class="w-20 h-24 rounded border border-slate-200 flex items-center justify-center bg-slate-100 text-slate-400 text-2xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                    <div class="space-y-1 text-center sm:text-left flex-1">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h4 class="text-base font-bold text-[#0e1e2e]" id="mName">-</h4>
                            <span class="px-2 py-0.5 text-xs font-mono font-semibold rounded bg-orange-100 text-[#fe7c03]" id="mStudentId">-</span>
                        </div>
                        <p class="text-xs text-slate-400"><i class="fa-regular fa-calendar-check mr-1"></i> Registered on: <span id="mDate">-</span></p>
                        <div class="flex flex-wrap gap-2 pt-1 justify-center sm:justify-start">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200" id="mSeatStatus">Seat: -</span>
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-sky-50 text-sky-700 border border-sky-200" id="mPaymentStatus">Payment: -</span>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white border border-slate-200 rounded-md p-3.5 space-y-2">
                        <h6 class="text-xs font-bold text-[#0e1e2e] uppercase tracking-wider pb-1.5 border-b border-slate-100 flex items-center gap-1.5">
                            <i class="fa-solid fa-address-book text-sky-600"></i> Contact Details
                        </h6>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Email Address</span>
                            <span class="text-xs font-semibold text-slate-800" id="mEmail">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Phone Number</span>
                            <span class="text-xs font-semibold text-slate-800" id="mPhone">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Aadhaar Card</span>
                            <span class="text-xs font-mono font-bold text-slate-800" id="mAadhaar">-</span>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-md p-3.5 space-y-2">
                        <h6 class="text-xs font-bold text-[#0e1e2e] uppercase tracking-wider pb-1.5 border-b border-slate-100 flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt text-emerald-600"></i> Payment &amp; Enrollment
                        </h6>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Seat Confirmation</span>
                            <span class="text-xs font-bold text-slate-800" id="mSeatDetail">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Payment Status</span>
                            <span class="text-xs font-bold text-slate-800" id="mPaymentDetail">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Razorpay Order / ID</span>
                            <span class="text-[11px] font-mono text-slate-600 break-all" id="mPaymentId">None</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-t border-slate-200 py-2.5 px-4 bg-slate-50">
                <button type="button" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-md hover:bg-slate-50" data-bs-dismiss="modal">Close</button>
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

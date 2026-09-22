<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? $_POST;

$action = trim($data['action'] ?? '');

// ==========================================
// 1. CALCULATE FEES & INSTALLMENT BREAKDOWN
// ==========================================
if ($action === 'calculate_fee') {
    $course_title = trim($data['course'] ?? '');
    
    // Fetch course details
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE title = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$course_title]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Course not found.']);
        exit;
    }

    // Parse Fee: extract digits e.g. "₹45,000" => 45000
    $raw_fee = preg_replace('/[^0-9]/', '', $course['fee'] ?? '');
    $base_fee = !empty($raw_fee) ? (float)$raw_fee : 35000.00;

    // Get discount setting from Admin
    $discount_percent = (float)getSetting('full_payment_discount_percent', '10');
    $discount_amount = ($base_fee * $discount_percent) / 100.0;
    $full_pay_final = $base_fee - $discount_amount;

    // Determine number of installments based on duration
    $dur_lower = strtolower($course['duration'] ?? '');
    $total_parts = 1;

    if (strpos($dur_lower, '12') !== false || strpos($dur_lower, '1 year') !== false) {
        $total_parts = 4; // 1 Year => 4 Installments (Quarterly)
    } elseif (strpos($dur_lower, '6') !== false) {
        $total_parts = 3; // 6 Months => 3 Installments (Bi-monthly)
    } elseif (strpos($dur_lower, '3') !== false || strpos($dur_lower, '2') !== false) {
        $total_parts = 2; // 2-3 Months => 2 Installments
    } else {
        $total_parts = 2; // Default for multi-month courses
    }

    // Installment amount (Part 1 upfront)
    $part_amount = round($base_fee / $total_parts, 2);

    // Build installment schedule preview
    $schedule = [];
    $months_interval = max(1, (int)(12 / max(1, $total_parts)));
    if (strpos($dur_lower, '6') !== false) $months_interval = 2;
    if (strpos($dur_lower, '3') !== false || strpos($dur_lower, '2') !== false) $months_interval = 1;

    for ($i = 1; $i <= $total_parts; $i++) {
        $due_date = ($i === 1) ? date('Y-m-d') : date('Y-m-d', strtotime("+" . (($i - 1) * $months_interval) . " months"));
        $schedule[] = [
            'part' => $i,
            'title' => ($i === 1) ? '1st Installment (Admission Down-Payment)' : ($i . 'th Installment Due'),
            'amount' => $part_amount,
            'due_date' => date('d M Y', strtotime($due_date)),
            'raw_due_date' => $due_date,
            'is_now' => ($i === 1)
        ];
    }

    echo json_encode([
        'success' => true,
        'course' => [
            'id' => $course['id'],
            'title' => $course['title'],
            'duration' => $course['duration'],
            'raw_fee' => $base_fee,
            'formatted_fee' => '₹' . number_format($base_fee, 2)
        ],
        'full_payment' => [
            'discount_percent' => $discount_percent,
            'discount_amount' => $discount_amount,
            'formatted_discount' => '₹' . number_format($discount_amount, 2),
            'final_amount' => $full_pay_final,
            'formatted_final' => '₹' . number_format($full_pay_final, 2)
        ],
        'installment_payment' => [
            'total_parts' => $total_parts,
            'part_amount' => $part_amount,
            'formatted_part_amount' => '₹' . number_format($part_amount, 2),
            'total_payable' => $base_fee,
            'schedule' => $schedule
        ]
    ]);
    exit;
}

// ==========================================
// 2. VERIFY PAYMENT & FINALIZE ADMISSION
// ==========================================
if ($action === 'verify_course_fee') {
    $admission_id = (int)($data['admission_id'] ?? 0);
    $payment_id   = trim($data['razorpay_payment_id'] ?? '');
    $plan_type    = trim($data['plan_type'] ?? 'full'); // 'full' or 'installment'
    $paid_amount  = (float)($data['paid_amount'] ?? 0);

    if ($admission_id <= 0 || empty($payment_id) || $paid_amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment confirmation data.']);
        exit;
    }

    // Lookup admission
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? LIMIT 1");
    $stmt->execute([$admission_id]);
    $admission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admission) {
        echo json_encode(['success' => false, 'message' => 'Admission application record not found.']);
        exit;
    }

    // Lookup course
    $c_stmt = $pdo->prepare("SELECT * FROM courses WHERE title = ? LIMIT 1");
    $c_stmt->execute([$admission['course']]);
    $course = $c_stmt->fetch(PDO::FETCH_ASSOC);

    $raw_fee = preg_replace('/[^0-9]/', '', $course['fee'] ?? '');
    $base_fee = !empty($raw_fee) ? (float)$raw_fee : 35000.00;

    $discount_percent = (float)getSetting('full_payment_discount_percent', '10');
    $discount_amount = ($plan_type === 'full') ? (($base_fee * $discount_percent) / 100.0) : 0.00;
    $final_course_total = $base_fee - $discount_amount;
    $pending_balance = max(0, $final_course_total - $paid_amount);

    $receipt_no = "REC-" . strtoupper(substr(md5($admission_id . time() . rand(100,999)), 0, 8));

    try {
        $pdo->beginTransaction();

        // 1. Update Admission Table Status to Approved / Active
        $upd_adm = $pdo->prepare("UPDATE admissions SET 
            status = 'Approved',
            fee_total = ?,
            fee_paid = ?,
            fee_pending = ?,
            payment_plan = ?,
            payment_status = ?
            WHERE id = ?");
        
        $p_status = ($pending_balance <= 0) ? 'Fully Paid' : 'Partial Paid';
        $upd_adm->execute([$final_course_total, $paid_amount, $pending_balance, $plan_type, $p_status, $admission_id]);

        // 2. Also Update Student Account if logged in
        $student_db_id = (int)($_SESSION['student_db_id'] ?? 0);
        if ($student_db_id > 0) {
            $upd_std = $pdo->prepare("UPDATE students SET 
                seat_status = 'Confirmed',
                payment_status = 'Paid',
                payment_id = ?,
                payment_amount = payment_amount + ?,
                payment_date = NOW()
                WHERE id = ?");
            $upd_std->execute([$payment_id, $paid_amount, $student_db_id]);
        }

        // 3. Insert Installments Schedule in student_installments
        $next_due_date = null;
        $next_due_amount = 0;

        if ($plan_type === 'full') {
            // Single Paid Record
            $ins_stmt = $pdo->prepare("INSERT INTO student_installments 
                (student_db_id, student_id, admission_id, course_title, total_course_fee, discount_amount, final_payable, payment_type, total_parts, installment_no, installment_title, installment_amount, due_date, paid_date, status, payment_id, receipt_no) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'full', 1, 1, 'Full Course Fee Payment', ?, CURDATE(), NOW(), 'Paid', ?, ?)");
            $ins_stmt->execute([
                $student_db_id, $admission['student_id'] ?: 'FS-GUEST', $admission_id, $admission['course'],
                $base_fee, $discount_amount, $final_course_total, $paid_amount, $payment_id, $receipt_no
            ]);
        } else {
            // Multi-part Installments
            $dur_lower = strtolower($course['duration'] ?? '');
            $total_parts = 2;
            if (strpos($dur_lower, '12') !== false || strpos($dur_lower, '1 year') !== false) {
                $total_parts = 4;
            } elseif (strpos($dur_lower, '6') !== false) {
                $total_parts = 3;
            }

            $part_amount = round($base_fee / $total_parts, 2);
            $months_interval = max(1, (int)(12 / max(1, $total_parts)));
            if (strpos($dur_lower, '6') !== false) $months_interval = 2;
            if (strpos($dur_lower, '3') !== false || strpos($dur_lower, '2') !== false) $months_interval = 1;

            for ($i = 1; $i <= $total_parts; $i++) {
                $due_d = ($i === 1) ? date('Y-m-d') : date('Y-m-d', strtotime("+" . (($i - 1) * $months_interval) . " months"));
                $is_p1 = ($i === 1);
                $st = $is_p1 ? 'Paid' : 'Pending';
                $p_id = $is_p1 ? $payment_id : null;
                $r_no = $is_p1 ? $receipt_no : null;
                $p_date = $is_p1 ? date('Y-m-d H:i:s') : null;

                if ($i === 2) {
                    $next_due_date = $due_d;
                    $next_due_amount = $part_amount;
                }

                $ins_stmt = $pdo->prepare("INSERT INTO student_installments 
                    (student_db_id, student_id, admission_id, course_title, total_course_fee, discount_amount, final_payable, payment_type, total_parts, installment_no, installment_title, installment_amount, due_date, paid_date, status, payment_id, receipt_no) 
                    VALUES (?, ?, ?, ?, ?, 0, ?, 'installment', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $title_str = ($i === 1) ? '1st Installment (Admission Down-Payment)' : ($i . 'th Installment');
                $ins_stmt->execute([
                    $student_db_id, $admission['student_id'] ?: 'FS-GUEST', $admission_id, $admission['course'],
                    $base_fee, $base_fee, $total_parts, $i, $title_str, $part_amount, $due_d, $p_date, $st, $p_id, $r_no
                ]);
            }
        }

        // 4. Log in fee_submissions table for accounting
        $fee_log = $pdo->prepare("INSERT INTO fee_submissions 
            (name, contact, email, fee_type, purpose, course, receipt, status, is_read) 
            VALUES (?, ?, ?, 'Course Admission Fee', ?, ?, ?, 'Verified', 0)");
        $purpose_txt = ($plan_type === 'full') ? "Full Course Fee (Paid in Full)" : "Course Fee Part 1 / Down Payment";
        $fee_log->execute([$admission['name'], $admission['mobile'], $admission['email'], $purpose_txt, $admission['course'], "Razorpay: {$payment_id} | Receipt: {$receipt_no}"]);

        $pdo->commit();

        // 5. Send automated confirmation and official receipt email
        $receiptDetails = [
            'receipt_no'       => $receipt_no,
            'plan_title'       => ($plan_type === 'full') ? 'Full Course Fee (10% Discount Applied)' : 'Part-wise Installment Plan',
            'total_fee'        => $base_fee,
            'discount_amount'  => $discount_amount,
            'paid_amount'      => $paid_amount,
            'payment_id'       => $payment_id,
            'pending_balance'  => $pending_balance,
            'next_due_date'    => $next_due_date,
            'next_due_amount'  => $next_due_amount
        ];

        sendCourseFeeReceiptEmail(
            $admission['email'],
            $admission['name'],
            $admission['student_id'] ?: 'FS-STUDENT',
            $admission['course'],
            $receiptDetails
        );

        // 6. Send Instant Payment & Installment Notification to Admin
        sendAdminFeePaymentNotificationEmail(
            [
                'name'       => $admission['name'],
                'student_id' => $admission['student_id'] ?: 'FS-STUDENT',
                'mobile'     => $admission['mobile'],
                'email'      => $admission['email']
            ],
            [
                'title'    => $admission['course'],
                'duration' => $course['duration'] ?? 'N/A'
            ],
            $receiptDetails
        );

        echo json_encode([
            'success' => true,
            'message' => 'Payment verified successfully! Admission confirmed.',
            'receipt_no' => $receipt_no,
            'admission_id' => $admission_id,
            'course' => $admission['course'],
            'paid_amount' => $paid_amount
        ]);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Payment verification error: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action request.']);
exit;

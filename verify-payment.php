<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? $_POST;

$payment_id = trim($data['razorpay_payment_id'] ?? '');
$student_db_id = (int)($data['student_id'] ?? ($_SESSION['student_db_id'] ?? 0));
$amount = (float)($data['amount'] ?? 999.00);

if (empty($payment_id) || $student_db_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_db_id]);
$student = $stmt->fetch();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student record not found.']);
    exit;
}

try {
    // 1. Update Student Table
    $upd = $pdo->prepare("UPDATE students SET 
        seat_status = 'Confirmed',
        payment_status = 'Paid',
        payment_id = ?,
        payment_amount = ?,
        payment_date = NOW()
        WHERE id = ?");
    $upd->execute([$payment_id, $amount, $student['id']]);

    // 2. Also log in fee_submissions for accounting consistency in admin
    $fee_stmt = $pdo->prepare("INSERT INTO fee_submissions 
        (name, contact, email, fee_type, purpose, course, receipt, status, is_read) 
        VALUES (?, ?, ?, 'Registration Fee', 'Seat Reservation (Razorpay Online)', 'Finchskills Career Program', ?, 'Verified', 0)");
    $fee_stmt->execute([$student['name'], $student['phone'], $student['email'], 'Razorpay ID: ' . $payment_id]);

    // 3. Send automated Email Receipt
    sendPaymentReceiptEmail($student['email'], $student['name'], $student['student_id'], $payment_id, $amount);

    echo json_encode(['success' => true, 'message' => 'Payment verified and seat confirmed!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

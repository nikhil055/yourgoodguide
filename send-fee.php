<?php
require_once __DIR__ . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $contact  = trim($_POST['contact'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $fee_type = trim($_POST['fee_type'] ?? '');
    $purpose  = trim($_POST['purpose'] ?? '');
    $course   = trim($_POST['course'] ?? '');

    // Handle File Uploads
    $upload_dir = __DIR__ . '/uploads/fees/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $receipt_file   = '';
    $signature_file = '';

    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] == 0) {
        $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
        $receipt_file = 'receipt_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['receipt']['tmp_name'], $upload_dir . $receipt_file);
    }

    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $ext = pathinfo($_FILES['signature']['name'], PATHINFO_EXTENSION);
        $signature_file = 'sign_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['signature']['tmp_name'], $upload_dir . $signature_file);
    }

    // 1. Save to Database
    try {
        $stmt = $pdo->prepare("INSERT INTO fee_submissions (name, contact, email, fee_type, purpose, course, receipt, signature, status, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 0, NOW())");
        $stmt->execute([
            $name, $contact, $email, $fee_type, $purpose, $course, $receipt_file, $signature_file
        ]);
    } catch (Exception $e) {
        // Log DB error if needed
    }

    // 2. Send Email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "finchskillsinstitute@gmail.com";
        $mail->Password = "kzsiuskuxqjjaglk";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom("finchskillsinstitute@gmail.com", "Finchskills Institute");
        $mail->addAddress("finchskillsinstitute@gmail.com");

        if (!empty($email)) {
            $mail->addReplyTo($email, $name);
        }

        if (!empty($receipt_file) && file_exists($upload_dir . $receipt_file)) {
            $mail->addAttachment($upload_dir . $receipt_file, $_FILES['receipt']['name']);
        }
        if (!empty($signature_file) && file_exists($upload_dir . $signature_file)) {
            $mail->addAttachment($upload_dir . $signature_file, $_FILES['signature']['name']);
        }

        $mail->isHTML(true);
        $mail->Subject = "New Fee Submission: " . htmlspecialchars($name);
        $mail->Body = "
        <h2>Fee Submission Details</h2>
        <table border='1' cellpadding='10' cellspacing='0' width='100%'>
            <tr><td><b>Name</b></td><td>" . htmlspecialchars($name) . "</td></tr>
            <tr><td><b>Contact</b></td><td>" . htmlspecialchars($contact) . "</td></tr>
            <tr><td><b>Email</b></td><td>" . htmlspecialchars($email) . "</td></tr>
            <tr><td><b>Fee Type</b></td><td>" . htmlspecialchars($fee_type) . "</td></tr>
            <tr><td><b>Purpose</b></td><td>" . htmlspecialchars($purpose) . "</td></tr>
            <tr><td><b>Course</b></td><td>" . htmlspecialchars($course) . "</td></tr>
        </table>";

        $mail->send();
    } catch (Exception $e) {
        // Handled
    }

    header("Location: thank-you.php");
    exit;
} else {
    header("Location: fee-submission.php");
    exit;
}
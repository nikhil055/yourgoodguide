<?php
require_once __DIR__ . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Website Contact Inquiry');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        header("Location: contact.php?error=Please+fill+all+required+fields");
        exit;
    }

    // 1. Save to Database
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message, status, is_read, created_at) VALUES (?, ?, ?, ?, ?, 'New', 0, NOW())");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
    } catch (Exception $e) {
        // Log DB Error
    }

    // 2. Send Email Notification to Admin
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

        $mail->isHTML(true);
        $mail->Subject = "New Contact Inquiry: " . htmlspecialchars($subject);
        $mail->Body = "
        <h2>New Contact Inquiry Received</h2>
        <table border='1' cellpadding='10' cellspacing='0' width='100%'>
            <tr><td><b>Name</b></td><td>" . htmlspecialchars($name) . "</td></tr>
            <tr><td><b>Email</b></td><td>" . htmlspecialchars($email) . "</td></tr>
            <tr><td><b>Phone</b></td><td>" . htmlspecialchars($phone) . "</td></tr>
            <tr><td><b>Subject</b></td><td>" . htmlspecialchars($subject) . "</td></tr>
            <tr><td><b>Message</b></td><td>" . nl2br(htmlspecialchars($message)) . "</td></tr>
        </table>";

        $mail->send();
    } catch (Exception $e) {
        // Mail handled
    }

    header("Location: contact.php?success=1#contact-form");
    exit;
} else {
    header("Location: contact.php");
    exit;
}

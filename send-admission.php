<?php
require_once __DIR__ . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $mobile      = trim($_POST['mobile'] ?? '');
    $aadhaar     = trim($_POST['aadhaar'] ?? '');
    $dob         = trim($_POST['dob'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $course      = trim($_POST['course'] ?? '');
    $education   = trim($_POST['education'] ?? '');
    $state       = trim($_POST['state'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $post_office = trim($_POST['post_office'] ?? '');
    $pincode     = trim($_POST['pincode'] ?? '');
    $address     = trim($_POST['address'] ?? '');

    // Handle File Uploads
    $upload_dir = __DIR__ . '/uploads/admissions/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $marksheet10_file = '';
    $marksheet12_file = '';
    $aadhaar_file     = '';
    $photo_file       = '';

    if (isset($_FILES['marksheet10']) && $_FILES['marksheet10']['error'] == 0) {
        $ext = pathinfo($_FILES['marksheet10']['name'], PATHINFO_EXTENSION);
        $marksheet10_file = 'm10_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['marksheet10']['tmp_name'], $upload_dir . $marksheet10_file);
    }

    if (isset($_FILES['marksheet12']) && $_FILES['marksheet12']['error'] == 0) {
        $ext = pathinfo($_FILES['marksheet12']['name'], PATHINFO_EXTENSION);
        $marksheet12_file = 'm12_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['marksheet12']['tmp_name'], $upload_dir . $marksheet12_file);
    }

    if (isset($_FILES['aadhaar_card']) && $_FILES['aadhaar_card']['error'] == 0) {
        $ext = pathinfo($_FILES['aadhaar_card']['name'], PATHINFO_EXTENSION);
        $aadhaar_file = 'aadh_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['aadhaar_card']['tmp_name'], $upload_dir . $aadhaar_file);
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_file = 'photo_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_file);
    }

    // 1. Save to Database
    try {
        $stmt = $pdo->prepare("INSERT INTO admissions (name, father_name, email, mobile, aadhaar, dob, gender, course, education, state, city, post_office, pincode, address, marksheet10, marksheet12, aadhaar_card, photo, status, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 0, NOW())");
        $stmt->execute([
            $name, $father_name, $email, $mobile, $aadhaar, $dob, $gender, $course, $education, $state, $city, $post_office, $pincode, $address,
            $marksheet10_file, $marksheet12_file, $aadhaar_file, $photo_file
        ]);
    } catch (Exception $e) {
        // Log DB error if any
    }

    // 2. Send Email via PHPMailer
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

        // Attachments from uploaded files
        if (!empty($marksheet10_file) && file_exists($upload_dir . $marksheet10_file)) {
            $mail->addAttachment($upload_dir . $marksheet10_file, $_FILES['marksheet10']['name']);
        }
        if (!empty($marksheet12_file) && file_exists($upload_dir . $marksheet12_file)) {
            $mail->addAttachment($upload_dir . $marksheet12_file, $_FILES['marksheet12']['name']);
        }
        if (!empty($aadhaar_file) && file_exists($upload_dir . $aadhaar_file)) {
            $mail->addAttachment($upload_dir . $aadhaar_file, $_FILES['aadhaar_card']['name']);
        }
        if (!empty($photo_file) && file_exists($upload_dir . $photo_file)) {
            $mail->addAttachment($upload_dir . $photo_file, $_FILES['photo']['name']);
        }

        $mail->isHTML(true);
        $mail->Subject = "New Admission Form: " . htmlspecialchars($name);
        $mail->Body = "
        <h2>New Admission Form</h2>
        <table border='1' cellpadding='8' cellspacing='0' width='100%'>
            <tr><td><b>Name</b></td><td>" . htmlspecialchars($name) . "</td></tr>
            <tr><td><b>Father Name</b></td><td>" . htmlspecialchars($father_name) . "</td></tr>
            <tr><td><b>Email</b></td><td>" . htmlspecialchars($email) . "</td></tr>
            <tr><td><b>Mobile</b></td><td>" . htmlspecialchars($mobile) . "</td></tr>
            <tr><td><b>Aadhaar</b></td><td>" . htmlspecialchars($aadhaar) . "</td></tr>
            <tr><td><b>DOB</b></td><td>" . htmlspecialchars($dob) . "</td></tr>
            <tr><td><b>Gender</b></td><td>" . htmlspecialchars($gender) . "</td></tr>
            <tr><td><b>Course</b></td><td>" . htmlspecialchars($course) . "</td></tr>
            <tr><td><b>Education</b></td><td>" . htmlspecialchars($education) . "</td></tr>
            <tr><td><b>State</b></td><td>" . htmlspecialchars($state) . "</td></tr>
            <tr><td><b>City</b></td><td>" . htmlspecialchars($city) . "</td></tr>
            <tr><td><b>Post Office</b></td><td>" . htmlspecialchars($post_office) . "</td></tr>
            <tr><td><b>Pincode</b></td><td>" . htmlspecialchars($pincode) . "</td></tr>
            <tr><td><b>Address</b></td><td>" . htmlspecialchars($address) . "</td></tr>
        </table>";

        $mail->send();
    } catch (Exception $e) {
        // Mail error handled silently if DB is saved or logged
    }

    header("Location: form-submission.php?success=1");
    exit;
} else {
    header("Location: form-submission.php");
    exit;
}

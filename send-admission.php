<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id       = trim($_POST['student_id'] ?? ($_SESSION['student_id'] ?? ''));
    $name             = trim($_POST['name'] ?? '');
    $father_name      = trim($_POST['father_name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $mobile           = trim($_POST['mobile'] ?? '');
    $aadhaar          = trim($_POST['aadhaar'] ?? '');
    $dob              = trim($_POST['dob'] ?? '');
    $gender           = trim($_POST['gender'] ?? '');
    $course           = trim($_POST['course'] ?? '');
    $education        = trim($_POST['education'] ?? '');
    $board_university = trim($_POST['board_university'] ?? '');
    $passing_year     = trim($_POST['passing_year'] ?? '');
    $percentage       = trim($_POST['percentage'] ?? '');
    $state            = trim($_POST['state'] ?? '');
    $city             = trim($_POST['city'] ?? '');
    $post_office      = trim($_POST['post_office'] ?? '');
    $pincode          = trim($_POST['pincode'] ?? '');
    $address          = trim($_POST['address'] ?? '');

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

    // Photo: If new file uploaded, use it. Otherwise use existing photo from registration
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_file = 'photo_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_file);
    } elseif (!empty($_POST['existing_photo'])) {
        $photo_file = trim($_POST['existing_photo']);
    }

    $application_id = 0;

    // 1. Save to Database
    try {
        $stmt = $pdo->prepare("INSERT INTO admissions 
            (student_id, name, father_name, email, mobile, aadhaar, dob, gender, course, education, board_university, passing_year, percentage, state, city, post_office, pincode, address, marksheet10, marksheet12, aadhaar_card, photo, status, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 0, NOW())");
        $stmt->execute([
            $student_id, $name, $father_name, $email, $mobile, $aadhaar, $dob, $gender, 
            $course, $education, $board_university, $passing_year, $percentage,
            $state, $city, $post_office, $pincode, $address,
            $marksheet10_file, $marksheet12_file, $aadhaar_file, $photo_file
        ]);
        $application_id = (int)$pdo->lastInsertId();
    } catch (Exception $e) {
        // Log DB error
        @file_put_contents(__DIR__ . '/uploads/email_logs.txt', "[Admission DB Error] " . $e->getMessage() . "\n", FILE_APPEND);
    }

    $app_ref = "ADM-" . str_pad($application_id ?: rand(100, 999), 5, '0', STR_PAD_LEFT);

    // 2. Prepare Email Notification to Admin / Institute
    $admin_subject = "New Online Application: {$name} - {$course} ({$app_ref})";
    $admin_html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: #0e1e2e; color: #fff; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>New Admission Application</h2>
            <p style='margin: 5px 0 0; color: #fe7c03;'>Ref: {$app_ref}</p>
        </div>
        <div style='padding: 24px; background: #fff;'>
            <table border='1' cellpadding='8' cellspacing='0' width='100%' style='border-collapse: collapse; border-color: #e2e8f0;'>
                " . (!empty($student_id) ? "<tr><td width='35%' style='background:#f8fafc;'><b>Student ID</b></td><td><b>{$student_id}</b></td></tr>" : "") . "
                <tr><td width='35%' style='background:#f8fafc;'><b>Applicant Name</b></td><td>{$name}</td></tr>
                <tr><td style='background:#f8fafc;'><b>Father's Name</b></td><td>{$father_name}</td></tr>
                <tr><td style='background:#f8fafc;'><b>Course Applied</b></td><td><b style='color:#fe7c03;'>{$course}</b></td></tr>
                <tr><td style='background:#f8fafc;'><b>Email</b></td><td>{$email}</td></tr>
                <tr><td style='background:#f8fafc;'><b>Mobile (WhatsApp)</b></td><td>{$mobile}</td></tr>
                <tr><td style='background:#f8fafc;'><b>Aadhaar</b></td><td>{$aadhaar}</td></tr>
                <tr><td style='background:#f8fafc;'><b>DOB & Gender</b></td><td>{$dob} ({$gender})</td></tr>
                <tr><td style='background:#f8fafc;'><b>Qualification</b></td><td>{$education} ({$board_university} - {$passing_year} - {$percentage})</td></tr>
                <tr><td style='background:#f8fafc;'><b>Location</b></td><td>{$city}, {$state} - {$pincode}</td></tr>
                <tr><td style='background:#f8fafc;'><b>Full Address</b></td><td>{$address}</td></tr>
            </table>
        </div>
    </div>";

    // Gather file attachments for email
    $attachments = [];
    if (!empty($marksheet10_file) && file_exists($upload_dir . $marksheet10_file)) {
        $attachments[] = ['path' => $upload_dir . $marksheet10_file, 'name' => '10th_Marksheet_' . $marksheet10_file];
    }
    if (!empty($marksheet12_file) && file_exists($upload_dir . $marksheet12_file)) {
        $attachments[] = ['path' => $upload_dir . $marksheet12_file, 'name' => '12th_Marksheet_' . $marksheet12_file];
    }
    if (!empty($aadhaar_file) && file_exists($upload_dir . $aadhaar_file)) {
        $attachments[] = ['path' => $upload_dir . $aadhaar_file, 'name' => 'Aadhaar_Card_' . $aadhaar_file];
    }
    if (!empty($photo_file)) {
        $p_path = file_exists($upload_dir . $photo_file) ? ($upload_dir . $photo_file) : (__DIR__ . '/' . $photo_file);
        if (file_exists($p_path)) {
            $attachments[] = ['path' => $p_path, 'name' => 'Passport_Photo_' . basename($p_path)];
        }
    }

    // Send to Admin
    $smtp_cfg = getSmtpConfig();
    $admin_inbox = !empty($smtp_cfg['smtp_username']) ? $smtp_cfg['smtp_username'] : 'finchskillsinstitute@gmail.com';
    sendFinchMail($admin_inbox, $admin_subject, $admin_html, ['attachments' => $attachments]);

    // 3. Send Confirmation Email to Candidate
    if (!empty($email)) {
        $candidate_subject = "Application Received - {$course} (Ref: {$app_ref}) - Finchskills";
        $candidate_html = "
        <div style='font-family: Arial, sans-serif; max-width: 540px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
            <div style='background: #0e1e2e; color: #fff; padding: 24px; text-align: center;'>
                <h2 style='margin: 0; font-size: 20px;'>FINCHSKILLS INSTITUTE</h2>
                <p style='margin: 4px 0 0; color: #fe7c03; font-weight: bold;'>Application Submission Confirmed</p>
            </div>
            <div style='padding: 26px; background: #fff;'>
                <h3 style='margin-top: 0; color: #0e1e2e;'>Dear {$name},</h3>
                <p style='color: #475569; font-size: 14px; line-height: 1.6;'>
                    Thank you for applying to <strong>{$course}</strong> at Finchskills Institute. We have successfully received your admission documents and application details.
                </p>
                <div style='background: #f8fafc; border: 1.5px solid #fe7c03; border-radius: 8px; padding: 16px; margin: 20px 0;'>
                    <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                        <span style='color: #64748b; font-size: 13px;'>Application Ref No:</span>
                        <strong style='color: #fe7c03; font-size: 14px;'>{$app_ref}</strong>
                    </div>
                    " . (!empty($student_id) ? "
                    <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                        <span style='color: #64748b; font-size: 13px;'>Student ID:</span>
                        <strong style='color: #0e1e2e; font-size: 13.5px;'>{$student_id}</strong>
                    </div>" : "") . "
                    <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                        <span style='color: #64748b; font-size: 13px;'>Selected Course:</span>
                        <strong style='color: #0e1e2e; font-size: 13.5px;'>{$course}</strong>
                    </div>
                    <div style='display: flex; justify-content: space-between;'>
                        <span style='color: #64748b; font-size: 13px;'>Status:</span>
                        <span style='background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;'>Under Review</span>
                    </div>
                </div>
                <p style='color: #475569; font-size: 13.5px; line-height: 1.5;'>
                    Our admissions counselor will review your credentials and contact you within 24 hours to confirm your batch allocation.
                </p>
            </div>
            <div style='background: #f8fafc; padding: 14px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;'>
                Helpline: +91 96503 86711 | hello@finchskills.com &bull; Finchskills Institute
            </div>
        </div>";

        sendFinchMail($email, $candidate_subject, $candidate_html);
    }

    // Check if request is AJAX (FormData submission from modern wizard)
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' || isset($_POST['ajax_submit']);

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted! Please proceed to fee payment.',
            'admission_id' => $application_id,
            'app_ref' => $app_ref,
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'course' => $course
        ]);
        exit;
    }

    // 4. Fallback standard redirect
    header("Location: thank-you.php?type=admission&ref=" . urlencode($app_ref) . "&course=" . urlencode($course));
    exit;
} else {
    header("Location: form-submission.php");
    exit;
}

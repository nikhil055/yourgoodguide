<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

$type   = $_GET['type'] ?? '';
$ref    = htmlspecialchars($_GET['ref'] ?? '');
$course = htmlspecialchars($_GET['course'] ?? '');
$isLoggedIn = isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Received - Finchskills Institute</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Plus Jakarta Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #0e1e2e;
        }
        .thankyou-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 42px 36px;
            max-width: 580px;
            margin: 40px auto;
            text-align: center;
        }
        .success-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f0fdf4;
            color: #16a34a;
            border: 2px solid #bbf7d0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .app-slip-box {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 10px;
            padding: 18px 24px;
            margin: 24px 0;
            text-align: left;
        }
        .slip-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13.5px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .slip-row:last-child {
            border-bottom: none;
        }
        .btn-brand {
            background: #fe7c03;
            color: #fff !important;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-brand:hover {
            background: #ea6c00;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="thankyou-card">
        <div class="success-icon-wrap">
            <i class="fa-solid fa-check"></i>
        </div>

        <?php if ($type === 'admission'): ?>
            <h2 class="fw-bold text-dark mb-1">Application Received!</h2>
            <p class="text-muted small">Your course admission application has been successfully submitted and logged in our system.</p>

            <div class="app-slip-box">
                <?php if (!empty($ref)): ?>
                    <div class="slip-row">
                        <span class="text-muted">Application Ref No:</span>
                        <strong class="font-monospace text-primary"><?= $ref ?></strong>
                    </div>
                <?php endif; ?>

                <?php if (!empty($course)): ?>
                    <div class="slip-row">
                        <span class="text-muted">Course Program:</span>
                        <strong class="text-dark"><?= $course ?></strong>
                    </div>
                <?php endif; ?>

                <div class="slip-row">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Under Counselor Review</span>
                </div>

                <div class="slip-row">
                    <span class="text-muted">Confirmation Email:</span>
                    <span class="text-success fw-semibold"><i class="fa-regular fa-envelope me-1"></i> Dispatched to Inbox</span>
                </div>
            </div>

            <p class="small text-muted mb-4">
                Our admissions counselor will review your uploaded documents and get in touch with you shortly on your WhatsApp number.
            </p>

            <div class="d-flex justify-content-center flex-wrap gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="student-profile.php" class="btn-brand">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Go to My Profile</span>
                    </a>
                <?php endif; ?>
                <a href="courses.php" class="btn btn-outline-secondary px-3 py-2 fw-semibold" style="border-radius: 8px;">
                    Browse More Courses
                </a>
                <a href="index.php" class="btn btn-light border px-3 py-2 fw-semibold" style="border-radius: 8px;">
                    Home
                </a>
            </div>

        <?php else: ?>
            <h2 class="fw-bold text-dark mb-1">Thank You!</h2>
            <p class="text-muted small mt-2">
                Your details have been submitted successfully. Our support team will verify and get back to you shortly.
            </p>
            <div class="mt-4">
                <a href="index.php" class="btn-brand">
                    <span>Back to Home</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
/**
 * Finchskills Institute Central Email Dispatcher (PHPMailer + Dynamic SMTP)
 * Reusable across the entire website (OTP, Admissions, Fees, Contact, Notifications)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    if (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
        require_once __DIR__ . '/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    }
}

/**
 * Fetch dynamic SMTP settings from database site_settings table
 */
function getSmtpConfig() {
    global $pdo;
    if (!isset($pdo) || $pdo === null) {
        require_once __DIR__ . '/db.php';
    }

    // Default Fallback Settings
    $settings = [
        'smtp_enabled'    => '1',
        'smtp_host'       => 'smtp.gmail.com',
        'smtp_port'       => '587',
        'smtp_secure'     => 'tls',
        'smtp_username'   => 'finchskillsinstitute@gmail.com',
        'smtp_password'   => 'kzsiuskuxqjjaglk',
        'smtp_from_name'  => 'Finchskills Institute',
        'smtp_from_email' => 'finchskillsinstitute@gmail.com',
        'smtp_reply_to'   => 'hello@finchskills.com'
    ];

    try {
        if ($pdo) {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'smtp_%'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
    } catch (Exception $e) {
        // Fallback to array defaults
    }

    return $settings;
}

/**
 * Core Universal Email Dispatcher
 * Can be used anywhere on the website
 * 
 * @param string|array $to Single email or array of emails
 * @param string $subject Email subject line
 * @param string $htmlMessage Full HTML email content
 * @param array $options Optional: ['reply_to' => '', 'reply_name' => '', 'attachments' => [], 'throw_error' => false]
 * @return array ['success' => bool, 'message' => string]
 */
function sendFinchMail($to, $subject, $htmlMessage, $options = []) {
    $cfg = getSmtpConfig();

    // 1. Audit / Debug Log locally in uploads/email_logs.txt
    $log_dir = __DIR__ . '/uploads';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0777, true);
    }
    $to_str = is_array($to) ? implode(', ', $to) : $to;
    $log_entry = "[" . date('Y-m-d H:i:s') . "] TO: {$to_str} | SUBJECT: {$subject}\n" . strip_tags($htmlMessage) . "\n----------------------------------------\n";
    @file_put_contents($log_dir . '/email_logs.txt', $log_entry, FILE_APPEND);

    // 2. Real Send via PHPMailer with dynamic SMTP
    $smtp_enabled = ($cfg['smtp_enabled'] ?? '1') === '1';
    if ($smtp_enabled && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = !empty($cfg['smtp_host']) ? trim($cfg['smtp_host']) : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = trim($cfg['smtp_username'] ?? '');
            $mail->Password   = trim($cfg['smtp_password'] ?? '');
            $mail->Port       = (int)(!empty($cfg['smtp_port']) ? $cfg['smtp_port'] : 587);

            // Security Encryption
            $sec = strtolower(trim($cfg['smtp_secure'] ?? 'tls'));
            if ($sec === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($sec === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAutoTLS = false;
                $mail->SMTPSecure  = false;
            }

            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 15;

            // Allow self-signed certificates for custom webmail domains
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // From header
            $fromEmail = !empty($cfg['smtp_from_email']) ? trim($cfg['smtp_from_email']) : $cfg['smtp_username'];
            $fromName  = !empty($cfg['smtp_from_name']) ? trim($cfg['smtp_from_name']) : 'Finchskills Institute';
            $mail->setFrom($fromEmail, $fromName);

            // Recipients
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    if (!empty($recipient)) $mail->addAddress(trim($recipient));
                }
            } else {
                $mail->addAddress(trim($to));
            }

            // Reply-To
            $replyTo = $options['reply_to'] ?? ($cfg['smtp_reply_to'] ?: $fromEmail);
            $replyName = $options['reply_name'] ?? $fromName;
            if (!empty($replyTo)) {
                $mail->addReplyTo($replyTo, $replyName);
            }

            // CC / BCC
            if (!empty($options['cc'])) {
                $mail->addCC($options['cc']);
            }
            if (!empty($options['bcc'])) {
                $mail->addBCC($options['bcc']);
            }

            // Attachments
            if (!empty($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $att) {
                    if (is_array($att) && !empty($att['path']) && file_exists($att['path'])) {
                        $mail->addAttachment($att['path'], $att['name'] ?? '');
                    } elseif (is_string($att) && file_exists($att)) {
                        $mail->addAttachment($att);
                    }
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlMessage;

            $mail->send();
            return ['success' => true, 'message' => 'Email sent successfully via PHPMailer.'];
        } catch (Exception $e) {
            $err = $mail->ErrorInfo ?: $e->getMessage();
            @file_put_contents($log_dir . '/email_logs.txt', "[SMTP Error] " . $err . "\n", FILE_APPEND);

            if (!empty($options['throw_error'])) {
                return ['success' => false, 'message' => $err];
            }
        }
    }

    // 3. Fallback to PHP native mail() if PHPMailer is disabled or fails
    $fromEmail = !empty($cfg['smtp_from_email']) ? $cfg['smtp_from_email'] : 'no-reply@finchskills.com';
    $fromName  = !empty($cfg['smtp_from_name']) ? $cfg['smtp_from_name'] : 'Finchskills Institute';
    $headers = [
        "MIME-Version: 1.0",
        "Content-type: text/html; charset=UTF-8",
        "From: {$fromName} <{$fromEmail}>",
        "Reply-To: " . ($cfg['smtp_reply_to'] ?? $fromEmail),
        "X-Mailer: PHP/" . phpversion()
    ];
    @mail($to_str, $subject, $htmlMessage, implode("\r\n", $headers));
    return ['success' => true, 'message' => 'Email dispatched via fallback mail()'];
}

/**
 * 1. Send Registration Verification OTP
 */
function sendRegistrationOtp($email, $name, $otp) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['debug_latest_otp'] = $otp;

    $subject = "Your Verification OTP - Finchskills Institute";
    $html = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f8fafc;margin:0;padding:20px;color:#334155;}.card{max-width:520px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}.header{background:#0e1e2e;padding:24px;text-align:center;color:#ffffff;}.body{padding:28px;}.otp-box{background:#fff7ed;border:1.5px dashed #fe7c03;border-radius:8px;padding:16px;text-align:center;font-size:30px;font-weight:800;letter-spacing:6px;color:#fe7c03;margin:20px 0;}.footer{background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;}</style></head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin:0;font-size:20px;letter-spacing:0.5px;color:#ffffff;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#fe7c03;font-weight:600;">Candidate Email Verification</p>
            </div>
            <div class="body">
                <p style="font-size:15px;margin-top:0;">Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
                <p style="font-size:14px;line-height:1.6;color:#475569;">Thank you for registering with Finchskills Institute. Please enter the following 6-digit One-Time Password (OTP) on the verification screen:</p>
                <div class="otp-box">' . htmlspecialchars($otp) . '</div>
                <p style="font-size:13px;color:#64748b;line-height:1.5;">This OTP is valid for <strong>15 minutes</strong>. For your security, please do not share this code with anyone.</p>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Finchskills Institute. All rights reserved.
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($email, $subject, $html);
}

/**
 * 2. Send Welcome & Unique Student ID Email
 */
function sendWelcomeEmail($email, $name, $studentId) {
    $subject = "Welcome to Finchskills! Your Student ID: {$studentId}";
    $login_url = "http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000') . "/student-login.php";

    $html = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f8fafc;margin:0;padding:20px;color:#334155;}.card{max-width:540px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}.header{background:#0e1e2e;padding:26px;text-align:center;color:#ffffff;}.body{padding:30px;}.id-card{background:#f8fafc;border:1.5px solid #fe7c03;border-radius:10px;padding:18px;margin:20px 0;}.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:13.5px;}.row:last-child{border-bottom:none;}.btn{display:inline-block;background:#fe7c03;color:#ffffff !important;text-decoration:none;padding:12px 26px;border-radius:6px;font-weight:700;font-size:14px;margin-top:10px;}.footer{background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;}</style></head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin:0;font-size:22px;letter-spacing:0.5px;color:#ffffff;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#fe7c03;font-weight:600;">Registration Confirmed</p>
            </div>
            <div class="body">
                <h3 style="margin-top:0;color:#0f172a;">Congratulations, ' . htmlspecialchars($name) . '!</h3>
                <p style="font-size:14px;line-height:1.6;color:#475569;">Your candidate registration with Finchskills Institute is officially verified and confirmed. Here are your credentials:</p>
                <div class="id-card">
                    <div class="row">
                        <span style="color:#64748b;">Unique Student ID:</span>
                        <strong style="color:#fe7c03;font-size:16px;letter-spacing:1px;">' . htmlspecialchars($studentId) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color:#64748b;">Full Name:</span>
                        <strong style="color:#0f172a;">' . htmlspecialchars($name) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color:#64748b;">Registered Email:</span>
                        <strong style="color:#0f172a;">' . htmlspecialchars($email) . '</strong>
                    </div>
                </div>
                <p style="font-size:13.5px;color:#475569;line-height:1.5;">You can use your Student ID or Email along with your chosen password to log in to your Candidate Portal anytime.</p>
                <div style="text-align:center;margin-top:24px;">
                    <a href="' . $login_url . '" class="btn">Log In to Candidate Portal &rarr;</a>
                </div>
            </div>
            <div class="footer">
                Need help? Contact helpline at +91 96503 86711 | &copy; ' . date('Y') . ' Finchskills Institute.
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($email, $subject, $html);
}

/**
 * 3. Send Forgot Password Reset OTP
 */
function sendForgotPasswordOtp($email, $name, $otp) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['debug_latest_otp'] = $otp;

    $subject = "Password Reset OTP - Finchskills Institute";
    $html = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f8fafc;margin:0;padding:20px;color:#334155;}.card{max-width:520px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}.header{background:#0e1e2e;padding:24px;text-align:center;color:#ffffff;}.body{padding:28px;}.otp-box{background:#fef2f2;border:1.5px dashed #ef4444;border-radius:8px;padding:16px;text-align:center;font-size:30px;font-weight:800;letter-spacing:6px;color:#ef4444;margin:20px 0;}.footer{background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;}</style></head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin:0;font-size:20px;color:#ffffff;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#f87171;font-weight:600;">Password Reset Request</p>
            </div>
            <div class="body">
                <p style="font-size:15px;margin-top:0;">Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
                <p style="font-size:14px;line-height:1.6;color:#475569;">We received a request to reset your candidate portal account password. Use this 6-digit OTP code:</p>
                <div class="otp-box">' . htmlspecialchars($otp) . '</div>
                <p style="font-size:13px;color:#64748b;line-height:1.5;">This OTP is valid for <strong>15 minutes</strong>. If you did not initiate this request, please ignore this email.</p>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Finchskills Institute. All rights reserved.
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($email, $subject, $html);
}

/**
 * Alias for sendForgotPasswordOtp
 */
function sendPasswordResetOtp($email, $name, $otp) {
    return sendForgotPasswordOtp($email, $name, $otp);
}

/**
 * 4. Send Payment / Seat Reservation Confirmation Receipt
 */
function sendPaymentReceiptEmail($email, $name, $studentId, $paymentId, $amount) {
    $subject = "Payment Receipt: Seat Reservation Confirmed - Finchskills";
    $formatted_amount = "₹" . number_format((float)$amount, 2);

    $html = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f8fafc;margin:0;padding:20px;color:#334155;}.card{max-width:540px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}.header{background:#0e1e2e;padding:26px;text-align:center;color:#ffffff;}.body{padding:30px;}.receipt-box{background:#f0fdf4;border:1.5px solid #22c55e;border-radius:10px;padding:20px;margin:20px 0;}.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #dcfce7;font-size:13.5px;}.row:last-child{border-bottom:none;}.badge{background:#22c55e;color:#fff;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}.footer{background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;}</style></head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin:0;font-size:22px;color:#ffffff;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#4ade80;font-weight:600;">Payment & Seat Confirmation Receipt</p>
            </div>
            <div class="body">
                <h3 style="margin-top:0;color:#0f172a;">Thank you, ' . htmlspecialchars($name) . '!</h3>
                <p style="font-size:14px;line-height:1.6;color:#475569;">Your registration fee has been successfully received via Razorpay. Your seat has been provisionally confirmed.</p>
                
                <div class="receipt-box">
                    <div class="row">
                        <span style="color:#166534;">Payment Status:</span>
                        <span class="badge">SUCCESSFUL</span>
                    </div>
                    <div class="row">
                        <span style="color:#166534;">Student ID:</span>
                        <strong style="color:#0f172a;">' . htmlspecialchars($studentId) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color:#166534;">Amount Paid:</span>
                        <strong style="color:#166534;font-size:16px;">' . htmlspecialchars($formatted_amount) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color:#166534;">Razorpay Payment ID:</span>
                        <span style="font-family:monospace;font-size:12px;color:#0f172a;">' . htmlspecialchars($paymentId) . '</span>
                    </div>
                    <div class="row">
                        <span style="color:#166534;">Date & Time:</span>
                        <span style="color:#0f172a;">' . date('d M Y, h:i A') . '</span>
                    </div>
                </div>
                <p style="font-size:13.5px;color:#475569;line-height:1.5;">You can download or print your updated provisional ID card anytime from your student profile.</p>
            </div>
            <div class="footer">
                Questions about your payment? Contact accounts@finchskills.com | &copy; ' . date('Y') . ' Finchskills Institute.
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($email, $subject, $html);
}

/**
 * 5. Send Detailed Course Admission Fee & Installment Receipt Email
 */
function sendCourseFeeReceiptEmail($email, $name, $studentId, $courseTitle, $receiptDetails) {
    $subject = "Official Fee Receipt & Admission Confirmed - {$courseTitle} (Receipt #{$receiptDetails['receipt_no']})";
    $paid_amt = "₹" . number_format((float)$receiptDetails['paid_amount'], 2);
    $total_fee = "₹" . number_format((float)$receiptDetails['total_fee'], 2);
    $pending_balance = "₹" . number_format((float)$receiptDetails['pending_balance'], 2);

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: Inter, Arial, sans-serif; background: #f8fafc; margin: 0; padding: 24px 12px; color: #334155; }
            .card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
            .header { background: #0e1e2e; padding: 26px 20px; text-align: center; color: #ffffff; }
            .body { padding: 26px 24px; }
            .receipt-box { background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; }
            .row:last-child { border-bottom: none; }
            .badge-success { background: #15803d; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; }
            .next-due-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin-top: 18px; font-size: 13px; color: #92400e; }
            .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin: 0; font-size: 22px; color: #ffffff; letter-spacing: 0.5px;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin: 4px 0 0; font-size: 13px; color: #fe7c03; font-weight: 700;">Official Admission &amp; Course Fee Receipt</p>
            </div>
            <div class="body">
                <h3 style="margin-top: 0; color: #0f172a; font-size: 17px;">Dear ' . htmlspecialchars($name) . ',</h3>
                <p style="font-size: 13.5px; line-height: 1.6; color: #475569;">
                    Congratulations! Your course admission payment for <strong>' . htmlspecialchars($courseTitle) . '</strong> has been successfully verified. Your enrollment is now officially <strong>CONFIRMED</strong>.
                </p>

                <div class="receipt-box">
                    <div class="row">
                        <span style="color: #64748b;">Receipt Number:</span>
                        <strong style="color: #0f172a; font-family: monospace;">' . htmlspecialchars($receiptDetails['receipt_no']) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color: #64748b;">Student ID:</span>
                        <strong style="color: #0f172a; font-family: monospace;">' . htmlspecialchars($studentId) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color: #64748b;">Payment Plan:</span>
                        <strong style="color: #0f172a; text-transform: capitalize;">' . htmlspecialchars($receiptDetails['plan_title']) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color: #64748b;">Total Course Fee:</span>
                        <span style="color: #0f172a;">' . htmlspecialchars($total_fee) . '</span>
                    </div>
                    ' . (!empty($receiptDetails['discount_amount']) ? '
                    <div class="row">
                        <span style="color: #15803d;">Full Payment Discount:</span>
                        <strong style="color: #15803d;">- ₹' . number_format((float)$receiptDetails['discount_amount'], 2) . '</strong>
                    </div>' : '') . '
                    <div class="row" style="background: #f0fdf4; margin: 0 -20px; padding: 10px 20px;">
                        <span style="color: #166534; font-weight: bold;">Amount Paid (Now):</span>
                        <strong style="color: #15803d; font-size: 16px;">' . htmlspecialchars($paid_amt) . '</strong>
                    </div>
                    <div class="row">
                        <span style="color: #64748b;">Transaction / Payment ID:</span>
                        <span style="font-family: monospace; font-size: 12px; color: #0f172a;">' . htmlspecialchars($receiptDetails['payment_id']) . '</span>
                    </div>
                    <div class="row">
                        <span style="color: #64748b;">Remaining Course Balance:</span>
                        <strong style="color: ' . ((float)$receiptDetails['pending_balance'] > 0 ? '#b91c1c' : '#15803d') . ';">' . htmlspecialchars($pending_balance) . '</strong>
                    </div>
                </div>

                ' . (!empty($receiptDetails['next_due_date']) ? '
                <div class="next-due-box">
                    <strong><i class="fa-solid fa-clock me-1"></i> Next Installment Schedule:</strong><br>
                    <span>Your next installment of <strong>₹' . number_format((float)$receiptDetails['next_due_amount'], 2) . '</strong> is due on <strong>' . date('d M Y', strtotime($receiptDetails['next_due_date'])) . '</strong>. You will receive an email reminder before this date.</span>
                </div>' : '<div style="padding: 10px; background: #ecfdf5; border-radius: 6px; color: #065f46; font-size: 13px; font-weight: 600; text-align: center;">✓ Course fee paid in full. No further payments due.</div>') . '

                <p style="font-size: 13px; color: #64748b; margin-top: 20px; line-height: 1.5;">
                    You can log in to your <a href="student-profile.php" style="color: #fe7c03; font-weight: 600;">Student Dashboard</a> anytime to track your enrolled courses, view payment breakdown, and download receipts.
                </p>
            </div>
            <div class="footer">
                Helpline: +91 96503 86711 | accounts@finchskills.com &bull; Finchskills Institute
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($email, $subject, $html);
}

/**
 * 6. Send Comprehensive Payment Alert Notification to Admin
 */
function sendAdminFeePaymentNotificationEmail($studentData, $courseData, $paymentDetails) {
    $cfg = getSmtpConfig();
    $admin_email = !empty($cfg['smtp_admin_email']) ? $cfg['smtp_admin_email'] : (!empty($cfg['smtp_username']) ? $cfg['smtp_username'] : 'finchskillsinstitute@gmail.com');

    $studentName = $studentData['name'] ?? 'Student';
    $studentId   = $studentData['student_id'] ?? 'N/A';
    $mobile      = $studentData['mobile'] ?? 'N/A';
    $email       = $studentData['email'] ?? 'N/A';

    $courseTitle = $courseData['title'] ?? 'Selected Course';
    $duration    = $courseData['duration'] ?? 'N/A';

    $subject = "💳 New Fee Received: ₹" . number_format((float)$paymentDetails['paid_amount'], 2) . " from {$studentName} ({$studentId})";

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: Inter, Arial, sans-serif; background: #0f172a; margin: 0; padding: 24px 12px; color: #334155; }
            .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
            .header { background: #0e1e2e; padding: 24px 20px; border-bottom: 3px solid #fe7c03; }
            .body { padding: 24px; }
            .badge-plan { display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 12px; text-transform: uppercase; }
            .info-table { width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 18px; }
            .info-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
            .info-table tr:last-child td { border-bottom: none; }
            .label-td { color: #64748b; width: 40%; }
            .val-td { color: #0f172a; font-weight: 600; }
            .alert-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 14px 16px; margin: 16px 0; }
            .btn-admin { display: inline-block; background: #fe7c03; color: #ffffff !important; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-weight: 700; font-size: 13.5px; }
            .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="header">
                <h2 style="margin: 0; font-size: 20px; color: #ffffff;">FINCHSKILLS INSTITUTE</h2>
                <p style="margin: 4px 0 0; font-size: 13px; color: #fe7c03; font-weight: 700;">Admin Fee Payment Notification Alert</p>
            </div>
            <div class="body">
                <div class="alert-box">
                    <span style="color: #166534; font-size: 15px; font-weight: 700;">✓ Payment Successfully Received &amp; Processed</span>
                    <div style="font-size: 13px; color: #15803d; margin-top: 4px;">
                        A student has just submitted course fees through the portal.
                    </div>
                </div>

                <h4 style="margin: 16px 0 8px; color: #0f172a; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Student Details</h4>
                <table class="info-table">
                    <tr>
                        <td class="label-td">Student Name:</td>
                        <td class="val-td">' . htmlspecialchars($studentName) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Student ID:</td>
                        <td class="val-td" style="color: #fe7c03; font-family: monospace; font-size: 15px;">' . htmlspecialchars($studentId) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Mobile Contact:</td>
                        <td class="val-td">' . htmlspecialchars($mobile) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Email Address:</td>
                        <td class="val-td">' . htmlspecialchars($email) . '</td>
                    </tr>
                </table>

                <h4 style="margin: 20px 0 8px; color: #0f172a; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Course &amp; Fee Breakdown</h4>
                <table class="info-table">
                    <tr>
                        <td class="label-td">Enrolled Course:</td>
                        <td class="val-td">' . htmlspecialchars($courseTitle) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Course Duration:</td>
                        <td class="val-td">' . htmlspecialchars($duration) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Payment Mode / Plan:</td>
                        <td class="val-td"><span class="badge-plan">' . htmlspecialchars($paymentDetails['plan_title']) . '</span></td>
                    </tr>
                    <tr>
                        <td class="label-td">Total Course Fee:</td>
                        <td class="val-td">₹' . number_format((float)$paymentDetails['total_fee'], 2) . '</td>
                    </tr>
                    ' . (!empty($paymentDetails['discount_amount']) ? '
                    <tr>
                        <td class="label-td" style="color: #15803d;">Full Pay Discount Applied:</td>
                        <td class="val-td" style="color: #15803d;">- ₹' . number_format((float)$paymentDetails['discount_amount'], 2) . '</td>
                    </tr>' : '') . '
                    <tr style="background: #f8fafc;">
                        <td class="label-td" style="font-weight: 700; color: #0f172a;">Amount Paid (Now):</td>
                        <td class="val-td" style="color: #16a34a; font-size: 16px;">₹' . number_format((float)$paymentDetails['paid_amount'], 2) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Transaction / Payment ID:</td>
                        <td class="val-td" style="font-family: monospace;">' . htmlspecialchars($paymentDetails['payment_id']) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Receipt Number:</td>
                        <td class="val-td" style="font-family: monospace;">' . htmlspecialchars($paymentDetails['receipt_no']) . '</td>
                    </tr>
                    <tr>
                        <td class="label-td">Remaining Fee Balance:</td>
                        <td class="val-td" style="color: ' . ((float)$paymentDetails['pending_balance'] > 0 ? '#b91c1c' : '#15803d') . ';">₹' . number_format((float)$paymentDetails['pending_balance'], 2) . '</td>
                    </tr>
                    ' . (!empty($paymentDetails['next_due_date']) ? '
                    <tr style="background: #fffbeb;">
                        <td class="label-td" style="color: #92400e; font-weight: 700;">Next Installment Due:</td>
                        <td class="val-td" style="color: #92400e;">₹' . number_format((float)$paymentDetails['next_due_amount'], 2) . ' on ' . date('d M Y', strtotime($paymentDetails['next_due_date'])) . '</td>
                    </tr>' : '') . '
                </table>

                <div style="text-align: center; margin-top: 24px;">
                    <a href="http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000') . '/admin/admissions.php" class="btn-admin">View Admissions in Admin Panel &rarr;</a>
                </div>
            </div>
            <div class="footer">
                Finchskills Institute &bull; Automated Payment Notification System
            </div>
        </div>
    </body>
    </html>';

    return sendFinchMail($admin_email, $subject, $html);
}



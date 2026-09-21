<?php
/**
 * 1-Click Database Installer & Setup Script for Live Server
 * Access via: https://yourdomain.com/setup.php
 */

require_once __DIR__ . '/db.php';

$message = '';
$status = '';

if (isset($_POST['install'])) {
    try {
        // SQL Queries Array
        $queries = [
            // 1. Admins Table
            "CREATE TABLE IF NOT EXISTS `admins` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 2. Admissions Table
            "CREATE TABLE IF NOT EXISTS `admissions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `father_name` VARCHAR(150) NULL,
                `email` VARCHAR(150) NULL,
                `mobile` VARCHAR(25) NULL,
                `aadhaar` VARCHAR(30) NULL,
                `dob` VARCHAR(30) NULL,
                `gender` VARCHAR(20) NULL,
                `course` VARCHAR(150) NULL,
                `education` VARCHAR(150) NULL,
                `state` VARCHAR(100) NULL,
                `city` VARCHAR(100) NULL,
                `post_office` VARCHAR(100) NULL,
                `pincode` VARCHAR(20) NULL,
                `address` TEXT NULL,
                `marksheet10` VARCHAR(255) NULL,
                `marksheet12` VARCHAR(255) NULL,
                `aadhaar_card` VARCHAR(255) NULL,
                `photo` VARCHAR(255) NULL,
                `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 3. Fee Submissions Table
            "CREATE TABLE IF NOT EXISTS `fee_submissions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `contact` VARCHAR(25) NULL,
                `email` VARCHAR(150) NULL,
                `fee_type` VARCHAR(100) NULL,
                `purpose` VARCHAR(200) NULL,
                `course` VARCHAR(150) NULL,
                `receipt` VARCHAR(255) NULL,
                `signature` VARCHAR(255) NULL,
                `status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 4. Contact Inquiries Table
            "CREATE TABLE IF NOT EXISTS `contact_inquiries` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `email` VARCHAR(150) NOT NULL,
                `phone` VARCHAR(25) NULL,
                `subject` VARCHAR(200) NULL,
                `message` TEXT NOT NULL,
                `status` ENUM('New', 'In Progress', 'Resolved') DEFAULT 'New',
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 5. Site Settings Table
            "CREATE TABLE IF NOT EXISTS `site_settings` (
                `setting_key` VARCHAR(100) PRIMARY KEY,
                `setting_value` TEXT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        // Execute Table Queries
        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }

        // Create Default Admin User (admin / admin123)
        $admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmtAdmin = $pdo->prepare("INSERT INTO `admins` (`id`, `username`, `email`, `password`) VALUES (1, 'admin', 'admin@finchskills.com', ?) ON DUPLICATE KEY UPDATE `username`='admin'");
        $stmtAdmin->execute([$admin_hash]);

        // Insert Default Site Settings
        $defaultSettings = [
            'phone_1' => '+919650386711',
            'phone_2' => '+919876543210',
            'email_1' => 'hello@finchskills.com',
            'email_2' => 'admission@finchskills.com',
            'site_logo' => 'img/logo.png',
            'footer_logo' => 'img/footer-logo.png',
            'site_favicon' => 'img/favicon.png',
            'address' => 'Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016',
            'timing_mon_fri' => 'Monday - Friday: 10:00 AM - 05:00 PM',
            'timing_sat' => 'Saturday: 10:00 AM - 02:00 PM',
            'facebook_url' => 'https://facebook.com/',
            'instagram_url' => 'https://instagram.com/',
            'linkedin_url' => 'https://linkedin.com/',
            'twitter_url' => 'https://twitter.com/',
            'youtube_url' => 'https://youtube.com/',
            'whatsapp_number' => '919650386711',
            'map_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.9501409758395!2d77.43262847457278!3d28.63125638414035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cee300c363997%3A0xe53a3aaacf648c83!2sOrbit%20plaza%2C%20Crossings%20Republik%2C%20Ghaziabad%2C%20Uttar%20Pradesh%20201016!5e0!3m2!1sen!2sin!4v1784362846035!5m2!1sen!2sin'
        ];

        $stmtSet = $pdo->prepare("INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`");
        foreach ($defaultSettings as $k => $v) {
            $stmtSet->execute([$k, $v]);
        }

        // Create Required Upload Folders with permissions
        $folders = [
            __DIR__ . '/uploads',
            __DIR__ . '/uploads/admissions',
            __DIR__ . '/uploads/fees',
            __DIR__ . '/uploads/site',
        ];

        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
        }

        $status = 'success';
        $message = 'Setup Completed Successfully! All database tables, default admin, settings & folders have been created.';
    } catch (Exception $e) {
        $status = 'danger';
        $message = 'Setup Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1-Click Auto Database Setup - Finchskills</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .setup-card {
            background: #fff;
            max-width: 560px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .setup-header {
            background: #4361ee;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="setup-card">
    <div class="setup-header">
        <i class="fa-solid fa-database fs-1 mb-2"></i>
        <h3 class="fw-bold mb-1">1-Click Auto Setup</h3>
        <p class="mb-0 opacity-75">Finchskills Database & Tables Installation</p>
    </div>

    <div class="p-4 p-md-5">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $status ?> d-flex align-items-center mb-4">
                <i class="fa-solid <?= $status === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> fs-4 me-2"></i>
                <div><?= htmlspecialchars($message) ?></div>
            </div>
            <?php if ($status === 'success'): ?>
                <div class="bg-light p-3 rounded-3 mb-4 small">
                    <strong>Admin Credentials:</strong><br>
                    • <strong>Username:</strong> <code>admin</code><br>
                    • <strong>Password:</strong> <code>admin123</code><br><br>
                    <span class="text-danger"><strong>Security Warning:</strong> Once setup is complete, please delete <code>setup.php</code> from your live server.</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="admin/login.php" class="btn btn-primary btn-lg fw-semibold">Go to Admin Login <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    <a href="index.php" class="btn btn-outline-secondary">Go to Website Homepage</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted">Yeh script automatically aapke live database me saari tables (<code>admins</code>, <code>admissions</code>, <code>fee_submissions</code>, <code>contact_inquiries</code>, <code>site_settings</code>), default admin user, aur upload folders create kar dega.</p>

            <form method="POST">
                <div class="d-grid">
                    <button type="submit" name="install" class="btn btn-primary btn-lg fw-bold shadow-sm" style="background-color: #4361ee;">
                        <i class="fa-solid fa-bolt me-2"></i> Run Auto Setup & Install Tables
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

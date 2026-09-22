<?php
require_once __DIR__ . '/db.php';

try {
    // 1. Installments & Fee Schedule Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `student_installments` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_db_id` INT(11) DEFAULT NULL,
        `student_id` VARCHAR(50) NOT NULL,
        `admission_id` INT(11) NOT NULL,
        `course_title` VARCHAR(255) NOT NULL,
        `total_course_fee` DECIMAL(10,2) NOT NULL,
        `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
        `final_payable` DECIMAL(10,2) NOT NULL,
        `payment_type` ENUM('full','installment') NOT NULL DEFAULT 'full',
        `total_parts` INT(11) NOT NULL DEFAULT 1,
        `installment_no` INT(11) NOT NULL DEFAULT 1,
        `installment_title` VARCHAR(100) NOT NULL DEFAULT '1st Installment (Admission Fee)',
        `installment_amount` DECIMAL(10,2) NOT NULL,
        `due_date` DATE DEFAULT NULL,
        `paid_date` DATETIME DEFAULT NULL,
        `status` ENUM('Paid','Pending','Overdue') NOT NULL DEFAULT 'Pending',
        `payment_id` VARCHAR(100) DEFAULT NULL,
        `receipt_no` VARCHAR(50) DEFAULT NULL,
        `reminder_sent` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `student_id` (`student_id`),
        KEY `admission_id` (`admission_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 2. Add payment fields to admissions table if not present
    $columns = $pdo->query("SHOW COLUMNS FROM `admissions`")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('fee_total', $columns)) {
        $pdo->exec("ALTER TABLE `admissions` ADD COLUMN `fee_total` DECIMAL(10,2) DEFAULT 0.00 AFTER `status`");
    }
    if (!in_array('fee_paid', $columns)) {
        $pdo->exec("ALTER TABLE `admissions` ADD COLUMN `fee_paid` DECIMAL(10,2) DEFAULT 0.00 AFTER `fee_total`");
    }
    if (!in_array('fee_pending', $columns)) {
        $pdo->exec("ALTER TABLE `admissions` ADD COLUMN `fee_pending` DECIMAL(10,2) DEFAULT 0.00 AFTER `fee_paid`");
    }
    if (!in_array('payment_plan', $columns)) {
        $pdo->exec("ALTER TABLE `admissions` ADD COLUMN `payment_plan` ENUM('full','installment') DEFAULT NULL AFTER `fee_pending`");
    }
    if (!in_array('payment_status', $columns)) {
        $pdo->exec("ALTER TABLE `admissions` ADD COLUMN `payment_status` ENUM('Pending Payment','Partial Paid','Fully Paid') DEFAULT 'Pending Payment' AFTER `payment_plan`");
    }

    // 3. Add full fee discount setting in site_settings if not present
    $check_setting = $pdo->prepare("SELECT COUNT(*) FROM `site_settings` WHERE `setting_key` = ?");
    $check_setting->execute(['full_payment_discount_percent']);
    if ($check_setting->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES ('full_payment_discount_percent', '10')");
    }

    echo "DATABASE_MIGRATION_SUCCESSFUL\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

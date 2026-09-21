CREATE DATABASE IF NOT EXISTS `finchskills_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `finchskills_db`;

-- Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin if not exists (username: admin, pass: admin123)
INSERT INTO `admins` (`id`, `username`, `email`, `password`) 
VALUES (1, 'admin', 'admin@finchskills.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Admissions Table
CREATE TABLE IF NOT EXISTS `admissions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fee Submissions Table
CREATE TABLE IF NOT EXISTS `fee_submissions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact Inquiries Table
CREATE TABLE IF NOT EXISTS `contact_inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(25) NULL,
    `subject` VARCHAR(200) NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('New', 'In Progress', 'Resolved') DEFAULT 'New',
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
    `setting_key` VARCHAR(100) PRIMARY KEY,
    `setting_value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('phone_1', '+919650386711'),
('phone_2', '+919876543210'),
('email_1', 'hello@finchskills.com'),
('email_2', 'admission@finchskills.com'),
('address', 'Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016'),
('timing_mon_fri', 'Monday - Friday: 10:00 AM - 05:00 PM'),
('timing_sat', 'Saturday: 10:00 AM - 02:00 PM'),
('facebook_url', 'https://facebook.com/'),
('instagram_url', 'https://instagram.com/'),
('linkedin_url', 'https://linkedin.com/'),
('twitter_url', 'https://twitter.com/'),
('youtube_url', 'https://youtube.com/'),
('whatsapp_number', '919650386711'),
('map_iframe', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.9501409758395!2d77.43262847457278!3d28.63125638414035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cee300c363997%3A0xe53a3aaacf648c83!2sOrbit%20plaza%2C%20Crossings%20Republik%2C%20Ghaziabad%2C%20Uttar%20Pradesh%20201016!5e0!3m2!1sen!2sin!4v1784362846035!5m2!1sen!2sin')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

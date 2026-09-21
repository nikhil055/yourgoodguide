<?php
if (session_status() === PHP_SESSION_NONE) session_start();

unset($_SESSION['student_logged_in']);
unset($_SESSION['student_db_id']);
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
unset($_SESSION['student_email']);
unset($_SESSION['student_photo']);

header("Location: index.php");
exit;

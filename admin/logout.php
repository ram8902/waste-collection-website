<?php
require_once '../config.php';

// Destroy admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Redirect to admin login
header('Location: login.php');
exit();
?>


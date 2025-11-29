<?php
require_once '../config.php';

// Destroy staff session
unset($_SESSION['staff_id']);
unset($_SESSION['staff_name']);
unset($_SESSION['staff_username']);

// Redirect to staff login
header('Location: login.php');
exit();
?>


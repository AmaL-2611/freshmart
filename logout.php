<?php
require_once 'includes/init.php';

// Initialize User class
$user = new User($db);

// Log the user out
$user->logout();

// Redirect to login page with a success message
$_SESSION['success_message'] = 'You have been successfully logged out.';
header('Location: login.php');
exit();

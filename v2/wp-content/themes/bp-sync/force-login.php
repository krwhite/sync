<?php
// Require login for site
get_currentuserinfo();
global $user_ID;
if ($user_ID == '') {
	$currentPath = $_SERVER['REQUEST_URI'];
	$loginPath = get_settings('home') . '/' . 'wp-login.php';
	header('Location: ' . $loginPath . '?redirect_to=' . $currentPath); exit(); 
} ?>
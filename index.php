<?php
session_start();

// Load core files
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get the page user wants to see
$page = $_GET['page'] ?? 'landing';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn) {
    $currentUser = getUserById($_SESSION['user_id']);
}

// Pages that don't need login
$publicPages = ['home', 'products', 'login', 'register', 'landing', 'admin/dashboard', 'admin/products', 'admin/api/product-action', 'admin/api/get-product'];

// If not logged in and trying to access private page, go to landing
if (!$isLoggedIn && !in_array($page, $publicPages)) {
    $page = 'landing';
}

// Show the requested page
$pageFile = "pages/{$page}.php";
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    include 'pages/not-found.php';
}
?>
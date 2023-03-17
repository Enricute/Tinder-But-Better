<?php
session_start();

// Read products from the JSON file
$products_file = file_get_contents('products.json');
$products = json_decode($products_file, true);

// Get the current user's products
if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  $user_products = $products[$username];
} else {
  // If the user is not logged in, redirect to login page
  header('Location: login.php');
  exit();
}
?>
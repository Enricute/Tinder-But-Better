<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

ini_set('display_errors', 0); // disable error output
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
ob_start(); // start output buffering

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // check request method
    // Get form data
    $productName = filter_input(INPUT_POST, 'productName', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);

    // Handle product images
    $productImages = [];
    if (!empty($_FILES['productImage'])) {
        // Create the uploads directory if it doesn't exist
        if (!file_exists('uploads')) {
            mkdir('uploads');
        }

        // Loop through each uploaded file
        foreach ($_FILES['productImage']['tmp_name'] as $key => $tmpName) {
            // Generate a unique filename
            $filename = uniqid() . "_" . basename($_FILES['productImage']['name'][$key]);

            // Move the file to the uploads directory
            if (move_uploaded_file($tmpName, "uploads/" . $filename)) {
                // Add the file path to the product images array
               $productImages[] = "uploads/" . $filename;
            } else {
                // Log upload errors to error.log
                error_log("Error uploading file: " . $tmpName);
            }
        }
    }

    // Create the product array
    $product = [
        'productName' => $productName,
        'description' => $description,
        'phoneNumber' => $phoneNumber,
        'productImage' => $productImages
    ];

    // Get the user's data from the session
    $uname = $_SESSION['uname'] ?? '';

    // Create the products.json file if it doesn't exist
    if (!file_exists('products.json')) {
        file_put_contents('products.json', '{}');
    }

    // Get the existing data from the products.json file
    $data = json_decode(file_get_contents('products.json'), true);

    // Add the product to the user's data
    $data[$uname][] = $product;

    // Save the updated data to the products.json file
    file_put_contents('products.json', json_encode($data));

    // Create product page for the uploaded product
    $productId = array_key_last($data[$uname] ?? []);
    $productPageContent = '<html><head><title>' . $productName . '</title></head><body><h1>' . $productName . '</h1><p>' . $description . '</p><p>Phone number: ' . $phoneNumber . '</p>';
    foreach ($productImages as $image) {
        $productPageContent .= '<img src="' . $image . '">';
    }
    $productPageContent .= '</body></html>';

if (!file_exists('productspages')) {
    mkdir('productspages');
}
$productPageFilename = 'productspages/' . $productId . '.html';
if (file_put_contents($productPageFilename, $productPageContent) === false) {
    // Log file creation errors to error.log
    error_log("Error creating product page file: " . $productPageFilename);
}

// Redirect back to the product page
header('Location: /products.html');
exit();
}
?>
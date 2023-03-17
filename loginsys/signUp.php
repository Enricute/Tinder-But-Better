<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'signInErrors.log');

// Create a constant variable to store the maximum length of username and password
define('MAX_LENGTH', 20);

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    $username = $_POST['uname'];
    $password = $_POST['pwd'];

    if (strlen($username) > MAX_LENGTH || strlen($password) > MAX_LENGTH) {
        trigger_error('Username and password should be no more than '.MAX_LENGTH.' characters');
        exit();
    }

// Load existing users from JSON file or create a new empty array
    $users = [];
    if (file_exists('users.json')) {
        $users_data = file_get_contents('users.json');
        if (!empty($users_data)) {
            $users = json_decode($users_data, true);
            if (!is_array($users)) {
                trigger_error('Failed to load user data');
                exit();
            }
        }
    }
    
    // Check if the user already exists
    if (is_array($users) && array_key_exists($username, $users)) {
        trigger_error('Username already exists');
        exit();
    }


    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Add the new user to the array
    $users[$username] = $hashed_password;

    // Write the array back to the JSON file
    file_put_contents('users.json', json_encode($users));

    // Redirect to index.html after successful sign-up
    header('Location: ../index.html');
    exit();
}

// Log any errors
$log = fopen('signInErrors.log', 'a');
$log_message = file_get_contents('php://stderr');
if (!empty($log_message)) {
    fwrite($log, date('[Y-m-d H:i:s] ') . $log_message . "\n");
}
fclose($log);
?>
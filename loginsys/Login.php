<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'loginErrors.log');

require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['uname'];
    $password = $_POST['password'];

    if (authenticate_user($username, $password)) {
        header('Location: ../index.html');
        exit();
    } else {
        trigger_error('Invalid username or password');
        exit();
    }
}


$log = fopen('loginErrors.log', 'a');
$log_message = file_get_contents('php://stderr');
if (!empty($log_message)) {
    fwrite($log, date('[Y-m-d H:i:s] ') . $log_message . "\n");
}
fclose($log);
?>
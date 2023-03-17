<?php
session_start();

function authenticate_user($username, $password) {
    $users = json_decode(file_get_contents('users.json'), true) ?? [];

    if (array_key_exists($username, $users) && password_verify($password, $users[$username])) {
        $_SESSION['uname'] = $username;

        return true;
    } else {
        return false;
    }
}
?>
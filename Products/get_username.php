<?php
// Start the session
session_start();

// Check if the username is stored in the session
if (isset($_SESSION['username'])) {
  // Return the username
  echo $_SESSION['username'];
} else {
  // Return an error message if the username is not set in the session
  echo 'Error: username not set in the session.';
}
?>
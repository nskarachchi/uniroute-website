<?php
session_start();

// Clear all session variables
session_unset();

session_destroy();

// Redirect to the sign-in page with a success message
header("Location: ../user/signIn.php?message=You have been logged out successfully");
exit();
?>
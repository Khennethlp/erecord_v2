<?php
// Start the session
// session_start();

// Set timeout duration (e.g., 600 seconds = 10 minutes)
$timeout_duration = 600;

// Check if the last activity is set
if (isset($_SESSION['last_activity'])) {
    // Calculate the session's lifetime
    $elapsed_time = time() - $_SESSION['last_activity'];

    // If the session has timed out
    if ($elapsed_time > $timeout_duration) {
        // Unset session variables
        session_unset();

        // Destroy the session
        session_destroy();

        // Redirect to the login page with a timeout message
        header("Location: /erecord_v2/login.php");
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>

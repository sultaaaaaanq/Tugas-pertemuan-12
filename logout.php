<?php
session_start();
// Clear all session variables
$_SESSION = array();
// If session uses cookies, clear the cookie as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// Destroy the session
session_destroy();
// Redirect back to login with a flag so we can show a message
header("Location: login.php?logged_out=1");
exit;
?>
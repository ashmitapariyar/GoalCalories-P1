<?php
/**
 * Get User Info API
 * Returns current logged-in user's information
 */

session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.'
    ]);
    exit();
}

// Return user information from session
echo json_encode([
    'success' => true,
    'data' => [
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'fullname' => $_SESSION['fullname']
    ]
]);
?>

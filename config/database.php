<?php
/**
 * Database Configuration
 * 
 * Centralized database connection configuration
 * Include this file in any PHP script that needs database access
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'goalcaloriep1');

/**
 * Get Database Connection
 * 
 * @return mysqli Database connection object
 * @throws Exception if connection fails
 */
function getDatabaseConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Close Database Connection
 * 
 * @param mysqli $conn Database connection to close
 */
function closeDatabaseConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>

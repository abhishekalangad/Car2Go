<?php
/**
 * Database Connection with Prepared Statements Support
 * Secure connection to MySQL database
 */

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'carservice');

// Character set
define('DB_CHARSET', 'utf8mb4');

// Create connection with error handling
try {
    $con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($con->connect_error) {
        error_log("Database Connection Failed: " . $con->connect_error);
        die("Sorry, we're experiencing technical difficulties. Please try again later.");
    }

    // Set character set
    $con->set_charset(DB_CHARSET);

} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Sorry, we're experiencing technical difficulties. Please try again later.");
}

/**
 * Prepared statement helper function
 * 
 * @param mysqli $con Database connection
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (i, d, s, b)
 * @param array $params Parameters to bind
 * @return mysqli_stmt|false
 */
function db_execute($con, $query, $types = "", $params = [])
{
    $stmt = $con->prepare($query);

    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        return false;
    }

    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }

    return $stmt;
}

/**
 * Fetch single row
 * 
 * @param mysqli $con Database connection
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array|null
 */
function db_fetch_one($con, $query, $types = "", $params = [])
{
    $stmt = db_execute($con, $query, $types, $params);

    if ($stmt === false) {
        return null;
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row;
}

/**
 * Fetch all rows
 * 
 * @param mysqli $con Database connection
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array
 */
function db_fetch_all($con, $query, $types = "", $params = [])
{
    $stmt = db_execute($con, $query, $types, $params);

    if ($stmt === false) {
        return [];
    }

    $result = $stmt->get_result();
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();

    return $rows;
}
?>
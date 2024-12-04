<?php
// config.php
define('DB_SERVER', 'localhost'); // Replace with your server name
define('DB_USERNAME', 'root');    // Replace with your database username
define('DB_PASSWORD', '');        // Replace with your database password
define('DB_NAME', 'gad_db');      // Replace with your database name

class Database
{
    private $connection;

    // Create a new connection or return an existing one
    public function connect()
    {
        if ($this->connection === null) {
            $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

            // Check for connection errors
            if ($this->connection->connect_error) {
                // Log the error instead of showing it in production
                die('Connection failed: ' . $this->connection->connect_error);
            }
        }
        return $this->connection;
    }

    // Close the connection
    public function close()
    {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}

// Make this global for use throughout the app
$db = new Database();
$conn = $db->connect(); // Get the database connection object globally
?>
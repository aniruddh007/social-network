<?php
class Database {
    private $conn;
    private $host = "localhost";
    private $username = "root"; 
    private $password = ""; 
    private $database = "social_network";

    public function __construct() {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        if (!$this->conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

    public function escapeString($string) {
        return mysqli_real_escape_string($this->conn, $string);
    }
}
?>
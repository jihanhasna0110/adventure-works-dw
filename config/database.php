<?php
/**
 * Database Configuration
 * MySQL XAMPP - Localhost Setup
 */

class Database {
    // ==========================================
    // KONFIGURASI UNTUK XAMPP LOCALHOST
    // ==========================================
    private $host = "localhost";        // atau "127.0.0.1"
    private $port = "3306";             // Port default MySQL XAMPP
    private $db_name = "adventureworks_dw";  // Nama database Data Warehouse kamu
    private $username = "root";         // Username default XAMPP
    private $password = "";             // Password default XAMPP (kosong)
    
    public $conn;

    /**
     * Membuat koneksi ke database
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Connection string dengan PDO
            $dsn = "mysql:host=" . $this->host . 
                   ";port=" . $this->port . 
                   ";dbname=" . $this->db_name;
            
            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password
            );
            
            // Set charset UTF-8 (penting untuk karakter khusus)
            $this->conn->exec("set names utf8");
            
            // Set error mode ke Exception (untuk debugging)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Optional: Set fetch mode default
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            echo "❌ Connection Error: " . $exception->getMessage();
            // Di production, jangan tampilkan error detail:
            // error_log("Database Error: " . $exception->getMessage());
            // die("Database connection failed. Please contact administrator.");
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
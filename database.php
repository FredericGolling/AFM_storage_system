<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database {

    private $host = 'localhost';
    private $user = 'root';
    private $password = 'masterpw';
    private $db = 'project_storage';

    /**
     * Creates a simple database-connection.
     *
     * @return PDO
     */
    private function create_connection() {
        $conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    private function check_if_table_exist($connection, $table) {
        try {
            $connection->query("SELECT 1 FROM $table");
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Create User Table
     * ---
     * Checks if "user" table exists already.
     * Creates the table if not already exist.
     *
     * TABLE user:
     *  - user_id
     *  - username
     *  - password
     *  - email
     *  - register_date
     */
    private function create_sample_table() {
        // here: create table if not exist.
        try {
            $conn = $this->create_connection();
            if (!$this->check_if_table_exist($conn, 'samples')) {
                // sql to create table
                $sql = "CREATE TABLE samples (
                    sample_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    sample_name VARCHAR(40) NOT NULL,
                    material VARCHAR(40) NOT NULL,
                    set_up_date VARCHAR(160) NOT NULL,
                    container_number VARCHAR(60),
                    TIMESTAMP )";
                // use exec() because no results are returned
                $conn->exec($sql);
                echo "sample table created successfully";
            } else {
                // echo "user table already exist.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    }

    private function create_nid_table() {
        // here: create table if not exist.
        try {
            $conn = $this->create_connection();
            if (!$this->check_if_table_exist($conn, 'nid_files')) {
                // sql to create table
                $sql = "CREATE TABLE nid_files (
                    nid_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    sample_id VARCHAR(40) NOT NULL,
                    nid_name VARCHAR(40) NOT NULL,
                    date_of_recording VARCHAR(50) NOT NULL,
                    nr_of_lines VARCHAR(5),
                    TIMESTAMP )";
                // use exec() because no results are returned
                $conn->exec($sql);
                echo "nid table created successfully";
            } else {
                // echo "user table already exist.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    }

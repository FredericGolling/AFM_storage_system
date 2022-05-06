<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database {

    private $host = 'localhost';
    private $user = 'root';
    private $password = 'masterpw';
    private $db = 'AFM_storage';

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
     * Create file Table
     * ---
     * Checks if "files" table exists already.
     * Creates the table if not already exist.
     *
     * TABLE files:
     *  - file_id
     *  - file name
     *  - file
     *  - sample
     *  - image size
     *  - time per line
     *  - nr of lines
     */

    private function create_user_table() {
        // here: create table if not exist.
        try {
            $conn = $this->create_connection();
            if (!$this->check_if_table_exist($conn, 'user')) {
                // sql to create table
                $sql = "CREATE TABLE user (
                    user_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(40) NOT NULL,
                    password VARCHAR(160) NOT NULL,
                    email VARCHAR(60),
                    register_date TIMESTAMP )";
                // use exec() because no results are returned
                $conn->exec($sql);
                echo "user table created successfully";
            } else {
                // echo "user table already exist.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    }

    public function prepare_storage() {
        $this->create_user_table();
        return true;
    }

    public function prepare_registration() {
        $this->create_user_table();
        return true;
    }

    public function register_user($username, $password, $email=null) {
        // here: insert a new user into the database.
        try {
            $conn = $this->create_connection();
            $query = "SELECT * FROM `user` WHERE username = ?";
            $statement = $conn->prepare($query);
            $statement->execute([$username]);

            $user = $statement->fetchAll(PDO::FETCH_CLASS);
            if (!empty($user)) {
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        // now: save user.
        try {
            $conn = $this->create_connection();

            $sql = 'INSERT INTO user(username, password, email, register_date)
            VALUES(?, ?, ?, NOW())';
            $statement = $conn->prepare($sql);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $statement->execute([$username, $password_hash, $email]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function drop_all() {
        try {
            $conn = $this->create_connection();

            $sql = 'DROP TABLE user';
            $conn->exec($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
<?php
class Database
{

    // DB info
    private $db_host = 'localhost';
    private $db_name = 'contactkeeper';
    private $db_username = 'root';
    private $db_password = '123456';

    // connecting to DB
    public function dbConnection()
    {
        try {
            $conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_username, $this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["msg" => $e->getMessage()]);
            exit;
        }
    }
}

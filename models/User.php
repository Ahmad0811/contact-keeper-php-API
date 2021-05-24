<?php
class User
{

    // DB stuff
    private $conn;
    private $table = 'users';

    // User properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;

    // Constructor with db
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get user by id
    public function getUserById()
    {
        // Create query
        $query = 'SELECT `id` ,`name` , `email`, `created_at` FROM ' . $this->table . ' WHERE id= :id';

        // Prepare statement 
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':id', $this->id);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get user by email
    public function getUserByEmail()
    {
        // Create query
        $query = 'SELECT `id`,`name` , `email`, `password` FROM ' . $this->table . ' WHERE email= :email';

        // Prepare statement 
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':email', $this->email);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Create User
    public function createUser()
    {
        // Create query
        $query = "INSERT INTO users (`name`,`email`,`password`) VALUES(:name,:email,:password)";

        // Prepare statement 
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindValue(':name', htmlspecialchars(strip_tags($this->name)), PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT), PDO::PARAM_STR);

        // Execute query and check for success
        if ($stmt->execute()) {
            return true;
        }

        return $stmt->error;
    }

    // Check email 
    public function checkEmail()
    {
        // Create query
        $query = "SELECT `email` FROM users WHERE `email`=:email";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind Value
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

        // Execute query
        $stmt->execute();

        // Check if any similar email found
        if ($stmt->rowCount()) {
            return true;
        }
        return false;
    }
}

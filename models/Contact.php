<?php
class Contact
{

    // DB stuff
    private $conn;
    private $table = 'contact';

    // User properties
    public $id;
    public $userId;
    public $name;
    public $email;
    public $phone;
    public $type;
    public $created_at;

    // Constructor with db
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all contacts for a user
    public function getAllUserContacts()
    {
        // Create query
        $query = 'SELECT * FROM contact WHERE userId = :userId';

        // Prepare statement 
        $stmt = $this->conn->prepare($query);

        // Bind params
        $stmt->bindValue(':userId', $this->userId, PDO::PARAM_INT);

        // Execute query 
        $stmt->execute();

        return $stmt;
    }

    // Create contact
    public function createContact()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' (`userId` , `name` , `email` , `phone` , `type`) 
        VALUES (:userId,:name,:email,:phone,:type)';

        // Prepare statement 
        $stmt = $this->conn->prepare($query);


        // Bind params
        $stmt->bindValue(':userId', $this->userId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->bindValue(':type', $this->type);

        // Execute query 
        if ($stmt->execute()) {

            return true;
        }

        return $stmt->error;
    }

    // Update Contact
    public function updateContact()
    {
        // Create query
        $query = 'UPDATE contact
         SET `name`=:name, email=:email, phone=:phone,type=:type
         WHERE id=:id AND userId=:userId';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindValue(':id', $this->id);
        $stmt->bindValue(':userId', $this->userId);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':type', $this->type);

        // Execute query 
        if ($stmt->execute()) {
            return true;
        }

        return $stmt->error;
    }

    // Delete Contact
    public function deleteContact()
    {
        // Create query
        $query = 'DELETE FROM contact
        WHERE id=:id AND userId=:userId';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindValue(':id', $this->id);
        $stmt->bindValue(':userId', $this->userId);

        // Execute query 
        if ($stmt->execute()) {
            return true;
        }
        return $stmt->error;
    }
}

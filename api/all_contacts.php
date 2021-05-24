<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../middleware/Auth.php';
require_once '../models/Contact.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn, $allHeaders);

// Instantiate a Contact
$contact = new Contact($conn);

$returnData = [];

// If the request method is equal to GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(404);
    $returnData = ["msg" => "Page not found"];
}
// Check if there is a valid token
elseif ($auth->isAuth()) {
    $contact->userId = $auth->isAuth();
    try {

        $stmt = $contact->getAllUserContacts();

        $num = $stmt->rowCount();

        // Check if any contact
        if ($num > 0) {

            // contact array
            $contacts_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                // Make each row in the db to an array item
                $contact_item = array(
                    'id' => $id,
                    'userId' => $userId,
                    'name' => html_entity_decode($name),
                    'email' => $email,
                    'phone' => $phone,
                    'type' => $type,
                    'created_at' => $created_at
                );

                // Push to array
                array_push($contacts_arr, $contact_item);
            }
            http_response_code(200);
            $returnData = $contacts_arr;
        }
        // If no contacts
        else {
            http_response_code(200);
            $returnData = [];
        }
    } catch (PDOException $e) {
        http_response_code(500);
        $returnData = ['msg' => $e->getMessage()];
    }
}

echo json_encode($returnData);

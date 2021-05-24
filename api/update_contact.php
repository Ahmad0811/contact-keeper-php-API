<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Including DB and making objects
require '../config/Database.php';
require_once '../models/contact.php';
require_once '../middleware/Auth.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();

// Instantiate a contact
$contact = new Contact($conn);

$allHeaders = getallheaders();
$auth = new Auth($conn, $allHeaders);

// Get data from the request body
$data = json_decode(file_get_contents("php://input"));

$returnData = [];

// If the request method is equal to PUT
if ($_SERVER["REQUEST_METHOD"] != "PUT") {
    http_response_code(404);
    $returnData = ["msg" => "Page not found"];
}
// Check if the token is valid
elseif ($auth->isAuth()) {
    // Check if the required data is send in the body
    if (
        !isset($data->id)
        || !isset($data->name)
        || !isset($data->email)
        || !isset($data->phone)
        || !isset($data->type)
        || empty(trim($data->name))
        || empty(trim($data->email))
        || empty(trim($data->phone))
        || empty(trim($data->type))
    ) {

        $returnData = ["msg" => "All fields are required"];
        http_response_code(406);
    } else {

        try {
            // Clean Data
            $contact->userId = $auth->isAuth();
            $contact->id = $data->id;
            $contact->name = htmlspecialchars(strip_tags($data->name));
            $contact->email = htmlspecialchars(strip_tags($data->email));
            $contact->phone = htmlspecialchars(strip_tags($data->phone));
            $contact->type = htmlspecialchars(strip_tags($data->type));

            $contact->updateContact();

            // Update the contact
            http_response_code(201);
            $returnData = ["msg" => "Contact Updated"];
        } catch (PDOException $e) {
            http_response_code(500);
            $returnData = ['msg' => $e->getMessage()];
        }
    }
}

echo json_encode($returnData);

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Including DB and making objects
require '../config/Database.php';
require_once '../models/contact.php';
require_once '../middleware/Auth.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();
$allHeaders = getallheaders();
$auth = new Auth($conn, $allHeaders);

// Instantiate a contact
$contact = new Contact($conn);

// Get data from the request body
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// If the request method is equal to DELETE
if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
    $returnData = ["msg" => "Page not found"];
}
// Check if the token is valid
elseif ($auth->isAuth()) {

    // Check if the required data is send in the body
    if (
        !isset($data->id)
        || empty(trim($data->id))

    ) {
        http_response_code(406);
        $returnData = ["msg" => "Id is required"];
    } else {
        try {

            // Get userId from token
            $contact->userId = $auth->isAuth();

            // Clean Data
            $contact->id = htmlspecialchars(strip_tags($data->id));

            $contact->deleteContact();
            http_response_code(200);
            $returnData = ["msg" => "Contact deleted"];
        } catch (PDOException $e) {
            http_response_code(500);
            $returnData = ['msg' => $e->getMessage()];
        }
    }
}

echo json_encode($returnData);

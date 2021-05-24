<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Including DB and making objects
require_once '../config/Database.php';
require_once '../middleware/Auth.php';
require_once '../models/User.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn, $allHeaders);

// Instantiate a User
$user = new User($conn);

$returnData = [];


// If the request method is equal to GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(404);
    $returnData = ["msg" => "Page not found"];
}
// Check if there is a valid token
elseif ($auth->isAuth()) {
    try {
        // Get userId from the token
        $user->id = $auth->isAuth();

        // Get user from database by id
        $stmt = $user->getUserById();

        // Check if there is any data
        if ($stmt->rowCount()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the user with 200 Status code
            http_response_code(200);
            $returnData = $row;
        } else {

            // Return user not found
            http_response_code(404);
            $returnData = ["msg" => "User not found"];
            return null;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        $returnData = ['msg' => $e->getMessage()];
    }
}
echo json_encode($returnData);

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Including DB and making objects
require_once '../config/Database.php';
require_once '../config/JwtHandler.php';
require_once '../models/User.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// Instantiate a User
$user = new User($conn);


// Get data from the request body
$data = json_decode(file_get_contents("php://input"));

$returnData = [];

// If the request method is equal to POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(404);
    $returnData = ["msg" => 'page not found'];

    // Check if the required data is send in the body
} elseif (
    !isset($data->name)
    || !isset($data->email)
    || !isset($data->password)
    || empty(trim($data->name))
    || empty(trim($data->email))
    || empty(trim($data->password))
) {
    http_response_code(406);
    $returnData = ["msg" => 'Please Fill in all Required Fields!'];
} else {
    $name = trim($data->name);
    $email = trim($data->email);
    $password = trim($data->password);

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(406);
        $returnData = ["msg" => 'Invalid Email Address!'];

        // Check if password length less than 6 chars
    } elseif (strlen($password) < 6) {
        http_response_code(406);
        $returnData = ["msg" => 'Your password must be more than 6 chars'];
    } else {
        try {

            // Check if there is any similar email registered 
            $user->email = $email;
            if ($user->checkEmail()) {
                http_response_code(400);
                $returnData = ["msg" => 'This E-mail already in use!'];
            } else {
                $user->name = $name;
                $user->password = $password;

                // create User
                $create = $user->createUser();

                $stmt = $user->getUserByEmail();

                // Create a token for the new user
                if ($stmt->rowCount()) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $jwt = new JwtHandler();

                    // Encode the token with userId
                    $token = $jwt->_jwt_encode_data(
                        array("user_id" => $row['id'])
                    );

                    http_response_code(200);
                    $returnData = ["token" => $token];
                }
            }
        } catch (PDOException $e) {
            http_response_code(500);
            $returnData =  ['msg' => $e->getMessage()];
        }
    }
}

echo json_encode($returnData);

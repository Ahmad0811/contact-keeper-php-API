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
    $returnData = ['msg' => 'Page Not Found!'];
}
// Check if the required data is send in the body
elseif (
    !isset($data->email)
    || !isset($data->password)
    || empty(trim($data->email))
    || empty(trim($data->password))
) {

    http_response_code(406);
    $returnData = ['msg' => 'Please Fill in all Required Fields!'];
} else {
    $email = trim($data->email);
    $password = trim($data->password);


    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(406);
        $returnData = ['msg' => 'Invalid Email Address!'];
    }
    // Check if password length less than 6 chars
    elseif (strlen($password) < 6) {
        http_response_code(406);
        $returnData = ['msg' => 'Your password must be at least 8 characters long!'];
    } else {

        try {

            $user->email = $email;
            $stmt = $user->getUserByEmail();
            if ($stmt->rowCount()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // Compare the entered password with the one in the database
                $check_password = password_verify($password, $row['password']);

                // Check if the password is correct or no
                // Create a token for the new user
                if ($check_password) {

                    $jwt = new JwtHandler();
                    // Encode the token with userId
                    $token = $jwt->_jwt_encode_data(
                        array("user_id" => $row['id'])
                    );
                    http_response_code(200);
                    $returnData = ["token" => $token];
                }
                // If password is not valid
                else {
                    http_response_code(404);
                    $returnData = ['msg' => 'Invalid Password!'];
                }
            }
            // If no user with entered email address
            else {
                http_response_code(404);
                $returnData = ['msg' => 'Invalid Email Address!'];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            $returnData = ['msg' => $e->getMessage()];
        }
    }
}

echo json_encode($returnData);

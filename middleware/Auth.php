<?php
require '../config/JwtHandler.php';
class Auth extends JwtHandler
{

    // DB and headers properties
    protected $db;
    protected $headers;
    protected $token;
    public function __construct($db, $headers)
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }


    public function isAuth()
    {
        // Check for the token in the headers
        if (array_key_exists('x-auth-token', $this->headers) && !empty(trim($this->headers['x-auth-token']))) {

            $this->token =  trim($this->headers['x-auth-token']);

            // Check if is not empty
            if (isset($this->token) && !empty(trim($this->token))) {

                $data = $this->_jwt_decode_data($this->token);

                // Check for data after decoding and if everything ok return userId
                if (isset($data['auth']) && isset($data['data']->user_id) && $data['auth']) {
                    $user = $data['data']->user_id;
                    return $user;
                }
                // return unauthorized when something goes wrong 
                else {
                    echo json_encode(["msg" => "unauthorized"]);
                    http_response_code(401);
                    return null;
                }
            } else {
                echo json_encode(["msg" => "unauthorized"]);
                http_response_code(401);
                return null;
            }
        } else {
            echo json_encode(["msg" => "unauthorized"]);
            http_response_code(401);
            return null;
        }
    }
}

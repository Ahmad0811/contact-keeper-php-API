<?php
require '../jwt/src/JWT.php';
require '../jwt/src/ExpiredException.php';
require '../jwt/src/SignatureInvalidException.php';
require '../jwt/src/BeforeValidException.php';

use \Firebase\JWT\JWT;

class JwtHandler
{
    protected $jwt_secret;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // set your default time-zone
        date_default_timezone_set('Asia/Kolkata');
        $this->issuedAt = time();

        // Token Validity (3600 second = 1hr)
        $this->expire = $this->issuedAt + 3600;

        // Set your secret or signature
        $this->jwt_secret = "this_is_my_secret";
    }

    // Encoding the token
    public function _jwt_encode_data($data)
    {

        // Adding data to the token before encoding
        $this->token = array(
            //Adding the identifier to the token (who issue the token)
            "iat" => $this->issuedAt,
            // Token expiration
            "exp" => $this->expire,
            // Payload
            "data" => $data
        );

        // Encoding the token
        $this->jwt = JWT::encode($this->token, $this->jwt_secret);
        return $this->jwt;
    }

    // Token error
    protected function _errMsg($msg)
    {
        return [
            "auth" => 0,
            "message" => $msg
        ];
    }

    // Decoding the token
    public function _jwt_decode_data($jwt_token)
    {
        try {
            $decode = JWT::decode($jwt_token, $this->jwt_secret, array('HS256'));
            return [
                "auth" => 1,
                "data" => $decode->data
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->_errMsg($e->getMessage());
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->_errMsg($e->getMessage());
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return $this->_errMsg($e->getMessage());
        } catch (\DomainException $e) {
            return $this->_errMsg($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return $this->_errMsg($e->getMessage());
        } catch (\UnexpectedValueException $e) {
            return $this->_errMsg($e->getMessage());
        }
    }
}

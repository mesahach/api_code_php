<?php
class Auth2
{

    public function __construct(private UserGateway $user_gateway)
    {

    }
    public function authenticateAPIKey(): bool
    {
        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(["message" => "missing API key"]);
            return false;
        }
        $api_key = $_SERVER['HTTP_X_API_KEY'];

        if ($this->user_gateway->getByAPIKey($api_key) === false) {
            http_response_code(401);
            echo json_encode(['message' => "invalid API key"]);
            return false;
        }

        return true;
    }

    public function accountAuth($key): bool
    {
        if (empty($key)) {
            http_response_code(400);
            echo json_encode(["account_number" => "Missing User Key"]);
        }

        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(["password" => "Missing User Secret key"]);
        }

        if (empty($key) || empty($_SERVER['HTTP_X_API_KEY'])) {
            return false;
        } else {

            $this->user_gateway->setEmail($key);
            $this->user_gateway->setPassword($_SERVER['HTTP_X_API_KEY']);

            if ($this->user_gateway->checkAcctN() == true) {

                if ($this->user_gateway->checkIsPasswordAPI()) {
                    http_response_code(200);
                    return json_encode($this->user_gateway->login());
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Sorry, Wrong Password"]);
                    return false;
                }
            } else {

                http_response_code(404);
                echo json_encode(["message" => "Sorry, can`t login the account number"]);
                return false;
            }
        }
    }
}
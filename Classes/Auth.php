<?php

class Auth
{
    private $user_id;

    public function __construct(private UserGateway $user_gateway)
    {

    }
    public function authenticateAPIKey(): bool
    {
        $key = trim($_SERVER['HTTP_X_API_KEY']);
        $this->user_gateway->setEmail($key);
        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(["message" => "missing API key"]);
            return false;
        }

        if ($this->user_gateway->getByAPIKey() === false) {
            http_response_code(401);
            echo json_encode(['message' => "invalid API key"]);
            return false;
        }

        return true;
    }

    public function accountAuth(): array|bool
    {
        $key = trim($_SERVER['HTTP_X_API_KEY']);
        $this->user_gateway->setEmail($key);
        $user = $this->user_gateway->getByAPIKey();

        if ($user == false) {
            http_response_code(404);
            echo json_encode(["message" => "Sorry, can`t login the account number"]);
            return false;
        }

        if (!password_verify($_SERVER['HTTP_X_PASSWORD'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Sorry, Wrong Password"]);
            return false;
        }
        $this->user_id = $user['id'];
        http_response_code(200);
        return json_encode($user);
    }


    /**
     * @return int
     */
    public function getUser_id(): int
    {
        return $this->user_id;
    }
}
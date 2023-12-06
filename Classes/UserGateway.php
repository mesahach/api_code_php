<?php

class UserGateway extends DbConnect
{
    private string $api_key;
    private int $email;
    private string $password;

    function __construct()
    {
        $db = new DbConnect($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->dbConn = $db->connect();

        $arguments = func_get_args();

        if (!empty($arguments)) {
            foreach ($arguments[0] as $key => $property) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $property;
                }
            }
        }
    }

    function __destruct()
    {
        $this->dbConn = null;
    }

    public function getByAPIKey(): array|false
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM `users` WHERE `account_number` = :api_key");

        $stmt->bindValue(':api_key', $this->email, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        } else {
            return false;
        }

    }

    public function checkAcctN(): bool
    {
        $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM `users` WHERE `account_number` = :email");
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $num = $stmt->fetchColumn();
        if ($num > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIsPasswordAPI(): bool
    {
        $stmt = $this->dbConn->prepare("SELECT `password` FROM `users` WHERE `account_number` = :email");
        $stmt->bindParam(':email', $this->email);
        if ($stmt->execute()) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $pass = password_verify($this->password, $userData['password']);
            if ($pass) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function login()
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE `account_number`=:email");
        $stmt->bindParam(':email', $this->email);
        if ($stmt->execute()) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $userData;
        } else {
            return FALSE;
        }
    }

    /**
     * @param  $email 
     * @return self
     */
    public function setEmail(int $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param  $password 
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
}
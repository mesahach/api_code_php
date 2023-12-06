<?php

class TaskController {
    public function processRequest(string $method, ?string $resource, int $user_id, int $id): void {
        if($user_id === null) {
            $this->respondUnprocessableEntity(['Sorry, '.$id.' can`t be used']);
            return;
        } else {

            switch($method) {
                case 'GET':
                    $type = substr($resource, 0, 5);
                    $numData = substr($resource, 5);

                    if($type == 'trans') {
                        if(is_int($numData)) {
                            $task = $this->getSomeUserTransaction($user_id, $numData);
                        } elseif($numData == 'CREDIT') {
                            $task = $this->getTypeUserTransaction($user_id, $numData);
                        } elseif($numData == 'DEBIT') {
                            $task = $this->getTypeUserTransaction($user_id, $numData);
                        } else {
                            $task = $this->getuserTransaction($user_id);
                        }
                    } elseif($type == 'cards') {
                        if(!empty($numData)) {
                            $task = $this->getSomeUserCards($user_id, $numData);
                        } else {
                            $task = $this->getAllUserCards($user_id);
                        }
                    } elseif($type == 'notif') {
                        $task = $this->getUserNotification($user_id);
                    } else {
                        $task = $this->getUserData($user_id);
                        if($task === false) {
                            $this->respondNotFound($user_id);
                            return;
                        }
                    }
                    echo json_encode($task);
                    break;

                case 'PATCH':
                    $type = substr($resource, 0, 5);
                    $numData = substr($resource, 5);
                    $data = (array)json_decode(file_get_contents("php://input", true));

                    if($type == 'userC') {
                        $task = $this->updateColum($user_id, $data);
                        echo json_encode($task);

                    } elseif($type == "mesag") {
                        $errors = $this->getValidationErrors($data, false);

                        if(!empty($errors)) {
                            $this->respondUnprocessableEntity($errors);
                            return;
                        }

                        $rows = $this->update($id, $data);
                        echo json_encode(["message" => "Updated row(s) = $rows"]);
                    } else {
                        echo json_encode(["message" => "Provide type of update to be done"]);

                    }
                    break;

                case 'POST':
                    $data = (array)json_decode(file_get_contents('php://input', true));

                    $type = substr($resource, 0, 5);
                    $numData = substr($resource, 5);

                    if($data === null && json_last_error() !== JSON_ERROR_NONE) {
                        // Handle JSON decoding error
                        http_response_code(400); // Bad Request
                        echo json_encode(['error' => 'Invalid JSON data']);
                    } else {
                        switch($type) {
                            case 'tnfer':
                                $this->handleTnfer($user_id, $data);
                                break;

                            case "sendM":
                                $this->sendMail($user_id, $data);
                                break;
                            // Add more cases as needed
                            // case 'otherType':
                            //     handleOtherType($data);
                            // break;

                            default:
                                // Handle unknown type or provide an error response
                                echo "Unknown type: $type";
                                break;
                        }
                    }
                    break;

                case "DELETE":
                    $rows = $this->delete($id);
                    echo json_encode(["message" => "Deleted row(s) = $rows"]);
                    break;

                default:
                    $this->responseMethodNotAllowed("GET, POST, PATCH, DELETE");
                    ;
                    break;
            }
        }
    }

    function handleTnfer($user_id, $data) {
        try {
            // Extract required parameters from $data
            $type = $data['type'];
            $recActName = $data['recname'];
            $recBankName = $data['recbank'];
            $amount = $data['amount'];
            //more data

            $ObjHub = new AllUsers();
            $user_data = $ObjHub->getUserByAct($user_id);

            if(is_array($user_data)) {
                if($user_data['balance'] >= $amount) {

                    $newBalance = $user_data['balance'] - $amount;
                    if($this->debitAccount($user_id, $newBalance)) {

                        $ObjTrans = new transactionsClass([
                            'user_id' => $user_id,
                            'topic' => $type,
                            'data' => $data,
                            'datas' => $data,
                        ]);
                        $ObjTrans->getDate();
                        $result = $ObjTrans->saveTranfer();
                        $currency = currencySym($user_data['currency']);

                        $messagesInt = [
                            'message' => "Debit alert of ".$currency.number_format($amount, 2)." to ".$recActName." of ".$recBankName
                        ];
                        $this->createMessage($messagesInt, $user_id);

                        if(is_array($result)) {
                            http_response_code(200);
                            echo json_encode($result);
                        } else {
                            http_response_code(500);
                            return false;
                        }
                    }
                } else {
                    $this->insufficentFunds($user_data['balance']);
                    return false;
                }
            } else {
                http_response_code(500);
                return false;
            }
        } catch (Exception $e) {
            // Log the error for debugging
            error_log($e->getMessage());

            // Return an error response
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error = '.$e->getMessage()]);
            return false;
        }
    }

    private function getUserNotification($user_id): array {
        $ObjMessages = new messagesClass(['user_id' => $user_id]);
        $data = $ObjMessages->getConversationsWithUser();
        return $data;
    }

    private function sendMail($user_id, array $data) {
        $title = $data['title']; // Adjust accordingly
        $message = $data['message']; // Adjust accordingly

        $ObjHub = new AllUsers();
        $user_data = $ObjHub->getUserByAct($user_id);

        $message = "More codes for the email here(HTML";

        $success = $this->sendEmail($title, $message, $user_data);

        if($success) {
            http_response_code(200);
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error']);
        }
    }

    private function respondUnprocessableEntity(array $errors): void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    private function responseMethodNotAllowed(string $allowed_method): void {
        http_response_code(405);
        header("Allow: $allowed_method");
    }

    private function respondNotFound(string $user_id): void {
        http_response_code(404);
        echo json_encode(["message" => "User with ID $user_id is not found!"]);
    }

    private function insufficentFunds(string $balance): void {
        http_response_code(422);
        echo json_encode(["message" => "Insufficent funds, your account balance is $balance"]);
    }
    private function getUserData($user_id) {
        $ObjHub = new AllUsers(['user_id' => $user_id]);

        $code5 = rand(10000, 99999);

        if(
            $ObjHub->updateUserRow(
                [
                    'code5' => $code5
                ]
            )
        ) {
            return $ObjHub->getUserByAct($user_id);
        }
    }

    private function updateColum($user_id, array $data) {
        $ObjHub = new AllUsers(['user_id' => $user_id]);
        try {
            $colum = $data['colum'];
            $input = $data['input'];

            if(
                $ObjHub->updateUserRow(
                    [
                        $colum => $input
                    ]
                )
            ) {
                http_response_code(200);
                return $ObjHub->getUserByAct($user_id);
            } else {
                http_response_code(422);
            }
        } catch (Exception $e) {
            // Log the error for debugging
            error_log($e->getMessage());

            // Return an error response
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    /**
     * Summary of create
     * @param mixed $data
     * @param mixed $user_id
     * @return |
     */
    public function createMessage(array $data, $user_id) {
        $ObjHub = new messagesClass(array("user_id" => $user_id));

        if(isset($data['message']) && !empty($data['message'])) {
            $ObjHub->setMessage($data['message']);
            $ObjHub->createdOn();

            return $ObjHub->save();

        } else {
            return false;
        }
    }

    public function debitAccount(string $user_id, $newBalance): bool {
        $ObjUser = new AllUsers(['user_id' => $user_id]);
        $ObjUser->setAmount($newBalance);

        if($ObjUser->load()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Summary of getuserTransaction
     * @param mixed $user_id
     * @return |false
     */
    public function getuserTransaction($user_id): array|false {
        $ObjHub = new transactionsClass(array("user_id" => $user_id));
        $data = $ObjHub->getAllUTrans();
        return $data;
    }
    public function getSomeUserTransaction($user_id, $numData): array|false {
        $ObjHub = new transactionsClass(array("user_id" => $user_id, 'detail' => $numData));
        $data = $ObjHub->getNumUTrans();
        return $data;
    }

    public function getTypeUserTransaction($user_id, $numData): array {
        $ObjHub = new transactionsClass(array("user_id" => $user_id, 'type' => $numData));
        $data = $ObjHub->getUTransByTopic();
        return $data;
    }


    public function getSomeUserCards($user_id, $numData): array|false {
        $ObjHub = new CardsClass(array('user_id' => $user_id, 'numbers' => $numData));
        $data = $ObjHub->getSomeUserCards();
        return $data;
    }

    public function getAllUserCards($user_id): array|false {
        $ObjHub = new CardsClass(array('user_id' => $user_id));
        $data = $ObjHub->getAllUserCards();
        return $data;
    }

    private function respondCreated(string $id): void {
        http_response_code(201);
        echo json_encode(["message" => "Data Sent", "id" => $id]);
    }

    private function respondCreatedFailed(): void {
        http_response_code(405);
        echo json_encode(["message" => "Not Successful", "id" => null]);
    }

    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];

        if($is_new && empty($data["message"])) {
            $errors[] = "message is required";
        }

        if(!empty($data['priority'])) {
            if(filter_var($data['priority'], FILTER_VALIDATE_INT) === false) {
                $errors[] = "Priority must be an integer";
            }
        }
        return $errors;
    }

    private function updateOld(string $user_id, array $data): bool {
        $ObjHub = new messagesClass(array("user_id" => $user_id));

        if((int)$user_id) {
            if(!empty($data['message'])) {
                $ObjHub->setMessage($data['message']);
                $ObjHub->createdOn();
                if($ObjHub->update()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function update(string $user_id, array $data): int {
        $fields = [];

        if(!empty($data['property'])) {
            $fields['property'] = [
                $data["property"],
                PDO::PARAM_STR
            ];
        }

        if(!empty($data['message'])) {
            $fields['message'] = [
                $data["message"],
                PDO::PARAM_STR
            ];
        }

        if(array_key_exists('priority', $data)) {
            $fields['priority'] = [
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if(array_key_exists('is_completed', $data)) {
            $fields['is_completed'] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }

        if(empty($fields)) {
            return 0;
        } else {
            $ObjHub = new messagesClass(array("user_id" => $user_id, "id" => $user_id));
            $data = $ObjHub->updateDate($fields);
            return $data;
        }

    }

    public function delete(string $user_id): int {
        $ObjHub = new messagesClass(array("user_id" => $user_id, "id" => $user_id));
        $data = $ObjHub->delete();
        return $data;
    }

    public function sendEmail($title, $message, $user_data) {
        global $mail;

        $domain = siteDomain;

        $senderEmail = supportMail;
        $senderName = siteName;
        $senderPass = emailPass;

        $receiverEmail = $user_data['email'];
        $receiverName = $user_data['firstname'];

        // Server settings
        $mail->isSMTP();
        $mail->Host = $domain;
        $mail->SMTPAuth = true;
        $mail->Username = $senderEmail;
        $mail->Password = $senderPass;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($receiverEmail, $receiverName);
        $mail->addReplyTo($senderEmail, $senderName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $message;

        // Send email
        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


}
<?php
header("Content-Type: application/json; charset=UTF-8");
// composer dump-autoload [load the json file on terminal and load code]
// ini_set("display_errors", "On");

// ini_set('display_errors', 1);
// error_reporting(E_ALL);
require dirname(__DIR__)."../vendor/autoload.php";
include "../src/functions.php";

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");


// composer require vlucas/phpdotenv [for screte key file]
use Dotenv\Dotenv;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//$mail = new EmailSendingClass;//most be secure mail 

$dotenv = Dotenv::createImmutable(dirname(__DIR__.'/../api/'));
$dotenv->load();
<?php
declare(strict_types=1);

DEFINE('siteName', 'Clintons Bnk');
DEFINE('siteDomain', 'myactiveinvestment.com');
DEFINE('siteLink', 'https://myactiveinvestment.com');
DEFINE('supportMail', 'support@myactiveinvestment.com');
DEFINE('infoMail', 'info@myactiveinvestment.com');
DEFINE('siteAddress', 'USA');
DEFINE('sitePhone', '+1237905555');
DEFINE('emailPass', 'mymeshmail@');
// date_default_timezone_set("America/Los_Angeles");
/**
 * 
 */
class DbConnect
{
	protected $dbConn;

	public function __construct(
		private string $host,
		private string $dbName,
		private string $user,
		private string $password,
	) {

	}

	protected function connect()
	{
		try {
			$conn = new PDO('mysql:host=' . $this->host . '; dbname=' . $this->dbName, $this->user, $this->password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch (PDOException $e) {
			echo 'Database Error: ' . $e->getMessage();
		}
	}
}
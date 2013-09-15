<?php

class Logger {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($pdo) {

		$this->_pdo = $pdo;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Logs a Message to the Database
	 *
	 * If the Message could not be logged to the Logs-Table in the Database,
	 * it tries to log to the Local Log with error_log.
	 *
	 * @param  string $message        The message to log
	 * @param  string $category       The category of the message
	 * @param  string $severity       The severity of the message
	 * @param  string $additionalData Additional Data usable to track bugs etc,
	 *                                formatted as JSON
	 * @return boolean                True on Success, False if an Error
	 *                                occurred while logging to the Database
	 */
	public function log(
		$message,
		$category = NULL,
		$severity = NULL,
		$additionalData = NULL) {

		$sev = (isset($severity)) ? $severity : '';
		$addData = (isset($additionalData)) ? $additionalData : '';
	}

	public function categorySet() {

	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function logUpload(
		$message,
		$category,
		$severity,
		$additionalData) {

		try {
			$this->_pdo->exec("CALL loggerAddLog(
				'$message', '$category', '$severity', '$additionalData')");

		} catch (PDOException $e) {
			error_log(
				'BaBeSK: Could not log an Error with Severity "%s"' .
				'and Category "%s". Message: %s'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Allows the Connection to the Database to log stuff
	 * @var PDO
	 */
	protected $_pdo;
}

?>

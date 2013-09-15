<?php

/**
 * Allows to Log Messages with a Category and Severity to the Database
 */
class Logger {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Logger
	 * @param PDO   $pdo The PDO-Object used to connect to the Database
	 */
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
	 * If the category is set with categorySet() and no category is given,
	 * the function will use the preset Category.
	 *
	 * @param  string $message        The message to log
	 * @param  string $severity       The severity of the message
	 * @param  string $category       The category of the message
	 * @param  string $additionalData Additional Data usable to track bugs etc,
	 *                                formatted as JSON
	 * @return boolean                True on Success, False if an Error
	 *                                occurred while logging to the Database
	 */
	public function log(
		$message,
		$severity = '',
		$category = '',
		$additionalData = '') {

		if($category == '' && !empty($this->_presetCategory)) {
			$category = $this->_presetCategory;
		}

		return $this->logUpload(
			$message, $severity, $category, $additionalData);
	}

	/**
	 * Allows to set a Category so that the Category can be leaved out in log()
	 *
	 * @param  string $category The Preset Category
	 */
	public function categorySet($category) {

		$this->_presetCategory = $category;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Adds the new Log to the Logtable
	 *
	 * @param  string $message        The message to log
	 * @param  string $severity       The severity of the message
	 * @param  string $category       The category of the message
	 * @param  string $additionalData Additional Data usable to track bugs etc,
	 *                                formatted as JSON
	 * @return boolean Returns false on Error, else true
	 */
	protected function logUpload(
		$message,
		$severity,
		$category,
		$additionalData) {

		try {
			$stmt = $this->_pdo->prepare("CALL loggerAddLog(
				:message, :category, :severity, :additionalData)");

			$stmt->execute(array(':message' => $message,
				':category' => $category,
				':severity' => $severity,
				':additionalData' => $additionalData));

		} catch (PDOException $e) {
			error_log(
				"BaBeSK: Could not log an Error with Severity '%severity'" .
				"and Category '%category'. Message: '%message'"
			);
			return false;
		}

		return true;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Allows the Connection to the Database to log stuff
	 * @var PDO
	 */
	protected $_pdo;

	/**
	 * Allows the User to preset a Category
	 * @var string
	 */
	protected $_presetCategory;
}

?>

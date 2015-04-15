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
	 * The log-severities should follow the syslog-standard.
	 * Following log-severities should be used:
	 *     debug   - Debugging level. logging for debugging only. Should not
	 *               happen in production environments.
	 *     info    - Informational. Normal events of interest.
	 *     notice  - Notice. Normal but significant event has occurred, for
	 *               example an unexpected event. Not an error, but could
	 *               require attention.
	 *     warning - Warning. An event has occurred that has the potential to
	 *               cause an error, such as invalid parameters being passed to
	 *               a function.
	 *     error   - Error. An error condition has occurred, such as a failed
	 *               system call. The system is still functioning.
	 *     crit    - Critical. A critical condition exists.
	 *     alert   - Alert. Immediate action is required to prevent the system
	 *               from becoming unstable.
	 *     emerg   - Panic. System does not work anymore, and action needs to
	 *               be taken immediatly.
	 *
	 * @param  string $message        The message to log
	 * @param  string $severity       The severity of the message. If not
	 *                                given, the severity "notice" is assumed
	 * @param  string $category       The category of the message. If not
	 *                                given and not preset with categorySet(),
	 *                                the category "Undefined" is assumed
	 * @param  string $additionalData Additional Data usable to track bugs etc,
	 *                                formatted as JSON
	 * @return boolean                True on Success, False if an Error
	 *                                occurred while logging to the Database
	 */
	public function log(
		$message,
		$severity = 'notice',
		$category = Null,
		$additionalData = '') {

		if(!isset($category)) {
			if(!empty($this->_presetCategory)) {
				$category = $this->_presetCategory;
			}
			else {
				$category = 'Undefined';
			}
		}

		return $this->logUpload(
			$message, $severity, $category, $additionalData
		);
	}

	/**
	 * Like log(), but allows optional data as an array
	 * @param  string $message The message to log
	 * @param  array  $opt     The optional data for the log-entry.
	 *                         Valid keys are:
	 *                             'sev' - The severity,
	 *                             'cat' - The category,
	 *                             'moreJson' - additional data that will be
	 *                                 encoded to json,
	 *                             'moreStr' - additional data that will be
	 *                                 directly written as a string
	 *                             Please note that moreStr and moreJson should
	 *                             not be used in the same call.
	 * @return [type]          [description]
	 */
	public function logO($message, array $opt) {

		$opt['sev'] = (isset($opt['sev'])) ? $opt['sev'] : 'notice';
		$opt['cat'] = (isset($opt['cat'])) ? $opt['cat'] : Null;
		$opt['moreStr'] = (isset($opt['moreStr'])) ? $opt['moreStr'] : '';
		if(isset($opt['moreJson'])) {
			$opt['moreStr'] = json_encode($opt['moreJson']);
		}
		$this->log(
			$message, $opt['sev'], $opt['cat'], $opt['moreStr']
		);
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
			$e_message = $e->getMessage();
			error_log(
				"BaBeSK: Could not log an Error with Severity '$severity'" .
				"and Category '$category'. Message: '$e_message'." .
				"Logmessage: '$message'. Additional Data: '$additionalData'."
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

<?php
	/**
	 * defines the Exceptions used in the Project
	 */

	/**
	 * VoidDataException
	 * This Exception is thrown when void data would be returned
	 * @author voelkerball
	 *
	 */
	class VoidDataException extends Exception{
		function __construct($strMessage) {
			parent::__construct($strMessage);
		}
	}

	/**
	 * MySQLException
	 * This Exception is thrown when a general Error with MySQL-Data occured
	 * @author voelkerball
	 */
	class MySQLException extends Exception{
		function __construct($strMessage) {
			parent::__construct($strMessage);
		}
	}

	/**
	 * MySQLVoidDataException
	 * This Exception is thrown when MySQL has returned no entry
	 * @author voelkerball
	 *
	 */
	class MySQLVoidDataException extends VoidDataException{
		function __construct($strMessage) {
			parent::__construct($strMessage);
		}
	}

	/**
	 * MySQLConnectionException
	 * If the connection to MySQL fails, this Exception should be thrown
	 * @author voelkerball
	 *
	 */
	class MySQLConnectionException extends Exception{
		function __construct($strMessage) {
			parent::__construct($strMessage);
		}
	}

	class ModuleException extends Exception {
		function __construct($msg) {
			parent::__construct($msg);
		}
	}

	class ModuleNotFoundException extends ModuleException {
		function __construct($msg) {
			parent::__construct($msg);
		}
	}

	class HeadModuleNotFoundException extends ModuleException {
		function __construct($msg) {
			parent::__construct($msg);
		}
	}

	class WrongInputException extends Exception{
		function __construct($strMessage, $strFieldName = 'Input') {
			parent::__construct($strMessage);
			$this->strFieldName = $strFieldName;
		}
		function getFieldName (){
			return $this->strFieldName;
		}
		/**
		 * The name of the Field the value was entered
		 */
		protected $strFieldName;
	}

	class CsvExportException extends Exception {
		function __construct($msg) {
			parent::__construct($msg);
		}
	}

	class TemporaryFileException extends Exception {
		function __construct($msg, $code = 0, $previous = NULL) {
			parent::__construct($msg, $code, $previous);
		}
	}
?>
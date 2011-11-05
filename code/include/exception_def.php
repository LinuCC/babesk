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
?>
<?php
require_once PATH_ACCESS . '/TableManager.php';

class FitsManager extends TableManager {
	function __construct() {
		parent::__construct('Fits');
	}

	/**
	 * prepares the user in the fits table
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return boolean
	 */
	function prepUser($uid) {
		if ($this->existsEntry("ID", $uid)) {
			return true;
		} else {
			$this->addEntry("ID", $uid,"passedTest",0,"locked",0);
			return false;

		}
	}

	/**
	 * returns if Fits is passed in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return boolean
	 */
	function  getFits($uid) {
		if ($this->prepUser($uid)) {
			if ($this->getEntryValue($uid, 'passedTest')==1) return true;
		} else {
			return false;
		}
	}

	/**
	 * returns the schoolyear
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 */
	function  getFitsYear($uid) {
		if ($this->prepUser($uid)) {
			return $this->getEntryValue($uid, 'schoolyear');
		} else {
			return false;
		}
	}

	/**
	 * sets fits
	 *
	 *@throws MySQLConnectionException if a problem with MySQL happened
	 */
	function SetFits($uid,$hasFits,$fitsYear) {
		if(isset($uid)) {
			parent::alterEntry($uid, 'passedTest', $hasFits, 'schoolyear', $fitsYear);
		}
	}
}
?>
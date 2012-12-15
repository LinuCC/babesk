<?php

/**
 * Exports not-registrated users as a Csv-file
 */
class UsersExportNonRegistratedCsv {
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	public static function init ($interface, $databaseAccessManager) {
	}
	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////
	/**
	 * Returns the Non-registrated Users of the active schoolyear
	 */
	protected static function nonRegUsersGet () {
		$schoolyearId = self::$_databaseAccessManager->schoolyearActiveGetId ();
		$userIds = self::$_databaseAccessManager->jointUserInSchoolyearGetBySchoolyearId ();
		foreach ($userIds as $id) {
			self::$databaseAccessManager->userIdAddToUserIdArray ($id);
		}
		$users = self::$databaseAccessManager->userGetByUserIdArray ();
		//Move on here when you got to implement some things you need
	}
	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
	protected static $_interface;
	protected static $_databaseAccessManager;
}

?>
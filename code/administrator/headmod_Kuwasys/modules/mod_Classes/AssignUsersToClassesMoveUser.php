<?php

class AssignUsersToClassesMoveUser {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface, $tableName) {
		self::$_userId = self::formVarGet ('userId');
		self::$_classId = self::formVarGet ('classId');
		self::$_movedFromClassId = self::formVarGet ('movedFromClassId');
		self::$_statusId = self::formVarGet ('statusId');
		self::$_oldLinkId = self::formVarGet ('oldLinkId');
		self::$_interface = $interface;
		self::$_tableName = $tableName;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute () {
		if (self::$_classId === NULL && self::$_statusId === NULL) {
			//show select-class-and-status-dialog
			self::dialogClassSelectShow ();
		}
		else {
			//user has chosen in the dialog, add user to class
			self::userMove ();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected static function dialogClassSelectShow () {
		self::statusFetch ();
		self::classesFetch ();
		$userFullname = self::userFullnameGet ();
		self::$_interface->showAssignUsersToClassesMoveUser (
			self::$_userId, $userFullname, self::$_oldLinkId,
			self::$_movedFromClassId, self::$_classes,self::$_statuses);
	}

	protected static function userMove () {
		self::linkNewAdd ();
		self::linkOldDelete ();
		self::$_interface->showAssignUsersToClassesMoveUserFinished (self::$_movedFromClassId);
	}

	protected static function formVarGet ($name) {
		if (isset ($_GET [$name])) {
			return $_GET [$name];
		}
		else if (isset ($_POST [$name])) {
			return $_POST [$name];
		}
		else {
			return NULL;
		}
	}

	protected static function userFullnameGet () {
		$name = '';
		$query = sprintf (
			'SELECT CONCAT(forename, " ", name) AS fullname
			FROM users
			WHERE ID = "%s"', self::$_userId);
		try {
			$user = TableMng::query ($query, true);
			$name = $user [0] ['fullname'];
		} catch (Exception $e) {
			self::$_interface->showError ('Konnte den Namen des Benutzers nicht abrufen');
		}
		return $name;
	}

	protected static function classesFetch () {
		$activeSchoolyearQuery = sprintf (
			'SELECT sy.ID FROM schoolYear sy WHERE sy.active = "%s"', 1);
		$query = sprintf (
			'SELECT c.ID as classId, c.label as classLabel
			FROM class c
			JOIN jointClassInSchoolYear cisy ON c.ID = cisy.ClassID
			WHERE cisy.SchoolYearID = (%s)', $activeSchoolyearQuery);
		try {
			self::$_classes = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Keine Kurse gefunden');
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte die Kurse nicht abrufen');
		}
	}

	protected static function statusFetch () {
		$query = sprintf (
			'SELECT ID AS statusId, translatedName
			FROM usersInClassStatus
			WHERE name IN ("active", "waiting")');
		try {
			self::$_statuses = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Keine Status gefunden');
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte die Status nicht abrufen');
		}
	}

	protected static function linkOldDelete () {
		$query = sprintf (
			'DELETE FROM %s WHERE ID = "%s"',
			self::$_tableName, self::$_oldLinkId);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte den Schüler von dem alten Kurs nicht löschen');
		}
	}

	protected static function linkNewAdd () {
		$query = sprintf (
			'INSERT INTO %s (UserID, ClassID, statusId)
			VALUES ("%s", "%s", "%s");',
			self::$_tableName, self::$_userId, self::$_classId, self::$_statusId);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte den Schüler zu den neuem Kurs nicht hinzufügen');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_classId;
	protected static $_movedFromClassId;
	protected static $_statusId;
	protected static $_oldLinkId;
	protected static $_userId;

	protected static $_classes;
	protected static $_statuses;

	protected static $_interface;
	protected static $_tableName;

}

?>
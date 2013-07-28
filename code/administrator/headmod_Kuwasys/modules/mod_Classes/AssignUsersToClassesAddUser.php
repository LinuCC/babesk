<?php

class AssignUsersToClassesAddUser {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface, $tablename) {
		self::$_interface = $interface;
		self::$_tableName = $tablename;
		self::$_classId = self::formVarGet ('classId');
		self::$_userId = self::formVarGet ('userSelected');
		self::$_statusId = self::formVarGet ('statusId');
		self::$_usernameInput = self::formVarGet ('usernameInput');
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute () {
		if (self::$_userId === NULL && self::$_statusId === NULL || isset ($_POST ['searchUser'])) {
			$users = array ();
			if (self::$_usernameInput !== NULL) {
				self::usersGetSimilarTo (self::$_usernameInput, 10);
			}
			self::statusFetch ();
			self::$_interface->showAssignUsersToClassesAddUserSearch (
				self::$_classId, self::$_bestMatches, self::$_statuses);
		}
		else {
			self::linkNewAdd ();
			self::$_interface->showAssignUsersToClassesAddUserFinished (self::$_classId);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

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

	protected static function usersGetSimilarTo ($username, $maxUsersToGet) {
		self::$_bestMatches = array ();
		self::usersFetch ();
		foreach (self::$_users as $key => $user) {
			$per = 0.0;
			similar_text($username, $user ['userFullname'],
				$per);
			self::$_users [$key] ['percentage'] = $per;
		}
		usort (self::$_users,
			array ('AssignUsersToClassesAddUser', 'userPercentageComp'));
		for ($i = 0; $i < $maxUsersToGet; $i++) {
			self::$_bestMatches [] = self::$_users [$i];
		}
	}

	protected static function userPercentageComp ($user1, $user2) {
		if ($user1 ['percentage'] == $user2 ['percentage']) {
			return 0;
		}
		else if ($user1 ['percentage'] < $user2 ['percentage']) {
			return 1;
		}
		else if ($user1 ['percentage'] > $user2 ['percentage']) {
			return -1;
		}
	}

	/**
	 * Returns all users of this schoolyear
	 */
	protected static function usersFetch () {

		try {
			self::$_users = TableMng::query (
				'SELECT u.ID AS userId,
					CONCAT(u.forename, " ", u.name) AS userFullname
				FROM users u
				JOIN usersInGradesAndSchoolyears uigs ON u.ID = uigs.UserID
					AND uigs.schoolyearId = @activeSchoolyear', true);

		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Konnte keine Benutzer finden');

		} catch (Exception $e) {
			self::$_interface->dieError ('Ein Fehler ist beim Abrufen der Benutzer aufgetreten' . $e->getMessage ());
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

	/**
	 * Adds a new joint between the selected user and the class
	 */
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

	protected static $_users;
	protected static $_statuses;
	protected static $_bestMatches;

	protected static $_classId;
	protected static $_statusId;
	protected static $_userId;

	protected static $_usernameInput;

	protected static $_interface;
	protected static $_tableName;
}

?>

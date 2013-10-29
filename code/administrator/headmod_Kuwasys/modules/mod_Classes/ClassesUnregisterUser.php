<?php

/**
 * Handles things to allow the User to Unregister someone from a Class
 */
class ClassesUnregisterUser {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init($interface) {
		self::$_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute($jointId) {
		self::$_id = $jointId;
		if(!isset($_POST["unregisterDeclined"])
			&& !isset($_POST["unregisterConfirmed"])) {
			self::dialog();
		}
		else if(isset($_POST["unregisterConfirmed"])) {
			self::remove();
		}
		else {
			self::declined();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Shows a confirmation-dialog to the user
	 */
	protected static function dialog() {
		$username = NULL;
		$classname = NULL;
		$query = sprintf(
			"SELECT CONCAT(u.forename, ' ', u.name), c.label
			FROM jointUsersInClass uic
			LEFT JOIN users u ON u.ID = uic.UserID
			LEFT JOIN class c ON c.ID = uic.ClassID
			WHERE uic.ID = ?;
			");
		$stmt = TableMng::getDb()->prepare($query);
		if(!$stmt){
			self::$_interface->dieError('Konnte die Benutzerdaten nicht abrufen');
		}
		$stmt->bind_param("i", self::$_id);
		$stmt->execute();
		$stmt->bind_result($username, $classname);
		$stmt->fetch();
		self::$_interface->unregisterUserConfirmation(self::$_id, $username, $classname);
	}

	/**
	 * Removes the link in the Db
	 */
	protected static function remove() {
		$query = "DELETE FROM jointUsersInClass WHERE ID = ?";
		$stmt = TableMng::getDb()->prepare($query);
		$stmt->bind_param("i", self::$_id);
		if($stmt->execute()) {
			self::linkBackCreate(self::$_id);
			self::$_interface->dieMsg('Der Schüler wurde erfolgreich aus dem Kurs entfernt.' . self::$_backToClassLink);
		}
		else {
			self::$_interface->dieError('Konnte die Verlinkung des Schülers nicht löschen');
		}
	}

	/**
	 * The User declined the Unregistration
	 */
	protected static function declined() {
		self::linkBackCreate(self::$_id);
		self::$_interface->dieMsg('Der Schüler wurde nicht aus dem Kurs entfernt.' . self::$_backToClassLink);
	}

	/**
	 * Creates HTML-Code that links back to the Classdetails, saved in
	 * backToClassLink
	 */
	protected static function linkBackCreate($jointId) {
		$classId = NULL;
		$query = "SELECT ClassID
			FROM jointUsersInClass uic
			WHERE uic.ID = ?";
		$stmt = TableMng::getDb()->prepare($query);
		$stmt->bind_param('i', $jointId);
		$stmt->bind_result($classId);
		$stmt->execute();
		if($stmt->fetch()){
			self::$_backToClassLink = sprintf('<br /><a href="index.php?section=Kuwasys|Classes&amp;action=showClassDetails&amp;ID=%s">zurück</a>', $classId);
		}
		else {
			//Error fetching the ClassID, default to normal Classes-Page
			self::$_backToClassLink = sprintf('<br /><a href="index.php?section=Kuwasys|Classes">zurück</a>');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
	protected static $_interface;

	/**
	 * The ID of the link to delete
	 */
	protected static $_id;

	protected static $_backToClassLink;
}

?>

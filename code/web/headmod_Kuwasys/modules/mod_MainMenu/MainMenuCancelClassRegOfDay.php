<?php

require_once PATH_ACCESS_KUWASYS . '/KuwasysClassUnitManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';

class MainMenuCancelClassRegOfDay {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes the Variables of the Class
	 */
	public static function init ($smarty, $tplPath) {
		self::$_smarty = $smarty;
		self::$_tplPath = $tplPath;
		self::$_unitMng = new KuwasysClassUnitManager ();
		self::$_jUserInClassMng = new KuwasysJointUsersInClass ();
		self::$_jStatusOfClassMng = new KuwasysUsersInClassStatusManager ();
		self::$_globalSettingsMng = new GlobalSettingsManager ();
		self::$_classMng = new KuwasysClassManager ();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute () {
		if (!isset ($_GET ['unitId'])) {
			self::errorMsgDie ('unitId not set');
		}
		if (isset ($_POST ['cancelConfirmed'])) {
			self::delete ();
			self::$_smarty->display (self::$_tplPath . 'finCancelClassRegOfDay.tpl');
		}
		else {
			self::confirmDialog ();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Shows an Confirmation-Dialog
	 */
	protected static function confirmDialog () {
		$unit = self::unitFetch ($_GET ['unitId']);
		self::$_smarty->assign ('unit', $unit);
		self::$_smarty->display (self::$_tplPath. 'confirmCancelClassRegOfDay.tpl');
	}

	/**
	 * Removes the Classes and the Links to the User
	 */
	protected static function delete () {
		if (!self::isGlobalRegEnabled ()) {
			self::errorMsgDie ('Die Registrationen sind gesperrt!');
		}
		$joints = self::jointsFetch ();
		$joints = self::removeByUnit ($joints);
		$allowedStatus = self::validStatusIdFetch ();
		foreach ($joints as $joint) {
			foreach ($allowedStatus as $statId) {
				if ($joint ['statusId'] == $statId) {
					self::jointDelete ($joint ['ID']);
				}
			}
		}
	}

	protected static function errorMsgAdd ($str) {
		$this->_smarty->append('error', $str . '<br>');
	}

	protected static function errorMsgDie ($str) {
		self::$_smarty->append('error', $str . '<br>');
		self::$_smarty->display(self::$_tplPath . 'nothing.tpl');
		die ();
	}

	protected static function jointDelete ($id) {
		try {
			self::$_jUserInClassMng->deleteJoint ($id);
		} catch (Exception $e) {
			self::errorMsgAdd ('Konnte dich nicht aus dem Kurs von der Verbindungs-ID "' . $id . '" löschen!');
		}
	}

	/**
	 * Fetches the Unit from the Database
	 */
	protected static function unitFetch ($id) {
		try {
			$unit = self::$_unitMng->unitGet ($id);
		} catch (Exception $e) {
			self::errorMsgDie ('Konnte die Kurswahleinheit nicht abrufen');
		}
		return $unit;
	}

	/**
	 * returns all JointsUserInClass with the userId of the user
	 */
	protected static function jointsFetch () {
		$userId = $_SESSION ['uid'];
		try {
			$j = self::$_jUserInClassMng->getAllJointsOfUserId ($userId);
		} catch (Exception $e) {
			self::errorMsgDie ('Konnte die Verbindungen zu den Kursen nicht abrufen');
		}
		return $j;
	}

	/**
	 * Returns only those joints that are in the unit to delete
	 */
	protected static function removeByUnit ($joints) {
		$unitId = $_GET ['unitId'];
		$classIds = array ();
		//get all Classes of Joints
		foreach ($joints as $joint) {
			$classIds [] = $joint ['ClassID'];
		}
		$classes = self::$_classMng->getClassesByClassIdArray ($classIds);
		//sort out the classes with the wrong unit
		$classesWithAllowedUnit = array ();
		foreach ($classes as $class) {
			if ($class ['unitId'] == $unitId) {
				$classesWithAllowedUnit [] = $class;
			}
		}
		//and transfer the sort out of classes to the joints, sort them out and return them
		$jointsAllowed = array ();
		foreach ($joints as $joint) {
			foreach ($classesWithAllowedUnit as $class) {
				if ($joint ['ClassID'] == $class ['ID']) {
					$jointsAllowed [] = $joint;
				}
			}
		}
		return $jointsAllowed;
	}

	/**
	 * Returns all statusIds that are valid to delete
	 */
	protected static function validStatusIdFetch () {
		$statusNames = array('request1', 'request2');
		$statusIds = array ();
		try {
			$status = self::$_jStatusOfClassMng->statusGetMultipleByNames ($statusNames);
		} catch (Exception $e) {
			self::errorMsgDie ('Konnte die erlaubten Status nicht abrufen');
		}
		foreach ($status as $stat) {
			$statusIds [] = $stat ['ID'];
		}
		return $statusIds;
	}

	/**
	 * Checks if the Global Registration is allowed or not
	 */
	protected static function isGlobalRegEnabled () {
		try {
			$isEn = self::$_globalSettingsMng->valueGet (GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch (Exception $e) {
			self::errorMsgDie ('Konnte nicht abrufen, ob Kursregistrationen erlaubt sind');
		}
		return ($isEn != '0' && $isEn != '');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_smarty;
	protected static $_tplPath;
	protected static $_unitMng;
	protected static $_jUserInClassMng;
	protected static $_jStatusOfClassMng;
	protected static $_globalSettingsMng;
	protected static $_classMng;

}

?>
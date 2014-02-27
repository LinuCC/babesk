<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassTeacherInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysDatabaseAccess.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';
require_once PATH_WEB . '/WebInterface.php';
require_once 'ClRegSelection.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class ClassList extends Kuwasys {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'formSubmitted':
					$this->registerUserInClasses();
					break;
			}
		}
		else {
			$this->showClassList();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_WEXEC') or die("Access denied");

		$this->_smarty = $dataContainer->getSmarty();
		$this->_pdo = $dataContainer->getPdo();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_databaseAccessManager = new KuwasysDatabaseAccess($this->_interface);
		$this->_userId = $_SESSION ['uid'];
	}

	/**
	 * Shows the the List of selectable Classes to the User
	 */
	private function showClassList() {

		$classes = $this->addRegistrationForUserAllowedToClass();
		$classes = $this->addClassteacherToClass($classes);
		// $sortedClasses = $this->sortClassesAfterWeekdayInArray($classes);
		$classUnits = $this->_databaseAccessManager->kuwasysClassUnitGetAll();
		// $this->_smarty->assign('sortedClasses', $sortedClasses);
		$this->_smarty->assign('classUnits', $classUnits);
		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'classList.tpl');
	}

	/**
	 * Checks if the Class Registration is globally enabled
	 * @return boolean true if classRegistration is enabled
	 */
	private function getIsClassRegistrationGloballyEnabled() {
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		$globalSettingsManager = new GlobalSettingsManager();
		try {
			$value = $globalSettingsManager->valueGet(GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch(Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen vom KurswahlWert aufgetreten. Breche ab.');
		}
		return $value;
	}

	/**
	 * returns all Classes
	 *
	 * @return array  The fetched Classes
	 * @throws Exception If Classes could not be fetched
	 */
	private function getAllClasses() {

		$stmt = $this->_pdo->query('SELECT * FROM class
			WHERE schoolyearId = @activeSchoolyear');

		return $stmt->fetchAll();

		// $classes = $this->_databaseAccessManager->dbAccessExec(
		// 	KuwasysDatabaseAccess::ClassManager, 'getAllClasses');
		// return $classes;
	}

	/**
	 * Returns all JUserInClass of the user logged in
	 * @return array() All Joints, represented by another array
	 */
	private function getAllJointsUsersInClassOfUser() {
		$jFetchExc = new DbAccExceptionMods(DbAccExceptionMods::$MySQLVoidDataException, DbAccExceptionMods::$ModDoNothing);
		$joints = $this->_databaseAccessManager->dbAccessExec(
			KuwasysDatabaseAccess::JUserInClassManager, 'getAllJointsOfUserId',
			array($this->_userId), 'webFetchJUserInClass', array($jFetchExc));
		if(isset($joints))
			{return $joints;}
	}

	/**
	 * Gets all classes and adds elements to the Array
	 * These elements are describing if the registration for the classes are
	 * allowed, This allows to display which class can be selected and which not.
	 * @return array() An array of all classes, with some elements added
	 */
	private function addRegistrationForUserAllowedToClass() {
		$classes = $this->getAllClasses();
		$jointsUsersInClass = $this->getAllJointsUsersInClassOfUser();
		$alreadyUsedWeekdays = array();
		//init the array alreadyUsedWeekdays
		foreach($classes as $class) {
			if(isset($jointsUsersInClass)) {
				foreach($jointsUsersInClass as $joint) {
					if($joint['ClassID'] == $class['ID']) {
						foreach($alreadyUsedWeekdays as $weekday) {
							if($class['unitId'] == $weekday) {
								continue 2;
							}
						}
						$alreadyUsedWeekdays[] =$this->_databaseAccessManager->kuwasysClassUnitGet($class['unitId']);
					}
				}
			}
		}
		foreach($classes as & $class) {
			{
				//check if $class can be selected by the User
				foreach($alreadyUsedWeekdays as $alreadyUsedWeekday) {
					if($class['unitId'] == $alreadyUsedWeekday) {
						$class['registrationForUserAllowed'] = false;
						continue 2;
					}
				}
				if($jointsUsersInClass) {
					foreach($jointsUsersInClass as $joint) {
						if($joint['ClassID'] == $class['ID']) {
							$class['registrationForUserAllowed'] = false;
							continue 2;
						}
					}
				}
				if(!$class['registrationEnabled']) {
					$class['registrationForUserAllowed'] = false;
					continue;
				}
			}
			$class['registrationForUserAllowed'] = true;
		}
		return $classes;
	}

	private function addClassteacherToClass($classes) {
		try {
			$joints = $this->getAllJClassteacherInClass();
			$cts = $this->getAllClassteachers();
		} catch(Exception $e) {
			return $classes;
		}
		foreach($classes as &$class) {
			foreach($joints as $joint) {
				if($joint ['ClassID'] == $class ['ID']) {
					foreach($cts as $ct) {
						if($ct ['ID'] == $joint ['ClassTeacherID']) {
							$class ['classteacher'] = $ct;
						}
					}
				}
			}
		}
		return $classes;
	}

	private function getAllClassteachers() {
		$exc = new DbAccExceptionMods(DbAccExceptionMods::$AllExceptions, DbAccExceptionMods::$ModRethrow);
		return $this->_databaseAccessManager->dbAccessExec(KuwasysDatabaseAccess::ClassteacherManager, 'getAllClassTeachers', array(), 'getAllClassTeachers', array($exc));
	}

	private function getAllJClassteacherInClass() {
		$exc = new DbAccExceptionMods(DbAccExceptionMods::$AllExceptions, DbAccExceptionMods::$ModRethrow);
		return $this->_databaseAccessManager->dbAccessExec(KuwasysDatabaseAccess::JClassteacherInClassManager, 'getAllJoints', array(), 'getAllJoints', array($exc));
	}

	/**
	 * Restructures the array of arrays representing objects after units
	 * @return array the restructured array, every unit has its classes
	 */
	private function sortClassesAfterWeekdayInArray($classes) {
		$classesSorted = array();
		foreach($classes as $class) {
			$classesSorted[$class['unitId']][] = $class;
		}
		return $classesSorted;
	}

	/**
	 * The main-Routine to check and commit the selections of the User
	 */
	private function registerUserInClasses() {
		$this->checkClassListInput();
		$this->checkIsClassRegistrationGloballyEnabled();
		$this->checkAreAllPickedClassesEnabled();
		$this->addRequestsToDatabase();
		$this->_interface->DieMessage(
			'Das Formular wurde erfolgreich verarbeitet. Im Hauptmenü sehen sie ihre Registrierungen.');
	}

	/**
	 * Initializes the Selections of this class
	 */
	private function setSelections() {
		//for each selection checked in the form, add an object
		$selections = array();
		$classUnits = $this->_databaseAccessManager->kuwasysClassUnitGetAll();
		$classUnitIds = array();
		foreach($classUnits as $cU) {
			$classUnitIds [] = $cU ['ID'];
		}
		foreach($classUnitIds as $classUnit) {
			$unitId = $classUnit;
			if(isset($_POST['firstChoice' . $classUnit])) {
				$classId = $_POST['firstChoice' . $classUnit];
				$statusName = 'request1';
				$selections [] = new clRegSelection($classId, $statusName, $unitId);
			}
			if(isset($_POST['secondChoice' . $classUnit])) {
				$classId = $_POST['secondChoice' . $classUnit];
				$statusName = 'request2';
				$selections [] = new clRegSelection($classId, $statusName, $unitId);
			}
		}
		$this->_selections = $selections;
	}

	/**
	 * Initializes the selections of this class further, replacing simple IDs with Objects
	 */
	private function setSelectionVars() {
		$classIds = array();
		$statNames = array();
		$unitIds = array();
		//Prepare to fetch the data alltogether
		foreach($this->_selections as $sel) {
			$classIds [] = $sel->classId;
			$statNames [] = $sel->statusName;
			$unitIds [] = $sel->unitId;
		}
		//Fetch the data
		$classes = $this->_databaseAccessManager->dbAccessExec(KuwasysDatabaseAccess::ClassManager, 'getClassesByClassIdArray', array($classIds));
		$status = $this->_databaseAccessManager->dbAccessExec(KuwasysDatabaseAccess::UserInClassStatusManager, 'statusGetMultipleByNames', array($statNames));
		$units = $this->_databaseAccessManager->dbAccessExec(
			KuwasysDatabaseAccess::ClassUnitManager, 'unitGetMultiple', array($unitIds));
		//Assign the data to the selections
		$this->_selections = ClRegSelection::classesSet($this->_selections, $classes);
		$this->_selections = ClRegSelection::statusSet($this->_selections, $status);
		$this->_selections = ClRegSelection::unitsSet($this->_selections, $units);
	}

	/**
	 * Checks if all selected Classes are Enabled, else dies
	 */
	private function checkAreAllPickedClassesEnabled() {
		$classes = ClRegSelection::classesGetBy($this->_selections, $this->_databaseAccessManager);
		foreach($this->_selections as $sel) {
			if(!$this->checkIsClassEnabled($sel->class ['ID'], $classes)) {
				$this->_interface->DieError('Entry forbidden. Stop Hacking!');
			}
		}
	}

	/**
	 * Checks if the Class is Enabled
	 * @param $classId the ClassId of the Class to check
	 * @param $allClasses the array of classes to search for the class of $classId
	 */
	private function checkIsClassEnabled($classId, $allClasses) {
		foreach($allClasses as $class) {
			if($class ['ID'] == $classId) {
				return(boolean) $class ['registrationEnabled'];
			}
		}
	}

	/**
	 * Checks if the ClassRegistration is globally enabled, else dies
	 */
	private function checkIsClassRegistrationGloballyEnabled() {
		if(!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->DieError('Klassenregistration ist momentan nicht erlaubt!');
		}
	}

	/**
	 * Checks if the user selected something from the list, else dies
	 */
	private	function checkClassListInputForSomethingWasChecked() {
		if(!count($this->_selections)) {
			$this->_interface->DieError('Es wurde nichts ausgewählt.');
		}
	}

	/**
	 * The main-Routine to check the input of the User
	 */
	private function checkClassListInput() {
		$this->setSelections();
		$this->checkClassListInputForSomethingWasChecked();
		$this->setSelectionVars();
		$this->checkClassListInputForDoubledChoices();
		$this->checkClassListInputForOnlySecondChoiceSelected();
		$this->checkClassListInputForExistingJoints();
	}

	/**
	 * checks the input of the user for doubled choices(class selected as first- and second-choice at the same time)
	 */
	private function checkClassListInputForDoubledChoices() {
		foreach($this->_selections as $sel) {
			foreach($this->_selections as $selCheck) {
				if($sel->unitId == $selCheck->unitId &&
					$sel->classId == $selCheck->classId &&
					$sel !== $selCheck) {
					$this->_interface->DieError(
						'Ein Kurs kann nicht gleichzeitig als erste und als zweite Wahl gewählt werden!');
				}
			}
		}
	}

	/**
	 * Checks if the user selected classes at one unit with only a second choice
	 */
	private function checkClassListInputForOnlySecondChoiceSelected() {
		foreach($this->_selections as $sel) {
			if($sel->status ['name'] == 'request2') {
				if(!ClRegSelection::unitHasFirstRequest($this->_selections,
					$sel->unit ['ID'])) {
					$this->_interface->DieError(sprintf( 'Für einen bestimmten Tag wurde keine erste Wahl gewählt, aber eine Zweitwahl. Wenn sie nur eine Wahl haben, wählen sie den Kurs bitte als Erstwahl.%s', Kuwasys::$buttonBackToMM));
				}
			}
		}
	}

	/**
	 * Checks if there are already Classes selected for that day
	 */
	// private function checkClassListInputForExistingJoints() {
	// 	try {
	// 		$joints = ClRegSelection::jUserInClassGetByStatus($this->_selections, $this->_databaseAccessManager);
	// 	} catch(MySQLVoidDataException $e) {
	// 		return; //no joints existing, no problems
	// 	}
	// 	if(!count($joints)) {
	// 		return; //workaround for bug, DbMultiQueryManager not throwing when void
	// 	}
	// 	$classIds = array();
	// 	foreach($joints as $joint) {
	// 		$classIds [] = $joint ['ClassID'];
	// 	}
	// 	//get all classes of classIds
	// 	$classes = $this->_databaseAccessManager->dbAccessExec(
	// 		KuwasysDatabaseAccess::ClassManager, 'getClassesByClassIdArray',
	// 		array($classIds));
	// 	foreach($this->_selections as $sel) {
	// 		$extrClasses = $this->extrElements($classes, 'unitId', $sel->class ['unitId']); //Classes with the same unitId
	// 		if(count($extrClasses)) {
	// 			$this->_interface->DieError(sprintf('Du bist am %s schon für Kurse angemeldet! %s', $sel->unit ['translatedName'], Kuwasys::$buttonBackToMM));
	// 		}
	// 	}
	// }

	protected function checkClassListInputForExistingJoints() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT COUNT(*) FROM jointUsersInClass uic
				JOIN class c ON uic.ClassID = c.ID
				WHERE uic.UserID = :userId AND c.unitId = :unitId
					AND c.schoolyearId = :schoolyearId');

			foreach($this->_selections as $classSelection) {
				$stmt->execute(array(
					'userId' => $_SESSION['uid'],
					'unitId' => $classSelection->unitId,
					'schoolyearId' => $classSelection->class['schoolyearId']
				));

				if((int) $stmt->fetchColumn() > 0) {
					$this->_interface->dieError(_g('You have already chosen ' .
						'a class at %1$s! %2$s',
						$classSelection->unit['translatedName'],
						Kuwasys::$buttonBackToMM
					));
				}
			}

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not check if you already selected classes!') . $e->getMessage());
		}
	}

	/**
	 * Commits the selections of the User to the database
	 */
	private function addRequestsToDatabase() {
		foreach($this->_selections as $sel) {
			$this->_databaseAccessManager->dbAccessExec(
				KuwasysDatabaseAccess::JUserInClassManager, 'addJoint',
				array($this->_userId, $sel->class ['ID'], $sel->status ['ID']));
		}
	}

	/**
	 * Extracts an Element from an Array
	 */
	private function extrElements($array, $needleName, $needle) {
		$elements = array();
		foreach($array as $element) {
			if(!isset($element [$needleName])) {
				continue;
			}
			if($element [$needleName] == $needle) {
				$elements [] = $element;
			}
		}

		return $elements;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	protected $_databaseAccessManager;
	protected $_interface;
	protected $_smarty;
	protected $_smartyPath;

	private $_selections;
	private $_userId;

	protected $_pdo;
}

?>

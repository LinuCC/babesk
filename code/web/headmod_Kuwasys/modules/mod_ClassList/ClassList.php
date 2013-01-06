<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysDatabaseAccess.php';
require_once PATH_WEB . '/WebInterface.php';
require_once 'ClRegSelection.php';

class ClassList extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute ($dataContainer) {

		$this->entryPoint();
		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
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

	private function entryPoint () {

		defined('_WEXEC') or die("Access denied");

		global $smarty;
		$this->_smarty = $smarty;

		$this->_jointUsersInClass = new KuwasysJointUsersInClass();
		$this->_classManager = new KuwasysClassManager();
		$this->_usersManager = new KuwasysUsersManager();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_databaseAccessManager = new KuwasysDatabaseAccess ($this->_interface);
		$this->initWeekdayIdArray ();
		$firstStatusRequest = $this->_databaseAccessManager->usersInClassStatusGetByName ('request1');
		$this->_firstStatusRequestId = $firstStatusRequest ['ID'];
		$secondStatusRequest = $this->_databaseAccessManager->usersInClassStatusGetByName ('request2');
		$this->_secondStatusRequestId = $secondStatusRequest['ID'];
	}

	private function initWeekdayIdArray () {
		$classUnits = $this->_databaseAccessManager->kuwasysClassUnitGetAll ();
		$classUnitIdArray = array();
		foreach ($classUnits as $classUnit) {
			$classUnitIdArray [] = $classUnit ['ID'];
		}
		$this->_weekdayIdArray = $classUnitIdArray;
	}

	private function showClassList () {

		$classes = $this->addRegistrationForUserAllowedToClass();
		// $sortedClasses = $this->sortClassesAfterWeekdayInArray($classes);
		$classUnits = $this->_databaseAccessManager->kuwasysClassUnitGetAll ();
		// $this->_smarty->assign ('sortedClasses', $sortedClasses);
		$this->_smarty->assign ('classUnits', $classUnits);
		$this->_smarty->assign ('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'classList.tpl');
	}

	private function getIsClassRegistrationGloballyEnabled () {

		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		$globalSettingsManager = new GlobalSettingsManager();
		try {
			$value = $globalSettingsManager->valueGet (GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch (Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen vom KurswahlWert aufgetreten. Breche ab.');
		}
		return $value;
	}

	private function getAllClasses () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->DieError('Es sind keine Kurse vorhanden');
		}
		catch (Exception $e) {
			$this->_interface->DieError('Die Kurse konnten nicht abgerufen werden');
		}
		return $classes;
	}

	private function getAllJointsUsersInClassOfUser () {

		try {
			$joints = $this->_jointUsersInClass->getAllJointsOfUserId($_SESSION['uid']);
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		catch (Exception $e) {
			$this->_interface->DieError('konnte deine Kurse nicht abrufen!');
		}
		return $joints;
	}

	private function addRegistrationForUserAllowedToClass () {

		$classes = $this->getAllClasses();
		$jointsUsersInClass = $this->getAllJointsUsersInClassOfUser();
		$alreadyUsedWeekdays = array();

		//init the array alreadyUsedWeekdays
		foreach ($classes as $class) {
			if ($jointsUsersInClass) {
				foreach ($jointsUsersInClass as $joint) {
					if ($joint['ClassID'] == $class['ID']) {
						foreach ($alreadyUsedWeekdays as $weekday) {
							if ($class['unitId'] == $weekday) {
								continue 2;
							}
						}
						$alreadyUsedWeekdays[] =$this->_databaseAccessManager->kuwasysClassUnitGet ($class['unitId']);
					}
				}
			}
		}

		foreach ($classes as & $class) {
			{
				//check if $class can be selected by the User
				foreach ($alreadyUsedWeekdays as $alreadyUsedWeekday) {
					if ($class['unitId'] == $alreadyUsedWeekday) {
						$class['registrationForUserAllowed'] = false;
						continue 2;
					}
				}
				if ($jointsUsersInClass) {
					foreach ($jointsUsersInClass as $joint) {
						if ($joint['ClassID'] == $class['ID']) {
							$class['registrationForUserAllowed'] = false;
							continue 2;
						}
					}
				}
				if (!$class['registrationEnabled']) {
					$class['registrationForUserAllowed'] = false;
					continue;
				}
			}
			$class['registrationForUserAllowed'] = true;
		}
		return $classes;
	}

	private function sortClassesAfterWeekdayInArray ($classes) {

		$classesSorted = array();

		foreach ($classes as $class) {
			$classesSorted[$class['unitId']][] = $class;
		}
		return $classesSorted;
	}

	private function registerUserInClasses () {

		$this->setSelections ();
		$this->setSelectionVars ();
		var_dump($this->_selections);

		die ();
		$this->checkIsClassRegistrationGloballyEnabled();
		$this->checkAreAllPickedClassesEnabled();
		$this->checkClassListInput();
		$this->addRequestsToDatabase();
		$this->_interface->DieMessage(
			'Das Formular wurde erfolgreich verarbeitet. Im Hauptmenü sehen sie ihre Registrierungen.');
	}

	private function setSelections () {
		//for each selection checked in the form, add an object
		$selections = array ();
		foreach ($this->_weekdayIdArray as $weekday) {
			$unitId = $weekday;
			if (isset($_POST['firstChoice' . $weekday])) {
				$classId = $_POST['firstChoice' . $weekday];
				$statusName = 'request1';
				$selections [] = new clRegSelection ($classId, $statusName, $unitId);
			}
			if (isset($_POST['secondChoice' . $weekday])) {
				$classId = $_POST['secondChoice' . $weekday];
				$statusName = 'request2';
				$selections [] = new clRegSelection ($classId, $statusName, $unitId);
			}
		}
		$this->_selections = $selections;
	}

	private function setSelectionVars () {
		$classIds = array ();
		$statNames = array ();
		$unitIds = array ();
		//Prepare to fetch the data alltogether
		foreach ($this->_selections as $sel) {
			$classIds [] = $sel->classId;
			$statNames [] = $sel->statusName;
			$unitIds [] = $sel->unitId;
		}
		//Fetch the data
		$classes = $this->_databaseAccessManager->dbAccessExec (KuwasysDatabaseAccess::ClassManager, 'getClassesByClassIdArray', array ($classIds));
		$status = $this->_databaseAccessManager->dbAccessExec (KuwasysDatabaseAccess::UserInClassStatusManager, 'statusGetMultipleByNames', array ($statNames));
		$units = $this->_databaseAccessManager->dbAccessExec (
			KuwasysDatabaseAccess::ClassUnitManager, 'unitGetMultiple', array ($unitIds));
		//Assign the data to the selections
		$this->_selections = ClRegSelection::classesSet ($this->_selections, $classes);
		$this->_selections = ClRegSelection::statusSet ($this->_selections, $status);
		$this->_selections = ClRegSelection::unitsSet ($this->_selections, $units);
	}

	private function checkAreAllPickedClassesEnabled () {
		$classes = ClRegSelection::classesGetBy ($this->_selections, $this->_databaseAccessManager);
		foreach ($this->_selections as $sel) {
			if(!$this->checkIsClassEnabled($classId, $classes)) {
				$this->_interface->DieError('Entry forbidden. Stop Hacking!');
			}
		}
	}

	// private function checkAreAllPickedClassesEnabled () {

	// 	$classes = $this->getAllClasses();

	// 	foreach ($this->_weekdayIdArray as $weekday) {
	// 		if (isset($_POST['firstChoice' . $weekday])) {
	// 			$classId = $_POST['firstChoice' . $weekday];
	// 			if(!$this->checkIsClassEnabled($classId, $classes)) {
	// 				$this->_interface->DieError('Entry forbidden. Stop Hacking!');
	// 			}
	// 		}
	// 		if (isset($_POST['secondChoice' . $weekday])) {
	// 			$classId = $_POST['secondChoice' . $weekday];
	// 			if(!$this->checkIsClassEnabled($classId, $classes)) {
	// 				$this->_interface->DieError('Entry forbidden. Stop Hacking!');
	// 			}
	// 		}
	// 	}
	// }

	private function checkIsClassEnabled ($classId, $allClasses) {

		foreach ($allClasses as $class) {
			if ($class ['ID'] == $classId) {
				return (boolean) $class ['registrationEnabled'];
			}
		}
	}

	private function checkIsClassRegistrationGloballyEnabled () {
		if(!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->DieError('Klassenregistration ist momentan nicht erlaubt!');
		}
	}

	private	function checkClassListInputForSomethingWasChecked () {
		if (!count ($this->_selections)) {
			$this->_interface->DieError('Es wurde nichts ausgewählt.');
		}
	}

	// private function checkClassListInputForSomethingWasChecked () {

	// 	foreach ($this->_weekdayIdArray as $weekday) {
	// 		if (isset($_POST['firstChoice' . $weekday]) || isset($_POST['secondChoice' . $weekday])) {
	// 			return;
	// 		}
	// 	}
	// 	$this->_interface->DieError('Es wurde nichts ausgewählt.');
	// }

	private function checkClassListInput () {

		$this->checkClassListInputForSomethingWasChecked();
		foreach ($this->_weekdayIdArray as $weekday) {
			$this->checkClassListInputForDoubledChoices($weekday);
			$this->checkClassListInputForOnlySecondChoiceSelected($weekday);
			$this->checkClassListInputForAlreadyExistingJointsForWeekday($weekday);
		}
	}

	private function checkClassListInputForDoubledChoices () {
		foreach ($this->_selections as $sel) {
			foreach ($this->_selections as $selCheck) {
				if ($sel->unitId == $selCheck->unitId &&
					$sel->classId == $selCheck->classId &&
					$sel !== $selCheck) {
					$this->_interface->DieError(
						'Ein Kurs kann nicht gleichzeitig als erste und als zweite Wahl gewählt werden!');
				}
			}
		}
	}


	// private function checkClassListInputForDoubledChoices ($weekday) {

	// 	if (isset($_POST['firstChoice' . $weekday], $_POST['secondChoice' . $weekday])) {
	// 		if ($_POST['firstChoice' . $weekday] == $_POST['secondChoice' . $weekday]) {
	// 			$this->_interface->DieError(
	// 				'Ein Kurs kann nicht gleichzeitig als erste und als zweite Wahl gewählt werden!');
	// 		}
	// 	}
	// }

	private function checkClassListInputForOnlySecondChoiceSelected () {
		foreach ($this->_selections as $sel) {
			if ($sel->status ['name'] == 'request2') {
				if (!ClRegSelection::unitHasFirstRequest ($this->_selections,
					$sel->unit ['ID'])) {
					$this->_interface->DieError(sprintf( 'Für einen bestimmten Tag wurde keine erste Wahl gewählt, aber eine Zweitwahl. Wenn sie nur eine Wahl haben, wählen sie den Kurs bitte als Erstwahl.%s', Kuwasys::$buttonBackToMM));
				}
			}
		}
	}

	// private function checkClassListInputForOnlySecondChoiceSelected ($weekday) {

	// 	if (isset($_POST['secondChoice' . $weekday]) && !isset($_POST['firstChoice' . $weekday])) {
	// 		$this->_interface->DieError(sprintf(
	// 						'Für einen bestimmten Tag wurde keine erste Wahl gewählt, aber eine Zweitwahl.
	// 							Wenn sie nur eine Wahl haben, wählen sie den Kurs bitte als Erstwahl.%s', Kuwasys::$buttonBackToMM)
	// 			);
	// 	}
	// }

	private function checkClassListInputForExistingJoints () {
		try {
			$joints = ClRegSelection::jUserInClassGetByStatus ($this->_selections, $this->_databaseAccessManager);
		} catch (MySQLVoidDataException $e) {
			return; //no joints existing, no problems
		}
		$classIds = array ();
		foreach ($joints as $joint) {
			$classIds [] = $joint ['ClassID'];
		}
		$classes = $this->_databaseAccessManager->dbAccessExec (
			KuwasysDatabaseAccess::ClassManager, 'getClassesByClassIdArray',
			array ($classIds));
		foreach ($this->_selections as $sel) {
			foreach ($classes as $class) {
				if ($sel->class ['unitId'] == $class ['unitId']) {

				}
			}
		}
	}


	// private function checkClassListInputForAlreadyExistingJointsForWeekday ($weekday) {
	// 	var_dump($_POST['firstChoice' .
	// 					$weekday]);
	// 	if (isset($_POST['firstChoice' . $weekday])) {
	// 		try {
	// 			try {
	// 				$this->_jointUsersInClass->getJointOfUserIdAndClassId($_SESSION['uid'], $_POST['firstChoice' .
	// 					$weekday]);
	// 			} catch (MySQLVoidDataException $e) {
	// 				//correct when no joint found
	// 				throw new Exception();
	// 			} catch (Exception $e) {
	// 				$this->_interface->DieError('Fehler beim Abrufen von Daten!');
	// 			}
	// 			$this->_interface->DieError(sprintf('Sie sind schon für diesen Kurs angemeldet! %s', Kuwasys::$buttonBackToMM));
	// 		} catch (Exception $e) {
	// 		}
	// 	}
	// 	if (isset($_POST['secondChoice' . $weekday])) {
	// 		try {
	// 			try {
	// 				$this->_jointUsersInClass->getJointOfUserIdAndClassId($_SESSION['uid'], $_POST['firstChoice' .
	// 					$weekday]);
	// 			} catch (MySQLVoidDataException $e) {
	// 				//correct when no joint found
	// 				throw new Exception();
	// 			} catch (Exception $e) {
	// 				$this->_interface->DieError('Fehler beim Abrufen von Daten!');
	// 			}
	// 			$this->_interface->DieError(sprintf('Sie sind schon für diesen Kurs angemeldet! %s', Kuwasys::$buttonBackToMM));
	// 		} catch (Exception $e) {
	// 		}
	// 	}
	// }

	// private function checkClassListInputForAlreadyExistingJointsForWeekday ($weekday) {
	// }

	// private function checkClInputForJointsBy ($status, $unitId) {
	// 	$this->_databaseAccessManager->dbAccessExec (KuwasysDatabaseAccess::JUserInClassManager, 'getAllJointsByStatusIdAndUserId', array ($status, $_SESSION ['uid']))
	// }

	private function addRequestsToDatabase () {
		die ();
		$userId = $_SESSION['uid'];

		foreach ($this->_weekdayIdArray as $weekday) {
			if (isset($_POST['firstChoice' . $weekday])) {
				$firstChoiceClassId = $_POST['firstChoice' . $weekday];
				$this->_jointUsersInClass->addJoint($userId, $firstChoiceClassId, $this->_firstStatusRequestId);
			}
			if (isset($_POST['secondChoice' . $weekday])) {
				$secondChoiceClassId = $_POST['secondChoice' . $weekday];
				$this->_jointUsersInClass->addJoint($userId, $secondChoiceClassId, $this->_secondStatusRequestId);
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_jointUsersInClass;
	private $_classManager;
	private $_usersManager;
	private $_databaseAccessManager;
	private $_interface;
	private $_smarty;
	private $_smartyPath;
	private $_weekdayIdArray;
	private $_firstStatusRequestId;
	private $_secondStatusRequestId;

	private $_selections;
}

?>
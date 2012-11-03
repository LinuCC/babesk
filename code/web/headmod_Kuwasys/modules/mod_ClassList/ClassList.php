<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_WEB . '/WebInterface.php';

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
		$this->_weekdayAbbreviationArr = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$this->_firstRequestStatusStr = 'request#1';
		$this->_secondRequestStatusStr = 'request#2';
	}

	private function showClassList () {

		$classes = $this->addRegistrationForUserAllowedToClass();
		$sortedClasses = $this->sortClassesAfterWeekdayInArray($classes);
		$this->_smarty->assign('sortedClasses', $sortedClasses);
		$this->_smarty->display($this->_smartyPath . 'classList.tpl');
	}

	private function getIsClassRegistrationGloballyEnabled () {

		require_once PATH_ACCESS_KUWASYS . '/KuwasysGlobalSettingsManager.php';
		$globalSettingsManager = new KuwasysGlobalSettingsManager();
		try {
			$value = $globalSettingsManager->isClassRegistrationGloballyEnabledGet();
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
							if ($class['weekday'] == $weekday) {
								continue 2;
							}
						}
						$alreadyUsedWeekdays[] = $class['weekday'];
					}
				}
			}
		}

		foreach ($classes as & $class) {
			{
				//check if $class can be selected by the User
				foreach ($alreadyUsedWeekdays as $alreadyUsedWeekday) {
					if ($class['weekday'] == $alreadyUsedWeekday) {
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
			$classesSorted[$class['weekday']][] = $class;
		}
		return $classesSorted;
	}

	private function registerUserInClasses () {

		$this->checkIsClassRegistrationGloballyEnabled();
		$this->checkAreAllPickedClassesEnabled();
		$this->checkClassListInput();
		$this->addRequestsToDatabase();
		$this->_interface->DieMessage(
			'Das Formular wurde erfolgreich verarbeitet. Im Hauptmenü sehen sie ihre Registrierungen.');
	}

	private function checkAreAllPickedClassesEnabled () {

		$classes = $this->getAllClasses();

		foreach ($this->_weekdayAbbreviationArr as $weekday) {
			if (isset($_POST['firstChoice' . $weekday])) {
				$classId = $_POST['firstChoice' . $weekday];
				if(!$this->checkIsClassEnabled($classId, $classes)) {
					$this->_interface->DieError('Entry forbidden. Stop Hacking!');
				}
			}
			if (isset($_POST['secondChoice' . $weekday])) {
				$classId = $_POST['secondChoice' . $weekday];
				if(!$this->checkIsClassEnabled($classId, $classes)) {
					$this->_interface->DieError('Entry forbidden. Stop Hacking!');
				}
			}
		}
	}

	private function checkIsClassEnabled ($classId, $allClasses) {

		foreach ($allClasses as $class) {
			if ($class ['ID'] == $classId) {
				return (boolean) $class ['registrationEnabled'];
			}
		}
	}

	private function checkIsClassRegistrationGloballyEnabled () {
		var_dump((boolean)$this->getIsClassRegistrationGloballyEnabled());
		if(!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->DieError('Klassenregistration ist momentan nicht erlaubt!');
		}
	}

	private function checkClassListInputForSomethingWasChecked () {

		foreach ($this->_weekdayAbbreviationArr as $weekday) {
			if (isset($_POST['firstChoice' . $weekday]) || isset($_POST['secondChoice' . $weekday])) {
				return;
			}
		}
		$this->_interface->DieError('Es wurde nichts ausgewählt.');
	}

	private function checkClassListInput () {

		$this->checkClassListInputForSomethingWasChecked();
		foreach ($this->_weekdayAbbreviationArr as $weekday) {
			$this->checkClassListInputForDoubledChoices($weekday);
			$this->checkClassListInputForOnlySecondChoiceSelected($weekday);
			$this->checkClassListInputForAlreadyExistingJointsForWeekday($weekday);
		}
	}

	private function checkClassListInputForDoubledChoices ($weekday) {

		if (isset($_POST['firstChoice' . $weekday], $_POST['secondChoice' . $weekday])) {
			if ($_POST['firstChoice' . $weekday] == $_POST['secondChoice' . $weekday]) {
				$this->_interface->DieError(
					'Ein Kurs kann nicht gleichzeitig als erste und als zweite Wahl gewählt werden!');
			}
		}
	}

	private function checkClassListInputForOnlySecondChoiceSelected ($weekday) {

		if (isset($_POST['secondChoice' . $weekday]) && !isset($_POST['firstChoice' . $weekday])) {
			$this->_interface->DieError(
				'Für einen bestimmten Tag wurde keine erste Wahl gewählt, aber eine Zweitwahl.
					Wenn sie nur eine Wahl haben, wählen sie den Kurs bitte als Erstwahl.'
				);
		}
	}

	private function checkClassListInputForAlreadyExistingJointsForWeekday ($weekday) {

		if (isset($_POST['firstChoice' . $weekday])) {
			try {
				try {
					$this->_jointUsersInClass->getJointOfUserIdAndClassId($_SESSION['uid'], $_POST['firstChoice' .
						$weekday]);
				} catch (MySQLVoidDataException $e) {
					//correct when no joint found
					throw new Exception();
				} catch (Exception $e) {
					$this->_interface->DieError('Fehler beim Abrufen von Daten!');
				}
				$this->_interface->DieError('Sie sind schon für diesen Kurs angemeldet!');
			} catch (Exception $e) {
			}
		}
		if (isset($_POST['secondChoice' . $weekday])) {
			try {
				try {
					$this->_jointUsersInClass->getJointOfUserIdAndClassId($_SESSION['uid'], $_POST['firstChoice' .
						$weekday]);
				} catch (MySQLVoidDataException $e) {
					//correct when no joint found
					throw new Exception();
				} catch (Exception $e) {
					$this->_interface->DieError('Fehler beim Abrufen von Daten!');
				}
				$this->_interface->DieError('Sie sind schon für diesen Kurs angemeldet!');
			} catch (Exception $e) {
			}
		}
	}

	private function addRequestsToDatabase () {

		$userId = $_SESSION['uid'];

		foreach ($this->_weekdayAbbreviationArr as $weekday) {
			if (isset($_POST['firstChoice' . $weekday])) {
				$firstChoiceClassId = $_POST['firstChoice' . $weekday];
				$this->_jointUsersInClass->addJoint($userId, $firstChoiceClassId, $this->_firstRequestStatusStr);
			}
			if (isset($_POST['secondChoice' . $weekday])) {
				$secondChoiceClassId = $_POST['secondChoice' . $weekday];
				$this->_jointUsersInClass->addJoint($userId, $secondChoiceClassId, $this->_secondRequestStatusStr);
			}

		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_jointUsersInClass;
	private $_classManager;
	private $_usersManager;
	private $_interface;
	private $_smarty;
	private $_smartyPath;
	private $_weekdayAbbreviationArr;
	private $_firstRequestStatusStr;
	private $_secondRequestStatusStr;
}

?>
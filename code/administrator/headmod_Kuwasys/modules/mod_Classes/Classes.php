<?php

require_once 'ClassesInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassInSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_INCLUDE . '/Module.php';

/**
 *
 * Notice that a class has to have only one SchoolYear!
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Classes extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute ($dataContainer) {

		$this->entryPoint($dataContainer);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'addClass':
					$this->addClass();
					break;
				case 'csvImport':
					$this->importClassesByCsvFile();
					break;
				case 'showClass':
					$this->showClasses();
					break;
				case 'deleteClass':
					$this->deleteClass();
					break;
				case 'changeClass':
					$this->changeClass();
					break;
				case 'showClassDetails':
					$this->showClassDetails();
					break;
				default:
					$this->_interface->dieError($this->_languageManager->getText('errorWrongActionValue'));
			}
		}
		else {
			$this->showMainMenu();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_classManager = new KuwasysClassManager();
		$this->_syManager = new KuwasysSchoolYearManager();
		$this->_syJointManager = new KuwasysJointClassInSchoolYearManager();
		$this->_jointUserInClassManager = new KuwasysJointUsersInClass();
		$this->_userManager = new KuwasysUsersManager();
		$this->_interface = new ClassesInterface($this->relPath, $this->_dataContainer->getSmarty());
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Classes');
	}

	private function showMainMenu () {

		$this->_interface->showMainMenu();
	}

	private function addClass () {

		if (isset($_POST['label'], $_POST['maxRegistration'], $_POST['description'])) {
			$this->checkClassInput();
			$this->addClassToDatabaseByPost();
			$this->addJointSchoolYearByPost();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddClass'));
		}
		else {
			$this->showAddClass();
		}
	}

	private function showAddClass () {

		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showAddClass($schoolYears);
	}

	private function showClassDetails () {

		$class = $this->getClass();
		$class = $this->addUsersAndSumStatusToClass($class);
		$class = $this->addWeekdayTranslatedToClass($class);
		$this->_interface->showClassDetails($class);
	}

	private function addUsersAndSumStatusToClass ($class) {

		$jointsOfClass = $this->getAllJointsUsersInClassWithClassId($class['ID']);

		if (isset($jointsOfClass)) {
			foreach ($jointsOfClass as $joint) {
				$user = $this->_userManager->getUserByID($joint['UserID']);
				$user['status'] = $joint['status'];
				$class['users'][] = $user;

				if (isset($class['sumStatus'][$user['status']])) {
					$class['sumStatus'][$user['status']] += 1;
				}
				else {
					$class['sumStatus'][$user['status']] = 1;
				}
			}
		}
		return $class;
	}

	private function getAllJointsUsersInClassWithClassId ($classId) {

		try {
			$joints = $this->_jointUserInClassManager->getAllJointsWithClassId($classId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorNoJointsUsersInClassOfClassId'));
			return;
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointsUsersInClassOfClassId'));
		}
		return $joints;
	}

	private function checkClassInput () {

		try {
			inputcheck($_POST['label'], '/\A.{3,100}\z/', $this->_languageManager->getText('formLabel'));
			inputcheck($_POST['maxRegistration'], 'number', $this->_languageManager->getText('formMaxRegistration'));
			inputcheck($_POST['description'], '/\A.{3,1024}\z/', $this->_languageManager->getText('formDescription'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorWrongInput'), $e->getFieldName())
				);
		}
		if (!isset($_POST['schoolYear']) || !$_POST['schoolYear'] || $_POST['schoolYear'] == '') {
			$this->_interface->dieError($this->_languageManager->getText('errorInputSchoolYear'));
		}
		$this->checkSchoolYearExisting();
	}

	private function addClassToDatabaseByPost () {

		$allowRegistration = (isset($_POST['allowRegistration'])) ? true : false;
		$this->addClassToDatabase($_POST['label'], $_POST['description'], $_POST['maxRegistration'], $allowRegistration,
			$_POST['weekday']);
	}

	private function addClassToDatabase ($label, $description, $maxRegistration, $allowRegistration, $weekday) {

		try {
			$this->_classManager->addClass($label, $description, $maxRegistration, $allowRegistration, $weekday);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddClass'));
		}
	}

	private function addClassToDatabaseByCsvImport ($contentArray) {

		foreach ($contentArray as $rowArray) {
			$idOfClass = $this->getNextAutoincrementIdOfClass();
			$this->addClassToDatabase($rowArray['label'], $rowArray['description'], $rowArray['maxRegistration'],
				$rowArray['registrationEnabled'], $rowArray['weekday']);
			if($rowArray ['schoolyearId'] != '') {
				$this->addJointSchoolYear($rowArray ['schoolyearId'], $idOfClass);
			}
		}
	}
	
	private function getNextAutoincrementIdOfClass () {
		
		try {
			$idOfClass = $this->_classManager->getNextAutoIncrementID();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchNextAutoIncrementId'));
		}
		return $idOfClass;
	}

	private function showClasses () {

		$classes = $this->getAllClasses();
		$classes = $this->addSchoolYearLabelToClasses($classes);
		$classes = $this->addRegistrationCountToClasses($classes);
		$classes = $this->addWeekdayTranslatedToClasses($classes);
		$this->_interface->showClasses($classes);
	}

	private function getAllClasses () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClasses'));
		}
		catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorFetchClassesFromDatabase'), $e->
				getMessage()));
		}
		return $classes;
	}

	private function deleteClass () {

		if (isset($_POST['dialogConfirmed'])) {
			$classId = $_GET['ID'];
			$this->deleteClassFromDatabase();
			$this->deleteJointsSchoolYear();
			$this->deleteAllJointsUsersInClassOfClass($classId);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteClass'));
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('deleteClassNotConfirmed'));
		}
		else {
			$this->showDeleteConfirmation();
		}
	}

	private function showDeleteConfirmation () {

		$promptMessage = sprintf($this->_languageManager->getText('confirmDeleteClass'), $this->getLabelOfClass());
		if($this->isClassJointedWithUsers($_GET['ID'])) {$this->_interface->showError($this->_languageManager->getText('warningUsersJointedToClassToDelete'));}
		$confirmYes = $this->_languageManager->getText('confirmDeleteClassYes');
		$confirmNo = $this->_languageManager->getText('confirmDeleteClassNo');
		$this->_interface->showDeleteClassConfirmation($_GET['ID'], $promptMessage, $confirmYes, $confirmNo);
	}
	
	private function isClassJointedWithUsers ($classId) {
		
		try {
			$this->_jointUserInClassManager->getAllJointsWithClassId($classId);
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		return true;
	}

	private function deleteClassFromDatabase () {

		try {
			$this->_classManager->deleteClass($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteClass'));
		}
	}

	private function getLabelOfClass () {

		try {
			$label = $this->_classManager->getLabelOfClass($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLabel'));
		}
		return $label;
	}

	private function changeClass () {

		if (isset($_POST['label'], $_POST['maxRegistration'])) {

			$this->checkClassInput();
			$this->changeClassInDatabase();
			$this->changeJointSchoolYearInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeClass'));
		}
		else {
			$this->showChangeClass();
		}
	}

	private function getClass () {

		try {
			$class = $this->_classManager->getClass($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClass'));
		}
		return $class;
	}

	private function showChangeClass () {

		$class = $this->getClass();
		$nowUsedSchoolYearId = $this->getSchoolYearIdByClassId($class['ID']);
		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showChangeClass($class, $schoolYears, $nowUsedSchoolYearId);
	}

	private function changeClassInDatabase () {

		$allowRegistration = (isset($_POST['allowRegistration'])) ? true : false;
		try {
			$this->_classManager->alterClass($_GET['ID'], $_POST['label'], $_POST['description'], $_POST[
				'maxRegistration'], $allowRegistration, $_POST['weekday']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeClass'));
		}
	}

	private function changeJointSchoolYearInDatabase () {

		try {
			$this->_syJointManager->alterSchoolYearIdOfClassId($_GET['ID'], $_POST['schoolYear']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeLinkSchoolYear'));
		}
	}

	private function getLastAddedClassId () {

		try {
			$lastID = $this->_classManager->getLastClassID();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClasses'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLastID'));
		}
		return $lastID;
	}

	/**-----------------------------------------------------------------------------
	 * Functions for getting variables from other tables
	 *----------------------------------------------------------------------------*/

	private function getAllSchoolYears () {

		try {
			$schoolYears = $this->_syManager->getAllSchoolYears();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}

		return $schoolYears;
	}

	/**
	 * @used-by Classes::checkClassInput()
	 */
	private function checkSchoolYearExisting () {

		try {

			$this->_syManager->getSchoolYear($_POST['schoolYear']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorMissSchoolYear'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYear'));
		}
	}

	/**
	 * connects the new Class-entry with a SchoolYear
	 * @used-by Classes::addClass()
	 */
	private function addJointSchoolYearByPost () {

		try {
			$this->_syJointManager->addJoint($_POST['schoolYear'], $this->getLastAddedClassId());
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorLinkSchoolYear') . $e->getMessage());
		}
	}
	/**
	 * connects the new Class-entry with a SchoolYear
	 */
	private function addJointSchoolYear ($schoolyearId, $classId) {

		try {
			$this->_syJointManager->addJoint($schoolyearId, $classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorLinkSchoolYear') . $e->getMessage());
		}
	}

	/**
	 * deletes all links between the deleted class and the Schoolyear
	 * @used-by Classes::deleteClass()
	 */
	private function deleteJointsSchoolYear () {

		try {

			$this->_syJointManager->deleteAllJointsOfClass($_GET['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningNoJointToSchoolyearFound'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointSchoolyear'));
		}
	}

	/**
	 * adds the labels of SchoolYear to the Class as a value in the array,
	 * to allow showing to the User which Class is linked with which schoolYear
	 */
	private function addSchoolYearLabelToClasses ($classes) {

		foreach ($classes as & $class) {
			$class['schoolYearLabel'] = $this->getSchoolYearLabelByClassId($class['ID']);
		}
		return $classes;
	}

	private function addRegistrationCountToClasses ($classes) {

		foreach ($classes as & $class) {

			$userCount = $this->getCountOfActiveUsersInClass($class['ID']);
			$class['userCount'] = $userCount;
		}
		return $classes;
	}

	private function getCountOfActiveUsersInClass ($classId) {

		try {
			$counter = $this->_jointUserInClassManager->getCountOfActiveUsersInClass($classId);
		} catch (MySQLVoidDataException $e) {
			$counter = 0;
		}
		catch (Exception $e) {
			$this->_interface->showError($this->_languageManager->getText('errorGetCountOfUsersForClass'));
			$counter = -1;
		}
		return $counter;
	}

	private function getSchoolYearLabelByClassId ($classID) {

		$schoolYearID = $this->getSchoolYearIdByClassId($classID);
		if (!isset($schoolYearID)) {
			return;
		}
		$schoolYear = $this->getSchoolYearById($schoolYearID);
		return $schoolYear['label'];
	}

	/**
	 * @used-by Classes::getSchoolYearLabelByClassId
	 */
	private function getSchoolYearById ($schoolYearID) {

		try {
			$schoolYear = $this->_syManager->getSchoolYear($schoolYearID);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYearInLink'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYear'));
		}
		return $schoolYear;
	}

	/**
	 * @used-by Classes::getSchoolYearLabelByClassId
	 */
	private function getSchoolYearIdByClassId ($classID) {

		try {
			$schoolYearID = $this->_syJointManager->getSchoolYearIdOfClassId($classID);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError(sprintf($this->_languageManager->getText('errorNoLinkSchoolYear'), $classID));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLinkSchoolYear'));
		}
		if (isset($schoolYearID)) {
			return $schoolYearID;
		}
	}

	private function getSchoolyearIdBySchoolyearName ($schoolyearName) {

		try {
			$id = $this->_syManager->getSchoolyearIdOfSchoolyearName($schoolyearName);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError(sprintf($this->_languageManager->getText('errorGetSchoolyearBySchoolyearName'),
				$schoolyearName));
		}
		if(isset($id)) {
			return $id;
		}
	}

	/**
	 * returns the translated string of the weekday
	 * @param string $weekdayString The name of the weekday in three Chars (like, "Mon", "Tue", ... )
	 */
	private function getTranslatedWeekday ($weekdayString) {

		$text = $this->_languageManager->getText('weekdayLabel' . $weekdayString);
		return $text;
	}

	private function addWeekdayTranslatedToClass ($class) {

		if (!isset($class['weekday']) || !$class['weekday']) {
			$class['weekdayTranslated'] = false;
		}
		else {
			$translatedWeekday = $this->getTranslatedWeekday($class['weekday']);
			$class['weekdayTranslated'] = $translatedWeekday;
		}
		return $class;
	}

	private function addWeekdayTranslatedToClasses ($classes) {

		foreach ($classes as & $class) {
			$class = $this->addWeekdayTranslatedToClass($class);
		}
		return $classes;
	}

	private function importClassesByCsvFile () {

		if (isset($_FILES['csvFile'])) {
			$this->handleCsvFile ();
		}
		else {
			$this->showImportClassesByCsvFile();
		}
	}

	private function handleCsvFile () {

		require_once PATH_INCLUDE . '/CsvImporter.php';
		$csvManager = new CsvImporter($_FILES['csvFile']['tmp_name'], ';');
		$contentArray = $csvManager->getContents();
		$contentArray = $this->controlVariablesOfCsvImport($contentArray);
		$this->addClassToDatabaseByCsvImport($contentArray);
		$this->_interface->dieMsg($this->_languageManager->getText('finAddClassesByCsv'));
	}

	private function controlVariablesOfCsvImport ($contentArray) {

		foreach ($contentArray as & $rowArray) {

			$rowArray = $this->checkCsvImportVariable('label', $rowArray);
			$rowArray = $this->checkCsvImportVariable('description', $rowArray);
			$rowArray = $this->checkCsvImportVariable('maxRegistration', $rowArray);
			$rowArray = $this->checkCsvImportVariable('registrationEnabled', $rowArray);
			$rowArray = $this->checkCsvImportVariable('weekday', $rowArray);

			$rowArray['schoolyearId'] = (isset($rowArray['schoolyearName'])) ? $this->getSchoolyearIdBySchoolyearName(
				$rowArray['schoolyearName']) : '';
		}
		return $contentArray;
	}

	private function checkCsvImportVariable ($varName, $rowArray) {

		if (!isset($rowArray[$varName])) {
			$rowArray[$varName] = '';
		}
		return $rowArray;
	}
	
	private function deleteAllJointsUsersInClassOfClass ($classId) {
		try {
			$this->_jointUserInClassManager->deleteAllJointsOfClassId($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointsUsersInClass'));
		}
	}

	private function showImportClassesByCsvFile () {

		$this->_interface->showImportClassesByCsvFile();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;

	private $_classManager;
	private $_syManager;
	private $_syJointManager;
	private $_jointUserInClassManager;
	private $_userManager;
	/**
	 * @var KuwasysDataContainer
	 */
	private $_dataContainer;

	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;
}

?>
<?php

require_once 'ClassesInterface.php';
require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE_KUWASYS . '/jointUserInClassStatusDefinition.php';
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
				case 'toggleGlobalClassRegistrationEnabled':
					$this->toggleGlobalClassRegistrationEnabled();
					break;
				case 'assignUsersToClasses':
					$this->assignUsersToClasses();
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
		$this->_interface = new ClassesInterface($this->relPath, $this->_dataContainer->getSmarty());
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Classes');
		require_once PATH_ADMIN . $this->relPath . '../../KuwasysDatabaseAccess.php';
		$this->_databaseAccessManager = new KuwasysDatabaseAccess($this->_interface);
		$this->_jointUserInClassStatusDefiner = new jointUserInClassStatusTranslation($this->_languageManager);

	}

	private function showMainMenu () {

		$isClassRegistrationGloballyEnabled = $this->getIsClassRegistrationGloballyEnabled();
		$this->_interface->showMainMenu($isClassRegistrationGloballyEnabled);
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

	private function toggleGlobalClassRegistrationEnabled () {

		if(isset($_GET['toggleFormSend'])) {
			$isToggled = isset($_POST['toggle']);
			$this->setIsClassRegistrationGloballyEnabled($isToggled);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeIsClassRegistrationEnabledGlobally'));
		}
		else {
			$isGlobalClassRegistrationEnabled = $this->getIsClassRegistrationGloballyEnabled();
			$this->_interface->showToggleGlobalClassRegistration($isGlobalClassRegistrationEnabled);
		}
	}

	private function getIsClassRegistrationGloballyEnabled () {

		return $this->_databaseAccessManager->classRegistrationGloballyEnabledGetAndAddingWhenVoid();
	}

	private function setIsClassRegistrationGloballyEnabled ($toggle) {

		$this->_databaseAccessManager->classRegistrationGloballyIsEnabledSet($toggle);
	}

	private function addIsClassRegistrationGloballyEnabled ($toggle) {

		$this->_databaseAccessManager->classRegistrationGloballyIsEnabledAdd($toggle);
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
				///@ToDo can be made faster with userIdArray!
				$user = $this->_databaseAccessManager->userGet($joint['UserID']);
				$user['status'] = $joint['status'];
				$user['statusTranslated'] = $this->_jointUserInClassStatusDefiner->statusTranslate($joint['status']);
				$user = $this->addChoicesOfDayToUser($user, $class ['weekday']);
				$user = $this->addGradeLabelToUser($user);
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

	private function addChoicesOfDayToUser ($user, $weekday) {

		$joints = $this->_databaseAccessManager->jointUserInClassGetAllByUserIdWithoutDyingWhenVoid($user ['ID']);
		$classIdArray = array();
		foreach ($joints as $joint) {
			$classIdArray [] = $joint ['ClassID'];
		}
		$classes = $this->_databaseAccessManager->classGetByClassIdArray($classIdArray);
		foreach ($classes as $class) {
			if($class ['weekday'] == $weekday) {
				$user ['classesOfSameDay'] [] = $class;
			}
		}
		return $user;
	}

	private function addGradeLabelToUser ($user) {

		$joint = $this->_databaseAccessManager->jointUserInGradeGetByUserIdWithoutDying($user ['ID']);
		if(isset($joint) && is_array($joint)) {
			$grade = $this->_databaseAccessManager->gradeGetById($joint ['GradeID']);
			$user ['gradeName'] = $grade ['gradeValue'] . $grade ['label'];
		}
		return $user;
	}

	private function getAllJointsUsersInClassWithClassId ($classId) {

		return $this->_databaseAccessManager->jointUserInClassGetAllByClassId($classId);
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

		$this->_databaseAccessManager->classAdd($label, $description, $maxRegistration, $allowRegistration, $weekday);
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

		return $this->_databaseAccessManager->classNextAutoincrementIdGet();
	}

	private function showClasses () {

		$classes = $this->getAllClasses();
		$classes = $this->addSchoolYearLabelToClasses($classes);
		$classes = $this->addRegistrationCountToClasses($classes);
		$classes = $this->addWeekdayTranslatedToClasses($classes);
		$classes = $this->addClassteachersToClasses($classes);
		$classes = $this->addCountOfWaitingUsersToClasses($classes);
		$this->_interface->showClasses($classes);
	}

	private function getAllClasses () {

		return $this->_databaseAccessManager->classGetAll();
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

		return $this->_databaseAccessManager->jointUserInClassIsClassJointedWithUser($classId);
	}

	private function deleteClassFromDatabase () {

		$this->_databaseAccessManager->classDelete($_GET['ID']);
	}

	private function getLabelOfClass () {

		return $this->_databaseAccessManager->classLabelGet($_GET['ID']);
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

		return $this->_databaseAccessManager->classGet($_GET['ID']);
	}

	private function showChangeClass () {

		$class = $this->getClass();
		$nowUsedSchoolYearId = $this->getSchoolYearIdByClassId($class['ID']);
		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showChangeClass($class, $schoolYears, $nowUsedSchoolYearId);
	}

	private function changeClassInDatabase () {

		$allowRegistration = (isset($_POST['allowRegistration'])) ? true : false;
		$this->_databaseAccessManager->classChange($_GET['ID'], $_POST['label'], $_POST['description'], $_POST[
				'maxRegistration'], $allowRegistration, $_POST['weekday']);
	}

	private function changeJointSchoolYearInDatabase () {

		$this->_databaseAccessManager->jointClassInSchoolyearChangeByClassId($_GET['ID'], $_POST['schoolYear']);
	}

	private function getLastAddedClassId () {

		return $this->_databaseAccessManager->classIdGetLastAdded();
	}

	/**-----------------------------------------------------------------------------
	 * Functions for getting variables from other tables
	 *----------------------------------------------------------------------------*/

	private function getAllSchoolYears () {

		return $this->_databaseAccessManager->schoolyearGetAll();
	}

	/**
	 * @used-by Classes::checkClassInput()
	 */
	private function checkSchoolYearExisting () {

		$this->_databaseAccessManager->schoolyearCheckExisting($_POST['schoolYear']);
	}

	/**
	 * connects the new Class-entry with a SchoolYear
	 * @used-by Classes::addClass()
	 */
	private function addJointSchoolYearByPost () {

		$this->_databaseAccessManager->jointClassInSchoolyearAdd($_POST['schoolYear'], $this->getLastAddedClassId());
	}
	/**
	 * connects the new Class-entry with a SchoolYear
	 */
	private function addJointSchoolYear ($schoolyearId, $classId) {

		$this->_databaseAccessManager->jointClassInSchoolyearAdd($schoolyearId, $classId);
	}

	/**
	 * deletes all links between the deleted class and the Schoolyear
	 * @used-by Classes::deleteClass()
	 */
	private function deleteJointsSchoolYear () {

		$this->_databaseAccessManager->jointClassInSchoolyearDelete($_GET['ID']);
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

		return $this->_databaseAccessManager->jointUserInClassGetCountOfActiveUsersOfClassId($classId);
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
	private function getSchoolYearById ($schoolyearId) {

		return $this->_databaseAccessManager->schoolyearGet($schoolyearId);
	}

	/**
	 * @used-by Classes::getSchoolYearLabelByClassId
	 */
	private function getSchoolYearIdByClassId ($classId) {

		return $this->_databaseAccessManager->jointClassInSchoolyearGetSchoolyearIdByClassIdWithoutDyingWhenVoid($classId);
	}

	private function getSchoolyearIdBySchoolyearName ($schoolyearName) {

		return $this->_databaseAccessManager->schoolyearIdGetBySchoolyearNameWithoutDying($schoolyearName);
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

	private function addClassteachersToClasses ($classes) {

		$classteachers = $this->getClassteachersByClassesWithoutDieingWhenVoidAndUpdateClasses ($classes);
		foreach ($classes as &$class) {
			foreach ($classteachers as $classteacher) {
				if(!isset($class ['classteacher'] ['ID'])) {
					$class ['classteacher'] = NULL;
				}
				if($class ['classteacher'] ['ID'] == $classteacher ['ID']) {
					$class ['classteacher'] = $classteacher;
				}
			}
		}
		return $classes;
	}

	private function getClassteachersByClassesWithoutDieingWhenVoidAndUpdateClasses (&$classes) {

		$joints = $this->getJointsClassteacherInClassByClassesWithoutDieingWhenVoid ($classes);
		$classteacherIdArray = array();
		foreach($joints as $joint) {
			$classteacherIdArray [] = $joint ['ClassTeacherID'];
			foreach ($classes as &$class) {
				if($joint ['ClassID'] == $class ['ID']) {
					$class ['classteacher'] ['ID'] = $joint ['ClassTeacherID'];
				}
			}
		}
		$classteachers = $this->_databaseAccessManager->classteacherGetByIdArrayWithoutDyingWhenVoid($classteacherIdArray);
		if(is_array($classteachers)) {
			return $classteachers;
		}
	}

	private function getJointsClassteacherInClassByClassesWithoutDieingWhenVoid ($classes) {

		$classIdArray = array();
		foreach ($classes as $class) {
			$classIdArray [] = $class ['ID'];
		}

		$joints = $this->_databaseAccessManager->jointClassteacherInClassGetByClassIdArrayWithoutDyingWhenVoid($classIdArray);
		if(is_array($joints)) {
			return $joints;
		}
	}

	private function getAllJointsOfUsersWaitingWithoutDieingWhenVoid () {

		return $this->_databaseAccessManager->jointUserInClassGetAllWithStatusWaitingWithoutDyingWhenVoid();
	}

	private function addCountOfWaitingUsersToClasses ($classes) {

		$joints = $this->getAllJointsOfUsersWaitingWithoutDieingWhenVoid();
		foreach ($classes as &$class) {
			$userCount = 0;
			if(is_array($joints)) {
				foreach ($joints as $joint) {
					if($joint ['ClassID'] == $class ['ID']) {
						$userCount++;
					}
				}
			}
			$class ['userWaitingCount'] = $userCount;
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

		$this->_databaseAccessManager->jointUserInClassDeleteAllOfClassId($classId);
	}

	private function showImportClassesByCsvFile () {

		$this->_interface->showImportClassesByCsvFile();
	}

	private function assignUsersToClasses () {

		require_once 'AssignUsersToClasses.php';
		$utcManager = new AssignUsersToClasses($this->_interface, $this->_languageManager);
		$utcManager->execute();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;

	private $_databaseAccessManager;

	/**
	 * @var KuwasysDataContainer
	 */
	private $_dataContainer;

	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;

	private $_jointUserInClassStatusDefiner;
}

?>
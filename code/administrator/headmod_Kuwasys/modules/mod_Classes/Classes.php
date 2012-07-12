<?php

require_once 'ClassesInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassInSchoolYearManager.php';
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
				case 'showClass':
					$this->showClass();
					break;
				case 'deleteClass':
					$this->deleteClass();
					break;
				case 'changeClass':
					$this->changeClass();
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
		$this->_interface = new ClassesInterface($this->relPath, $this->_dataContainer->getSmarty());
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Classes');
	}

	private function showMainMenu () {

		$this->_interface->showMainMenu();
	}

	private function addClass () {
		
		if (isset($_POST['label'], $_POST['maxRegistration'])) {
			$this->checkClassInput();
			$this->addClassToDatabase();
			$this->addJointSchoolYear();
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

	private function checkClassInput () {
		
		try {
			inputcheck($_POST['label'], '/\A.{3,100}\z/', $this->_languageManager->getText('formLabel'));
			inputcheck($_POST['maxRegistration'], 'number', $this->_languageManager->getText('formMaxRegistration'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorWrongInput'), $e->getFieldName())
				);
		}
		if(!isset($_POST['schoolYear']) || !$_POST['schoolYear'] || $_POST['schoolYear'] == '') {
			$this->_interface->dieError($this->_languageManager->getText('errorInputSchoolYear'));
		}
		$this->checkSchoolYearExisting();
	}

	private function addClassToDatabase () {

		$this->_classManager->addClass($_POST['label'], $_POST['maxRegistration']);
	}

	private function showClass () {

		$classes = $this->getAllClasses();
		$classes = $this->addSchoolYearLabelToClasses($classes);
		$this->_interface->showClasses($classes);
	}

	private function getAllClasses () {
		
		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorFetchClassesFromDatabase'), $e->
				getMessage()));
		}
		return $classes;
	}
	
	private function deleteClass () {
		
		if(isset($_POST['dialogConfirmed'])) {
			$this->deleteClassFromDatabase();
			$this->deleteJointsSchoolYear();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteClass'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('deleteClassNotConfirmed'));
		}
		else {
			$this->showDeleteConfirmation();
		}
	}
	
	private function showDeleteConfirmation () {
		
		$promptMessage = sprintf($this->_languageManager->getText('confirmDeleteClass'), $this->getLabelOfClass());
		$confirmYes = $this->_languageManager->getText('confirmDeleteClassYes');
		$confirmNo = $this->_languageManager->getText('confirmDeleteClassNo');
		$this->_interface->showDeleteClassConfirmation($_GET['ID'], $promptMessage, $confirmYes, $confirmNo);
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
		
		if(isset($_POST['label'], $_POST['maxRegistration'])) {
			
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
		$nowUsedSchoolYearId = $this->getSchoolYearIdByClassId($class ['ID']);
		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showChangeClass($class, $schoolYears, $nowUsedSchoolYearId);
	}
	
	private function changeClassInDatabase () {
		
		try {
			$this->_classManager->alterClass($_GET['ID'], $_POST['label'], $_POST['maxRegistration']);
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
		} catch (Exception $e) {
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
		} catch (Exception $e) {
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
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYear'));
		}
	}
	
	/**
	 * connects the new Class-entry with a SchoolYear
	 * @used-by Classes::addClass()
	 */
	private function addJointSchoolYear () {
		
		try {
			$this->_syJointManager->addJoint($_POST['schoolYear'], $this->getLastAddedClassId());
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
		
		foreach ($classes as &$class) {
			$class ['schoolYearLabel'] = $this->getSchoolYearLabelByClassId($class ['ID']);
		}
		return $classes;
	}
	
	private function getSchoolYearLabelByClassId ($classID) {
		
		$schoolYearID = $this->getSchoolYearIdByClassId($classID);
		$schoolYear = $this->getSchoolYearById($schoolYearID);
		return $schoolYear ['label'];
	}
	
	/**
	 * @used-by Classes::getSchoolYearLabelByClassId
	 */
	private function getSchoolYearById ($schoolYearID) {
		
		try {
			$schoolYear = $this->_syManager->getSchoolYear($schoolYearID);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYearInLink'));
		} catch (Exception $e) {
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
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorNoLinkSchoolYear'), $classID));
		} catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLinkSchoolYear'));
		}
		return $schoolYearID;
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	
	private $_classManager;
	private $_syManager;
	private $_syJointManager;
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
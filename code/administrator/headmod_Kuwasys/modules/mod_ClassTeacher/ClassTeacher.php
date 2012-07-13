<?php

require_once 'ClassTeacherInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassTeacherManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassTeacherInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassInSchoolYearManager.php';
require_once PATH_INCLUDE . '/Module.php';

/**
 * Grade-Module
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class ClassTeacher extends Module {

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
				case 'addClassTeacher':
					$this->addClassTeacher();
					break;
				case 'showClassTeacher':
					$this->showClassTeachers();
					break;
				case 'deleteClassTeacher':
					$this->deleteClassTeacher();
					break;
				case 'changeClassTeacher':
					$this->changeClassTeacher();
					break;
				default:
					$this->_interface->dieError($this->_languageManager->getText('errorWrongActionValue'));
					break;
			}
		}
		else {
			$this->_interface->displayMainMenu();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * This function initializes Data like the MySQL-Connection for this Class
	 * @param unknown_type $dataContainer
	 */
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('ClassTeacher');
		$this->_interface = new ClassTeacherInterface($this->relPath, $this->_dataContainer->getSmarty(), $this->
			_languageManager);
		$this->_classTeacherManager = new KuwasysClassTeacherManager();
		$this->_classManager = new KuwasysClassManager();
		$this->_classJointManager = new KuwasysJointClassTeacherInClass();
		$this->_schoolYearManager = new KuwasysSchoolYearManager();
		$this->_classInSchoolYearJointManager = new KuwasysJointClassInSchoolYearManager();
	}
	
	/**
	 * handles the part of the module to add the Class-Teacher
	 */
	private function addClassTeacher () {

		if (isset($_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone'])) {
			$this->checkInput();
			$this->checkForCorrectClassInput($_POST['class']);
			$this->addClassTeacherToDatabase();
			$this->addJointClassTeacherInClassAtAddDialog();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddClassTeacher'));
		}
		else {
			$activeClasses = $this->getAllClassesInActiveSchoolYear();
			$this->_interface->displayAddClassTeacher($activeClasses);
		}
	}

	/**
	 * checks the User-Input for errors.
	 */
	private function checkInput () {

		try {
			inputcheck($_POST['name'], 'name', $this->_languageManager->getText('formName'));
			inputcheck($_POST['forename'], 'name', $this->_languageManager->getText('formForename'));
			inputcheck($_POST['address'], '/\A.{5,100}\z/', $this->_languageManager->getText('formAddress'));
			inputcheck($_POST['telephone'], '/\A[0-9]{3,32}\z/', $this->_languageManager->getText('formTelephone'));
		} catch (WrongInputException $e) {

			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorInput'), $e->getFieldName()));
		}
	}

	/**
	 * adds a Class-Teacher to the MySQL-Database
	 */
	private function addClassTeacherToDatabase () {

		try {
			$this->_classTeacherManager->addClassTeacher($_POST['name'], $_POST['forename'], $_POST['address'], $_POST[
				'telephone']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddClassTeacher'));
		}
	}

	/**
	 * Adds a link between a Class-Teacher and a Class
	 * @param numeric_string $classTeacherID The ID of the Class Teacher
	 * @param numeric_string $classID the ID of the class
	 */
	private function addJointClassTeacherInClass ($classTeacherID, $classID) {
		
		try {
			$this->_classJointManager->addJoint($classTeacherID, $classID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddClassTeacherToClassLink'));
		}
	}
	
	/**
	 * Prepares to add a link between a Class-Teacher and a Class
	 * @uses ClassTeacher::addJointClassTeacherInClass
	 * @used-by ClassTeacher::addClassTeacher
	 */
	private function addJointClassTeacherInClassAtAddDialog () {

		$lastID = $this->getLastAddedClassTeacherId();
		foreach ($_POST['class'] as $class) {
			if ($class != 'NoClass') {
				$this->addJointClassTeacherInClass($lastID, $class);
			}
		}
	}
	
	/**
	 * Shows the Class-Teachers to the User
	 */
	private function showClassTeachers () {

		$classTeachers = $this->getAllClassTeachersWithClassLabel();
		$this->_interface->displayShowClassTeacher($classTeachers);
	}

	/**
	 * returns all Class-Teachers in the Database
	 */
	private function getAllClassTeachers () {

		try {
			$classTeachers = $this->_classTeacherManager->getAllClassTeachers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeachers'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassTeachers'));
		}
		return $classTeachers;
	}

	/**
	 * Returns a Class-Teacher with the ID of '$_GET['ID']'
	 */
	private function getClassTeacher () {

		try {
			$classTeacher = $this->_classTeacherManager->getClassTeacher($_GET['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeacher'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassTeacher'));
		}
		return $classTeacher;
	}
	
	/**
	 * entry-Point for deleting the ClassTeacher and showing dialogs to the User
	 */
	private function deleteClassTeacher () {

		if (isset($_POST['dialogConfirmed'])) {
			$this->deleteClassTeacherFromDatabase();
			$this->deleteJointClassTeacherToClassByClassTeacherId ($_GET['ID']);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteClassTeacher'));
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('classTeacherNotDeleted'));
		}
		else {
			$this->showConfirmationDialogDeleteClassTeacher();
		}
	}
	
	/**
	 * Displays a Confirmation-Dialog to the user to choose if the User really wants to delete the ClassTeacher
	 */
	private function showConfirmationDialogDeleteClassTeacher () {

		$classTeacher = $this->getClassTeacher();
		$this->_interface->displayConfirmDeleteClassTeacher($classTeacher);
	}
	
	/**
	 * Deletes the ClassTeacher with the ID '$_GET['ID']' from the Database
	 * @used-by ClassTeacher::deleteClassTeacher
	 */
	private function deleteClassTeacherFromDatabase () {

		try {
			$this->_classTeacherManager->deleteClassTeacher($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteClassTeacher'));
		}
	}
	
	/**
	 * entry-point for changing the ClassTeacher and displaying dialogs to the User
	 */
	private function changeClassTeacher () {

		if (isset($_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone'])) {
			$this->checkInput();
			$this->changeClassTeacherInDatabase();
			$this->changeJointsClassTeacherInClassInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeClassTeacher'));
		}
		else {
			$classTeacher = $this->getClassTeacher();
			$classes = $this->addIsSelectedValueToClassesByClassTeacher($this->getAllClasses(), $_GET['ID']);
			$this->_interface->displayChangeClassTeacher($classTeacher, $classes);
		}
	}
	
	/**
	 * changes the Class-Teacher in the Database based on post- and get-variables
	 */
	private function changeClassTeacherInDatabase () {

		try {
			$this->_classTeacherManager->alterClassTeacher($_GET['ID'], $_POST['name'], $_POST['forename'], $_POST[
				'address'], $_POST['telephone']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeacher'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeClassTeacher'));
		}
	}

	/**
	 * changes the Links between Classteachers and Classes in the Database based on post- and get- variables
	 */
	private function changeJointsClassTeacherInClassInDatabase () {

		$this->checkForCorrectClassInput($_POST['class']);
		$allJointsClassTeacherToClass = $this->getJointsClassTeacherToClassByClassTeacherId($_GET['ID']);
		foreach ($allJointsClassTeacherToClass as $joint) {
			foreach ($_POST['class'] as $class) {
				if ($class == $joint ['ClassID']) {
					continue 2;
				}
			}
			$this->deleteJointClassTeacherToClass($joint['ID']);
		}
		foreach ($_POST['class'] as $class) {
			foreach ($allJointsClassTeacherToClass as $joint) {
				if ($class == $joint ['ClassID']) {
					continue 2;
				}
			}
			$this->addJointClassTeacherInClass($_GET['ID'], $class);
		}
	}

	/**
	 * Checks if the Entry 'No Class' has been chosen with other entries defining a class,
	 * thus creating a contradictory statement
	 */
	private function checkForCorrectClassInput ($classes) {

		foreach ($_POST['class'] as $class) {
			if ($class == 'NoClass') {
				if (count($_POST['class']) > 1) {
					$this->_interface->dieError($this->_languageManager->getText(
						'errorNoClassSelectedButMultipleOthersToo'));
				}
				else {
					$this->_interface->showMsg($this->_languageManager->getText('warningClassNotAddedToClassTeacher'));
					return;
				}
			}
		}
	}

	/**
	 * returns the ID of the ClassTeacher last added
	 */
	private function getLastAddedClassTeacherId () {

		try {
			$lastID = $this->_classTeacherManager->getLastAddedId();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLastAddedClassTeacherID'));
		}
		return $lastID;
	}

	/**-----------------------------------------------------------------------------
	 * Functions handling other tables than Classteacher
	 *----------------------------------------------------------------------------*/

	/**
	 * returns all the classes that are in the active schoolYear
	 */
	private function getAllClassesInActiveSchoolYear () {

		$classes = $this->getAllClasses();
		if (isset($classes) && count($classes)) {
			$activeClasses = $this->separateClassesInActiveSchoolYear($classes);
		}
		return $activeClasses;
	}

	/**
	 * returns all Classes in the Database
	 */
	private function getAllClasses () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningNoClasses'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClasses'));
		}
		return $classes;
	}

	/**
	 * Returns the ID of the Active SchoolYear
	 */
	private function getActiveSchoolYearId () {

		try {
			$schoolYear = $this->_schoolYearManager->getActiveSchoolYear();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoActiveSchoolYear'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchActiveSchoolYear'));
		}
		return $schoolYear['ID'];
	}

	/**
	 * returns all Links between Classes and SchoolYears
	 */
	private function getAllJointsClassInSchoolYear () {

		try {
			$joints = $this->_classInSchoolYearJointManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoLinksClassInSchoolYear'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchLinksClassInSchoolYear'));
		}
		return $joints;
	}

	/**
	 * returns all Links between Class-Teachers and Classes
	 */
	private function getAllJointsClassTeacherInClass () {

		try {
			$joints = $this->_classJointManager->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningNoClassTeacherInClassLinks'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassTeacherInClassLinks'));
		}
		if (isset($joints)) {
			return $joints;
		}
	}

	/**
	 * Separates the Classes of the Active Schoolyear from the given classes-Parameter and returns them
	 * @param unknown_type $classes
	 */
	private function separateClassesInActiveSchoolYear ($classes) {

		$activeSchoolYearID = $this->getActiveSchoolYearId();
		$jointsClass = $this->getAllJointsClassInSchoolYear();
		$activeClasses = array();

		foreach ($jointsClass as $joint) {

			if ($joint['SchoolYearID'] == $activeSchoolYearID) {
				foreach ($classes as $class) {
					if ($class['ID'] == $joint['ClassID']) {
						$activeClasses[] = $class;
					}
				}
			}
		}
		return $activeClasses;
	}

	/**
	 * Adds the Array-Key 'classLabel' to every classTeacher who instructs a course.
	 */
	private function getAllClassTeachersWithClassLabel () {

		$classTeachers = $this->getAllClassTeachers();
		$classes = $this->getAllClasses();
		$classTeacherJoints = $this->getAllJointsClassTeacherInClass();

		if (isset($classTeacherJoints) && count($classTeacherJoints)) {
			foreach ($classTeachers as & $classTeacher) {
				foreach ($classTeacherJoints as $classTeacherJoint) {
					if ($classTeacherJoint['ClassTeacherID'] == $classTeacher['ID']) {
						foreach ($classes as $class) {
							if ($class['ID'] == $classTeacherJoint['ClassID']) {
								$classTeacher['classLabel'][] = $class['label'];
							}
						}
					}
				}
			}
		}
		return $classTeachers;
	}

	/**
	 * Returns all Links between Class-Teacher and Classes that are connected to the ID of the 
	 * ClassTeacher $classTeacherID
	 * @param unknown_type $classTeacherID
	 */
	private function getJointsClassTeacherToClassByClassTeacherId ($classTeacherID) {

		$joints = $this->getAllJointsClassTeacherInClass();
		if (isset($joints) && count($joints)) {
			$sortedJoints = array();
			foreach ($joints as $joint) {
				if ($joint['ClassTeacherID'] == $classTeacherID) {
					$sortedJoints[] = $joint;
				}
			}
			return $sortedJoints;
		}
	}

	/**
	 * deletes all links between the Class-Teacher and the Classes that are connected to the ID of the
	 * ClassTeacher $classTeacherID
	 * @param unknown_type $classTeacherID
	 */
	private function deleteJointClassTeacherToClassByClassTeacherId ($classTeacherID) {

		$joints = $this->getJointsClassTeacherToClassByClassTeacherId($classTeacherID);
		if (isset($joints) && count($joints)) {
			foreach ($joints as $joint) {
				$this->deleteJointClassTeacherToClass($joint['ID']);
			}
		}
	}

	/**
	 * deletes a link between the ClassTeacher and the Class in the Database
	 * @param unknown_type $ID
	 */
	private function deleteJointClassTeacherToClass ($ID) {

		try {
			$this->_classJointManager->deleteJoint($ID);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError($this->_languageManager->getText('warningDeleteJointMissing'));
		}
		catch (Exception $e) {
			$this->_interface->showError($this->_languageManager->getText('warningDeleteJoint'));
		}
	}

	/**
	 * Checks for every Class if it is teached by the ClassTeacher defined by the classTeacherID.
	 * If yes, the change-ClassTeacher-form needs to preselect this class
	 *
	 * @used-by ClassTeacher::
	 */
	private function addIsSelectedValueToClassesByClassTeacher ($classes, $classTeacherID) {

		$jointsClassTeacherInClass = $this->getJointsClassTeacherToClassByClassTeacherId($classTeacherID);
		foreach ($classes as & $class) {
			foreach ($jointsClassTeacherInClass as $joint) {
				if ($class['ID'] == $joint['ClassID']) {
					$class['selected'] = true;
					continue 2;
				}
			}
			$class['selected'] = false;
		}
		return $classes;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_classTeacherManager;
	private $_classJointManager;
	private $_classManager;
	private $_schoolYearManager;
	private $_classInSchoolYearJointManager;
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
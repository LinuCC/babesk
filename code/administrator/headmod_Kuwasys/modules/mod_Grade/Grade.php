<?php

require_once 'GradeInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysGradeManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointGradeInSchoolYear.php';
require_once PATH_INCLUDE . '/Module.php';

/**
 * Grade-Module
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Grade extends Module {

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
				case 'addGrade':
					$this->addGrade();
					break;
				case 'showGrades':
					$this->showGrades();
					break;
				case 'deleteGrade':
					$this->deleteGrade();
					break;
				case 'changeGrade':
					$this->changeGrade();
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
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Grade');
		$this->_interface = new GradeInterface($this->relPath, $this->_dataContainer->getSmarty(), $this->
			_languageManager);
		$this->_gradeManager = new KuwasysGradeManager();
		$this->_jointGradeInSchoolyear = new KuwasysJointGradeInSchoolYear();
		$this->_schoolyearManager = new KuwasysSchoolYearManager();
	}

	private function addGrade () {

		if (isset($_POST['label'], $_POST['year'])) {
			$this->checkGradeInput();
			$this->addGradeToDatabase();
			$this->addJointGradeInSchoolyearInAddGrade();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddGrade'));
		}
		else {
			$schoolyears = $this->getAllSchoolyears();
			$this->_interface->displayAddGrade($schoolyears);
		}
	}

	private function checkGradeInput () {

		try {
			inputcheck($_POST['label'], '/\A[^\+\^\~\\\" \/]{1,50}\z/', $this->_languageManager->getText('formLabel'));
			inputcheck($_POST['year'], '/\A\d{1,2}\z/', $this->_languageManager->getText('formYear'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorAddGradeInput'), $e->getFieldName
				()));
		}
	}

	private function addGradeToDatabase () {

		try {
			$this->_gradeManager->addGrade($_POST['label'], $_POST['year']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddGrade'));
		}
	}

	private function showGrades () {

		$grades = $this->getAllGrades();
		$grades = $this->setUpSchoolyearLabelInAllGrades($grades);
		$this->_interface->displayShowGrades($grades);
	}

	private function getAllGrades () {

		try {
			$grades = $this->_gradeManager->getAllGrades();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrades'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorShowGrades'));
		}
		return $grades;
	}

	private function deleteGrade () {

		if (isset($_POST['dialogConfirmed'])) {
			$this->deleteGradeFromDatabase();
			$this->deleteJointGradeInSchoolyearByGradeId($_GET['ID']);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteGrade'));
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('gradeNotDeleted'));
		}
		else {
			$this->showDeleteGradeConfirmation();
		}
	}

	private function showDeleteGradeConfirmation () {

		$grade = $this->getGrade();
		$this->_interface->displayDeleteGradeConfirmation($grade);
	}

	private function deleteGradeFromDatabase () {

		try {
			$this->_gradeManager->delEntry($_GET['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrade'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteGrade'));
		}
	}

	private function getGrade () {

		try {
			$grade = $this->_gradeManager->getGrade($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrade'));
		}
		return $grade;
	}

	private function changeGrade () {

		if (isset($_POST['label'], $_POST['year'])) {

			$this->checkGradeInput();
			$this->changeGradeInDatabase();
			$this->changeJointGradeInSchoolyearInChangeGrade();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeGrade'));
		}
		else {
			$this->showChangeGrade();
		}
	}

	private function changeGradeInDatabase () {

		try {
			$this->_gradeManager->alterGrade($_GET['ID'], $_POST['label'], $_POST['year']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrade'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeGrade'));
		}
	}

	private function showChangeGrade () {

		$grade = $this->getGrade();
		$grade = $this->setUpSchoolyearIdInGrade($grade);
		$schoolyears = $this->getAllSchoolyears();
		$this->_interface->displayChangeGrade($grade, $schoolyears);
	}

	/********************
	 * KuwasysSchoolYearManager
	 ********************/

	/**
	 * retunrs all entries of the SchoolYear-table in the Database
	 */
	private function getAllSchoolyears () {

		try {
			$schoolyears = $this->_schoolyearManager->getAllSchoolYears();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}
		return $schoolyears;
	}

	/********************
	 * KuwasysJointGradeInSchoolyear
	 ********************/

	/**
	 * adds a link between a grade and a Schoolyear
	 * @param unknown_type $gradeId
	 * @param unknown_type $schoolyearId
	 */
	private function addJointGradeInSchoolyear ($gradeId, $schoolyearId) {

		try {
			$this->_jointGradeInSchoolyear->addJoint($gradeId, $schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddLinkGradeInSchoolyear'));
		}
	}

	/**
	 * deletes a link between a grade and a schoolyear in database
	 * @param unknown_type $id
	 */
	private function deleteJointGradeInSchoolyearByGradeId ($id) {

		try {
			$this->_jointGradeInSchoolyear->deleteJointByGradeId($id);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointGradeInSchoolyear'));
		}
	}

	/**
	 * adds a link between a grade and a schoolyear using parameters given by the user in the form
	 */
	private function addJointGradeInSchoolyearInAddGrade () {

		$gradeId = $this->_gradeManager->getLastInsertedID();
		$this->addJointGradeInSchoolyear($gradeId, $_POST['schoolyear']);
	}

	private function getAllJointsGradeInSchoolyear () {
		
		try {
			$joints = $this->_jointGradeInSchoolyear->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}
		return $joints;
	}
	
	/**
	 * adds an array-key 'schoolyearId' to the Grade-array
	 */
	private function setUpSchoolyearIdInGrade ($grade) {

		try {
			$schoolyearId = $this->_jointGradeInSchoolyear->getSchoolyearIdOfGradeId($grade['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningGradeNotLinkedToSchoolyear'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolyearForGrade'));
		}
		if (isset($schoolyearId)) {
			$grade['schoolyearId'] = $schoolyearId;
		}
		return $grade;
	}

	/**
	 * returns the Link between a Grade and a Schoolyear that has the grade-ID $gradeId
	 * @param unknown_type $gradeId
	 */
	private function getJointGradeInSchoolyearByGradeId ($gradeId) {

		try {
			$joint = $this->_jointGradeInSchoolyear->getJointByGradeId($gradeId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointGradeInSchoolyear'));
		}
		return $joint;
	}

	/**
	 * If the user wants a grade to link to another schoolyear, delete the old table-entry and add a new one with the
	 * updated schoolyear
	 */
	private function changeJointGradeInSchoolyearInChangeGrade () {

		try {
			$jointBefore = $this->_jointGradeInSchoolyear->getJointByGradeId($_GET['ID']);
		} catch (Exception $e) {
			//no need to delete the old link because there is no link for this grade in the database
			$this->addJointGradeInSchoolyear($_GET['ID'], $_POST['schoolyear']);
			return;
		}
		if ($jointBefore['ID'] != $_POST['schoolyear']) {
			$this->deleteJointGradeInSchoolyearInChangeGrade($jointBefore['ID']);
			$this->addJointGradeInSchoolyear($_GET['ID'], $_POST['schoolyear']);
		}
	}

	/**
	 * deletes a link between a grade and a schoolyear, but its fitted for the changeGrade-part and it wont error out
	 * if the link could not be deleted.
	 * @param unknown_type $id
	 */
	private function deleteJointGradeInSchoolyearInChangeGrade ($id) {

		try {
			$this->_jointGradeInSchoolyear->deleteJoint($id);
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorDeleteJointGradeInSchoolyear'));
		}
	}

	private function setUpSchoolyearLabelInAllGrades ($grades) {
		
		$schoolyears = $this->getAllSchoolyears();
		$jointsGradeInSchoolyear = $this->getAllJointsGradeInSchoolyear();

		if (isset($jointsGradeInSchoolyear) && count($jointsGradeInSchoolyear)) {
			foreach ($schoolyears as $schoolyear) {
				foreach ($jointsGradeInSchoolyear as $gradeInSchoolYearJoint) {
					if ($gradeInSchoolYearJoint['SchoolYearID'] == $schoolyear['ID']) {
						foreach ($grades as & $grade) {
							if ($grade['ID'] == $gradeInSchoolYearJoint['GradeID']) {
								$grade ['schoolyearLabel'] = $schoolyear ['label'];
							}
						}
					}
				}
			}
		}
		return $grades;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_gradeManager;
	private $_schoolyearManager;
	private $_jointGradeInSchoolyear;
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
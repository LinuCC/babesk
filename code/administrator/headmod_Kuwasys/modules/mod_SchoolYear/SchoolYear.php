<?php

require_once 'SchoolYearInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysLanguageManager.php';
require_once PATH_INCLUDE . '/Module.php';

/**
 * SchoolYear-Module
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class SchoolYear extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
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
				case 'addSchoolYear':
					$this->addSchoolYear();
					break;
				case 'showSchoolYear':
					$this->showSchoolYears();
					break;
				case 'activateSchoolYear':
					$this->activateSchoolYear();
					break;
				case 'changeSchoolYear':
					$this->changeSchoolYear();
					break;
				case 'deleteSchoolYear':
					$this->deleteSchoolYear();
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
	private function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');


		$this->_dataContainer = $dataContainer;
		$this->_languageManager = new KuwasysLanguageManager();
		$this->_languageManager->setModule('SchoolYear');
		$this->_interface = new SchoolYearInterface($this->relPath,
			$this->_dataContainer->getSmarty(), $this->_languageManager);
		$this->_syManager = new KuwasysSchoolYearManager();
	}

	private function addSchoolYear() {

		if(isset($_POST['label'])) {

			$this->handleCheckboxActive();
			$this->checkInput();
			$this->addSchoolYearToDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddSchoolYear'));
		}
		else {
			$this->showAddSchoolYearForm();
		}
	}

	private function handleCheckboxActive() {

		if(!isset($_POST['active'])) {
			$_POST['active'] = 0;
		}
		else {
			$_POST['active'] = true;
		}
	}

	private function checkInput() {

		try {
			inputcheck($_POST['label'], 'name', $this->_languageManager->getText('formLabel'));
		} catch(WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorInput'), $e->getFieldName()));
		}
	}

	private function addSchoolYearToDatabase() {

		try {
			TableMng::query("INSERT INTO schoolyears(label, active)
				VALUES ($_POST['label'], $_POST['active'])");

		} catch(Exception $e) {
			$this->_interface->dieError(_('Could not add the Schoolyear'));
		}
	}

	private function showAddSchoolYearForm() {

		$this->_interface->displayAddSchoolYear();
	}

	private function showSchoolYears() {

		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->displayShowSchoolYears($schoolYears);
	}

	private function getAllSchoolYears() {

		try {
			$schoolYears = $this->_syManager->getAllSchoolYears();
		} catch(MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		}
		catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}
		return $schoolYears;
	}

	private function deleteSchoolYear() {

		if(isset($_POST['dialogConfirmed'])) {
			$this->deleteSchoolYearInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteSchoolYear'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('deleteSchoolYearDeclined'));
		}
		else {
			$this->_interface->displayDeleteSchoolYearConfirmation($this->getSchoolYear());
		}
	}

	private function deleteSchoolYearInDatabase() {

		try {
			$this->_syManager->deleteSchoolYear($_GET['ID']);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYear'));
		} catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteSchoolYear'));
		}
	}

	private function activateSchoolYear() {

		if(isset($_POST['dialogConfirmed'])) {
			$this->activateSchoolYearInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedActivateSchoolYear'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('notActivateSchoolYear'));
		}
		else {
			$this->showActivateSchoolYearConfirmationDialog();
		}
	}

	private function activateSchoolYearInDatabase() {

		$this->_syManager->activateSchoolYear($_GET['ID']);
	}

	private function showActivateSchoolYearConfirmationDialog() {

		$schoolYear = $this->getSchoolYear();
		$this->_interface->displayActivateSchoolYearConfirmation($schoolYear);
	}

	private function getSchoolYear() {

		try {
			$schoolYear = $this->_syManager->getSchoolYear($_GET['ID']);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYear'));
		} catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYear'));
		}
		return $schoolYear;
	}

	private function changeSchoolYear() {

		if(isset($_POST['label'])) {
			$this->checkInput();
			$this->handleCheckboxActive();
			$this->changeSchoolYearInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeSchoolYear'));
		}
		else {
			$this->showChangeSchoolYear();
		}
	}

	private function showChangeSchoolYear() {

		$schoolYear = $this->getSchoolYear();
		$this->_interface->displayChangeSchoolYear($schoolYear);
	}

	private function changeSchoolYearInDatabase() {

		try {
			$this->_syManager->alterSchoolYear($_GET['ID'], $_POST['label'], $_POST['active']);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYear'));
		} catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeSchoolYear'));
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_syManager;
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

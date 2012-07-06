<?php

require_once 'ClassTeacherInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassTeacherManager.php';
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
				case 'showClassTeacher' :
					$this->showClassTeachers();
					break;
				case 'deleteClassTeacher' :
					$this->deleteClassTeacher();
					break;
				case 'changeClassTeacher' :
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
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_languageManager = $this->_dataContainer->getLanguageManager();
		$this->_languageManager->setModule('ClassTeacher');
		$this->_interface = new ClassTeacherInterface($this->relPath, $this->_dataContainer->getSmarty(), $this->
			_languageManager);
		$this->_ctManager = new KuwasysClassTeacherManager();
	}

	private function addClassTeacher () {

		if (isset($_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone'])) {
			$this->checkInput();
			$this->addClassTeacherToDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddClassTeacher'));
		}
		else {
			$this->_interface->displayAddClassTeacher();
		}
	}

	private function checkInput () {

		try {
			inputcheck($_POST['name'], 'name', $this->_languageManager->getText('formName'));
			inputcheck($_POST['forename'], 'name', $this->_languageManager->getText('formForename'));
			inputcheck($_POST['address'], '/\A.{5,100}\z/', $this->_languageManager->getText('formAddress'));
			inputcheck($_POST['telephone'], '/\A[0-9]{3,32}\z/', $this->_languageManager->getText(
				'formTelephone'));
		} catch (WrongInputException $e) {

			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorInput'), $e->getFieldName()));
		}
	}

	private function addClassTeacherToDatabase () {

		try {
			$this->_ctManager->addClassTeacher($_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone'
				]);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddClassTeacher'));
		}
	}
	
	private function showClassTeachers () {
		
		$classTeachers = $this->getAllClassTeachers();
		$this->_interface->displayShowClassTeacher($classTeachers);
	}
	
	private function getAllClassTeachers () {
		
		try {
			$classTeachers = $this->_ctManager->getAllClassTeachers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeachers'));
		} catch(Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassTeachers'));
		}
		return $classTeachers;
	}
	
	private function getClassTeacher() {
		
		try {
			$classTeacher = $this->_ctManager->getClassTeacher($_GET['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeacher'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassTeacher'));
		}
		return $classTeacher;
	}
	
	private function deleteClassTeacher () {
		
		if(isset($_POST['dialogConfirmed'])) {
			$this->deleteClassTeacherFromDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteClassTeacher'));
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('classTeacherNotDeleted'));
		}
		else {
			$this->showConfirmationDialogDeleteClassTeacher();
		}
	}
	
	private function showConfirmationDialogDeleteClassTeacher () {
		
		$classTeacher = $this->getClassTeacher();
		$this->_interface->displayConfirmDeleteClassTeacher($classTeacher);
	}
	
	private function deleteClassTeacherFromDatabase () {
		
		try {
			$this->_ctManager->deleteClassTeacher($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteClassTeacher'));
		}
	}
	
	private function changeClassTeacher () {
		
		if (isset($_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone'])) {
			$this->checkInput();
			$this->changeClassTeacherInDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeClassTeacher'));
		}
		else {
			$classTeacher = $this->getClassTeacher();
			$this->_interface->displayChangeClassTeacher($classTeacher);
		}
	}
	
	private function changeClassTeacherInDatabase () {
		
		try {
			$this->_ctManager->alterClassTeacher($_GET['ID'], $_POST['name'], $_POST['forename'], $_POST['address'], $_POST['telephone']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClassTeacher'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeClassTeacher'));
		}
	}
	

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_ctManager;
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
<?php

require_once 'GradeInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysGradeManager.php';
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
		
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'addGrade' :
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
	}
	
	private function addGrade () {
		
		if(isset($_POST['label'], $_POST['year'])) {
			$this->checkGradeInput();
			$this->addGradeToDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddGrade'));
		}
		else {
			$this->_interface->displayAddGrade();
		}
	}
	
	private function checkGradeInput () {
		
		try {
			inputcheck($_POST['label'], '/\A[^\+\^\~\\\" \/]{1,50}\z/', $this->_languageManager->getText('formLabel'));
			inputcheck($_POST['year'], '/\A\d{1,2}\z/', $this->_languageManager->getText('formYear'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorAddGradeInput'), $e->getFieldName()));
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
		$this->_interface->displayShowGrades($grades);
	}
	
	private function getAllGrades () {
		
		try {
			$grades = $this->_gradeManager->getAllGrades();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrades'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorShowGrades'));
		}
		return $grades;
	}
	
	private function deleteGrade () {
		
		if(isset($_POST['dialogConfirmed'])) {
			$this->deleteGradeFromDatabase();
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteGrade'));
		}
		else if(isset($_POST['dialogNotConfirmed'])) {
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
		} catch(Exception $e) {
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
		
		if(isset($_POST['label'], $_POST['year'])) {
			
			$this->checkGradeInput();
			$this->changeGradeInDatabase();
		}
		else {
			$this->showChangeGrade();
		}
	}
	
	private function changeGradeInDatabase () {
		
		try {
			$this->_gradeManager->alterGrade($_GET['ID'], $_POST['label'], $_POST['year']);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeGrade'));
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrade'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeGrade'));
		}
	}
	
	private function showChangeGrade () {
		
		$grade = $this->getGrade();
		$this->_interface->displayChangeGrade($grade);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_gradeManager;
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
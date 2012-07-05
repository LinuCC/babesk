<?php

require_once 'ClassesInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_INCLUDE . '/Module.php';

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
			$this->_interface->dieMsg($this->_languageManager->getText('finishedAddClass'));
		}
		else {
			$this->showAddClass();
		}
	}

	private function showAddClass () {

		$this->_interface->showAddClass();
	}

	private function checkClassInput () {
		
		try {
			inputcheck($_POST['label'], '/\A.{3,100}\z/', $this->_languageManager->getText('formLabel'));
			inputcheck($_POST['maxRegistration'], 'number', $this->_languageManager->getText('formMaxRegistration'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorWrongInput'), $e->getFieldName())
				);
		}
	}

	private function addClassToDatabase () {

		$this->_classManager->addClass($_POST['label'], $_POST['maxRegistration']);
	}

	private function showClass () {

		$classes = $this->getAllClasses();
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
		$this->_interface->showChangeClass($class);
	}
	
	private function changeClassInDatabase () {
		
		try {
			$this->_classManager->alterClass($_GET['ID'], $_POST['label'], $_POST['maxRegistration']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorChangeClass'));
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_classManager;
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
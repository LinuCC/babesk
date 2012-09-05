<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class ClassTeacherInterface extends AdminInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty, $languageManager) {

		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
		$this->sectionString = 'Kuwasys|ClassTeacher';
		$this->languageManager = $languageManager;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function displayMainMenu () {

		$this->smarty->display($this->tplFilePath . 'mainMenu.tpl');
	}

	public function displayAddClassTeacher ($classes) {

		$this->smarty->assign('classes', $classes);
		$this->smarty->display($this->tplFilePath . 'addClassTeacher.tpl');
	}

	public function displayShowClassTeacher ($classTeachers) {

		$this->smarty->assign('classTeachers', $classTeachers);
		$this->smarty->display($this->tplFilePath . 'showClassTeachers.tpl');
	}

	public function displayConfirmDeleteClassTeacher ($classTeacher) {

		$promptMessage = sprintf($this->languageManager->getText('deleteClassTeacherPrompt'), $classTeacher['forename'],
			$classTeacher['name']);
		$actionString = 'deleteClassTeacher&ID=' . $classTeacher['ID'];
		$confirmedString = $this->languageManager->getText('deleteClassTeacherConfirmed');
		$notConfirmedString = $this->languageManager->getText('deleteClassTeacherNotConfirmed');
		$this->confirmationDialog($promptMessage, $this->sectionString, $actionString, $confirmedString,
			$notConfirmedString);
	}

	public function displayChangeClassTeacher ($classTeacher, $classes) {
		
		$this->smarty->assign('classTeacher', $classTeacher);
		$this->smarty->assign('classes', $classes);
		$this->smarty->display($this->tplFilePath . 'changeClassTeacher.tpl');
	}
	
	public function displayImportCsvForm () {
		
		$this->smarty->display($this->tplFilePath . 'importLocalCsvFile.tpl');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $sectionString;
	/**
	 * @var KuwasysLanguageManager
	 */
	private $languageManager;
}

?>
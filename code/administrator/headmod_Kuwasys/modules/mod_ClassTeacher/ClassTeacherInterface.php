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

	public function displayAddClassTeacher () {

		$inputContainer = array(
			array(
				'name'			 => 'forename',
				'displayName'	 => $this->languageManager->getText('formForename'),
				'type'			 => 'text',
			),
			array(
				'name'			 => 'name',
				'displayName'	 => $this->languageManager->getText('formName'),
				'type'			 => 'text',
			),
			array(
				'name'			 => 'address',
				'displayName'	 => $this->languageManager->getText('formAddress'),
				'type'			 => 'text',
			),
			array(
				'name'			 => 'telephone',
				'displayName'	 => $this->languageManager->getText('formTelephone'),
				'type'			 => 'text',
			),
		);
		$actionString = 'addClassTeacher';
		$submitString = $this->languageManager->getText('formAddClassTeacherSubmit');
		$headString = $this->languageManager->getText('formAddClassTeacherHead');

		parent::generalForm($headString, $this->sectionString, $actionString, $inputContainer, $submitString);
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

	public function displayChangeClassTeacher ($classTeacher) {

		$inputContainer = array(
			array(
				'name'			 => 'forename',
				'displayName'	 => $this->languageManager->getText('formForename'),
				'type'			 => 'text',
				'value'			 => $classTeacher['forename'],
			),
			array(
				'name'			 => 'name',
				'displayName'	 => $this->languageManager->getText('formName'),
				'type'			 => 'text',
				'value'			 => $classTeacher['name'],
			),
			array(
				'name'			 => 'address',
				'displayName'	 => $this->languageManager->getText('formAddress'),
				'type'			 => 'text',
				'value'			 => $classTeacher['address'],
			),
			array(
				'name'			 => 'telephone',
				'displayName'	 => $this->languageManager->getText('formTelephone'),
				'type'			 => 'text',
				'value'			 => $classTeacher['telephone'],
			),
		);
		
		$headString = $this->languageManager->getText('changeClassTeacherHead');
		$submitString = $this->languageManager->getText('changeClassTeacherSubmit');
		$actionString = 'changeClassTeacher&ID=' . $classTeacher ['ID'];
		parent::generalForm($headString, $this->sectionString, $actionString, $inputContainer, $submitString);
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
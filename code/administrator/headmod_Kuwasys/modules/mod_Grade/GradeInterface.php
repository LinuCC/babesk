<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class GradeInterface extends AdminInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty, $languageManager) {

		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
		$this->sectionString = 'Kuwasys|Grade';
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

	public function displayAddGrade () {

		$inputContainer = array(
			array(
				'name'			 => 'year',
				'displayName'	 => $this->languageManager->getText('formYear'),
				'type'			 => 'text',
			),
			array(
				'name'			 => 'label',
				'displayName'	 => $this->languageManager->getText('formLabel'),
				'type'			 => 'text',
			),
		);
		$submitString = $this->languageManager->getText('formAddGradeSubmit');
		$headString = $this->languageManager->getText('formAddGradeHeader');

		parent::generalForm($headString, $this->sectionString, 'addGrade', $inputContainer, $submitString);
	}

	public function displayShowGrades ($grades) {

		$this->smarty->assign('grades', $grades);
		$this->smarty->display($this->tplFilePath . 'showGrades.tpl');
	}

	public function displayDeleteGradeConfirmation ($grade) {

		$infoStr = sprintf($this->languageManager->getText('deleteGradeConfirmationString'), $grade['gradeValue'],
			$grade['label']);

		parent::confirmationDialog($infoStr, $this->sectionString, 'deleteGrade&ID=' . $grade['ID'], $this->
			languageManager->getText('deleteGradeConfirmationYes'), $this->languageManager->getText(
			'deleteGradeConfirmationNo'));
	}
	
	public function displayChangeGrade ($grade) {
		
		$inputContainer = array(
				array(
						'name'			 => 'year',
						'displayName'	 => $this->languageManager->getText('formYear'),
						'type'			 => 'text',
						'value'			 => $grade['gradeValue'],
				),
				array(
						'name'			 => 'label',
						'displayName'	 => $this->languageManager->getText('formLabel'),
						'type'			 => 'text',
						'value'			 => $grade['label'],
				),
		);
		$headString = $this->languageManager->getText('changeGradeHeader');
		$actionString = 'changeGrade&ID=' . $grade['ID'];
		$submitString = $this->languageManager->getText('changeGradeSubmit');
		
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
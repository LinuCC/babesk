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

	public function displayAddGrade ($schoolyears) {
		
		$this->smarty->assign('schoolyears', $schoolyears);
		$this->smarty->display($this->tplFilePath . 'addGrade.tpl');
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
	
	public function displayChangeGrade ($grade, $schoolyears) {
		
		$this->smarty->assign('grade', $grade);
		$this->smarty->assign('schoolyears', $schoolyears);
		$this->smarty->display($this->tplFilePath . 'changeGrade.tpl');
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
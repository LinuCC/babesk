<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class ClassesInterface extends AdminInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty) {

		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
		$this->sectionString = 'Kuwasys|Classes';
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getSmarty () {
		return $this->smarty;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function showMainMenu ($isClassRegistrationGloballyEnabled) {

		$isClassRegistrationGloballyEnabled = ($isClassRegistrationGloballyEnabled) ? true : false;
		$this->smarty->assign('isClassRegistrationGloballyEnabled', $isClassRegistrationGloballyEnabled);
		$this->smarty->display($this->tplFilePath . 'mainMenu.tpl');
	}

	public function showAddClass ($schoolYears) {

		$this->smarty->assign('schoolYears', $schoolYears);
		$this->smarty->display($this->tplFilePath . 'addClass.tpl');
	}
	
	public function showImportClassesByCsvFile () {
		
		$this->smarty->display($this->tplFilePath . 'importLocalCsvFile.tpl');
	}

	public function showClasses ($classes) {

		$this->smarty->assign('classes', $classes);
		$this->smarty->display($this->tplFilePath . 'showClasses.tpl');
	}

	public function showDeleteClassConfirmation ($ID, $promptMessage, $confirmedString, $notConfirmedString) {
		
		$this->confirmationDialog($promptMessage, $this->sectionString, 'deleteClass&ID=' . $ID, $confirmedString, $notConfirmedString);
	}
	
	public function showChangeClass ($class, $schoolYears, $nowUsedSchoolYearID) {
		
		$this->smarty->assign('class', $class);
		$this->smarty->assign('schoolYears', $schoolYears);
		$this->smarty->assign('nowUsedSchoolYearID', $nowUsedSchoolYearID);
		$this->smarty->display($this->tplFilePath . 'changeClass.tpl');
	}
	
	public function showClassDetails ($class) {
		
		$this->smarty->assign('class', $class);
		$this->smarty->display($this->tplFilePath . 'showClassDetails.tpl');
	}
	
	public function showToggleGlobalClassRegistration ($isGlobalClassRegistrationEnabled) {
		
		$this->smarty->assign('enabled', $isGlobalClassRegistrationEnabled);
		$this->smarty->display($this->tplFilePath . 'toggleGlobalClassRegistrationEnabled.tpl');
	}
	
	public function showAssignUsersToClassMenu () {
		
		$this->smarty->display($this->tplFilePath . 'assignUsersToClasses.tpl');
	}
	
	public function showConfirmDialogAssignUsersToClass ($requestsPassed, $requestsNotPassed) {
		
		$this->smarty->assign('requestsPassed', $requestsPassed);
		$this->smarty->assign('requestsNotPassed', $requestsNotPassed);
		$this->smarty->display($this->tplFilePath . 'assignUsersToClassesOutline.tpl');
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $sectionString;
}

?>
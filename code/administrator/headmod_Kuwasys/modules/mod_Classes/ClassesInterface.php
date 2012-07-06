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

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function showMainMenu () {

		$this->smarty->display($this->tplFilePath . 'mainMenu.tpl');
	}

	public function showAddClass ($languageManager) {

		$inputContainer = array(
				array(
						'name' => 'label',
						'displayName' => $languageManager->getText('formLabel'),
						'type' => 'text',
						),
				array(
						'name' => 'maxRegistration',
						'displayName' => $languageManager->getText('formMaxRegistration'),
						'type' => 'text',
						),
				);
		$actionString = 'addClass';
		$submitString = $languageManager->getText('formAddClassSubmit');
		$headString = $languageManager->getText('formAddClassHeader');
		
		$this->generalForm($headString, $this->sectionString, $actionString, $inputContainer, $submitString);
		//$this->smarty->display($this->tplFilePath . 'addClass.tpl');
	}

	public function showClasses ($classes) {

		$this->smarty->assign('classes', $classes);
		$this->smarty->display($this->tplFilePath . 'showClasses.tpl');
	}

	public function showDeleteClassConfirmation ($ID, $promptMessage, $confirmedString, $notConfirmedString) {
		
		$this->confirmationDialog($promptMessage, $this->sectionString, 'deleteClass&ID=' . $ID, $confirmedString, $notConfirmedString);
	}
	
	public function showChangeClass ($class) {
		
		$this->smarty->assign('class', $class);
		$this->smarty->display($this->tplFilePath . 'changeClass.tpl');
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
<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class SchoolYearInterface extends AdminInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty, $languageManager) {

		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
		$this->sectionString = 'Kuwasys|SchoolYear';
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

	public function displayAddSchoolYear () {

		$inputContainer = array(
			array(
				'name'			 => 'label',
				'displayName'	 => $this->languageManager->getText('formLabel'),
				'type'			 => 'text',
			),
			array(
				'name'			 => 'active',
				'displayName'	 => $this->languageManager->getText('formActive'),
				'type'			 => 'checkbox',
			),
		);

		$headString = $this->languageManager->getText('formAddSchoolYearHead');
		$submitString = $this->languageManager->getText('formAddSchoolYearSubmit');
		$actionString = 'addSchoolYear';

		$this->generalForm($headString, $this->sectionString, $actionString, $inputContainer, $submitString);
	}

	public function displayShowSchoolYears ($schoolYears) {

		$this->smarty->assign('schoolYears', $schoolYears);
		$this->smarty->display($this->tplFilePath . 'showSchoolYears.tpl');
	}

	public function displayActivateSchoolYearConfirmation ($schoolYear) {

		$promptMessage = sprintf($this->languageManager->getText('confirmActivateSchoolYear'), $schoolYear['label']);
		$actionString = 'activateSchoolYear&ID=' . $schoolYear['ID'];
		$confirmedString = $this->languageManager->getText('confirmActivateSchoolYearConfirmed');
		$notConfirmedString = $this->languageManager->getText('confirmActivateSchoolYearNotConfirmed');

		$this->confirmationDialog($promptMessage, $this->sectionString, $actionString, $confirmedString,
			$notConfirmedString);
	}

	public function displayChangeSchoolYear ($schoolYear) {


		$inputContainer = array(
			0 => array(
				'name'			 => 'label',
				'displayName'	 => $this->languageManager->getText('formLabel'),
				'type'			 => 'text',
				'value'			 => $schoolYear['label'],
			),
			array(
				'name'			 => 'active',
				'displayName'	 => $this->languageManager->getText('formActive'),
				'type'			 => 'checkbox',
				'value'			 => $schoolYear['active'],
			),
		);
		if($schoolYear['active']) {
			$inputContainer [1] ['optionString'] = 'checked';
		}
		$actionString = 'changeSchoolYear&ID=' . $schoolYear['ID'];
		$headString = $this->languageManager->getText('changeSchoolYearHead');
		$submitString = $this->languageManager->getText('changeSchoolYearSubmit');

		$this->generalForm($headString, $this->sectionString, $actionString, $inputContainer, $submitString);
	}

	public function displayDeleteSchoolYearConfirmation ($schoolYear) {

		$promptMessage = sprintf($this->languageManager->getText('deleteSchoolYearPrompt'), $schoolYear ['label']);
		$actionString = 'deleteSchoolYear&ID=' . $schoolYear ['ID'];
		$confirmedString = $this->languageManager->getText('deleteSchoolYearConfirm');
		$notConfirmedString = $this->languageManager->getText('deleteSchoolYearNotConfirm');

		$this->confirmationDialog($promptMessage, $this->sectionString, $actionString, $confirmedString, $notConfirmedString);
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

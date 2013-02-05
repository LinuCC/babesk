<?php

require_once PATH_INCLUDE . '/HeadModule.php';
require_once PATH_INCLUDE . '/exception_def.php';
require_once 'NachrichtenDataContainer.php';
require_once 'NachrichtenLanguageManager.php';

/**
 * class for Interface administrator
 * @author Mirek Hancl
 *
 */
class Nachrichten extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name) {

		parent::__construct($name, $display_name);
		defined('PATH_ACCESS_CONTRACT') or define('PATH_ACCESS_CONTRACT', PATH_ACCESS . '/headmod_Nachrichten');
		defined('PATH_INCLUDE_CONTRACT') or define('PATH_INCLUDE_CONTRACT', PATH_INCLUDE . '/headmod_Nachrichten');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($moduleManager, $dataContainer) {
		//function not needed, javascript is doing everything
	}

	public function executeModule ($mod_name, $dataContainer) {

		$this->_languageManager = new NachrichtenLanguageManager($dataContainer->getInterface());
		$contractDataContainer = new NachrichtenDataContainer($dataContainer->getSmarty(), $dataContainer->getInterface(),
			$this->_languageManager);
		parent::executeModule($mod_name, $contractDataContainer);
	}
}
?>
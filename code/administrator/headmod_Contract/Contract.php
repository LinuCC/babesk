<?php

require_once PATH_INCLUDE . '/HeadModule.php';
require_once PATH_INCLUDE . '/exception_def.php';
require_once 'ContractDataContainer.php';
require_once 'ContractLanguageManager.php';

/**
 * class for Interface administrator
 * @author Mirek Hancl
 *
 */
class Contract extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name) {

		parent::__construct($name, $display_name);
		defined('PATH_ACCESS_CONTRACT') or define('PATH_ACCESS_CONTRACT', PATH_ACCESS . '/headmod_Contract');
		defined('PATH_INCLUDE_CONTRACT') or define('PATH_INCLUDE_CONTRACT', PATH_INCLUDE . '/headmod_Contract');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($moduleManager, $dataContainer) {
		//function not needed, javascript is doing everything
	}

	public function executeModule ($mod_name, $dataContainer) {

		$this->_languageManager = new ContractLanguageManager($dataContainer->getInterface());
		$contractDataContainer = new ContractDataContainer($dataContainer->getSmarty(), $dataContainer->getInterface(),
			$this->_languageManager);
		parent::executeModule($mod_name, $contractDataContainer);
	}
}
?>
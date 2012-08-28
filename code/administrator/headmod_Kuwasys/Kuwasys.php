<?php

require_once PATH_INCLUDE . '/HeadModule.php';
require_once PATH_INCLUDE . '/exception_def.php';
require_once 'KuwasysDataContainer.php';
require_once 'KuwasysLanguageManager.php';

/**
 * class for Interface administrator
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Kuwasys extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name) {

		parent::__construct($name, $display_name);
		defined('PATH_ACCESS_KUWASYS') or define('PATH_ACCESS_KUWASYS', PATH_ACCESS . '/headmod_Kuwasys');
		defined('PATH_INCLUDE_KUWASYS') or define('PATH_INCLUDE_KUWASYS', PATH_INCLUDE . '/headmod_Kuwasys');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute () {
		//function not needed, javascript is doing everything
	}

	public function executeModule ($mod_name, $dataContainer) {

		$this->_languageManager = new KuwasysLanguageManager($dataContainer->getInterface());
		
		//$dataContainer->getInterface()->showMsg($this->_languageManager->getTextOfModule('alphaDisclaimer', 'Kuwasys'));
		
		$kuwasysDataContainer = new KuwasysDataContainer($dataContainer->getSmarty(), $dataContainer->getInterface(),
			$this->_languageManager);
		parent::executeModule($mod_name, $kuwasysDataContainer);
	}
}
?>
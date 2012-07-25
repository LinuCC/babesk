<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface web
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Babesk extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		parent::__construct($name, $display_name);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		
		require_once PATH_ACCESS . '/UserManager.php';
		$userManager = new UserManager();
		
		if ($userManager->firstPassword($_SESSION['uid'])) {
			$this->_moduleManager->execute('Babesk|ChangePassword', false);
		}
		else {
			$moduleManager->execute("Babesk|Menu", false);
		}
	}
}
?>
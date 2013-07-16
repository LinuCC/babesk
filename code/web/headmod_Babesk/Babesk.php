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
	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		require_once PATH_ACCESS . '/UserManager.php';
		$userManager = new UserManager();

		if ($userManager->firstPassword($_SESSION['uid'])) {
			$defaultMod = new ModuleExecutionInputParser(
				'root/web/Babesk/ChangePassword');
			$dataContainer->getAcl()->moduleExecute(
				$defaultMod, $dataContainer);
		}
		else {
			$defaultMod = new ModuleExecutionInputParser(
				'root/web/Babesk/Menu');
			$dataContainer->getAcl()->moduleExecute($defaultMod,
				$dataContainer);
		}
	}
}
?>

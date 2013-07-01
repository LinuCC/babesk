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
			$dataContainer->getAcl()->moduleExecute(
				'root/web/Babesk/ChangePassword', $dataContainer);
		}
		else {
			$dataContainer->getAcl()->moduleExecute('root/web/Babesk/Menu',
				$dataContainer);
		}
	}
}
?>

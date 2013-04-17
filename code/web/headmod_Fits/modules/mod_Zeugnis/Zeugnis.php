<?php

require_once PATH_INCLUDE . '/Module.php';

class Zeugnis extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");

		global $smarty;

		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		require_once PATH_ACCESS . '/FitsManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		$fm = new FitsManager();
		$um = new UserManager();

		if ($fm->getFits($_SESSION['uid'])) {
		$smarty->assign('forename',$um->getForename($_SESSION['uid']));
		$smarty->assign('name',$um->getName($_SESSION['uid']));
		$smarty->assign('year',$fm->getFitsYear($_SESSION['uid']) );

		$smarty->display($this->smartyPath . 'zeugnis.tpl');

		}

	}


}
?>
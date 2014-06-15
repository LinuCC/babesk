<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Fits/Fits.php';

class Zeugnis extends Fits {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");

		$smarty = $dataContainer->getSmarty();

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

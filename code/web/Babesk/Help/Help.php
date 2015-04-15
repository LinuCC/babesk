<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/Babesk/Babesk.php';

class Help extends Babesk {

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
		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		$cm = new CardManager();
		$um = new UserManager();
		$gsManager = new GlobalSettingsManager();
		try {
			$help_str = $gsManager->getHelpText();
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
		}
		if($dataContainer->getAcl()->moduleGet('root/web/Babesk')) {
			// set {cardid} in helptext administration to replace it with the cardnumber
			$help_str = str_replace("{cardid}", $cm->getCardnumberByUserID($_SESSION['uid']), $help_str);
		}
		//set {login} in helptext administration to replace it with the login name
		$help_str = str_replace("{login}", $um->getUsername($_SESSION['uid']), $help_str);
		$smarty->assign('help_str', $help_str);
		$smarty->display($this->smartyPath . "help.tpl");
	}
}
?>

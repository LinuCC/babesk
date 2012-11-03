<?php

require_once PATH_INCLUDE . '/Module.php';

class Help extends Module {

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
		// set {cardid} in helptext administration to replace it with the cardnumber
		//set {login} in helptext administration to replace it with the login name
		$help_str = str_replace("{cardid}", $cm->getCardnumberByUserID($_SESSION['uid']), $help_str);
		$help_str = str_replace("{login}", $um->getUsername($_SESSION['uid']), $help_str);
		$smarty->assign('help_str', $help_str);
		$smarty->display($this->smartyPath . "help.tpl");
	}
}
?>
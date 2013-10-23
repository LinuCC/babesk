<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Babesk/Babesk.php';

class Account extends Babesk {

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

		$userManager = new UserManager();

		if(isset($_POST['kontoSperren']) && $_POST['kontoSperren'] == 'lockAccount') {
			try {
				$userManager->lockAccount($_SESSION['uid']);
			} catch (Exception $e) {
				die('<p class="error">Ein Problem beim Sperren des Accounts ist aufgetreten!</p>');
			}

			$smarty->assign('status', '<p>Konto wurde erfolgreich gesperrt.</p>');
			header('Location: index.php?action=logout');
		}
		else {
			$smarty->display($this->smartyPath . "account.tpl");
			exit();
		}
	}
}


?>

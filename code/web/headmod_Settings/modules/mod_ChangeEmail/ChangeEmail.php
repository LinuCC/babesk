<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_WEB . '/headmod_Settings/Settings.php';

class ChangeEmail extends Settings {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute ($dataContainer) {
		$this->entry ();
		if (!isset($_GET ['action'])) {
			$this->menuShow ();
			return;
		}
		switch ($_GET ['action']) {
			case 'changeEmail':
				$this->emailCheck ();
				$this->changeSubmit ();
				$this->finShow ();
				break;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entry () {
		defined('_WEXEC') or die("Access denied");
		self::$uid = $_SESSION ['uid'];
		global $smarty;
		$this->_smarty = $smarty;
		$this->_interface = new WebInterface ($this->_smarty);
		$this->_userManager = new UserManager ();
	}

	protected function menuShow () {
		$emailOld = $this->emailOldFetch ();
		$this->_smarty->assign ('emailOld', $emailOld);
		$this->_smarty->display ($this->_smartyPath . 'changeEmailDialog.tpl');
	}

	protected function emailCheck () {
		try {
			if (!isset ($_POST ['emailNew'])) {
				throw new WrongInputException ();
			}
			$emailNew = $_POST ['emailNew'];
			inputcheck ($emailNew, 'email', 'EmailAdresse');
		} catch (WrongInputException $e) {
			$this->_interface->DieError (sprintf('Die EmailAdresse "%s" wurde falsch eingegeben.%s', $emailNew, Kuwasys::$buttonBackToMM));
		}
	}

	protected function changeSubmit () {
		$this->_userManager->changeEmailAdress (self::$uid, $_POST ['emailNew']);
	}

	protected function finShow () {
		$this->_interface->DieMessage (sprintf ('Die Emailadresse wurde erfolgreich zu "%s" verändert. %s', $_POST ['emailNew'], Kuwasys::$buttonBackToMM));
	}

	protected function emailOldFetch () {
		$user = $this->_userManager->getUser (self::$uid);
		return $user ['email'];
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;
	protected $_interface;
	protected $_userManager;
	protected static $uid;
}

?>

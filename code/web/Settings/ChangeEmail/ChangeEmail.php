<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_WEB . '/Settings/Settings.php';

class ChangeEmail extends Settings {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entry($dataContainer);
		if(!isset($_GET['action'])) {
			$this->menuShow();
			return;
		}
		switch($_GET['action']) {
			case 'changeEmail':
				$this->emailCheck();
				$this->changeSubmit();
				$this->finShow();
				break;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entry($dataContainer) {
		defined('_WEXEC') or die("Access denied");
		self::$uid = $_SESSION['uid'];

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_userManager = new UserManager();
	}

	protected function menuShow() {
		$emailOld = $this->emailOldFetch();
		$this->_smarty->assign('emailOld', $emailOld);
		$this->_smarty->display($this->_smartyPath . 'changeEmailDialog.tpl');
	}

	protected function emailCheck() {
		try {
			if(!isset($_POST['emailNew'])) {
				throw new WrongInputException();
			}
			$emailNew = $_POST['emailNew'];
			inputcheck($emailNew, 'email', 'EmailAdresse');
		} catch(WrongInputException $e) {
			$this->_interface->dieError(
				"Die Emailadresse '$emailNew' wurde falsch eingegeben!"
			);
		}
	}

	protected function changeSubmit() {
		$this->_userManager->changeEmailAdress(self::$uid, $_POST['emailNew']);
	}

	protected function finShow() {
		$this->_interface->dieSuccess(
			'Deine Emailadresse wurde erfolgreich verändert!'
		);
	}

	protected function emailOldFetch() {
		$user = $this->_userManager->getUser(self::$uid);
		return $user['email'];
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

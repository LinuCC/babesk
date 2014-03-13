<?php

require_once PATH_WEB . '/headmod_Settings/Settings.php';

class SettingsChangePassword extends Settings {
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
		if(!isset($_GET ['action'])) {
			$this->menuShow();
			return;
		}
		switch($_GET ['action']) {
			case 'changePassword':
				$this->pwChange($_POST ['newPassword'], $_POST ['newPasswordRepeat']);
				break;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entry($dataContainer) {

		defined('_WEXEC') or die("Access denied");
		self::$_uid = $_SESSION ['uid'];

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_userManager = new UserManager();
	}

	protected function menuShow() {
		$this->_smarty->display($this->_smartyPath . 'changePasswordDialog.tpl');
	}

	protected function pwChange($pwNew, $pwNewRep) {
		$this->_pwNew = $pwNew;
		$this->_pwNewRep = $pwNewRep;
		$this->pwInpCheck();
		$this->pwToDb();
		$this->_interface->DieMessage('Das Passwort wurde erfolgreich verändert');
	}

	protected function pwToDb() {
		try {
			$this->_userManager->changePassword(self::$_uid, $this->_pwNew);
		} catch(Exception $e) {
			$this->_interface->DieError('Konnte das Passwort nicht verändern; Ein interner Fehler ist aufgetreten');
		}
	}

	protected function pwInpCheck() {
		if($this->_pwNew != $this->_pwNewRep) {
			$this->_interface->DieError('Das Passwort stimmt nicht mit der Wiederholung überein. Bitte versuche es noch einmal.');
		}
		try {
			inputcheck($this->_pwNew, 'password', 'Passwort');
		} catch(WrongInputException $e) {
			$this->_interface->DieError('Das Passwort enthält nicht korrekte Zeichen oder ist zu kurz.');
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
	protected $_pwNew;
	protected $_pwNewRep;

	protected static $_uid;
	protected $_smarty;
	protected $_interface;
	protected $_userManager;
}

?>

<?php

class UsersPasswordResetter {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface, $databaseAccessManager, $languageManager) {
		$this->_interface = $interface;
		$this->_databaseAccessManager = $databaseAccessManager;
		$this->_languageManager = $languageManager;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute () {
		if (isset ($_POST['newPassword'])) {
			$this->confirmDialogShow ();
		}
		else if (isset ($_POST['dialogConfirmed'])) {
			$this->resetPasswords ();
		}
		else if (isset ($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg ('Die Passwörter wurden nicht zurückgesetzt.');
		}
		else {
			$this->mainDialogShow ();
		}
	}

	public function resetPasswords () {
		if (!isset($_SESSION ['pwResNewPw'])) {
			$this->_interface->dieError ('Bitte wiederholen sie den Vorgang');
		}
		$this->activeSchoolyearGet ();
		$joints = $this->_databaseAccessManager->jointUserInSchoolyearGetBySchoolyearId ($this->_activeSchoolyear ['ID']);
		foreach ($joints as $joint) {
			$this->_databaseAccessManager->userIdAddToUserIdArray ($joint ['UserID']);
		}
		$this->_databaseAccessManager->userChangePasswordByUserIdArray (hash_password($_SESSION ['pwResNewPw']));
		unset ($_SESSION ['pwResNewPw']);
		$this->_interface->dieMsg ($this->_languageManager->getText ('finResetPasswordsOfUsers'));
	}

	/** Shows the MainDialog to reset the Password.
	 *
	 */
	public function mainDialogShow () {
		$this->activeSchoolyearGet ();
		$this->_interface->showMainDialogResetPasswordOfAllUsers (
			$this->_activeSchoolyear ['label']);
	}

	/** Shows a Dialog to confirm the Passwordreset
	 *
	 */
	public function confirmDialogShow () {
		$_SESSION ['pwResNewPw'] = $_POST ['newPassword'];
		$this->activeSchoolyearGet ();
		$this->_interface->showConfirmResetPasswordOfAllUsers ($this->_activeSchoolyear ['label']);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function activeSchoolyearGet () {
		$syId = $this->_databaseAccessManager->schoolyearActiveGetId ();
		$this->_activeSchoolyear = $this->_databaseAccessManager->schoolyearGet ($syId);
	}


	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_databaseAccessManager;
	private $_languageManager;

	private $_activeSchoolyear;
}

?>
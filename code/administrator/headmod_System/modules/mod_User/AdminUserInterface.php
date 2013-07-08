<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * AdminUserInterface is to output the Interface
 * Enter description here ...
 * @author voelkerball
 *
 */
class AdminUserInterface extends AdminInterface {

	function __construct($mod_path) {

		parent::__construct($mod_path);

		$this->MOD_HEADING = $this->tplFilePath.'mod_user_header.tpl';
		$this->parentPath = $this->tplFilePath . 'mod_user_header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
		$this->smarty->assign('UserParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality() {
		$this->smarty->display($this->tplFilePath.'user_select.tpl');
	}

	function ShowRegisterForm($priceGroups, $grades, $schoolyears) {
		$this->smarty->assign('priceGroups', $priceGroups);
		$this->smarty->assign('grades', $grades);
		$this->smarty->assign('schoolyears', $schoolyears);
		$this->smarty->display($this->tplFilePath.'register.tpl');
	}

	function ShowUsers($users,$navbar) {
		$this->smarty->assign('users', $users);
		$this->smarty->assign('navbar', $navbar);
		$this->smarty->display($this->tplFilePath.'show_users.tpl');
	}

	function ShowChangeUser($user, $cardnumber, $priceGroups, $grades, $schoolyears, $groups) {
		$this->smarty->assign('user', $user);
		$this->smarty->assign('cardnumber', $cardnumber);
		$this->smarty->assign('priceGroups', $priceGroups);
		$this->smarty->assign('grades', $grades);
		$this->smarty->assign('schoolyears', $schoolyears);
		$this->smarty->assign('groups', $groups);

		$this->smarty->display($this->tplFilePath.'change.tpl');

	}

	function showConfirmAutoChangeUsernames () {
		$this->smarty->display ($this->tplFilePath . 'dialogAutoCreateUsernames.tpl');
	}

	public function showRemoveSpecialCharsFromUsername () {
		$this->smarty->display ($this->tplFilePath . 'usernameRemoveSpecialChars.tpl');
	}


	public function showDeletePdfSuccess () {
		$this->smarty->display ($this->tplFilePath .
			'showDeletePdfSuccess.tpl');
	}

	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>

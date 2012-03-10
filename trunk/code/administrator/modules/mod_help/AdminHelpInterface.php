<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class AdminHelpInterface extends AdminInterface {
	function __construct() {
		parent::__construct();
		$this->smPath = PATH_SMARTY_ADMIN_MOD . '/mod_help/';
	}
	
	/**
	 * Shows the initial Menu of this Module
	 */
	function IndexMenu() {
		$this->smarty->display($this->smPath . 'index.tpl');
	}
	
	/**
	 * Shows the Help
	 * @param string $str The Helpstring to be shown
	 */
	function ShowHelp($str) {
		$this->smarty->assign('help_str', $str);
		$this->smarty->display($this->smPath . 'show_helptext.tpl');
	}
	
	/**
	 * Shows the EditHelp-form
	 * @param string $str the original helptext
	 */
	function EditHelp($str) {
		$this->smarty->assign('helptext', $str);
		$this->smarty->display($this->smPath . 'edit_helptext.tpl');
	}
	
	function EditHelpFin() {
		die_msg('Der Hilfetext wurde erfolgreich bearbeitet.');
	}
	
	/**
	 * The Path to the Smarty-Templates of this module
	 * @var string
	 */
	protected $smPath;
}

?>
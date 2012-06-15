<?php

/**
 * The class the Interface-classes in the modules base on
 * Enter description here ...
 * @author Pascal Ernst
 *
 */
class AdminInterface {

	function __construct ($mod_rel_path) {

		global $smarty;
		$this->smarty = $smarty;
		$this->tplFilePath = PATH_SMARTY_ADMIN_TEMPLATES . $mod_rel_path;
		$this->parentPath = PATH_SMARTY . '/templates/administrator/base_layout.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
	}

	/**
	 * Show an error to the user and dies
	 * This function shows an error to the user and die()s the process.
	 * @param string $msg The message to be shown
	 */
	function dieError ($msg) {

		$this->smarty->assign('error', $msg);
		$this->smarty->display(PATH_SMARTY . '/templates/administrator/message.tpl', md5($_SERVER['REQUEST_URI']), md5(
			$_SERVER['REQUEST_URI']));
		die();
	}

	/**
	 * Show a message to the user and dies
	 * This function shows a message to the user and die()s the process.
	 * @param string $msg The message to be shown
	 */
	function dieMsg ($msg) {
		$this->smarty->assign('message', $msg);
		$this->smarty->display(PATH_SMARTY . '/templates/administrator/message.tpl', md5($_SERVER['REQUEST_URI']), md5(
			$_SERVER['REQUEST_URI']));
		die();
	}

	function showError ($msg) {
		$this->smarty->append('_userErrorOutput', $msg);
	}

	function showMsg ($msg) {
		$this->smarty->append('_userMsgOutput', $msg);
	}

	protected $smarty;

	/**
	 * The Path to the Parent-Smartyfile to inherit from
	 */
	protected $parentPath;

	/**
	 * The Path to the Smarty-Templates of the Module
	 */
	protected $tplFilePath;

}
?>
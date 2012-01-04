<?php

/**
 * The class the Interface-classes in the modules base on
 * Enter description here ...
 * @author Pascal Ernst
 *
 */
class AdminInterface {
	function __construct() {
		global $smarty;
		$this->smarty = $smarty;
	}
	
	/**
	* Show an error to the user
	* This function shows an error to the user.
	* @param string $msg The message to be shown
	* @param string $lnk if not null, a link to another side, under the error message
	*/
	function ShowError($msg) {
		$this->smarty->assign('msg', $msg);
		$this->smarty->display($this->PathUserTemplates.'error.tpl');
	}
	
	protected $smarty;
	
}
?>
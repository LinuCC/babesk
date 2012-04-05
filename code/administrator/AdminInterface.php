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
		$this->parentPath = PATH_SMARTY . '/templates/administrator/base_layout.tpl';
	}
	
	/**
	* Show an error to the user and dies
	* This function shows an error to the user and die()s the process.
	* @param string $msg The message to be shown
	*/
	function ShowError($msg) {
		die_error($msg, $this->parentPath);
	}
	
	/**
	* Show a message to the user and dies
	* This function shows a message to the user and die()s the process.
	* @param string $msg The message to be shown
	*/
	function ShowMsg($msg) {
		die_msg($msg, $this->parentPath);
	}
	
	protected $smarty;
	
	protected $PathUserTemplates;
	
	protected $parentPath;
	
}
?>
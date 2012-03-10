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
		///@todo Get the error.tpl to another, better place!
	}
	
	/**
	* Show an error to the user and dies
	* This function shows an error to the user and die()s the process.
	* @param string $msg The message to be shown
	* @param string $lnk if not null, a link to another side, under the error message
	*/
	function ShowError($msg) {
		die_error($msg);
	}
	
	protected $smarty;
	protected $PathUserTemplates;
	
}
?>
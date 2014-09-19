<?php

/**
 * A Class defining functions for userOutput. Intended to get inherited for program-parts.
 * @author voelkerball
 *
 */
class GeneralInterface {

	//////////////////////////////////////////////////////////////////////
	//Constructor
	//////////////////////////////////////////////////////////////////////

	public function __construct() {

	}

	//////////////////////////////////////////////////////////////////////
	//Methods
	//////////////////////////////////////////////////////////////////////

	/**
	 * Show an error to the user and dies
	 */
	public function dieError ($msg) {
		die('ERROR:' . $msg);
	}

	/**
	 * Show a message to the user and dies
	 */
	public function dieMsg ($msg) {
		die($msg);
	}

	public function showError ($msg) {
		echo 'ERROR: ' . $msg . '<br>';
	}

	public function showMsg ($msg) {
		echo $msg . '<br>';
	}

	public function flashError($msg) {
		echo 'ERROR: ' . $msg . '<br>';
	}

	public function flashMessage($msg) {
		echo $msg . '<br>';
	}

	public function dieDisplay () {
		die();
	}

	public function dieAjax($state, $data) {
		die(json_encode(
			array('state' => $state, 'data' => $data)
		));
	}

	//////////////////////////////////////////////////////////////////////
	//Implementations
	//////////////////////////////////////////////////////////////////////

}

?>
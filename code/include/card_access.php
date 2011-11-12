<?php

     /**
     * Provides functions for the RFID Cards
     */

require_once PATH_INCLUDE.'/access.php';

class CardManager extends TableManager {
	function __construct() {
		parent::__construct('cards');
	}
	/**
	 * Validates the card ID
	 * Enter description here ...
	 * @param numeric $card_ID
	 * @return boolean
	 */
	function valid_card_ID($card_ID) {
	
		require_once 'constants.php';
	
		if(!preg_match('/\A[0-9a-zA-Z]{10}\z/',$card_ID)){
			echo INVALID_CARD_ID."<br>";
			return false;
		}
		return true;
	}
	function addCard($cardnumber, $UID) {
		parent::addEntry('cardnumber', $cardnumber, 'UID', $UID);
	}
}
?>
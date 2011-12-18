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
	 * @param numeric $card_ID The Cardnumber
	 * @return boolean
	 */
	function valid_card_ID($card_ID) {
		/**
		 * @todo rename this function. this valids the cardnumber not the cardid
		 */
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
	function getUserID($cardnumber) {
		require_once PATH_INCLUDE.'/dbconnect.php';
		$query = sprintf('SELECT * FROM %s WHERE cardnumber=%s',
							$this->tablename, $cardnumber);
		$result = $this->db->query($query);
		$card = $result->fetch_assoc();
		$user = parent::getEntryData($card['UID'], 'UID');
		return $user['UID'];
	}
}
?>
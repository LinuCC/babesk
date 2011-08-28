<?php
     /**
     * Provides functions for the RFID Cards
     */


    function valid_card_ID($card_ID) {
    
        require_once 'constants.php';
        
        if(!preg_match('/\A[0-9a-zA-Z]{10}\z/',$card_ID)){
    		echo INVALID_CARD_ID."<br>";
    		return false;
    	}
    	return true;
    }
        
?>
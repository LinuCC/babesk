<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once "constants.php";
    
    /**
     * Checks if the session is valid 
     *
     * The function compares the ip address and 
     * saves it if no address was previously saved
     *
     * @return false if invalid
     */
    function validSession() {
        if(!isset($_SESSION)) {             
            return false;
        }
    	$ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SESSION['IP']) AND $_SESSION['IP'] != $ip) {
            return false;
        }
        else {
            $_SESSION['IP'] = $ip;
            return true;
        }
    }
    
    
    function formatDate($date) {
        if (is_numeric($date)) {        //a Unix timestamp
            return date('d.m.Y', $date);
        }
        else {                          //a date string
            return date('d.m.Y', strtotime($date));
        }
    }
    
    function formatDateTime($date) {
        if (is_numeric($date)) {        //a Unix timestamp
            return date('d.m.Y H:i', $date);
        }
        else {                          //a date string
            return date('d.m.Y H:i', strtotime($date));
        }
    }
    
    function sql_prev_inj($str) {
    	require 'dbconnect.php';
    	if(!is_string($str)) {
    		throw new BadFunctionCallException('Wrong parameter-format for $str in '.__FUNCTION__); 
    	}
    	$db->real_escape_string($str);
    	return $str;
    }
    
    ///@todo Replace md5 with this function
    function hash_password($pw_str) {
    	return md5($pw_str);
    }
?>

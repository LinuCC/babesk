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
?>

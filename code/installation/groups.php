<?php

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
    
    require "../include/dbconnect.php";

	if ('POST' == $_SERVER['REQUEST_METHOD'] AND !$_SESSION['processed_POST']) {
		if (!isset($_POST['Name'], $_POST['Max_Credit'])) {
			die(INVALID_FORM);
		}  //save values and check for empty fields
		if (($groupname = trim($_POST['Name'])) == '' OR
	        ($max_credit = trim($_POST['Max_Credit'])) == '') {
	        	die(EMPTY_FORM);
	   	}

		addGroup($groupname, $max_credit);
	
        //next step
	   require "price_classes.tpl"
	}
	else {
        require "groups.tpl";
    }





?>
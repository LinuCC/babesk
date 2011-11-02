<?php
	//header('Content-Type: text/html; charset=utf-8');
	/**
	 *@file register.php handles all parts of the registermodule and combines them with the needed sourcefiles outside (like Database-functions)
	 */
	//make sure Client has the rights to access this site
	defined('_AEXEC') or die("Access denied");
	
//---INCLUDE---
	require_once "register_constants.php";
	require_once "register_functions.php";
	
	global $smarty;
	
//---SAFETY---
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	    // �berpr�fung des POST
	    if (!isset($_POST['name']) and !isset($_POST['passwd']) and !isset($_POST['id'])) {
		  die(INVALID_FORM);
		}
		else if(isset($_POST['id'])){
			$_SESSION['CARD_ID'] = $_POST['id'];
			if(!preg_match('/\A[0-9a-zA-Z]{10}\z/',$_POST['id']))die(REG_ERROR_ID);
			group_init_smarty_vars();
			$smarty->display(PATH_TEMPLATE_REG);
		}
		else if (($name = trim($_POST['name'])) == '') {
	        die(EMPTY_FORM);
	    }
	    else if(!isset($_SESSION['CARD_ID'])) {
	    	die(REG_PLEASE_REPEAT_CARD_ID);
	    }
	    else {

//---INIT---
			$forename = $_POST['forename'];
			$name = $_POST['name'];
			$username = $_POST['username'];
			$ID = $_SESSION['CARD_ID'];
			$passwd = $_POST['passwd'];
			$passwd_repeat = $_POST['passwd_repeat'];
			$birthday = date( 'Y-m-d', strtotime( merge_birthday($_POST["b_day"],$_POST["b_month"],$_POST["b_year"]) ) );
			$GID = $_POST['gid'];
			$credits = correct_credits_input($_POST['credits']);
			
//---METHODS---
			if(register_process($forename,$name,$username,$passwd,$passwd_repeat,$ID,$birthday,$GID,$credits)) {
				unset($_SESSION['CARD_ID']); 
			}
		}
    }
	else {//show register-form
		$smarty->display(PATH_TEMPLATE_CARD);
	}

?>
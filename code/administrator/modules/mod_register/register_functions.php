<?php
    /**
	 *@file register_functions.php here are some outsourced functions of the register-module
	*/

/**
  *handles the input from the register-form and add the user in the mysql-table
  */
function register_process($forename,$name,$username,$passwd,$passwd_repeat,$ID,$birthday,$GID,$credits){

	require_once PATH_INCLUDE."/user_access.php";
	require_once PATH_INCLUDE."/logs.php";
	
	$user_access = new UserManager();
	$logger= new Logger;

	//checks the input for wrong Characters etc
	if(inputcheck($forename,$name,$username,$passwd,$passwd_repeat,$ID,$birthday,$GID,$credits)) {
		if($user_access->addUser($ID, $name, $forename, $username, $passwd, $birthday, $credits, $GID)) {
			echo "<br><b>Hallo ".$name."!</b><br>";
			echo '<a href="index.php?'.htmlspecialchars(SID).'">Zur&uuml;ck zum Admin Bereich</a>';
			$logger->log(ADMIN,NOTICE,"REG_ADDED_USER-ID:".$ID."-PASSWORD:".$passwd."-NAME:".$name."-FORENAME:".$forename."-BIRTHDAY:".
										$birthday."-CREDITS:".$credits."-GID:".$GID."-");
		}
		else {
			echo "<br>".REG_ERROR_MYSQL."<br>";
		}
	}
	else {
		echo REG_PLEASE_REPEAT.'<br>';
	}
}


/**
  *Checks the input from the user of the register-formular with some Regex
  */
function inputcheck($forename,$name,$username,$passwd, $passwd_repeat, $ID,$birthday,$GID,$credits){
	$severity = NOTICE;
	$categorie = ADMIN;
	
	/*Fehlerüberprüfung*/
	if(!preg_match('/\A^[a-zA-ZßäÄüÜöÖ ]{2,}\z/',$forename)){
		echo REG_ERROR_FORENAME."<br>";
		return false;
	}
 	else if(!preg_match('/\A^[a-zA-ZßäÄüÜöÖ ]{2,}\z/',$name)){
 		echo REG_ERROR_NAME."<br>";
 		return false;
 	}
	else if(!preg_match('/\A^[a-zA-Z]{1}[a-zA-Z0-9_-]{2,20}\z/',$username)){
		echo REG_ERROR_USERNAME."<br>";
		return false;
	}
	else if(!preg_match('/\A^[a-zA-Z0-9 _-]{4,20}\z/',$passwd)){
		echo REG_ERROR_PASSWORD."<br>"; 
		return false;
	}
	else if($passwd != $passwd_repeat){
		echo REG_UNMATCHED_PASSWORDS."<br>";
		return false;
	}
	else if(!preg_match('/\A\d{4}-\d{1,2}-\d{1,2}\z/',$birthday)){
		echo REG_ERROR_BIRTHDAY."<br>";
		return false;
	}
	else if(!preg_match('/\A^[0-9]'/*{1, 5}\z/*/.'\z/',$GID)){
		echo REG_ERROR_GID."<br>";
		return false;
	}
	else if((!preg_match('/\A\d{1,3}(.\d{2})?\z/',$credits) || ($credits > 100))){
		echo REG_ERROR_CREDITS."<br>";
		return false;
	}
	return true;
}

/**
  *We need the birthday in another format, so this function puts it together in the right order
  */
function merge_birthday($day, $month, $year){
	return $day."-".$month."-".$year;
}

/**
  *Corrects possible conflicts with MySQL-Server and flooat-numbers cause of mixing of commata and full stops.
  *replaces full stops with commata.
  *@param $str The String to correct
  */
function correct_credits_input($str){
	$a=str_replace(',', '.', $str);
	return $a;
}

/**
  *handles the groups for dropdown-box in register.tpl
  *
  *@param $sql_groups the groups queried from the SQL-server
  */
function group_init_smarty_vars() {
	require_once PATH_INCLUDE."/group_access.php";
	
	$group_manager = new GroupManager;
	global $smarty;

	$sql_groups = $group_manager->getAllGroups();
	
	foreach($sql_groups as $group) {
		$arr_group_id[] = $group["ID"];
		$arr_group_name[] = $group["name"];
	}
	$smarty->assign('gid', $arr_group_id);
	$smarty->assign('g_names', $arr_group_name);
}
?>
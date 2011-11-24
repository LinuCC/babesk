<?php
/**
 *@file register_functions.php here are some outsourced functions of the register-module
 */

/**
 *handles the input from the register-form and add the user in the mysql-table
 */
function register_process($forename,$name,$username,$passwd,$passwd_repeat,$cardID,$birthday,$GID,$credits){

	require_once PATH_INCLUDE."/user_access.php";
	require_once PATH_INCLUDE.'/card_access.php';
	require_once PATH_INCLUDE."/logs.php";

	$userManager = new UserManager();
	$cardManager = new CardManager();
	$logger= new Logger;

	//checks the input for wrong Characters etc
	if(inputcheck($forename,$name,$username,$passwd,$passwd_repeat,$cardID,$birthday,$GID,$credits)) {
		try {
			$userManager->addUser($name, $forename, $username, $passwd, $birthday, $credits, $GID);
			try {
				$cardManager->addCard($cardID, $userManager->getUserID($username));
			} catch (Exception $e) {
				echo 'Could not add the CardID!!';
			}
		} catch (Exception $e) {
			echo "<br>".REG_ERROR_MYSQL.$e->getMessage()."<br>";
			return false;
		}
		echo "<br><b>Hallo ".$name."!</b><br>";
		echo '<a href="index.php?'.htmlspecialchars(SID).'">Zur&uuml;ck zum Admin Bereich</a>';
		$logger->log(USERS,NOTICE,"REG_ADDED_USER-ID:".$cardID."-NAME:".$name."-FORENAME:".$forename."-BIRTHDAY:".
		$birthday."-CREDITS:".$credits."-GID:".$GID."-");
	}
	else {
		echo REG_PLEASE_REPEAT.'<br>';
		return false;
	}
	return true;
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

	$group_manager = new GroupManager('groups');
	global $smarty;

	$arr_group_id = array();
	$arr_group_name = array();

	$sql_groups = $group_manager->getTableData();
	if(!empty($sql_groups)){
		foreach($sql_groups as $group) {
			$arr_group_id[] = $group["ID"];
			$arr_group_name[] = $group["name"];
		}
	}
	$smarty->assign('gid', $arr_group_id);
	$smarty->assign('g_names', $arr_group_name);
}
?>
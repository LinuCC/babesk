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
	if (!isset($_SESSION)) {
		return false;
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SESSION['IP']) AND $_SESSION['IP'] != $ip) {
		return false;
	} else {
		$_SESSION['IP'] = $ip;
		return true;
	}
}

/**
 * Formats a dateformat to d.m.Y
 * This function formats the parameter $date to d.m.Y using strtotime (if the Parameter is a datestring) and date()
 * @param $date The datestring; UnixTimestamp or date string
 * @return string
 */
function formatDate($date) {
	if (is_numeric($date)) { //a Unix timestamp
		return date('d.m.Y', $date);
	} else { //a date string
		return date('d.m.Y', strtotime($date));
	}
}

/**
 * Formats a dateformat to d.m.Y H:i
 * @param $date UnixTimestamp or date string
 * @return string
 */
function formatDateTime($date) {
	if (is_numeric($date)) { //a Unix timestamp
		return date('d.m.Y H:i', $date);
	} else { //a date string
		return date('d.m.Y H:i', strtotime($date));
	}
}

function sql_prev_inj($str) {
	require 'sql_access/databaseDistributor.php';
	if (!is_string($str)) {
		throw new BadFunctionCallException('Wrong parameter-format for $str in ' . __FUNCTION__);
	}
	$db->real_escape_string($str);
	return $str;
}

/**
 * hash_password returns the md5-hash of the string
 * Enter description here ...
 * @param $pw_str the string to hash
 * @return string
 */
function hash_password($pw_str) {
	return md5($pw_str);
}

/**
 * Checks the string with the given regex or string
 * inputcheck takes the string and checks if it matches with a regex. The Regex can be
 * given by the parameter $regex_str, $regex_str can also have a string (for more information see param)
 * @param string $str the string to check
 * @param string $regex_str either the regex_str which should check the string or one of the
 * following:
 *  name - normal string for name : /\A^[a-zA-Z]{1}[a-zA-ZßäÄüÜöÖ -]{2,30}\z/ ||
 *  password - Checks if it matches general pw-conditions: /\A^[a-zA-ZßäÄüÜöÖ -]{2,30}\z/ ||
 *  card_id - specific conditions for the card_id: /\A^[0-9]{11}\z/ ||
 *  id - just a number, maximum 20 Chars: /\A^[0-9]{1,20}\z/ ||
 *  number - a number with min 1 char and no maximum limit: /\A\d{1,}\z/ ||
 *  birthday - the YYYY-MM-DD format of birthday: /\A\d{4}-\d{1,2}-\d{1,2}\z/ ||
 *  credits - credits-field: /\A\d{1,5}([.,]\d{2})?\z/ ||
 * @param string $name_str the name of the Field the Value was entered in
 * (is used for throwing a WrongInputException, handling the name-string not necessary)
 * @return boolean only when no error found
 * @throws WrongInputException if string does not match the regex
 */
function inputcheck($str, $regex_str, $name_str = 'Input') {
	switch ($regex_str) {
		case 'name':
			$regex_str = '/\A^[^\,\;\+\~]{2,30}\z/';
			break;
		case 'password':
			$regex_str = '/\A^[a-zA-Z0-9 _öäü\-\.]{4,20}\z/';
			break;
		case 'card_id':
			$regex_str = '/\A^[a-z0-9]{10}\z/';
			break;
		case 'id':
			$regex_str = '/\A^[0-9]{1,20}\z/';
			break;
		case 'birthday':
			$regex_str = '/\A\d{4}-\d{1,2}-\d{1,2}\z/';
			break;
		case 'credits':
			$regex_str = '/\A\d{1,5}([.,]\d{2})?\z/';
			break;
		case 'number':
			$regex_str = '/\A\d{1,}\z/';
			break;
		case 'email':
			$regex_str = '/\A[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\z/';
			break;
	}
	if (!preg_match($regex_str, $str)) {
		throw new WrongInputException($str, $name_str);
	}
}

/**
 * returns the first day of the week weeknr in the year $year
 * @param unknown_type The year in which the week is
 * @param unknown_type The weeknumber
 */
function getFirstDayOfWeek($year, $weeknr) {
	$offset = date('w', mktime(0, 0, 0, 1, 1, $year));
	$offset = ($offset < 5) ? 1 - $offset : 8 - $offset;
	$monday = mktime(0, 0, 0, 1, 1 + $offset, $year);

	return strtotime('+' . ($weeknr - 1) . ' weeks', $monday);
}

/**
 * Enter description here...
 */

function navBar($showPage, $table, $mod, $action,$filter) {
	require_once 'sql_access/DBConnect.php';
	$dbObject = new DBConnect();
	$dbObject->initDatabaseFromXML();
	$db = $dbObject->getDatabase();
	$db->query('set names "utf8";');

	$query = sql_prev_inj(sprintf('SELECT COUNT(*) AS total FROM %s', $table));
	$result = $db->query($query);
	if (!$result) {
		throw DB_QUERY_ERROR.$db->error;
	}

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$maxPages = ceil($row['total'] / 10);
	$string="";
	if($showPage > 1){
		$string .= '<a href="?sitePointer=1&section=System|'.$mod.'&filter='.$filter.'&action='.$action.'"><<</a>&nbsp;&nbsp;';
		$string .= '<a href="?sitePointer='.($showPage-1).'&section=System|'.$mod.'&filter='.$filter.'&action='.$action.'"><</a>&nbsp;&nbsp;';
	}

	for($x=$showPage-5;$x<=$showPage+5;$x++){
		if(($x>0 && $x<$showPage) || ($x>$showPage && $x<=$maxPages))
			$string .= '<a href="?sitePointer='.$x.'&section=System|'.$mod.'&filter='.$filter.'&action='.$action.'">'.$x.'</a>&nbsp;&nbsp;';

		if($x==$showPage)
			$string .= $x . '&nbsp;&nbsp;';
	}
	if($showPage < $maxPages){
		$string .= '<a href="?sitePointer='.($showPage+1).'&section=System|'.$mod.'&filter='.$filter.'&action='.$action.'">></a>&nbsp;&nbsp;';
		$string .= '<a href="?sitePointer='.$maxPages.'&section=System|'.$mod.'&filter='.$filter.'&action='.$action.'">>></a>&nbsp;&nbsp;';
	}

	return $string;
}
?>

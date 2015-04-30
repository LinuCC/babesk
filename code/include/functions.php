<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "PasswordHash.php";

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

/**
 * DEPRECATED
 */
function sql_prev_inj($str) {
	require 'sql_access/databaseDistributor.php';
	if (!is_string($str)) {
		throw new BadFunctionCallException('Wrong parameter-format for $str in ' . __FUNCTION__);
	}
	$db->real_escape_string($str);
	return $str;
}

/**
 * hash_password returns the hashed string
 * SHA265, PBKDF2 with 24 bytes
 * @param $pw_str the string to hash
 * @return string
 */
function hash_password($pw_str) {
	//return md5($pw_str);
	return create_hash($pw_str);
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
function inputcheck($str, $regex_str, $name_str = 'Input',
	$allowedVoid = false) {

	if($str == '') {
		if(!$allowedVoid) {
			throw new WrongInputException($str, $name_str);
		}
		else {
			return;
		}
	}

	switch ($regex_str) {
		case 'name':
			$regex_str = '/\A^[^\,\;\+\~]{2,30}\z/';
			break;
		case 'password':
			$regex_str = '/\A^[a-zA-Z0-9 _öäü\-\.]{4,32}\z/';
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
	if(substr($regex_str, -1, 1) != '/' || substr($regex_str, 0, 1) != '/') {
		throw new Exception('Wrong Regex');
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

function navBar($showPage, $table,$headmod, $mod, $action,$filter) {
	require_once 'sql_access/DBConnect.php';
	$dbObject = new DBConnect();
	$dbObject->initDatabaseFromXML();
	$db = $dbObject->getDatabase();
	$db->query('set names "utf8";');

	$query = sql_prev_inj(sprintf('SELECT COUNT(*) AS total FROM %s', $table));
	$result = $db->query($query);
	if (!$result) {
		throw new Exception('Fehler: Nichts gefunden!');
	}

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$maxPages = ceil($row['total'] / 10);
	$string="";
	if($showPage > 1){
		$string .= '<a href="?sitePointer=1&section='.$headmod.'|'.$mod.'&filter='.$filter.'&action='.$action.'"><<</a>&nbsp;&nbsp;';
		$string .= '<a href="?sitePointer='.($showPage-1).'&section='.$headmod.'|'.$mod.'&filter='.$filter.'&action='.$action.'"><</a>&nbsp;&nbsp;';
	}

	for($x=$showPage-5;$x<=$showPage+5;$x++){
		if(($x>0 && $x<$showPage) || ($x>$showPage && $x<=$maxPages))
			$string .= '<a href="?sitePointer='.$x.'&section='.$headmod.'|'.$mod.'&filter='.$filter.'&action='.$action.'">'.$x.'</a>&nbsp;&nbsp;';

		if($x==$showPage)
			$string .= $x . '&nbsp;&nbsp;';
	}
	if($showPage < $maxPages){
		$string .= '<a href="?sitePointer='.($showPage+1).'&section='.$headmod.'|'.$mod.'&filter='.$filter.'&action='.$action.'">></a>&nbsp;&nbsp;';
		$string .= '<a href="?sitePointer='.$maxPages.'&section='.$headmod.'|'.$mod.'&filter='.$filter.'&action='.$action.'">>></a>&nbsp;&nbsp;';
	}

	return $string;
}

/**
 * Gettext-function accepting multiple parameters for placeholders
 *
 * @param  String $id The ID of the Gettext-String
 * @return String The fetched gettext with the placeholders repalced by
 * additional Arguments
 */
function _g($id)
{
	$func_args=func_get_args();

//  return vsprintf(gettext($id), array_slice(func_get_args(), 1));
	return vsprintf(gettext($id), array_slice($func_args, 1));

}

/**
 * Like empty(), but accepts 0, 0.0 and "0" as true values, too
 * @param  mixed   $value The value to check for blank
 * @return boolean        false if the value is
 * 'NULL', 'FALSE', 'array()' (empty array) or '""' (empty string)
 * else true
 */
function isBlank($value) {

	return empty($value) && !is_numeric($value);
}

/**
 * Requires all that are in a a directory
 * @param  string $path The path of the direcory
 */
function require_all ($path) {
	foreach (glob($path.'*.php') as $filename) {
		require_once $filename;
	}
}

/**
 * Shortcut for the die(json_encode()) method
 */
function dieJson($data) {
	die(json_encode($data));
}

/**
 * Shortcut for http_response_code($statusCode); die($text);
 */
function dieHttp($text, $statusCode) {
	http_response_code($statusCode);
	die($text);
}

/**
 * Parses the PUT-data of a request and returns then
 * Since PHP has no inbuild way to parse PUT-Requests (like $_POST or $_GET)
 * we need to manually parse them.
 * @return array
 */
function parsePut() {
	$putData = [];
	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
		parse_str(file_get_contents("php://input"), $putData);
		foreach ($putData as $key => $value) {
			unset($putData[$key]);
			$putData[str_replace('amp;', '', $key)] = $value;
		}
	}
	return $putData;
}

?>

<?php
require_once PATH_ACCESS . '/TableManager.php';

class GlobalSettingsManager extends TableManager {
	function __construct() {
		parent::__construct('global_settings');
	}
	
	/**
	 * returns the helptext found in the MySQL-table global_settings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getHelpText() {
		$entry_arr = $this->searchEntry('name="helptext"');
		if (!isset($entry_arr['value']) || !$entry_arr['value'])
			throw new MySQLVoidDataException('helptext is void!');
		return $entry_arr['value'];
	}
	
	/**
	 * Changes the HelpText
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function changeHelpText($str) {
		$entry_arr = $this->searchEntry('name="helptext"');
		if (!isset($entry_arr) || !count($entry_arr)) {
			throw new MySQLVoidDataException('searchEntry returned void helparray');
		}
		$help_id = $entry_arr['id'];
		$this->alterEntry($help_id, 'value', $str);
	}
	
	/**
	 * Returns both of the infotexts of the table global_settings
	 * Enter description here ...
	 * @return array [0] = infotext1 [1] = infotext2
	 */
	function getInfoTexts() {
		$it_arr = array();
		$it1 = $this->searchEntry('name="menu_text1"');
		$it2 = $this->searchEntry('name="menu_text2"');
		
		$it_arr[0] = $it1['value'];
		$it_arr[1] = $it2['value'];
		
		if (!$it_arr[0] || !$it_arr[1]) {
			throw new MySQLVoidDataException('MySQL returned a void element!');
		}
		return $it_arr;
	}
	
	/**
	 * returns the value of soli_price
	 * @throws UnexpectedValueException when soli_price is NULL
	 * @throws something else when MySQL has problems
	 * @return string the soli_price
	 */
	function getSoliPrice() {
		$pid = parent::searchEntry("name = 'soli_price'");
		$soli_price = parent::getEntryValue($pid['id'], 'value');
		if($soli_price === NULL)
			throw new UnexpectedValueException('soli_price has no value!');
		return $soli_price;
	}
	
	/**
	 * Changes the value of "soli_price" to the given value
	 * @throws something if something has gone wrong
	 */
	function changeSoliPrice($value) {
		$pid = parent::searchEntry("name = 'soli_price'");
		parent::alterEntry($pid['id'], 'value', $value);
	}
	
	/**
	* returns the value of last_order_time
	* @throws UnexpectedValueException when last_order_time is NULL
	* @throws something else when MySQL has problems
	* @return string the last_order_time
	*/
	function getLastOrderTime() {
		$pid = parent::searchEntry("name = 'last_order_time'");
		$lastOrderTime = parent::getEntryValue($pid['id'], 'value');
		if($lastOrderTime === NULL)
		throw new UnexpectedValueException('last_order_time has no value!');
		return $lastOrderTime;
	}
}
?>
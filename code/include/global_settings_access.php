<?php
require_once PATH_INCLUDE . '/access.php';

class globalSettingsManager extends TableManager {
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
}
?>
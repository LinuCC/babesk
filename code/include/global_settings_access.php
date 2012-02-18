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
}
?>
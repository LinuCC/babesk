<?php
/**
 * Provides a class to manage the groups of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the groups, provides methods to add/modify groups or to get group data
 */
class GroupManager extends TableManager {

	function __construct() {
		parent::__construct('groups');
	}

	/**
	 * Returns the max_credit for the given group
	 * @param $ID the ID of the group
	 * @return float the max_credit
	 */
	function getMaxCredit($ID) {
		$group = parent::getEntryData($ID, 'max_credit');
		return $group['max_credit'];
	}

	/**
	 * returns the GroupID  which has the given name
	 * If there are multiple groups with the same name, it will return the ID of the first Group it found
	 * @param string $str the name of the Group to search for
	 */
	function getGroupIDByName($str) {
		$group = parent::searchEntry(sprintf('name = "%s"', $str));
		return $group['ID'];
	}
	
	/**
	 * getGroupName returns the name of a group
	 * @param numeric_string $ID
	 * @return string
	 */
	function getGroupName($ID) {
		$name = $this->getEntryValue($ID, 'name');
		return $name;
	}

	function addGroup($groupname, $max_credit) {
		parent::addEntry('name', $groupname, 'max_credit', $max_credit);
	}
}
?>
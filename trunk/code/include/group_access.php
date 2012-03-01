<?php
    /**
     * Provides a class to manage the groups of the system
     */

	require_once 'access.php';

    /**
     * Manages the groups, provides methods to add/modify groups or to get group data
     */
    class GroupManager extends TableManager{
    	
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
    }    
?>
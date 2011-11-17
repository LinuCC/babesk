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
    	//nothing here at the moment. all covered by TableManager
    }    
?>
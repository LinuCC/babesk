<?php
    /**
     * Provides a class to manage the groups of the system
     */

    /**
     * Manages the groups, provides methods to add/modify groups or to get group data
     */
    class GroupManager {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
        
        /**
         * Returns the value of the requested fields for the given group id.
         *
         * The Function takes a variable amount of parameters, the first being the group id
         * the other parameters are interpreted as being the fieldnames in the groups table.
         * In addition, if no parameters are given, the function will return all
         * stuff in the database.
         * The data will be returned in an array with the fieldnames being the keys.
         *
         * @return false if error
         */
        function getGroupData() {
        	$num_args = func_num_args();
        	if($num_args == 0){
        		$query = 'SELECT * FROM groups';
        	}
        	else if($num_args > 1){
        		$query = 'SELECT
 		   					'.$fields.'
           				FROM
           					groups
           				WHERE
           					ID = '.$id.'';
        		$id = func_get_arg(0);
        		$fields = "";
        		
        		for($i = 1; $i < $num_args - 1; $i++) {
        			$fields .= func_get_arg($i).', ';
        		}
        		//query must not contain an ',' after the last field name
        		$fields .= func_get_arg($num_args - 1);
        	}
        	else {
        		return false;
        	}
        	
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            $res_array = array();
            while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
            return $res_array;
        }
		
         /**
         * Adds a Group to the System
         *
         * The Function creates a new entry in the groups Table
         * consisting of the given Data
         *
         * @param name The name of the group
         * @param max_credit The maximal credit a user belonging to the group can have
         * @return false if error
         */
        function addGroup($name, $max_credit) {
        	$query = 'INSERT INTO
        	               groups(name, max_credit)
                      VALUES
                            ("'.$name.'", '.$max_credit.');';
    
           $result = $this->db->query($query);
        	if (!$result) {
            	echo "Table Groups: ".DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
        	return true;
    }
        
        /**
         * Deletes a group from the system
         *
         * Delete the entry from the groups table with the given ID
         *
         * @param ID The ID of the group
         * @return false if error
         */
        function delGroup($ID) {
        	$query = 'DELETE FROM
        	               groups(ID, name, max_credit)
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
    }    




?>
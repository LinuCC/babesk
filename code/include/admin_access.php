<?php
     /**
     * Provides a class to manage the administrators and their groups
     */
    
    /**
     * Manages the admins and admin groups, provides methods to add/modify admins/admin groups
     * or to get data
     */

    class AdminManager {
    
        private $db;
        
        function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
        
        /**
         * Returns the id of the admin with the given name
         * 
         * @return false if error otherwise the admin id
         */
        function getAdminID($adminname) {
            $query = 'SELECT
    					ID
    				FROM
    					administrators
    				WHERE
    					name = "'.$adminname.'"';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            $row = $result->fetch_assoc();
            if($row['ID']) {
                return $row['ID'];
            }
            else {               //the name doesn't exist
                return -1;    
            }             
        }
        
        function getAdminGroupID($groupname) {
        	$query = 'SELECT
    					ID
    				FROM
    					admin_groups
    				WHERE
    					name = "'.$groupname.'"';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            $row = $result->fetch_assoc();
            if($row['ID']) {
                return $row['ID'];
            }
            else {               //the name doesn't exist
                return -1;
            }
        }	
        
        /**
         * Check whether the password for the given admin is correct
         * 
         * @return true if password is correct
         */
        function checkPassword($aid, $password) {
        	require_once PATH_INCLUDE.'/functions.php';
            $query = 'SELECT
    					password
    				FROM
    					administrators
    				WHERE
    					ID = '.$aid.'';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
        	$row = $result->fetch_assoc();
            if(hash_password($password) == $row["password"]) {
            	return true;
            }
            else {
            	return false;
            }
        }
        
        /**
         * Returns the group id of the admin with the given name
         * 
         * @return false if error otherwise the group id
         */
        function getAdminGroup($adminname) {
            $query = 'SELECT
    					GID
    				FROM
    					administrators
    				WHERE
    					name = "'.$adminname.'"';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            $row = $result->fetch_assoc();
            return $row["GID"];
        }
        
        function getAdmins() {
        	$query = 'SELECT
    					name
    				  FROM
    					administrators';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
        	$adminNames = array();
        	while($row = $result->fetch_assoc()) {
        		$adminNames[] = $row["name"];
        	}
            return $adminNames;	
        }
        
        function getAdminGroups() {
        	$query = 'SELECT
    					name
    				  FROM
    					admin_groups';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
        	$adminNames = array();
        	while($row = $result->fetch_assoc()) {
        		$adminNames[] = $row["name"];
        	}
            return $adminNames;	
        }
        
        
        /**
         * Returns the value of the requested fields for the given admin group id.
         *
         * The Function takes a variable amount of parameters, the first being the admin group id
         * the other parameters are interpreted as being the fieldnames in the admin_groups table.
         * The data will be returned in an array with the fieldnames being the keys.
         *
         * @return false if error
         */
        function getAdminGroupData() {
            //at least 2 arguments needed
            $num_args = func_num_args(); 
            if ($num_args < 2) {
                return false;
            }
            $id = func_get_arg(0);
            $fields = "";
            
            for($i = 1; $i < $num_args - 1; $i++) {
                $fields .= func_get_arg($i).', ';
            }
            $fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name    
            
            $query = 'SELECT
    					'.$fields.'
    				FROM
    					admin_groups
    				WHERE
    					ID = '.$id.'';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            return $result->fetch_assoc();
        }
        
        /**
         * Adds an Administrator to the System
         *
         * The Function creates a new entry in the administrators Table
         * consisting of the given Data
         *
         * @param name The name of the new administrator
         * @param password His password
         * @param gid His Group ID
         * @return false if error
         */
        function addAdmin($name, $password, $gid) {
        	
        	require_once PATH_INCLUDE.'/functions.php';
            if ($this->getAdminID($name) != -1) {
                echo USERNAME_EXISTS;
                return false;
            }
        	$query = 'INSERT INTO
                            administrators(name, password, GID)
                      VALUES
                            ("'.$name.'", "'.md5($password).'", '.$gid.');';
    
           $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
         
        
        /**
         * Deletes an admin from the system
         *
         * Delete the entry from the administrators table with the given ID
         *
         * @param ID The ID of the admin
         * @return false if error
         */
        function delAdmin($ID) {
        	$query = 'DELETE FROM
        	               administrators
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
        /**
         * Adds an admin group to the System
         *
         * The Function creates a new entry in the admin_groups Table
         * consisting of the given Data
         *
         * @param name The name of the new admin group
         * @param modules A comma separated list of the modules that are allowed for members of the new group
         * @return false if error
         */
        function addAdminGroup($name, $modules) {
            if ($this->getAdminGroupID($name) != -1) {
                echo GROUP_EXISTS;
                return false;
            }
        	$query = 'INSERT INTO
                            admin_groups(name, modules)
                      VALUES
                            ("'.$name.'", "'.$modules.'");';
    
           $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
         
        
        /**
         * Deletes an admin group from the system
         *
         * Delete the entry from the admin_groups table with the given ID
         *
         * @param ID The ID of the admin group
         * @return false if error
         */
        function delAdminGroup($ID) {
        	$query = 'DELETE FROM
        	               admin_groups
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
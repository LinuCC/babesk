<?php
    /**
     * Provides a class to manage the users of the system
     */

	require_once 'access.php';
	
    /**
     * Manages the users, provides methods to add/modify users or to get user data
     */
    class UserManager extends TableManager{
        
        public function __construct() {
        	parent::__construct('users');
        }
        
         /**
         * Returns the id of the user with the given username
         * 
         * @param   $username The name of the user
         * @return  the user id or false if error
         */
        function getUserID($username) {
            $user = parent::getTableData('username="'.$username.'"');
            return $user[0]['ID'];
        }
        
        function updatePassword($uid, $new_passwd) {
        	$query = 'UPDATE users
                                SET first_passwd = 0,
                                    password = "'.md5($new_passwd).'"
                              WHERE ID = '.$uid.';';
        	$result = $this->db->query($query);
        	if (!$result) {
        		echo DB_QUERY_ERROR.$this->db->error;
        		return false;
        	}
        	return true;
        }

        function getMaxRechargeAmount($id) {
            $userData = $this->getEntryData($id, 'credit', 'GID');
            $credit = $userData['credit'];
            $gid = $userData['GID'];
            
            //require 'group_access.php';
            $groupManager = new GroupManager('groups');
            
            $groupData = $groupManager->getEntryData($gid, 'max_credit');
            if(!$groupData)die('Error in getMaxRechargeAmount');
	        $max_credit = $groupData['max_credit'];
            return $max_credit - $credit;
        }
        
        function changeBalance($id, $amount) {
            if($amount > $this->getMaxRechargeAmount($id)) {    //check whether the amount isn't to big
            	return false;
            }
                        
            $userData = parent::getEntryData($id, 'credit');
            $oldCredit = $userData['credit'];
            
            if($oldCredit + $amount < 0) {          //credit can't be negative
            	return false;
            }
            $credit = $oldCredit + $amount;

            $query = 'UPDATE users
        				SET credit = '.$credit.'
        			  WHERE ID = '.$id.';';
          	$result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
        /**
         * Check whether the password for the given user is correct
         * 
         * @return true if password is correct
         */
        function checkPassword($uid, $password) {
            $sql = 'SELECT
    					password
    				FROM
    					users
    				WHERE
    					ID = ?';
    	    $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                exit($this->db->error);
            }
            $stmt->bind_param('i', $uid);
            if (!$stmt->execute()) {
                exit($stmt->error);
            }

            $stmt->bind_result($result);
        	if (!$stmt->fetch()) {
            	return false;
        	}
        	$stmt->close();
			if (md5($password) == $result) {
				return true;
			} else {
				$sql = 'UPDATE
                        	users
                    	SET
                        	login_tries = login_tries + 1
                    	WHERE
                        	ID = ?';
            	$stmt = $this->db->prepare($sql);

            	if (!$stmt) {
                	exit($this->db->error);
            	}
            	$stmt->bind_param('s', $userID);
            	if (!$stmt->execute()) {
                	exit($stmt->error);
            	}
            	$stmt->close();
				return false;
			}
        }
        
         /**
         * Adds a User to the System
         *
         * The Function creates a new entry in the users Table
         * consisting of the given Data, and tests if the username already exists.
         *
         * @param ID The ID of the User
         * @param passwd The password of the user
         * @param name The lastname of the user
         * @param forename The forename of the User
         * @param birthday The birthday of the User
         * @param credit The initial credit of the User
         * @param GID The group the user belongs to
         * @return false if error
         */
        function addUser($cardID, $name, $forename, $username, $passwd, $birthday, $credit, $GID) {
        	try { //test if username already exists
        		parent::getTableData('username = "'.$username.'"');
        	} catch (MySQLVoidDataException $e) { //username does not exist
        		parent::addEntry('ID', $cardID,	'name', $name, 'forename', $forename, 'username', $username, 'password', md5($passwd),
        					 'birthday', $birthday, 'credit', $credit, 'GID', $GID, 'last_login', 'CURRENT_TIMESTAMP', 'login_tries', 0, 'first_passwd', 1);
        		return;
        	}
        	//username exists
        	throw new Exception(USERNAME_EXISTS);
        }
        
        // check for first password
        function firstPassword($ID) {
            $user_data = parent::getEntryData($ID, 'first_passwd');
            return $user_data['first_passwd'];
        }
    }
   

?>
<?php
    /**
     * Provides a class to manage the users of the system
     */

    /**
     * Manages the users, provides methods to add/modify users or to get user data
     */
    class UserManager {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
         /**
         * Returns the id of the user with the given username
         * 
         * @param   $username The name of the user
         * @return  the user id or false if error
         */
        function getUserID($username) {
            $sql = 'SELECT
                        ID
                    FROM
                        users
                    WHERE
                        username = ?';
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                exit($this->db->error);
            }
            $stmt->bind_param('s', $username);
            if (!$stmt->execute()) {
                exit($stmt->error);
            }

            $stmt->bind_result($result);
        	if (!$stmt->fetch()) {
            	return -1;
        	}
        	$stmt->close();
        	if($result) {
                return $result;
            }
            else {               //the name doesn't exist
                return -1;
            }
        }
        
        /**
         * Returns the id of the user with the given card id
         * 
         * @param   $cardnumber The id of the users card
         * @return  false if error otherwise the user id
         */
        function getCardOwner($cardID) {
            $query = 'SELECT
    					UID
    				FROM
    					cards
    				WHERE
    					cardnumber = "'.$cardID.'"';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            $row = $result->fetch_assoc();
            return $row["UID"];
        }
        
        
        function getMaxRechargeAmount($id) {
            $userData = $this->getUserData($id, 'credit', 'GID');
            $credit = $userData['credit'];
            $gid = $userData['GID'];
            
            //require 'group_access.php';
            $groupManager = new GroupManager('groups');
            
            $groupData = $groupManager->getTableData($gid, 'max_credit');
            $max_credit = $groupData['max_credit'];
            return $max_credit - $credit;
        }
        
        function changeBalance($id, $amount) {
            if($amount > $this->getMaxRechargeAmount($id)) {    //check whether the amount isn't to big
                return false;
            }
                        
            $userData = $this->getUserData($id, 'credit');
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
         * Returns the value of the requested fields for the given user id.
         *
         * The Function takes a variable amount of parameters, the first being the user id
         * the other parameters are interpreted as being the fieldnames in the users table.
         * The data will be returned in an array with the fieldnames being the keys.
         *
         * @return false if error
         */
        function getUserData() {
            //at least 2 arguments needed
            $num_args = func_num_args();
            if ($num_args < 2) {
                return false;
            }
            $uid = func_get_arg(0);
            $fields = "";
            
            for($i = 1; $i < $num_args - 1; $i++) {
                $fields .= func_get_arg($i).', ';
            }
            $fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name 
            
            $query = 'SELECT
    					'.$fields.'
    				FROM
    					users
    				WHERE
    					ID = '.$uid.'';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            return $result->fetch_assoc();
        }
		
		 /**  //// ############### Das Funktioniert doch nicht!!!??!! ###########################
         * Update the value of the requested fields for the given user id.
         *
         * The Function takes a variable amount of parameters, the first being the user id
         * the other parameters are interpreted as being the fieldnames in the users table.
         *
         * @return true if all ok/false if error
         */
        function updateUserData() {
            //at least 2 arguments needed
            $num_args = func_num_args(); 
            if ($num_args < 2) {
                return false;
            }
            $uid = func_get_arg(0);
            $fields = "";
            
            for($i = 1; $i < $num_args - 1; $i++) {
                $fields .= func_get_arg($i).', ';
            }
            $fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name 
            
            $query = 'SELECT
    					'.$fields.'
    				FROM
    					users
    				WHERE
    					ID = '.$uid.'';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            return $result->fetch_assoc();
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
		
		
		 /**
         * Return all data of the user from the table user
		 *
         * @return false if error
         */
        function getAllUserData($uid) {
		    $result = array();
            $sql = 'SELECT
                        name,
                        forename,
                        username,
                        birthday,
                        credit,
                        GID,
                        last_login,
                        login_tries
                    FROM
                        users
                    WHERE
                        ID = ?';
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                exit($this->db->error);
            }
            $stmt->bind_param('s', $uid);
            if (!$stmt->execute()) {
                exit($stmt->error);
            }
            
            $stmt->bind_result($result['name'], $result['forename'], $result['username'], $result['birthday'], $result['credit'], $result['GID'], $result['last_login'], $result['login_tries']);
            if (!$stmt->fetch()) {
                exit($stmt->error);
            }
            $stmt->close();
            
            $sql = 'UPDATE
                        users
                    SET
                        login_tries = 0,
						last_login = NOW()
                    WHERE
                        ID = ?';
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                exit($this->db->error);
            }
            $stmt->bind_param('s', $uid);
            if (!$stmt->execute()) {
                exit($stmt->error);
            }
            $stmt->close();
			return $result;
            
        }
        
         /**
         * Adds a User to the System
         *
         * The Function creates a new entry in the users Table
         * consisting of the given Data
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
            //check if username already exists
            $query = 'SELECT
    				    *
    				FROM
    					users
    				WHERE
    					username = "'.$username.'"';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
        	if ($result->num_rows != 0) {
        	    echo USERNAME_EXISTS;
        	    return false;
        	}
        	//add the entry in the users table
        	$query = 'INSERT INTO
        	               users(name, forename, username, password, birthday, credit, GID, last_login, login_tries, first_passwd)
                      VALUES
                           ("'.$name.'", "'.$forename.'", "'.$username.'", "'.md5($passwd).'", "'.$birthday.'", '.$credit.', '.$GID.', CURRENT_TIMESTAMP, 0, 1);';
            $result = $this->db->query($query);
            if (!$result) {
            	echo "Table Users
				: ".DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	//add the entry in the cards table -> connect the user with his card id
        	$query = 'INSERT INTO
        	               cards(cardnumber, UID)
                      VALUES
                           ("'.$cardID.'", '.$this->db->insert_id.');';    //the id of the row inserted last
            $result = $this->db->query($query);
            if (!$result) {
            	echo "Table Cards: ".DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
        /**
         * Deletes a User from the System
         *
         * Delete the entry from the Users table with the given ID
         *
         * @param ID The ID of the User
         * @return false if error
         */
        function delUser($ID) {
        	$query = 'DELETE FROM
        	               users
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
        // check for first password
        function firstPassword($ID) {
            $user_data = $this->getUserData($ID, 'first_passwd');
            return $user_data['first_passwd'];
        }
    }
   

?>
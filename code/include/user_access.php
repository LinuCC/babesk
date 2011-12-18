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
		require 'dbconnect.php';
		require_once PATH_INCLUDE.'/functions.php';
		$query = sprintf( 'UPDATE users SET first_passwd = 0, password = "%s" WHERE ID = %s;',
							hash_password($new_passwd),
							sql_prev_inj($uid));
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
		if($amount > $this->getMaxRechargeAmount($id)) {
			return false;
		}
		$userData = parent::getEntryData($id, 'credit');
		$oldCredit = $userData['credit'];

		if($oldCredit + $amount < 0) {
			//credit can't be negative
			return false;
		}
		$credit = $oldCredit + $amount;

		$query = $this->db->real_escape_string('UPDATE users SET credit = '.$credit.' WHERE ID = '.$id.';');
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
		require_once PATH_INCLUDE.'/functions.php';
		$sql = ('SELECT password FROM users WHERE ID = ?');
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
		if (hash_password($password) == $result) {
			return true;
		} else {
			$sql = 'UPDATE users SET login_tries = login_tries + 1 WHERE ID = ?';
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
	function addUser($name, $forename, $username, $passwd, $birthday, $credit, $GID) {
		
		try { //test if username already exists
			parent::getTableData('username = "'.$username.'"');
		} catch (MySQLVoidDataException $e) {
			//username does not exist
			parent::addEntry('name', $name, 'forename', $forename, 'username', $username, 'password', md5($passwd),
        					 'birthday', $birthday, 'credit', $credit, 'GID', $GID, 'last_login', 'CURRENT_TIMESTAMP', 'login_tries', 0, 'first_passwd', 1);
				
			return;
		}
		//username exists
		throw new Exception(USERNAME_EXISTS);
	}
	
	function alterUser($old_id, $id, $name, $forename, $username, $passwd, $birthday, $credit, $GID) {
		if(isset($passwd)) {
		parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
							$username, 'password', $passwd, 'birthday', $birthday, 'credit', $credit, 'GID', $GID);
		}
		else {
			parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
								$username, 'birthday', $birthday, 'credit', $credit, 'GID', $GID);
		}
	}

	// check for first password
	function firstPassword($ID) {
		$user_data = parent::getEntryData($ID, 'first_passwd');
		return $user_data['first_passwd'];
	}

	/**
	 * ResetLoginTries Resets the login tries of one specific user
	 * Resets the login tries of one specific user
	 * @param numeric $ID
	 * @throws MySQLConnectionException if it failed to reset the login tries
	 */
	function ResetLoginTries($ID) {
		require 'dbconnect.php';
		$query = sql_prev_inj(sprintf('UPDATE %s SET login_tries = 0 WHERE ID = %s', $this->tablename, $ID));
// 		$query = $db->real_escape_string('UPDATE '.$this->tablename.' SET login_tries = 0 WHERE ID = '.$ID);
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to reset login tries!');
		}
	}

	function AddLoginTry($ID) {
		require "dbconnect.php";
		$query = sql_prev_inj(sprintf('UPDATE %s SET login_tries = login_tries + 1 WHERE ID = %s', $this->tablename, $ID));
// 		$query = $db->real_escape_string('UPDATE '.$this->tablename.' SET login_tries = login_tries + 1 WHERE ID = '.$ID);
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to add a login try!');
		}
	}
}
?>
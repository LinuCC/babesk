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
	 * @return  the user id
	 * @throws MySQLVoidDataException if the username is not found
	 */
	function getUserID($username) {
		$user = parent::getTableData('username="'.$username.'"');
		return $user[0]['ID'];
	}

	/**
	 *  @todo this function is not necessary anymore, functionality is alredy in alterUser(), replace getUserID
	 */
	function updatePassword($uid, $new_passwd) {
		require 'dbconnect.php';
		require_once PATH_INCLUDE.'/functions.php';
		$query = sprintf( 'UPDATE users SET first_passwd = 0, password = "%s" WHERE ID = %s;',
							hash_password($new_passwd),
							sql_prev_inj($uid));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		return true;
	}
	
	/**
	 * Sorts the users it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getUsersSorted() {
		require_once 'dbconnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY name', $this->tablename));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
	
	/**
	 * Checks if the Username or the complete name (forename + name) are already existing
	 * Enter description here ...
	 * @param string $forename
	 * @param string $name
	 * @param string $username
	 * @return boolean true if User is existing, false if not
	 */
	///@todo mach datt hier fertig! beim registrieren erlaubt er den user zu registern, obwohl schon einer mit gleichem Namen vorhanden sit
	function isUserExisting($forename, $name, $username) {
		try {
			$this->getTableData(sprintf('username="%s"', $username));
		} catch (MySQLVoidDataException $e) {
			try {
				$this->getTableData(sprintf('forename="%s" AND name="%s"', $forename, $name));
			} catch (MySQLVoidDataException $e) {
				return false;
			}
		} 
		return true;
	}
	
	/**
	 * Looks if the Amount of Credits the user has exeeds the maximum Amount of the group of the user
	 * Enter description here ...
	 * @param numeric string $uid the userID 
	 * @return number
	 */
	function getMaxRechargeAmount($uid) {
		$userData = $this->getEntryData($uid, 'credit', 'GID');
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

		$query = sql_prev_inj(sprintf('UPDATE users SET credit = %s WHERE ID = %s;', $credit, $id));
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR.$this->db->error);
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
	* Check whether the account for the given user is locked
	*
	* @return true if account is locked
	*/
	function checkAccount($uid) {
		require_once PATH_INCLUDE.'/functions.php';
		$sql = ('SELECT locked FROM users WHERE ID = ?');
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
		return $result;
	}
	
	/**
	returns username of all users, where soli = 1
	*/
	function checkSoliAccounts() {
		try {
			$users = TableManager::getTableData('soli = "1"');
		} catch (MySQLVoidDataException $e) {
			$orders = NULL;
		}
		
		return $users;
	}
	
	/**
	 * returns ID of all users, whose have a coupon
	 */
	function checkCouponAccounts() {
		$date = date('Y').'-'.date('m').'-'.date('d');
		$id = array();
		$query = "enddate > '".$date."'";
		try {
			$table_coupons = new TableManager('soli_coupons');
			$users = $table_coupons->getTableData($query);
			foreach ($users as $user) {
				array_push($id, $user['UID']);
			}
		} catch (MySQLVoidDataException $e) {
		};
		return $id;
	}

	/**
	* Check who doesn't need to pay the full price
	*
	* @return array with user_ids from users who don't need to pay full price
	*/
	function checkSoliAccounts() {
		/**
		require_once PATH_INCLUDE.'/functions.php';
		$sql = sql_prev_inj(sprintf('SELECT ID FROM users WHERE soli = 1'));
		
		$result = $this->db->query($sql);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR.$this->db->error);
		}
	
		return $result;
		*/
		try {
			$users = TableManager::getTableData('soli = "1"');
		} catch (MySQLVoidDataException $e) {
			$orders = NULL;
		}
		
		return $users;
	}
	
	
	/**
	* Locks an account
	*
	*@throws MySQLConnectionException if a problem with MySQL happened
	*/
	function lockAccount($uid) {
	if(isset($uid)) {
		parent::alterEntry($uid, 'locked', '1');
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
	/**
	 * Alters the Userdata of a given User
	 * Enter description here ...
	 * @param unknown_type $old_id The "old" ID, the ID of the user he has before the change
	 * @param unknown_type $id The new ID
	 * @param unknown_type $name The new Name
	 * @param unknown_type $forename
	 * @param unknown_type $username
	 * @param unknown_type $passwd The (already hashed!) password
	 * @param unknown_type $birthday The birthday (format YYYY-MM-DD)
	 * @param unknown_type $credit
	 * @param unknown_type $GID
	 * @param unknown_type $locked
	 */
	function alterUser($old_id, $id, $name, $forename, $username, $passwd, $birthday, $credit, $GID, $locked,$soli) {
		if(isset($passwd) && $passwd != "d41d8cd98f00b204e9800998ecf8427e") {	
		parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
							$username, 'password', $passwd, 'birthday', $birthday, 'credit', $credit, 'GID', $GID,'locked', $locked,'soli',$soli);
		}
		else {
			
			parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
								$username, 'birthday', $birthday, 'credit', $credit, 'GID', $GID,'locked',$locked,'soli',$soli);
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
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to reset login tries!');
		}
	}

	function AddLoginTry($ID) {
		require "dbconnect.php";
		$query = sql_prev_inj(sprintf('UPDATE %s SET login_tries = login_tries + 1 WHERE ID = %s', $this->tablename, $ID));
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to add a login try!');
		}
	}
}
?>
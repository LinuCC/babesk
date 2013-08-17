<?php
/**
 * Provides a class to manage the users of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the users, provides methods to add/modify users or to get user data
 */
class UserManager extends TableManager{

	public function __construct() {
		parent::__construct('users');
	}

	function getUser ($uid) {
		return $this->getEntryData($uid, '*');
	}

	public function changeUsers ($rows) {
		$qMng = $this->getMultiQueryManager ();
		foreach ($rows as $row) {
			$qMng->rowAdd ($row);
		}
		$qMng->dbExecute (DbMultiQueryManager::$Alter);
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
	 * returns the forename of the given ID of the user
	 * @param numeric_string $uid The ID of the User
	 */
	function getForename($uid) {
		return $this->getEntryValue($uid, 'forename');
	}

	/**
	 * returns the name of the given ID of the user
	 * @param numeric_string $uid The ID of the User
	 */
	function getName($uid) {
		return $this->getEntryValue($uid, 'name');
	}

	/**
	 * returns the username of the given ID of the user
	 * @param numeric_string $uid The ID of the User
	 */
	function getUsername($uid) {
		return $this->getEntryValue($uid, 'username');
	}

	function getUserdata ($uid) {
		return $this->getEntryData($uid, '*');
	}

	function changePassword ($userId, $new_password) {
		$this->alterEntry ($userId, 'password', hash_password($new_password), 'first_passwd', '0');
		return true;
	}

	public function changeEmailAdress ($userId, $email) {
		$this->alterEntry ($userId, 'email', $email);
	}

	public function getAllUsers () {
		return $this->getTableData ();
	}

	public function getSingleUser ($uid) {
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE ID=%s', $this->tablename,$uid));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}

	/**
	 * Sorts the users it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getUsersSorted($pagePointer,$orderBy) {

		require_once PATH_ACCESS . '/databaseDistributor.php';
		$res_array = array();

		$query = sprintf(
			'SELECT u.*,
			(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM usersInGradesAndSchoolyears uigs
					LEFT JOIN Grades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
			FROM %s u ORDER BY %s LIMIT %s, 10',
			$this->tablename,$orderBy,$pagePointer);

		// $query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY %s LIMIT %s,10', $this->tablename,$orderBy,$pagePointer));

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
	// function getMaxRechargeAmount($uid) {
	// 	require_once PATH_ACCESS . '/GroupManager.php';
	// 	$userData = $this->getEntryData($uid, 'credit');

	// 	$query = sql_prev_inj(sprintf('SELECT groupId FROM UserInGroups WHERE userId=%s',$uid));
	// 	$result = $this->db->query($query);
	// 	if (!$result) {
	// 		throw DB_QUERY_ERROR.$this->db->error;
	// 	}

	// 	while($buffer = $result->fetch_assoc())
	// 		$res_array[] = $buffer;

	// 	$credit = $userData['credit'];


	// 	$gid = $res_array[0]['groupId'];

	// 	//require_once PATH_ACCESS . '/GroupManager.php';
	// 	$groupManager = new GroupManager('Groups');

	// 	$groupData = $groupManager->getEntryData($gid, 'max_credit');
	// 	if(!$groupData)die('Error in getMaxRechargeAmount');
	// 	$max_credit = $groupData['max_credit'];
	// 	return $max_credit - $credit;
	// }

	function changeBalance($id, $amount) {
		if($amount > $this->getMaxRechargeAmount($id)) {
			return false;
		}
		$userData = parent::getEntryData($id, 'credit');
		$oldCredit = $userData['credit'];

		if($oldCredit + $amount < 0) {
			//credit can't be negative
			throw new BadMethodCallException('Final Amount of money is negative!');
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
		$users = TableManager::getTableData('soli = "1"');
		return $users;
	}

	/**
	 * checks if user of given UID does not need to pay full price
	 */
	function isSoli($UID) {
		$is_soli = parent::getEntryData($UID, 'soli');
		return $is_soli['soli'];
	}

	/**
	 * returns the birthday
	 */
	function getBirthday($UID) {
		$birthday = parent::getEntryData($UID, 'birthday');
		return $birthday['birthday'];
	}

	/**
	 * returns all User with Soli-Status
	 */
	function getAllSoli() {
		$solis = $this->getTableData('soli = 1');
		return $solis;
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
	 * Unlocks an account
	 *
	 *@throws MySQLConnectionException if a problem with MySQL happened
	 */
	function unlockAccount($uid) {
		if(isset($uid)) {
			parent::alterEntry($uid, 'locked', '0');
		}
	}

	/**
	 * sets religion
	 *
	 *@throws MySQLConnectionException if a problem with MySQL happened
	 */
	function SetReligion($uid,$religion) {
		if(isset($uid)) {
		$string=implode("|", $religion);

				try {
					parent::alterEntry($uid, 'religion', $string);
				} catch (Exception $e) {
					$this->userInterface->dieError($this->messages['error']['change'] . $e->getMessage());
				}
		}
	}

	/**
	 * sets foreign languages
	 *
	 *@throws MySQLConnectionException if a problem with MySQL happened
	 */
	function SetForeignLanguage($uid,$foreignLanguages) {
		if(isset($uid)) {

			$string=implode("|", $foreignLanguages);

				try {
					parent::alterEntry($uid, 'foreign_language', $string);
				} catch (Exception $e) {
					$this->userInterface->dieError($this->messages['error']['change'] . $e->getMessage());
				}
		}
	}


	/**
	 * sets special courses
	 *
	 *@throws MySQLConnectionException if a problem with MySQL happened
	 */
	function SetSpecialCourse($uid,$specialCourses) {
		if(isset($uid)) {

			$string=implode("|", $specialCourses);

			try {
				parent::alterEntry($uid, 'special_course', $string);
			} catch (Exception $e) {
				$this->userInterface->dieError($this->messages['error']['change'] . $e->getMessage());
			}
		}
	}

	/**
	 * gets the class, religion, foreign_language and course of an user
	 */
	function getUserDetails($uid){
		$query = sql_prev_inj(sprintf('SELECT class, religion, foreign_language, special_course FROM %s WHERE ID = %s', $this->tablename, $uid));
		$result = $this->db->query($query);
		if (!$result) {
			throw new Exception(DB_QUERY_ERROR.$this->db->error);
		}
		$result = $result->fetch_assoc();
		return $result;
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
	 * @param class The class the user belongs to
	 * @return false if error
	 */
	function addUser($name, $forename, $username, $passwd, $birthday, $credit, $GID, $class) {

		try { //test if username already exists
			parent::getTableData('username = "'.$username.'"');
		} catch (MySQLVoidDataException $e) {
			//username does not exist
			parent::addEntry('name', $name, 'forename', $forename, 'username', $username, 'password', md5($passwd),
        					 'birthday', $birthday, 'credit', $credit, 'GID', $GID, 'last_login', 'CURRENT_TIMESTAMP', 'login_tries', 0, 'first_passwd', 1,'class',$class);

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
	function alterUser($old_id, $id, $name, $forename, $username, $passwd, $birthday, $credit, $GID, $locked,$soli,$class) {
		if(isset($passwd) && $passwd != "d41d8cd98f00b204e9800998ecf8427e") {
		parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
							$username, 'password', $passwd, 'birthday', $birthday, 'credit', $credit, 'GID', $GID,'locked', $locked,'soli',$soli,'class',$class);
		}
		else {
			parent::alterEntry($old_id, 'ID', $id, 'forename', $forename, 'name', $name, 'username',
								$username, 'birthday', $birthday, 'credit', $credit, 'GID', $GID,'locked',$locked,'soli',$soli,'class',$class);
		}
	}

	public function alterUsername ($userId, $newUsername) {
		$this->alterEntry ($userId, 'username', $newUsername);
	}

	// check for first password
	function firstPassword($ID) {
		$user_data = parent::getEntryData($ID, 'first_passwd');
		return $user_data['first_passwd'];
	}

	function setFirstPasswordOfUser ($userId, $isFirstPw) {
		$isFirstPwSql = ($isFirstPw) ? '1' : '0';
		$this->alterEntry ($userId, 'first_passwd', $isFirstPwSql);
	}

	/**
	 * ResetLoginTries Resets the login tries of one specific user
	 * Resets the login tries of one specific user
	 * @param numeric $ID
	 * @throws MySQLConnectionException if it failed to reset the login tries
	 */
	function ResetLoginTries($ID) {
		require 'databaseDistributor.php';
		$query = sql_prev_inj(sprintf('UPDATE %s SET login_tries = 0 WHERE ID = %s', $this->tablename, $ID));
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to reset login tries!');
		}
	}

	function AddLoginTry($ID) {
		require "databaseDistributor.php";
		$query = sql_prev_inj(sprintf('UPDATE %s SET login_tries = login_tries + 1 WHERE ID = %s', $this->tablename, $ID));
		if(!$this->db->query($query)) {
			throw new MySQLConnectionException('failed to add a login try!');
		}
	}

	public function updateLastLoginToNow ($userId) {

		$query = sql_prev_inj(sprintf('UPDATE %s SET last_login = NOW() WHERE ID = %s', $this->tablename, $userId));
		$this->executeQuery($query);
	}

	function getClassByUsername($username) {
		$user = parent::getTableData('username="'.$username.'"');
		return $user[0]['class'];
	}
}
?>

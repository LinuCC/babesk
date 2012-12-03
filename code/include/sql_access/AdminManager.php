<?php
/**
 * Provides a class to manage the administrators and their groups
 */
require_once PATH_ACCESS . '/TableManager.php';
/**
 * Manages the admins and admin groups, provides methods to add/modify admins/admin groups
 * or to get data
 * @todo Den Kram auch refactorn
 * Aus dieser einen Klasse müssen 2 gemacht werden, da sie auf 2 Tabellen zugreift und
 * der TabelManager nur eine handeln kann.
 */

class AdminManager extends TableManager{

	function __construct() {
		parent::__construct('administrators');
	}

	/**
	 * Returns the id of the admin with the given name
	 *
	 * @return false if error otherwise the admin id
	 */
	function getAdminID($adminname) {

		$query = sql_prev_inj(sprintf('SELECT ID FROM administrators WHERE name = "%s"', $adminname));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
			echo "schinken";
			return false;
		}
		$row = $result->fetch_assoc();
		if($row['ID']) {
			return $row['ID'];
		}
		else {	//the name doesn't exist
			return -1;
		}
	}

	function getAdminName($ID) {
		$admin = $this->getEntryData($ID);
		if(!$admin) {
			throw new MySQLVoidDataException('MySQL returned no data!');
		}
		return $admin['name'];
	}
	
	function getAdminGroup($ID) {
		$admin = $this->getEntryData($ID);
		if(!$admin) {
			throw new MySQLVoidDataException('MySQL returned no data!');
		}
		return $admin['GID'];
	}

	/**
	 * Check whether the password for the given admin is correct
	 *
	 * @return true if password is correct
	 */
	function checkPassword($aid, $password) {
		require_once PATH_INCLUDE.'/functions.php';
		$query = sql_prev_inj(sprintf('SELECT password FROM administrators WHERE ID = %s', $aid));
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

	function getAdmins() {
		$query = sql_prev_inj(sprintf('SELECT name FROM administrators'));
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


	function getAllAdmins () {
		$admins = $this->getTableData ();
		return $admins;
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
	 *
	 * @todo Use addEntry instead!
	 */
	function addAdmin($name, $password, $gid) {

		require_once PATH_INCLUDE.'/functions.php';
		if ($this->getAdminID($name) != -1) {
			throw new Exception(USERNAME_EXISTS);
		}
		$query = sql_prev_inj(sprintf('INSERT INTO administrators(name, password, GID)
                      VALUES ("%s", "%s", %s);', $name, $password, $gid));
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
		parent::delEntry($ID);
	}
}

?>
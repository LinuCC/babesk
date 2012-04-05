<?php

require_once PATH_ACCESS . '/TableManager.php';

class AdminGroupManager extends TableManager{
	function __construct() {
		parent::__construct('admin_groups');
	}

	function getAdminGroupIdByName($groupname) {
		$admingroup = $this->getTableData(sprintf('name="%s"', $groupname));
		if($admingroup[0]['ID']) {
			return $admingroup[0]['ID'];
		} else {
			throw new VoidDataException('MySQL returned no data; the groupname does not exist!');
		}
	}
	
	function getAdminGroupName($ID) {
		$admingroup = $this->getEntryData($ID);
		$name = $admingroup['name'];
		if(!$name) {
			throw new VoidDataException('MySQL returned no name for the GroupID '.$ID.' !');
		}
		return $name;
	}

	/**
	 * Returns the group id of the admin with the given name
	 *
	 * @return false if error otherwise the group id
	 */
	function getAdminGroup($adminname) {
		$query = sql_prev_inj(sprintf('SELECT GID FROM administrators WHERE name = "%s"', $adminname));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
			return false;
		}
		$row = $result->fetch_assoc();
		return $row["GID"];
	}

	function getAdminGroups() {
		$query = sql_prev_inj(sprintf('SELECT name FROM admin_groups'));
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

		$query = sql_prev_inj(sprintf('SELECT %s FROM admin_groups WHERE ID = %s', $fields, $id));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
			return false;
		}
		return $result->fetch_assoc();
	}

	/**
	 * Adds an admin group to the System
	 *
	 * The Function creates a new entry in the admin_groups Table
	 * consisting of the given Data
	 *
	 * @param name The name of the new admin group
	 * @param modules A space separated list of the modules that are allowed for members of the new group
	 * @return false if error
	 */
	function addAdminGroup($name, $modules) {
		$query = sql_prev_inj(sprintf('SELECT COUNT(*) AS anzahl FROM admin_groups WHERE name="%s";', $name));
		$result = $this->db->query($query);
		$buffer = $result->fetch_assoc();

		
		
		if ($buffer["anzahl"] == "1") {
			echo GROUP_EXISTS;
			return false;
		}
		$query = sql_prev_inj(sprintf('INSERT INTO admin_groups(name, modules)
	                      VALUES ("%s", "%s");', $name, $modules));
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
		$query = sql_prev_inj(sprintf('DELETE FROM admin_groups WHERE ID = %s;', $ID));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_QUERY_ERROR.$this->db->error;
			return false;
		}
		return true;
	}

}

?>
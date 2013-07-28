<?php

class GroupModuleRight {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs a new Object
	 * @param int $moduleId The Id of the related Module
	 * @param int $groupId Tje Id of the related group
	 * @param boolean $accessAllowed If access to the module is allowed or not
	 */
	public function __construct($moduleId, $groupId) {

		$this->moduleId = $moduleId;
		$this->groupId = $groupId;
	}

	public static function initMultiple($array) {

		$rights = array();

		foreach($array as $arObj) {
			$rights[] = new GroupModuleRight(
				$arObj['moduleId'],
				$arObj['groupId']);
		}
		return $rights;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Converts an Array of Right-Arrays to GroupModuleRight-Objects
	 *
	 * @param  Array $rightArray An Array containing the rights, which are
	 * represented by another Array containing these keys: moduleId, groupId,
	 * accessAllowed
	 * @return Array an Array of the GroupModuleRight-Objects
	 */
	public static function multipleRightsArrayToObjects($rightArray) {

		$rightObj = array();

		foreach($rightArray as $right) {
			$rightObj[] = new GroupModuleRight(
				$right['moduleId'],
				$right['groupId'],
				$right['accessAllowed']);
		}

		return $rightObj;
	}

	/**
	 * Deletes an entry from the GroupModuleRights-Table
	 *
	 * @param  int $groupId  The ID of the Group
	 * @param  int $moduleId The ID of the Module
	 */
	public static function rightDelete($moduleId, $groupId) {

		TableMng::query("DELETE FROM GroupModuleRights
			WHERE `groupId` = '$groupId' AND `moduleId` = '$moduleId'");
	}

	/**
	 * Creates an entry in the GroupModuleRights Table
	 *
	 * @param  int $groupId   The Group-ID
	 * @param  int $moduleId  The Module-ID
	 */
	public static function rightCreate($moduleId, $groupId) {

		TableMng::query("INSERT INTO GroupModuleRights
			(`groupId`, `moduleId`) VALUES
			('$groupId', '$moduleId')");
	}

	/**
	 * Adds multiple Rights to the GroupModuleRights-Table
	 *
	 * @param  Array $rights An Array of GroupModuleRight-Objects
	 */
	public static function multipleRightsAdd($rights) {

		$inserts = '';

		foreach($rights as $right) {
			$inserts .= "($right->moduleId, $right->groupId), ";
		}
		$inserts = rtrim($inserts, ', ');

		TableMng::query("INSERT INTO GroupModuleRights
				(moduleId, groupId)
			VALUES $inserts;");
	}

	public static function rightsOfGroupsGet($groups) {

		$whereStr = '';

		foreach($groups as $group) {
			$id = $group->getId();
			$whereStr .= "`groupId` = '$id' OR ";
		}
		$whereStr = rtrim($whereStr, ' OR');

		$rights = TableMng::query(
			"SELECT * FROM GroupModuleRights WHERE $whereStr");
		return $rights;
	}

	public static function rightsOfUserGet($userId) {

		$res = TableMng::query("SELECT * FROM GroupModuleRights
			WHERE `userId` = '$userId'");

		return $res;
	}


	public function rightsByMultipleModulesGet($modules, $group) {

		$whereStr = '';
		$groupId = $group->getId();
		foreach($modules as $module) {
			$id = $module->getId();
			$whereStr .= "`groupId` = '$id' AND `moduleId` = '$id' OR ";
		}
		$whereStr = rtrim($whereStr, ' OR');

		$rights = TableMng::query(
			"SELECT * FROM GroupModuleRights WHERE $whereStr");

		return $rights;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dbArrayConvert($array) {

		$rights = array();

		foreach($array as $right) {
			$rights[] = new GroupModuleRight(
				$array['moduleId'],
				$array['groupId']
			);
		}

		return $rights;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	public $groupId;
	public $moduleId;
}

?>

<?php

class Rightsmanager {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($modulemanager, $groupmanager) {

		$this->_modulemanager = $modulemanager;
		$this->_groupmanager = $groupmanager;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function allowedModulesOfGroupGet($groupPath) {

		$groupIds = array();
		$rootModule = $_modulemanager->moduleRootGet();
		$moduleArray = $rootModule->moduleAsArrayGet();
		$groups = Group::groupsGetAllInPath($groupPath,
			$this->_groupmanager->groupRootGet());

		foreach($groups as $group) {
			$groupIds[] = $group->getId();
		}

		$allRights = $this->rightsOfGroupGet($group->getId());

		$allowedModuleIds = array();
		foreach($groups as $group) {
			foreach($allRights as $right) {
				if($right['groupId'] == $group->getId()) {

				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function rightsOfGroupsGet($groupIds) {

		$whereStr = '';

		foreach($groupIds as $id) {
			$whereStr .= "`groupId` = '$id' OR ";
		}
		rtrim($whereStr, ' OR');

		$rights = TableMng::query(
			"SELECT * FROM GroupModuleRights WHERE $whereStr",true);
		return $rights;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_modulemanager;
	protected $_groupmanager;
}

?>

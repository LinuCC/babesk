<?php

require_once PATH_INCLUDE . '/GroupModuleRight.php';
require_once PATH_INCLUDE . '/ModuleGenerator.php';
require_once PATH_INCLUDE . '/Group.php';

/**
 * The AccessControlLayer
 */
class Acl {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		$this->_moduleroot = ModuleGenerator::modulesLoad();
		$this->_grouproot = Group::groupsLoad();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function getModuleroot() {
		return $this->_moduleroot;
	}

	public function getGrouproot() {
		return $this->_grouproot;
	}

	/**
	 * Sets the module-Access according to the Groups of the User
	 *
	 * @param  int $userId The ID of the User
	 */
	public function accessControlInit($userId) {

		if(!$this->_accessControlInitialized) {
			$groups = $this->groupsForAccessGet($userId);
			$this->applyRightsByGroups($groups);
			$this->_accessControlInitialized = true;
		}
		else {
			throw new Exception('Access-Control already initialized');
		}
	}

	/**
	 * Sets the module-Access according to the Group
	 *
	 * @param  int $userId The ID of the User
	 */
	public function accessControlInitByGroup($group) {

		if(!$this->_accessControlInitialized) {
			$groups = Group::parentsGet($group, $this->_grouproot);
			$groups[] = $group;
			$this->applyRightsByGroups($groups);
			$this->_accessControlInitialized = true;
		}
		else {
			throw new Exception('Access-Control already initialized');
		}
	}


	public function moduleExecute($path, $dataContainer) {

		$module = $this->_moduleroot->moduleByPathGet($path);
		if(!empty($module)) {
			if($module->isAccessAllowed()) {
				$module->execute($dataContainer);
			}
			else {
				throw new AclException('Module-Access forbidden', 105);
			}
		}
		else {
			throw new AclException(
				"Module could not be loaded by path '$path'");
		}
	}

	public function moduleGet($path = 'root', $nonAccessGet = false) {

		if($origMod = $this->_moduleroot->moduleByPathGet($path)) {
			//Deep Clone of the object
			$module = unserialize(serialize($origMod));
			if(!$nonAccessGet) {
				$module->notAllowedChildsRemove();
			}
			return $module;
		}
		else {
			throw new AclException('Modulepath could not be resolved');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns all Groups needed to Set the Access-Rights of the User
	 *
	 * @param  int $userId The ID of the User to check his access
	 * @return Array[Group] The Group-Instances needed to set the rights
	 */
	protected function groupsForAccessGet($userId) {

		$groupsToCheck = array();

		if(($usergroups = Group::groupsGetAllOfUser($userId))) {

			foreach($usergroups as $group) {
				$groupsToCheck[] = $group;
				//We need to combine the rights with the Parents of the Users
				//Groups, too
				$parents = Group::parentsGet(
					$group,
					$this->_grouproot);
				foreach($parents as $parent) {
					$groupsToCheck[] = $parent;
				}
			}
		}
		else {
			throw new AclException('User is in no Group', 104);
		}

		return $groupsToCheck;
	}

	protected function applyRightsByGroups($groups) {

		$allRights = GroupModuleRight::rightsOfGroupsGet($groups);

		foreach($groups as $group) {
			foreach($allRights as $right) {
				if($right['groupId'] == $group->getId()) {
					$this->applyRight($right);
				}
			}
		}
	}

	protected function applyRight($right) {

		try {
			$this->_moduleroot = ModuleGenerator::accessChangeWithParents(
				$right['moduleId'], true, $this->_moduleroot);

		} catch (Exception $e) {
			throw new AclException(
				'Could not change the Access of a module',
				0,
				$e);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_moduleroot;
	protected $_grouproot;

	protected $_accessControlInitialized;
}

?>

<?php

require_once PATH_INCLUDE . '/GroupModuleRight.php';
require_once PATH_INCLUDE . '/NModule.php';
require_once PATH_INCLUDE . '/Group.php';

/**
 * The AccessControlLayer
 */
class Acl {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		$mods = NModule::modulesLoad();
		$this->_moduleroot = $mods[1];
		$groups = Group::groupsLoad();
		$this->_grouproot = $groups[1];
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
	 * Returns all Modules and sets Modules to enabled if Group is allowed to
	 * have access to them
	 *
	 * @param Group $group The Group which rights to check for
	 * @return Module The Root-Module containing all other Modules
	 */
	public function allowedModulesOfGroupGet($group) {

		$path = $this->_grouproot->grouppathGet($group);

		$groupIds = array();
		$groups = Group::groupsGetAllInPath($path,
			$this->_grouproot);
		$modRootBuffer = $this->_moduleroot;

		$allRights = GroupModuleRight::rightsOfGroupsGet($groups);

		$allowedModuleIds = array();

		foreach($groups as $group) {
			foreach($allRights as $right) {
				if($right['groupId'] == $group->getId()) {
					if($modRootBuffer->getId() != $right['moduleId']) {
						$mod = &$modRootBuffer->anyChildAsReferenceByIdGet(
							$right['moduleId']);
						if(!empty($mod)) {
							$mod->isEnabledChangeWithChilds(true);
						}
					}
					else {
						//Dont try to search for root-module
						$modRootBuffer->isEnabledChangeWithChilds(true);
					}
				}
			}
		}

		return $modRootBuffer;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_moduleroot;
	protected $_grouproot;
}

?>

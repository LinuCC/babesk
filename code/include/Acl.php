<?php

require_once PATH_INCLUDE . '/GroupModuleRight.php';
require_once PATH_INCLUDE . '/ModuleGenerator.php';
require_once PATH_INCLUDE . '/Group.php';

/**
 * The AccessControlLayer
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
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

	/**
	 * Returns the Moduleroot containing its childs
	 *
	 * @return ModuleGenerator The Moduleroot
	 */
	public function getModuleroot() {
		return $this->_moduleroot;
	}

	/**
	 * Returns the Grouproot containing its childs
	 *
	 * @return Group The Grouproot
	 */
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

	/**
	 * Executes a module
	 *
	 * @param  String $section       The Path to the Module. Supported are:
	 *     "Headmodule|Module" - The old way. deprecated
	 *     "Headmodule" - Another notation of the old way. deprecated
	 *     "root/path/to/module" - The new way. preferred
	 * @param  dataContainer $dataContainer The dataContainer that is given to
	 *     the executed Module
	 * @throws AclException If ModuleAccess is forbidden
	 * @throws AclException If Module could not be loaded by path
	 */
	public function moduleExecute($moduleExecutionParser, $dataContainer) {

		$moduleToExecutePath = $moduleExecutionParser->moduleExecutionGet();
		$subRequest = $moduleExecutionParser->submoduleExecutionGet();
		$dataContainer->setSubmoduleExecutionRequest($subRequest);
		$module = $this->_moduleroot->moduleByPathGet($moduleToExecutePath);

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
				"Module could not be loaded by path '$moduleToExecutePath'");
		}
	}

	/**
	 * Executes a module, even it is not allowed
	 *
	 * @param  String $section       The Path to the Module. Supported are:
	 *     "Headmodule|Module" - The old way. deprecated
	 *     "Headmodule" - Another notation of the old way. deprecated
	 *     "root/path/to/module" - The new way. preferred
	 * @param  dataContainer $dataContainer The dataContainer that is given to
	 *     the executed Module
	 * @throws AclException If ModuleAccess is forbidden
	 * @throws AclException If Module could not be loaded by path
	 */
	public function moduleNotAllowedExecute($moduleExecutionParser,
		$dataContainer) {

		$moduleToExecutePath = $moduleExecutionParser->moduleExecutionGet();
		$subRequest = $moduleExecutionParser->submoduleExecutionGet();
		$dataContainer->setSubmoduleExecutionRequest($subRequest);
		$module = $this->_moduleroot->moduleByPathGet($moduleToExecutePath);
		if(!empty($module)) {
			$module->execute($dataContainer);
		}
		else {
			throw new AclException(
				"Module could not be loaded by path '$moduleToExecutePath'");
		}
	}

	/**
	 * Returns the Module by the given path
	 *
	 * This function does not return the Modules that are set as not allowed
	 *
	 * @param  string $path The path of the Module. If not given returns the
	 *     rootmodule
	 * @return ModuleGenerator The Module by the Path
	 * @throws  AclException If Modulepath could not be resolved
	 */
	public function moduleGet($path = 'root') {

		if($origMod = $this->_moduleroot->moduleByPathGet($path)) {
			//Deep Clone of the object
			$module = unserialize(serialize($origMod));
			$module->notAllowedChildsRemove();
			if(!$module->isAccessAllowed()) {
				return false;
			}
			return $module;
		}
		else {
			throw new AclException('Modul nicht vorhanden');
		}
	}

	/**
	 * Returns all Modules, regardless of their notAllowed-Status
	 *
	 * @param  string $path The path of the Module. If not given returns the
	 *     rootmodule
	 * @return ModuleGenerator The Module by the Path
	 * @throws  AclException If Modulepath could not be resolved
	 */
	public function moduleGetWithNotAllowedModules($path = 'root') {

		if($origMod = $this->_moduleroot->moduleByPathGet($path)) {
			//Deep Clone of the object
			$module = unserialize(serialize($origMod));
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

	protected function sectionToPath($section) {

		if(preg_match('/^root\|.*$/', $section)) {
			$mods = explode('|', $section);
			return "$mods[0]/$mods[1]/$mods[2]/$mods[3]";
		}
		else if(preg_match('/^[^\/\|]+\|[^\/\|]+$/', $section)) {
			return $this->headmoduleAndModuleToPath($section);
		}
		else if(preg_match('/^[a-zA-Z]+$/', $section)) {
			return $this->headmoduleToPath($section);
		}
		else {
			throw new AclException('Could not parse section!');
		}
	}

	/**
	 * Creates the Path to the Module from the old-style section-string
	 *
	 * @param  string $section Sectionstring, foramt 'Headmodule|Module'
	 * @return string          The Path
	 * @throws AclException If Suprogrampath not sert
	 */
	protected function headmoduleAndModuleToPath($section) {

		if(!empty($this->_subProgramPath)) {
			return $this->fromHeadmoduleAndModuleCreatePath($section);
		}
		else {
			throw new AclException('Subprogrampath not set but old-style Sectionpath given');
		}
	}

	/**
	 * Creates the Path to the Module from the old-style section-string
	 *
	 * @param  string $section Sectionstring, foramt 'Headmodule|Module'
	 * @return string          The Path
	 */
	protected function fromHeadmoduleAndModuleCreatePath($section) {

		$modSubPath = explode('|', $section);
		$headmod = $modSubPath[0];
		$mod = $modSubPath[1];
		return $this->_subProgramPath . "/$headmod/$mod";
	}

	protected function headmoduleToPath($section) {

		if(!empty($this->_subProgramPath)) {
			return $this->_subProgramPath . "/$section";
		}
		else {
			throw new AclException('Subprogrampath not set but old-style Sectionpath given');
		}
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

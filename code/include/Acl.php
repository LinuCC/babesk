<?php


require_once PATH_INCLUDE . '/GroupModuleRight.php';
require_once PATH_INCLUDE . '/ModuleGenerator.php';
require_once PATH_INCLUDE . '/Group.php';
require_once PATH_INCLUDE . '/ModuleGeneratorManager.php';

/**
 * The AccessControlLayer
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class Acl {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($logger, $pdo) {

		$this->_grouproot = Group::groupsLoad();

		$this->_logger = clone($logger);
		$this->_logger->categorySet('Acl');
		$this->_moduleGenManager = new ModuleGeneratorManager($logger, $pdo);
		$this->_moduleGenManager->modulesLoad();

		$this->_maxExecutionTries = 20;
		$this->_executionTries = 0;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the Grouproot containing its childs
	 *
	 * @return Group The Grouproot
	 */
	public function getGrouproot() {
		return $this->_grouproot;
	}

	public function moduleGeneratorManagerGet() {
		return $this->_moduleGenManager;
	}

	public function accessControlInitAllowAll() {

		if(!$this->_accessControlInitialized) {
			$this->_moduleGenManager->moduleEnableStatusAllowAll();
		}
		else {
			throw new Exception('Access-Control already initialized');
		}
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
	 * @param  Object $command The ModuleExecutionCommand that knows
	 *                               which Module to execute
	 * @param  Object $dataContainer The DataContainer that is given to
	 *                               the executed Module
	 * @throws AclException If ModuleAccess is forbidden
	 * @throws AclException If Module could not be loaded by path
	 */
	public function moduleExecute($command, $dataContainer) {

		// Check for infinite loop
		if($this->_executionTries > $this->_maxExecutionTries) {
			$this->_logger->log('Too many module-Executions!', 'error',
				NULL, json_encode(array('modulepath' => $command->pathGet()))
			);
			throw new AclException('Too many ModuleExecutions!', 105);
		}

		if($this->moduleExecutionIsAllowed($command)) {

			$this->_executionTries += 1;
			$dataContainer->setExecutionCommand($command);
			try {
				$this->moduleExecuteHelper(
					$command, $dataContainer);
			} catch (AclException $e) {
				$this->_logger->log(__METHOD__ . ': ' .
					'None of the Modules in Path found!','error', NULL,
					json_encode(array('path' => $command->pathGet()))
				);
				throw $e;
			}
		}
		else {
			throw new AclAccessDeniedException(
				'Module-Access forbidden', 105
			);
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

		$origMod = $this->_moduleGenManager->moduleByPathGet($path);
		if($origMod) {
		// if($origMod = $this->_moduleroot->moduleByPathGet($path)) {
			//Deep Clone of the object
			$module = unserialize(serialize($origMod));
			$module->notAllowedChildsRemove();
			if(!$module->isEnabled() || !$module->userHasAccess()) {
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

		if($origMod = $this->_moduleGenManager->moduleByPathGet($path)) {
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
			$this->_moduleGenManager->moduleEnabledStatusChange($right['moduleId'], true);
			// $this->_moduleroot = ModuleGenerator::isEnabledChangeWithParents(
				// $right['moduleId'], true, $this->_moduleroot);

		} catch (Exception $e) {
			throw new AclException(
				'Could not change the Access of a module',
				0,
				$e);
		}
	}

	protected function moduleExecutionIsAllowed($command) {

		$moduleToExecutePath = $command->pathGet();
		$module = $this->_moduleGenManager->moduleByPathGet($moduleToExecutePath);

		if(!empty($module)) {
			return ($module->isEnabled() && $module->userHasAccess());
		}
		else {
			$this->_logger->log("Module could not be loaded by path!",
				'error', NULL,
				json_encode(array('path' => $moduleToExecutePath)));
			return false;
		}
	}

	/**
	 * Helps in executing the Modules.
	 *
	 * If one Module is not found, this function tries to go up the hierarchy
	 * and tries to execute the Module above, and so on
	 *
	 * @param  string $moduleToExecutePath The Path of the Module to execute
	 * @param  Object $dataContainer       The DataContainer for the Module
	 */
	protected function moduleExecuteHelper($moduleCommand, $dataContainer) {

		$command = clone($moduleCommand);

		if($this->moduleExecutionIsAllowed($command)) {
			if($this->_moduleGenManager->moduleExecute(
				$command, $dataContainer)
			) {
				exit(0); // Everything fine, quit the program
			}
			else {
				// $this->_logger->log('A module could not be executed!' .
				// 	'Trying to execute higher Module.', 'Notice', NULL,
				// 	json_encode(array('modulepath' => $command->pathGet())));
				// Module could not be executed, try executing a higher Module
				if($command->lastModuleElementRemove()) {
					$this->moduleExecuteHelper($command, $dataContainer);
				}
				else {
					throw new AclException(
						'None of the Modules in Path found!'
					);
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_grouproot;

	protected $_logger;

	protected $_moduleGenManager;

	protected $_accessControlInitialized;

	protected $_executionTries;

	protected $_maxExecutionTries;
}

?>

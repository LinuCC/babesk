<?php

class ModuleGeneratorManager {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($logger) {

		$this->_logger = $logger;
		$this->_logger->categorySet('ModuleGeneratorManager');
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function moduleRootGet() {

		return $this->_moduleRoot;
	}

	public function modulesLoad() {

		try {
			$data = this->modulesFetchAll();
			$moduleArray = this->nestedSetToModules($data);
			//find root
			foreach($moduleArray as $mod) {
				if($mod->_name == 'root') {
					$this->_moduleRoot = $mod;
				}
			}
			throw new Exception('Root-Module not found!');

		} catch (Exception $e) {
			throw new ModulesException("Could not fetch Modules!", 1, $e);
		}
	}

	public function modulePathGet($module) {

		$path = $this->modulePathGetRecHelper(
			$module->_id,
			$rootmodule,
			'root');
		return rtrim($path, '/');
	}

	public function moduleEnabledStatusChange($moduleId, $accessAllowed) {

		//rootmodule gets changed as reference
		this->moduleEnabledStatusChangeHelper(
			$moduleId, $this->_moduleRoot, $accessAllowed);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function modulesFetchAll($pdo) {

		try {
			$stmt = $pdo->query(
				'SELECT node.ID AS ID, node.lft AS lft,
					node.rgt AS rgt, node.name AS name, node.enabled AS enabled,
					node.executablePath AS executablePath,
					node.displayInMenu AS displayInMenu,
						(COUNT(parent.ID) - 1) AS level
				FROM Modules AS node, Modules AS parent
					WHERE node.lft BETWEEN parent.lft AND parent.rgt
				GROUP BY node.ID
				ORDER BY node.lft;');

			return $stmt->fetchAll();

		} catch (PDOException $e) {
			$msg = 'Error occured while fetching the modules.';
			$this->_logger->log($msg, 'Critical', NULL,
				json_encode(array('error' => $e->getMessage()))
			);
			throw new Exception($msg, 0, $e);
		}
	}

	/**
	 * Converts the nested set Array to modules
	 *
	 * Code shamelessly stolen from http://www.tutorials.de/php/312024-verschachteltes-array-aus-nested-sets.html and changed
	 *
	 * @param  Array $array An Array of Modules, each represented by
	 * 	another Array. These need to contain the following keys:
	 * ['lft', 'rgt', 'ID', 'name', 'enabled']
	 * @return Module the Module with all Childs
	 */
	protected function nestedSetToModules($array) {

		$struct = array();
		$level = 0;
		$helper =& $struct;

		foreach($array as $item) {

			if($level < $item['level']) {
				$keys = array_keys($helper);
				$buffer = $helper[$keys[count($keys)-1]];
				$helper =& $buffer->_childs;
			}

			else if($level > $item['level']) {
				$helper =& $struct;
				$i=0;
				while($i < $item['level']) {
					$keys = array_keys($helper);
					$buffer = $helper[$keys[count($keys)-1]];
					$helper =& $buffer->_childs;
					$i++;
				}
			}

			///@todo Add a constructor for ModuleGenerator that is less silly
			$helper[$item['ID']] = new ModuleGenerator(
				$item['ID'],
				$item['name'],
				(boolean) $item['enabled'],
				$item['rgt'],
				$item['lft'],
				$item['executablePath'],
				$item['displayInMenu']);
			$level = $item['level'];
		}

		return $struct;

	}

	protected function modulePathGetRecHelper($moduleId, $rootModule,
		$begPath) {

		if(count($rootModule->_childs)) {

			foreach($rootModule->_childs as $mod) {
				$path = $begPath . '/' . $mod->_name;

				if($mod->_id == $moduleId) {
					return $path;
				}
				else {
					$retPath = $this->modulePathGetRecHelper(
						$moduleId,
						$mod,
						$path);
					if($retPath) {
						return $retPath;
					}
				}
			}
		}
		else {
			return false;
		}
	}

	protected function moduleEnabledStatusChangeHelper($searchedId,
		&$module, $access) {

		if($module->_id == $searchedId) {
			$module->_userHasAccess = $access;
			return true;
		}
		else {
			if(count($module->_childs)) {
				foreach($module->_childs as &$child) {
					$ret = $this->isEnabledChangeWithParentsHelper($searchedId,
						$child, $access);
					if($ret) {
						$module->_userHasAccess = $access;
						return true;
					}
				}
			}
		}
		return false;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_moduleRoot;

	/**
	 * Allows to log stuff
	 * @var Logger
	 */
	protected $_logger;
}

?>
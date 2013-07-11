<?php

/**
 * Represents a module in the Database#table Modules
 *
 * Since Modules are structured hierarchically, an ModuleGenerator also
 * containsall its childs.
 * When executed, this Class creates and executes the proper Module.
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class ModuleGenerator {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($ID, $name, $accessAllowed, $rgt, $lft,
		$executablePath, $displayInMenu) {

		$this->_id = $ID;
		$this->_name = $name;
		$this->_accessAllowed = $accessAllowed;
		$this->_rgt = $rgt;
		$this->_lft = $lft;
		$this->_executablePath = $executablePath;
		$this->_displayInMenu = $displayInMenu;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function getName() {
		return $this->_name;
	}

	public function getId() {
		return $this->_id;
	}

	public function getChilds() {
		return $this->_childs;
	}

	public function isDisplayInMenuAllowed() {
		return (boolean) $this->_displayInMenu;
	}

	/**
	 * Loads and Executes the Module
	 *
	 * @todo  Parameter are long outdated; We need to change the parameters,
	 *     but that means changing every existing modulefile, too!
	 */
	public function execute($dataContainer) {

		if(file_exists(PATH_CODE . "/$this->_executablePath")) {
			require_once PATH_CODE . "/$this->_executablePath";

			$executablePathPieces = explode('/', $this->_executablePath);
			array_shift($executablePathPieces); //remove Subprogram
			array_pop($executablePathPieces); //Remove class-File
			$subPathPart = implode('/', $executablePathPieces);
			$subPath = "/$subPathPart/";

			if(class_exists($classname = $this->_name)) {
				$module = new $classname($classname, $classname, $subPath);
				$module->execute($dataContainer);
			}
			else {
				throw new Exception("Could not load Module-Class $classname");
			}
		}
		else {
			throw new Exception("Could not find Module-File in Path " .
				"'$this->_executablePath'", 104);
		}
	}

	/**
	 * Returns a boolean describing if the module can be used by the user
	 *
	 * @return Boolean true if enabled, else false
	 */
	public function isAccessAllowed() {
		return $this->_accessAllowed;
	}

	/**
	 * Sets a boolean describing if the module can be used by the user
	 *
	 * @param Boolean $accessAllowed true if enabled, else false
	 */
	public function setAccessAllowed($accessAllowed) {
		$this->_accessAllowed = $accessAllowed;
		return $this;
	}

	/**
	 * Loads the Modules from the Database
	 *
	 * Fetches all Modules from the Database and converts this Array into the
	 * actual Modules
	 */
	public static function modulesLoad() {

		try {
			$data = self::modulesFetchAll();
			$moduleArray = self::nestedSetToModules($data);
			//find root
			foreach($moduleArray as $mod) {
				if($mod->_name == 'root') {
					return $mod;
				}
			}
			throw new Exception('Root-Module not found!');

		} catch (Exception $e) {
			throw new ModuleException("Could not fetch Modules!", 1, $e);
		}
	}

	/**
	 * Fetches a ModuleChild beginning by this Instance's Path
	 * @param  String $path The (relative) Path of the module, for
	 *     example root/administrator/Kuwasys/User
	 * @param  boolean $checkThis If true, function checks if path starts with
	 *     this module-instance
	 * @return Module The Module if found, false if not
	 */
	public function moduleByPathGet($path, $checkThis = true) {

		$tree = explode('/', $path);
		$treeIterator = $this;

		if($checkThis) {
			if(!(array_shift($tree) == $this->_name)) {
				return false;
			}
		}

		//check for each Module in the given path if it exists
		foreach($tree as $wantedNodeName) {
			foreach($treeIterator->_childs as $node) {
				//If name of Module found
				if($node->_name == $wantedNodeName) {
					$treeIterator = $node;
					continue 2;
				}
			}
			return false; //Module with name not found
		}
		$module = $treeIterator;

		return $module;
	}

	public static function modulePathGet($module, $rootmodule) {

		$path = self::modulePathGetRecHelper(
			$module->_id,
			$rootmodule,
			'root');
		return rtrim($path, '/');
	}

	public static function modulePathGetRecHelper($moduleId,
		$rootModule, $begPath) {

		if(count($rootModule->_childs)) {
			foreach($rootModule->_childs as $mod) {
				$path = $begPath . '/' . $mod->_name;
				if($mod->_id == $moduleId) {
					return $path;
				}
				else {
					$retPath = self::modulePathGetRecHelper(
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

	public function moduleAsArrayGet() {

		$data = array(
			'name' => $this->_name,
			'id' => $this->_id,
			'enabled' => $this->_accessAllowed,
			'displayInMenu' => $this->_displayInMenu);
		$childs = $this->childsAsArrayGet($this->_childs);
		$data['childs'] = $childs;
		return $data;
	}

	/**
	 * Changes the Access of the Module with $moduleId and its Parents
	 *
	 * @param  int $moduleId The ID of the rootmodule or any of the Childs
	 *     within it
	 * @param  boolean $accessAllowed If Module-Access is allowed or not
	 * @param  ModuleGenerator $rootmodule The Parents gets changed and the
	 *     moduleId gets searched from this module on
	 * @return ModuleGenerator the changed rootmodule
	 * @throws  Exception If This Module-Instance and no Child of it has this
	 *     module-ID
	 */
	public static function accessChangeWithParents(
		$moduleId,
		$accessAllowed,
		$rootmodule) {

		//rootmodule gets changed as reference
		self::accessChangeWithParentsHelper($moduleId,
											$rootmodule,
											$accessAllowed);

		return $rootmodule;
	}

	public static function moduleAddQueryCreate($modulename, $parentmodule) {

		$id = $parentmodule->getId();

		return "SELECT @modRight := rgt FROM Modules WHERE ID = '$id';
			UPDATE Modules SET rgt = rgt + 2 WHERE rgt >= @modRight;
			UPDATE Modules SET lft = lft + 2 WHERE lft >= @modRight;
			INSERT INTO Modules(name, lft, rgt) VALUES('$modulename',
				@modRight, @modRight + 1);";
	}

	public static function moduleDeleteQueryCreate($module) {

		if($module->_lft > 0 && $module->_rgt > 0) {
			return "DELETE FROM Modules WHERE lft
						BETWEEN $module->_lft AND $module->_rgt;
				UPDATE Modules
					SET lft=lft-ROUND(( $module->_rgt - $module->_lft +1))
					WHERE lft > $module->_rgt ;
				UPDATE Modules
					SET rgt=rgt-ROUND(( $module->_rgt - $module->_lft +1))
					WHERE rgt > $module->_rgt;";
		}
		else {
			return false;
		}
	}

	public function anyChildByIdGet($id) {

		if(!empty($this->_childs)) {
			foreach($this->_childs as $child) {
				if($child->_id == $id) {
					return $child;
				}
				else {
					if(($ret = $child->anyChildByIdGet($id))) {
						return $ret;
					}
				}
			}
		}
		return false;
	}

	public function notAllowedChildsRemove() {

		if(count($this->_childs)) {
			foreach($this->_childs as $key => $child) {
				if($child->_accessAllowed) {
					$child->notAllowedChildsRemove();
				}
				else {
					unset($this->_childs[$key]);
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function childsAsArrayGet() {

		$childArray = array();
		if(!empty($this->_childs)) {
			foreach($this->_childs as $child) {
				$childArray[] = array(
					'id' => $child->_id,
					'name' => $child->_name,
					'enabled' => $child->_accessAllowed,
					'rgt' => $child->_rgt,
					'lft' => $child->_lft,
					'executablePath' => $child->_executablePath,
					'displayInMenu' => $child->_displayInMenu,
					'childs' => $child->childsAsArrayGet());
			}
		}
		return $childArray;
	}

	/**
	 * Fetches all Modules from the Datbase and returns them
	 * @return Array The Modules as an Array
	 */
	protected static function modulesFetchAll() {

		$data = TableMng::query("SELECT node.ID AS ID, node.lft AS lft,
			node.rgt AS rgt, node.name AS name, node.enabled AS enabled,
			node.executablePath AS executablePath,
			node.displayInMenu AS displayInMenu,
				(COUNT(parent.ID) - 1) AS level
			FROM Modules AS node, Modules AS parent
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			GROUP BY node.ID
			ORDER BY node.lft;", true);

		return $data;
	}

	/**
	 * Converts the nested set Array to modules
	 *
	 * Code shamelessly stolen from http://www.tutorials.de/php/312024-verschachteltes-array-aus-nested-sets.html and changed
	 *
	 * @param  Array $nestedSetArr An Array of Modules, each represented by
	 * another Array. These need to contain the following keys:
	 * ['lft', 'rgt', 'ID', 'name', 'enabled']
	 * @return Module the Module with all Childs
	 */
	protected static function nestedSetToModules($nestedSetArr) {

		$struct = array();
		$level = 0;
		$helper =& $struct;

		foreach($nestedSetArr as $item) {
			if($level < $item['level']) {
				$keys = array_keys($helper);
				$buffer = $helper[$keys[count($keys)-1]];
				$helper =& $buffer->_childs;
			} else if($level > $item['level']) {
				$helper =& $struct;
				$i=0;
				while($i < $item['level']) {
					$keys = array_keys($helper);
					$buffer = $helper[$keys[count($keys)-1]];
					$helper =& $buffer->_childs;
					$i++;
				}
			}
			$helper[$item['ID']] = new ModuleGenerator(
				$item['ID'],
				$item['name'],
				$item['enabled'],
				$item['rgt'],
				$item['lft'],
				$item['executablePath'],
				$item['displayInMenu']);
			$level = $item['level'];
		}

		return $struct;
	}

	public function &anyChildByIdGetAsReference($id) {

		if(!empty($this->_childs)) {
			foreach($this->_childs as &$child) {
				if($child->_id == $id) {
					return $child;
				}
				else {
					if(($ret = $child->anyChildByIdGet($id))) {
						return $ret;
					}
				}
			}
		}
		return NULL;
	}

	/**
	 * Adds a new module to the DatabaseTable
	 *
	 * Requirement is that the Parent has no childs
	 *
	 * @param String $name The name of the new Module
	 * @param String $parentName The name of the parent-Module
	 * @todo  if multiple parents with this name exist, problem!
	 */
	protected static function moduleAddToNodeWithoutChildren($name,
		$parentName) {

		TableMng::getDb()->autocommit(false);

		TableMng::query("SELECT @myLeft := lft FROM Modules
			WHERE name = '$parentName';
			UPDATE Modules SET rgt = rgt + 2 WHERE rgt > @myLeft;
			UPDATE Modules SET lft = lft + 2 WHERE lft > @myLeft;
			INSERT INTO Modules(name, lft, rgt) VALUES('$name',
							@myLeft + 1, @myLeft + 2);", false, true);

		TableMng::getDb()->autocommit(true);
	}

	/**
	 * Adds a new module to the DatabaseTable
	 *
	 * Requirement: the Parent has childs
	 *
	 * @param String $name The name of the new Module
	 * @param String $parentName The name of the parent-Module
	 * @todo  if multiple parents with this name exist, problem!
	 */
	protected static function moduleAddToNodeWithChildren($name,
		$parentName) {

		TableMng::getDb()->autocommit(false);

		TableMng::query("SELECT @myRight := rgt FROM Modules
			WHERE name = '$parentName';
			UPDATE Modules SET rgt = rgt + 2 WHERE rgt >= @myRight;
			UPDATE Modules SET lft = lft + 2 WHERE lft > @myRight;
			INSERT INTO Modules(name, lft, rgt) VALUES('$name',
							@myRight, @myRight + 1);
			", false, true);

		TableMng::getDb()->autocommit(true);
	}

	protected static function accessChangeWithParentsHelper($searchedId,
		&$module, $access) {

		if($module->_id == $searchedId) {
			$module->_accessAllowed = $access;
			return true;
		}
		else {
			if(count($module->_childs)) {
				foreach($module->_childs as &$child) {
					$ret = self::accessChangeWithParentsHelper($searchedId,
						$child, $access);
					if($ret) {
						$module->_accessAllowed = $access;
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

	/**
	 * The ID that is representing the Element in the Database-Table
	 * @var numeric
	 */
	protected $_id;

	/**
	 * The name of the Module
	 * @var String
	 */
	protected $_name;

	/**
	 * If the module is Enabled in general => if it can be accessed
	 * @var boolean
	 */
	protected $_accessAllowed;

	/**
	 * The Childs of this module
	 * @var Array
	 */
	protected $_childs;

	protected $_lft;

	protected $_rgt;

	protected $_smartyTemplatePath;

	protected $_executablePath;

	protected $_displayInMenu;
}

?>

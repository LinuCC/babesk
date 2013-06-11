<?php

class NModule {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($ID, $name, $isEnabled) {

		$this->_id = $ID;
		$this->_name = $name;
		$this->_isEnabled = $isEnabled;
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

	public static function modulesLoad() {

		try {
			$data = self::modulesFetchAll();
			self::$_rootModule = self::nestedSetToModules($data);

		} catch (Exception $e) {
			throw new ModuleException("Could not fetch Modules!", 1, $e);
		}
	}

	public static function moduleAdd($name, $parentName) {

		$name = mysql_real_escape_string($name);
		$parentName = mysql_real_escape_string($parentName);

		try {
			$parent = TableMng::query("SELECT lft, rgt FROM Modules
				WHERE `name` = '$parentName'", true);

			if($parent[0]['rgt'] == $parent[0]['lft'] + 1) {
				//No Children existing
				self::moduleAddToNodeWithoutChildren($name, $parentName);
			}
			else {
				self::moduleAddToNodeWithoutChildren($name, $parentName);
			}

		} catch (Exception $e) {
			die('Could not add Module' . $e->getMessage());
		}
	}

	/**
	 * Fetches the Module by its path
	 * @param  String $path The Path of the Module
	 * @return Module false on error else the Module
	 */
	public static function moduleByPathGet($path) {

		$tree = explode('/', $path);
		$treeIterator = self::$_rootModule;

		//start Iterating from root
		foreach($treeIterator as $iter) {
			if($iter->_name == 'root') {
				$treeIterator = $iter;
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

	public static function moduleChildsByPathGet($path) {

		if($mod = self::moduleByPathGet($path)) {
			return $mod->_childs;
		}
		else {
			return false;
		}

	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches all Modules from the Datbase and returns them
	 * @return Array The Modules as an Array
	 */
	protected static function modulesFetchAll() {

		$data = TableMng::query("SELECT node.ID AS ID, node.lft AS lft,
			node.rgt AS rgt, node.name AS name, node.enabled AS enabled,
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
			$helper[$item['ID']] = new NModule($item['ID'], $item['name'], $item['enabled']);
			$level = $item['level'];
		}

		return $struct;
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
			UPDATE Modules SET rgt = rgt + 2 WHERE rgt > @myRight;
			UPDATE Modules SET lft = lft + 2 WHERE lft > @myRight;
			INSERT INTO Modules(name, lft, rgt) VALUES('$name',
							@myRight + 1, @myRight + 2);
			", false, true);

		TableMng::getDb()->autocommit(true);
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
	protected $_isEnabled;

	/**
	 * The Childs of this module
	 * @var Array
	 */
	protected $_childs;

	protected $_smartyTemplatePath;

	protected $_executablePath;

	protected static $_rootModule;
}

?>

<?php

/**
 * Represents a Group
 *
 * contains all its childs
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class Group {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($id, $name, $lft = 0, $rgt = 0) {

		$this->_id = $id;
		$this->_name = $name;
		$this->_lft = $lft;
		$this->_rgt = $rgt;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function getId() {
		return $this->_id;
	}

	public static function groupsLoad() {

		$grouproot = false;

		try {
			$data = self::groupsFetchAll();
			$groupArray = self::nestedSetToGroups($data);
			//get Root
			foreach($groupArray as $group) {
				if($group->_name == 'root') {
					return $group;
				}
			}

			throw new Exception('Grouproot not found');

		} catch (Exception $e) {
			throw new ModuleException("Could not fetch Groups!", 1, $e);
		}
	}

	/**
	 * Just a test-function. Echoes the Tree somewhat readable
	 */
	public static function treeDump($mod, $chars = '') {

		echo "$chars $mod->_name<br />";
		if(!empty($mod->_childs)) {
			$chars .= '____';
			foreach($mod->_childs as $childMod) {
				self::treeDump($childMod, $chars);
			}
		}
	}

	public static function groupAddQueryCreate($groupname, $parentGroup) {

		$id = $parentGroup->getId();

		return "SELECT @groupRight := rgt FROM Groups WHERE ID = '$id';
			UPDATE Groups SET rgt = rgt + 2 WHERE rgt >= @groupRight;
			UPDATE Groups SET lft = lft + 2 WHERE lft >= @groupRight;
			INSERT INTO Groups(name, lft, rgt) VALUES('$groupname',
				@groupRight, @groupRight + 1);";
	}

	public static function groupChangeQueryCreate($group, $newGroupname) {

		$id = $group->getId();
		return "UPDATE Groups SET `name` = '$newGroupname'
			WHERE `ID` = '$id';";
	}

	public static function groupDeleteQueryCreate($group) {

		if($group->_lft > 0 && $group->_rgt > 0) {
			return "DELETE FROM Groups WHERE lft
						BETWEEN $group->_lft AND $group->_rgt;
				UPDATE Groups
					SET lft=lft-ROUND(( $group->_rgt - $group->_lft +1))
					WHERE lft > $group->_rgt ;
				UPDATE Groups
					SET rgt=rgt-ROUND(( $group->_rgt - $group->_lft +1))
					WHERE rgt > $group->_rgt;";
		}
		else {
			return false;
		}
	}

	public static function groupAdd($name, $parentName) {

		TableMng::sqlSave($name);
		TableMng::sqlSave($parentName);

		try {
			$parent = TableMng::query("SELECT lft, rgt FROM Groups
				WHERE `name` = '$parentName'", true);

			if($parent[0]['rgt'] == $parent[0]['lft'] + 1) {
				//No Children existing
				self::groupAddToNodeWithoutChildren($name, $parentName);
			}
			else {
				self::groupAddToNodeWithChildren($name, $parentName);
			}

		} catch (Exception $e) {
			die('Could not add Group ' . $e->getMessage());
		}
	}

	public function groupAsArrayGet() {

		$data = array('name' => $this->_name, 'id' => $this->_id);
		$childs = $this->childsAsArrayGet();
		$data['childs'] = $childs;
		return $data;
	}

	/**
	 * returns all Parents of the given Group, beginning from grouproot
	 * @param  Group $group The Group which Parents to search and return
	 * @param  Group $grouproot The Root of the Groups from where the search
	 *     for the parents starts
	 * @return Array[Groups] the Parent-Groups of the Group
	 */
	public static function parentsGet($group, $grouproot) {

		$path = $grouproot->grouppathGet($group);
		//remove last Element, we only want the Parents
		$splitPath = explode('/', $path);
		array_pop($splitPath);
		if(count($splitPath)) {
			$path = implode('/', $splitPath);
			$groups = Group::groupsGetAllInPath($path, $grouproot);
			return $groups;
		}
		else {
			return array();
		}
	}

	/**
	 * Fetches a GroupChild beginning by this Instance's Path
	 * @param  String  $path      The (relative) Path of the Group, for
	 *     example administrator/Kuwasys/User
	 * @param  boolean $checkThis If true, function checks if path starts with
	 *     this Group-instance
	 * @return Group The Group if found, false if Path could not be resolved
	 */
	public function groupByPathGet($path, $checkThis = true) {

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
		$group = $treeIterator;

		return $group;
	}

	public function grouppathGet($group) {

		$path = self::grouppathGetRecHelper(
			$group->_id,
			$this,
			$this->_name);
		return rtrim($path, '/');
	}

	public static function grouppathGetRecHelper(
		$groupId, $rootgroup, $begPath) {

		if(count($rootgroup->_childs)) {
			foreach($rootgroup->_childs as $group) {
				$path = $begPath . '/' . $group->_name;
				if($group->_id == $groupId) {
					return $path;
				}
				else {
					$retPath = self::grouppathGetRecHelper(
						$groupId,
						$group,
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

	/**
	 * Fetches all Groups of a User and returns them
	 *
	 * @param  int $userId The ID of the User
	 * @return Array[Group] An Array of Group-Objects or false if no Groups
	 *     found
	 */
	public static function groupsGetAllOfUser($userId) {

		$groupArray = TableMng::query(
			"SELECT g.* FROM Groups g
			JOIN UserInGroups uig ON uig.groupId = g.ID
			WHERE uig.userId = '$userId';", true);

		if(count($groupArray)) {
			$groups = self::arrayToObjects($groupArray);
		}
		else {
			return false;
		}

		return $groups;
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

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns all groups that are in the given Path
	 *
	 * @param  String $path      The Group-Path, for example
	 *     'root/administrator/Kuwasys/User'
	 * @param  Group $rootgroup The root-Element of the Group-hierarchie
	 * @return Array An Array containing the Groups. the first element of the
	 *     Array is the first element given in the Path. If the Group could
	 *     not be found, the Element is false
	 */
	protected static function groupsGetAllInPath($path, $rootgroup) {

		$groupNames = explode('/', $path);
		$groups = array();

		//we need to get the whole path of each group because we need to check
		//for the path, not the name
		foreach($groupNames as $name) {
			$grouppath = preg_replace("/(.*$name).*/", '$1', $path);
			$groups[] = $rootgroup->groupByPathGet($grouppath);
		}

		return $groups;
	}

	/**
	 * Converts an Array representing Groups to actual Group-Objects
	 *
	 * @param  Array $groupArray The Array containing the Group-Data
	 * @return Array[Group] The Group-Objects or false on Error
	 */
	protected static function arrayToObjects($groupsArray) {

		$groups = array();

		foreach($groupsArray as $groupArray) {
			if(!empty($groupArray['ID'])) {
				$groups[] = new Group(
					$groupArray['ID'],
					$groupArray['name'],
					$groupArray['lft'],
					$groupArray['rgt']);
			}
			else {
				return false;
			}
		}

		return $groups;
	}

	protected function childsAsArrayGet() {

		$childArray = array();
		if(!empty($this->_childs)) {
			foreach($this->_childs as $child) {
				$childArray[] = array(
					'id' => $child->_id,
					'name' => $child->_name,
					'childs' => $child->childsAsArrayGet());
			}
		}
		return $childArray;
	}

	/**
	 * Fetches all Groups from the Datbase and returns them
	 * @return Array The Groups as an Array
	 */
	protected static function groupsFetchAll() {

		$data = TableMng::query("SELECT node.ID AS ID, node.lft AS lft,
			node.rgt AS rgt, node.name AS name,
				(COUNT(parent.ID) - 1) AS level
			FROM Groups AS node, Groups AS parent
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			GROUP BY node.ID
			ORDER BY node.lft;", true);

		return $data;
	}

	/**
	 * Converts the nested set Array to groups
	 *
	 * Code shamelessly stolen from http://www.tutorials.de/php/312024-verschachteltes-array-aus-nested-sets.html and changed
	 *
	 * @param  Array $nestedSetArr An Array of Groups, each represented by
	 * another Array. These need to contain the following keys:
	 * ['lft', 'rgt', 'ID', 'name']
	 * @return Group the root-Group with all Childs
	 */
	protected static function nestedSetToGroups($nestedSetArr) {

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
			$helper[$item['ID']] = new Group(
				$item['ID'],
				$item['name'],
				$item['lft'],
				$item['rgt']);
			$level = $item['level'];
		}

		return $struct;
	}

	/**
	 * Adds a new group to the DatabaseTable
	 *
	 * Requirement is that the Parent has no childs
	 *
	 * @param String $name The name of the new Group
	 * @param String $parentName The name of the parent-Group
	 * @todo  if multiple parents with this name exist, problem!
	 */
	protected static function groupAddToNodeWithoutChildren($name,
		$parentName) {

		TableMng::getDb()->autocommit(false);

		TableMng::query("SELECT @myLeft := lft FROM Groups
			WHERE name = '$parentName';
			UPDATE Groups SET rgt = rgt + 2 WHERE rgt > @myLeft;
			UPDATE Groups SET lft = lft + 2 WHERE lft > @myLeft;
			INSERT INTO Groups(name, lft, rgt) VALUES('$name',
							@myLeft + 1, @myLeft + 2);", false, true);

		TableMng::getDb()->autocommit(true);
	}

	/**
	 * Adds a new group to the DatabaseTable
	 *
	 * Requirement: the Parent has childs
	 *
	 * @param String $name The name of the new Group
	 * @param String $parentName The name of the parent-Group
	 * @todo  if multiple parents with this name exist, problem!
	 */
	protected static function groupAddToNodeWithChildren($name,
		$parentName) {

		TableMng::getDb()->autocommit(false);

		TableMng::query("SELECT @myRight := rgt FROM Groups
			WHERE name = '$parentName';
			UPDATE Groups SET rgt = rgt + 2 WHERE rgt >= @myRight;
			UPDATE Groups SET lft = lft + 2 WHERE lft >= @myRight;
			INSERT INTO Groups(name, lft, rgt) VALUES('$name',
							@myRight, @myRight + 1);
			", false, true);

		TableMng::getDb()->autocommit(true);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_id;

	protected $_name;

	protected $_childs;

	protected $_lft;

	protected $_rgt;
}

?>

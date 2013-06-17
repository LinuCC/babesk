<?php

class HierarchicalData {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($id, $name) {

		$this->_id = $id;
		$this->_name = $name;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Just a test-function. Echoes the Tree somewhat readable
	 */
	public function treeDump() {

		$this->treeDumpIterator($this);
	}

	public function childsByPathGet($path) {

		return $this->_childs;
	}

	public function jsonDataGet() {

		$data = array('name' => $this->_name, 'id' => $this->_id);
		$childs = $this->childsAsArrayGet($this->_childs);
		$data['childs'] = $childs;
		return $data;
	}

	/**
	 * Fetches the Element by its path starting from this
	 *
	 * @param  String $path The Path of the Group
	 * @return Group false on error else the Group
	 */
	public function elementByPathGet($path) {

		$tree = explode('/', $path);
		$treeIterator = $this;

		// first Iteration is different since we dont already have an object
		// with children
		// foreach($treeIterator as $iter) {
		// 	if($iter->_name == $tree[0]) {
		// 		$treeIterator = $iter;
		// 		array_shift($tree);
		// 	}
		// }
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
		$element = $treeIterator;

		return $element;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function childsAsArrayGet($childs) {

		$childArray = array();
		if(!empty($childs)) {
			foreach($childs as $child) {
				$childArray[] = array(
					'id' => $child->_id,
					'name' => $child->_name,
					'childs' => $this->childsAsArrayGet($child->_childs));
			}
		}
		return $childArray;
	}

	protected function treeDumpIterator($treeRoot, $chars = '') {

		echo "$chars $treeRoot->_name<br />";
		if(!empty($treeRoot->_childs)) {
			$chars .= '____';
			foreach($treeRoot->_childs as $childMod) {
				$this->treeDump($childMod, $chars);
			}
		}
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
			$helper[$item['ID']] = new Group($item['ID'], $item['name']);
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

	protected static $_rootGroup;
}

?>

<?php

namespace Babesk\System;

require_once PATH_INCLUDE . '/Group.php';

class GroupsManager {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($pdo, $logger) {

		$this->_pdo = $pdo;
		$this->_logger = clone($logger);
		$this->_logger->categorySet('Babesk\System\GroupsManager');
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches from db and returns the group with the given Id
	 * @param  int    $nodeId The Id of the group
	 * @return Group          The group and its children
	 */
	protected function subGroupLoadById($nodeId) {

		try {
			$res = $this->_pdo->query(
				"SELECT node.ID AS ID, node.name AS name, node.lft AS lft,
					node.rgt AS rgt
				FROM SystemGroups AS node,
					SystemGroups AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND parent.id = $nodeId
				GROUP BY node.ID
				ORDER BY node.lft ASC"
			);
			$nestedSet = $res->fetchAll(\PDO::FETCH_ASSOC);
			$data = \ArrayFunctions::nestedSetToArray($nestedSet);
			return $this->arrayToGroupObj($data[0]);

		} catch (\PDOException $e) {
			$this->_logger->log('error loading the groups',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			return false;
		}
		return $data;
	}

	/**
	 * Fetches and returns the id of the sub-group-node in the Groups-table
	 * A sub-group is a group at layer one, meaning its parent is the
	 * root-node.
	 * @return int    the id of the node or false if not found
	 */
	protected function idOfSubGroupNodeGet($groupName) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT node.ID
				FROM SystemGroups AS node,
						SystemGroups AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.name = ?
				GROUP BY node.ID
				HAVING (COUNT(parent.name) - 1) = 1'
			);
			$stmt->execute(array($groupName));
			return $stmt->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the id of the usernode',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Returns the groups as a flattened array
	 * @param  Group  $group The root-Group
	 * @return array         The groups in a flat array
	 */
	protected function groupsFlattenedGet($group) {

		$groups = array();
		$childs = $group->childsGet();
		if(!empty($childs)) {
			foreach($childs as $child) {
				$cGroups = $this->groupsFlattenedGet($child);
				$groups = array_merge($groups, $cGroups);
			}
		}
		$group->childsRemove();
		array_unshift($groups, $group);
		return $groups;
	}

	/**
	 * Converts an hierarchically structured array to a Group with childs
	 * @param  array  $arElement The array-element containing the root-group
	 * @return Group             The root-Group
	 */
	private function arrayToGroupObj($arElement) {

		$group = new \Group(
			$arElement['item']['ID'],
			$arElement['item']['name'],
			$arElement['item']['lft'],
			$arElement['item']['rgt']
		);
		if(!empty($arElement['children'])) {
			foreach($arElement['children'] as $child) {
				$groupChild = $this->arrayToGroupObj($child);
				$group->childAdd($groupChild);
			}
		}
		return $group;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Object that allows querying the database
	 * @var PDO
	 */
	protected $_pdo;

	/**
	 * Allows logging things
	 * @var Logger
	 */
	protected $_logger;

}

?>
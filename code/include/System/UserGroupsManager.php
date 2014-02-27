<?php

namespace Babesk\System;

require_once PATH_INCLUDE . '/System/GroupsManager.php';
require_once PATH_INCLUDE . '/ArrayFunctions.php';


class UserGroupsManager extends GroupsManager {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($pdo, $logger) {

		parent::__construct($pdo, $logger);
		$this->_logger->categorySet('Babesk\System\UserGroupsManager');
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function userGroupGet() {
		return $this->_rootGroup;
	}

	/**
	 * Loads the usergroups from the server
	 * @return bool  true if successful, false on error
	 */
	public function groupsLoad() {

		$uNodeId = $this->idOfSubGroupNodeGet('Users');
		if($uNodeId) {
			$gData = $this->subGroupLoadById($uNodeId);
			if($gData) {
				$this->_rootGroup = $gData;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
		return true;
	}

	public function flatGroupsGet() {
		return $this->groupsFlattenedGet($this->_rootGroup);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_rootGroup;

}

?>
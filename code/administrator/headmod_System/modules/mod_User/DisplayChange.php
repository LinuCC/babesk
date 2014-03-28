<?php

require_once 'User.php';
require_once PATH_INCLUDE . '/System/UserGroupsManager.php';

/**
 *
 */
class DisplayChange extends User
{
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->dataFetch();
		$this->displayTpl('change.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->moduleTemplatePathSet();
	}

	protected function dataFetch() {

		$userId = $_GET['ID'];
		// var_dump($userId);
		$this->_smarty->assign('user', $this->userGet($userId));
		$this->_smarty->assign('grades', $this->gradesGetAllFlattened());
		$this->_smarty->assign('usergroups', $this->usergroupsGet());
		$this->_smarty->assign(
			'isKuwasysActivated', $this->isKuwasysActivated()
		);
		$this->_smarty->assign(
			'isBabeskActivated', $this->isBabeskActivated()
		);
		$this->_smarty->assign('schoolyears',
			$this->schoolyearsGetAllFlattened());
		$this->_smarty->assign(
			'userInGroups', $this->groupsOfUserGet($userId)
		);
		$this->_smarty->assign(
			'gradesAndSchoolyearsOfUser',
			$this->gradeAndSchoolyearDataOfUserGet($userId)
		);
		$this->_smarty->assign(
			'cardnumber', $this->cardnumberGetByUserId($userId)
		);
		try {
			$this->_smarty->assign('pricegroups', $this->pricegroupsFetch());
		} catch (PDOException $e) {
			//Pricegroups is Babesk-specific, dont crash when table not exists
			$this->_smarty->assign('pricegroups', array());
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>
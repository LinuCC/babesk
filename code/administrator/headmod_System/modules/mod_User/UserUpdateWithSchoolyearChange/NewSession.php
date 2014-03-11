<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';
require_once PATH_INCLUDE . '/System/UserGroupsManager.php';

class NewSession extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_POST['schoolyearSelect'])) {
			$this->schoolyearSelectDisplay();
		}
		else if(isset($_POST['schoolyearSelected'])) {
			$this->schoolyearSelected();
		}
		else if(isset($_POST['csvHelp'])) {
			$this->csvHelpDisplay();
		}
		else {
			$this->introductionDisplay();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Displays an introductionary text to the User
	 */
	private function introductionDisplay() {
		$this->displayTpl('introduction.tpl');
	}

	private function schoolyearSelectDisplay() {

		$switchTypes = array(0 => _g('Full year'), 1 => _g('Half year'));
		$this->_smarty->assign('schoolyears', $this->schoolyearsGet());
		$this->_smarty->assign('switchTypes', $switchTypes);
		$this->_smarty->assign('usergroups', $this->groupsGet());
		$this->displayTpl('schoolyears.tpl');
	}

	/**
	 * Fetches and returns the Schoolyears
	 * @return array format: <ID> => <label>
	 */
	private function schoolyearsGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT ID, label FROM schoolYear WHERE ID <> @activeSchoolyear'
			);
			return $res->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not fetch the schoolyears!', 'Notice',
				NULL, json_encode(array('msg' => $e->getMessage()))
			);
			$this->_interface->dieError(
				_g('Could not fetch the schoolyears!')
			);
		}
	}

	/**
	 * Fetches the user-groups and returns them
	 * @return array  The groups or void array on error/not found
	 */
	private function groupsGet() {

		try {
			$manager = new \Babesk\System\UserGroupsManager(
				$this->_pdo, $this->_logger
			);
			if($manager->groupsLoad()) {
				$userGroup = $manager->userGroupGet();
				return $manager->flatGroupsGet();
			}
			else {
				throw new \Exception('General error occured');
			}

		} catch (\Exception $e) {
			$this->_logger->log('Error loading the groups',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->showError(_g('Could not load the user-' .
				'groups. Adding new users to groups is disabled!'));
			return array();
		}
	}

	/**
	 * User finished selecting the schoolyear, move on
	 */
	private function schoolyearSelected() {

		$this->schoolyearSelectedCheckInput();

		if($this->schoolyearAlreadyUsedCheck($_POST['schoolyear'])) {
			$this->_interface->showError(_g('This schoolyear was already ' .
				'used! Please select one that was not used yet.'));
			$this->schoolyearSelectDisplay();
			die();
		}

		$_SESSION['UserUpdateWithSchoolyearChange']['switchType'] = $_POST['switchType'];

		$this->schoolyearIdUpload($_POST['schoolyear']);
		$groupId = (!empty($_POST['usergroup'])) ? $_POST['usergroup'] : 0;
		$this->groupToAddNewUsersToSet($groupId);

		//Now execute the CsvImport-Module
		$mod = new \ModuleExecutionCommand('root/administrator/System/' .
			'User/UserUpdateWithSchoolyearChange/CsvImport');
		$this->_dataContainer->getAcl()->moduleExecute(
			$mod, $this->_dataContainer
		);
	}

	/**
	 * Sets the id of the group every newly added user will be assigned to
	 * Dies displaying a message on error
	 * @param  int    $groupId The id of the group
	 */
	private function groupToAddNewUsersToSet($groupId) {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE SystemGlobalSettings SET value = ?
				WHERE name = "UserUpdateWithSchoolyearChangeGroupOfNewUser"'
			);
			$stmt->execute(array($groupId));

		} catch (\Exception $e) {
			$this->_logger->log('Error changing the groupOfNewUser-value',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not initialize the process!')
			);
		}
	}

	/**
	 * Checks if the schoolyear has already been used
	 * @return bool   true if it has already been used
	 */
	private function schoolyearAlreadyUsedCheck($schoolyearId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT COUNT(*)
				FROM usersInGradesAndSchoolyears WHERE schoolyearId = ?');
			$stmt->execute(array($schoolyearId));
			return (bool)(int)$stmt->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log(
				'Could not check if the schoolyear has already been used',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not check if the ' .
				'schoolyear has already been used'));
		}
	}

	/**
	 * Sets the schoolyearId for later use
	 * Dies displaying a message on error
	 * @param  int    $id the schoolyearId
	 */
	private function schoolyearIdUpload($id) {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE SystemGlobalSettings SET value = ?
				WHERE name = "userUpdateWithSchoolyearChangeNewSchoolyearId"'
			);
			$stmt->execute(array($id));

		} catch (\PDOException $e) {
			$this->_logger->log('could not set the schoolyear-Id',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Checks the input of the form for selecting the Schoolyear
	 *
	 * Dies displaying a message on errornous input
	 */
	private function schoolyearSelectedCheckInput() {

		$existingSchoolyears = $this->schoolyearsGet();
		$error = '';
		if(isset($_POST['schoolyear']) && isset($_POST['switchType'])) {
			if($_POST['switchType'] < 0 || $_POST['switchType'] > 1) {
				$error = _g('Wrong type of switch-type of schoolyear!');
			}
			else if(!array_key_exists(
				$_POST['schoolyear'], $existingSchoolyears
				)) {
				$error = _g('The Schoolyear with this Id does not exist!');
			}
		}
		else {
			$error = _g('The formular was not filled out completely. Please correct your mistakes and try again.');
		}
		if(!empty($error)) {
			$this->_smarty->assign('backlink', 'index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession');
			$this->_interface->dieError($error);
		}
	}

	private function csvHelpDisplay() {
		$this->_interface->backlink('administrator|System|User|' .
			'UserUpdateWithSchoolyearChange|NewSession');
		$this->displayTpl('csvhelp.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
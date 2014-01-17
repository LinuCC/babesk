<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';

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
		$this->displayTpl('schoolyears.tpl');
	}

	/**
	 * Fetches and returns the Schoolyears
	 * @return array format: <ID> => <label>
	 */
	private function schoolyearsGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT ID, label FROM schoolYear WHERE 1'
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
	 * User finished selecting the schoolyear, move on
	 */
	private function schoolyearSelected() {

		$this->schoolyearSelectedCheckInput();

		$_SESSION['UserUpdateWithSchoolyearChange']['schoolyearId'] = $_POST['schoolyear'];
		$_SESSION['UserUpdateWithSchoolyearChange']['switchType'] = $_POST['switchType'];

		//Now execute the CsvImport-Module
		$mod = new \ModuleExecutionCommand('root/administrator/System/' .
			'User/UserUpdateWithSchoolyearChange/CsvImport');
		$this->_dataContainer->getAcl()->moduleExecute(
			$mod, $this->_dataContainer
		);
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

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
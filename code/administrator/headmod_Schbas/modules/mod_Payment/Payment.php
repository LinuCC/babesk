<?php

require_once PATH_INCLUDE . '/Module.php';

class Payment extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminPaymentInterface.php';

		$this->PaymentInterface = new AdminPaymentInterface($this->relPath);
		
			if(!isset($_GET['action'])){
				$PaymentInterface->ShowSelectionFunctionality();
			}else{
				switch ($_GET['action']){
					case 1:
						$this->showUsers();break;
					case 2: 
						$this->showUsersFilter();break;
				}
			}
	}
	private function showUsers () {
	
		$users = TableMng::query('SELECT * FROM users', true);
		$users = $this->addGradeLabelToUsers($users);
		$users = KuwasysFilterAndSort::elementsSort ($users);
		$users = KuwasysFilterAndSort::elementsFilter ($users);
		$this->PaymentInterface->showAllUsers($users);
	}
	
	private function showUsersFilter() {
		$schoolyearAll = TableMng::query('SELECT * FROM schoolYear', true);
		$schoolyearDesired = $this->getDesiredSchoolyear($schoolyearAll);
		$gradesOfDesiredSchoolyear = $this->getGradesOfSchoolyearDesired($schoolyearDesired);
		$gradeDesired = $this->getDesiredGrade($gradesOfDesiredSchoolyear);
		$users = $this->getAllUsersOfDesiredGrade($gradeDesired);
		try {
			$preUsers = $users;
			$users = KuwasysFilterAndSort::elementsFilter ($users);
			$users = KuwasysFilterAndSort::elementsSort ($users);
		} catch (Exception $e) {
			$users = $preUsers;
			$this->_interface->showMsg ('Konnte die Benutzer nicht nach den angegebenen Kriterien filtern. Hinweis: da hier einige
				Filteroptionen überflüssig sind, funktionieren sie auch nicht.');
		}
		$this->PaymentInterface->showAllUsers($schoolyearAll, $schoolyearDesired, $gradesOfDesiredSchoolyear,
				$gradeDesired, $users);
	}
	
	private function addGradeLabelToUsers ($users) {
	
		$jointsUsersInGrade = $this->getAllJointsUsersInGrade();
		$grades = $this->getAllGrades();
		if (isset($users) && count ($users) && isset($jointsUsersInGrade) && count ($jointsUsersInGrade)) {
			foreach ($users as & $user) {
				foreach ($jointsUsersInGrade as $joint) {
					if ($joint['UserID'] == $user['ID']) {
						foreach ($grades as $grade) {
							if ($grade['ID'] == $joint['GradeID']) {
								$user['gradeLabel'] = $grade['gradeValue'] . '-' . $grade['label'];
							}
						}
					}
				}
			}
		}
		return $users;
	}
	
	
	private function getAllJointsUsersInGrade () {
	
		try {
			$joints = TableMng::query('SELECT * FROM jointUsersInGrade', true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningNoJointsUsersInGrade'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInGrade'));
		}
		if(isset($joints)) {
			return $joints;
		}
	}
	
	private function getAllGrades () {
	
		try {
			$grades = TableMng::query('SELECT * FROM grade', true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrades'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrades'));
		}
		return $grades;
	}
	
	
	private function getDesiredSchoolyear ($schoolyearAll) {
	
		if(isset($_GET['schoolyearIdDesired'])) {
			foreach ($schoolyearAll as $schoolyear) {
				//pick the schoolyear the Admin wants
				if($_GET['schoolyearIdDesired'] == $schoolyear ['ID']) {
					return $schoolyear;
				}
			}
		}
		else {
			foreach ($schoolyearAll as $schoolyear) {
				//pick the schoolyear thats active at the moment
				if($schoolyear ['active']) {
					return $schoolyear;
				}
			}
		}
		$this->_interface->dieError($this->_languageManager->getText('errorSelectDesiredSchoolyear'));
	}
	
	private function getGradesOfSchoolyearDesired ($schoolyearDesired) {
		$jointsGradeInSchoolyear = TableMng::query(sprintf("SELECT * FROM jointGradeInSchoolYear WHERE 'GradeID' = %s ",$schoolyearDesired['ID']), true);
		foreach ($jointsGradeInSchoolyear as $joint) {
			$this->_databaseAccessManager->gradeIdAddToFetchArray($joint ['GradeID']);
		}
		$grades = $this->_databaseAccessManager->gradeGetAllByFetchArray();
		return $grades;
	}
	
}

?>
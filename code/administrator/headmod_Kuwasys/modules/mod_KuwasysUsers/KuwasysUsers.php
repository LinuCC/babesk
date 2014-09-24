<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/Kuwasys.php';

/**
 * Allows the User to change Kuwasys-specific Userdata
 */
class KuwasysUsers extends Kuwasys {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 *
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		$execReq = $dataContainer->getExecutionCommand()->pathGet();
		if($this->submoduleCountGet($execReq)) {
			$this->submoduleExecuteAsMethod($execReq);
		}
		else {
			$this->mainMenu();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 * @param  DataContainer $dataContainer Contains data needed by
	 *                                      KuwasysUsers
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->_interface = $dataContainer->getInterface();
		$this->initSmartyVariables();
	}

	/**
	 * Displays a MainMenu to the User
	 *
	 * Dies displaying the Main Menu
	 */
	protected function mainMenu() {

		$grades = $this->gradesGetAll();
		$this->_smarty->assign('grades', ArrayFunctions::arrayColumn(
			$grades, 'gradename', 'ID'));
		$this->displayTpl('mainmenu.tpl');
	}

	/**
	 * Fetches all Grades in the Database
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array The Grades
	 */
	protected function gradesGetAll() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT *, CONCAT(gradelevel, "-", label) AS gradename
				FROM SystemGrades');

			return $stmt->fetchAll();

		} catch (PDOException $e) {
			$msg = 'Could not fetch all Grades.';
			$this->_logger->log(__METHOD__ . ": $msg", 'Moderate', NULL,
				json_encode(array('error' => $e->getMessage())));
			throw new PDOException($msg, 0, $e);
		}
	}

	/*********************************************************************
	 * Allows the User to Print Participation-Confirmations
	 */
	protected function submodulePrintParticipationConfirmationExecute() {

		/**
		 * @todo  this is old and outdated stuff, rework it
		 */
		require_once 'KuwasysUsersCreateParticipationConfirmation.php';

		$gradeId = $_GET['gradeId'];
		$query = "SELECT u.ID as userId
			FROM SystemUsers u
				JOIN SystemUsersInGradesAndSchoolyears uigsy
					ON uigsy.UserID = u.ID
			WHERE uigsy.schoolyearId = @activeSchoolyear AND
				uigsy.gradeId = {$gradeId}
			";
		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Es wurden keine Schüler gefunden, für die man die Dokumente hätte erstellen können');
		} catch (Exception $e) {
			$this->_interface->dieError ('konnte die Daten der Schüler nicht abrufen' . $e->getMessage ());
		}
		$userIds = array ();
		foreach ($data as $row) {
			$userIds [] = $row ['userId'];
		}
		KuwasysUsersCreateParticipationConfirmationPdf::init ($this->_interface);
		KuwasysUsersCreateParticipationConfirmationPdf::execute ($gradeId, $userIds);
	}

	/**==========================================**
	 * Allows the User to Assign Users to Classes *
	 **==========================================**/
	protected function submoduleAssignUsersToClassesExecute() {

		if($this->execPathHasSubmoduleLevel(
			2, $this->_submoduleExecutionpath)) {

			$this->submoduleExecuteAsMethod($this->_submoduleExecutionpath, 2,
				'assignUsersToClasses');
		}
		else {
			$this->assignUsersToClassesMainmenu();
		}
	}

	/**
	 * Checks if the Table for the Temporary assignUsersToClasses data exists
	 *
	 * Dies displaying a Message when the Query could not be executed
	 *
	 * @return boolean true if it exists, else false
	 */
	protected function assignUsersToClassesTableExists() {

		try {
			$stmt = $this->_pdo->query(
				'SHOW TABLES LIKE "KuwasysTemporaryRequestsAssign";');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not check if the UsersToClasses-Table exists!') .
				$e->getMessage());
		}

		return (boolean) $stmt->fetch();
	}

	/**
	 * Displays the Mainmenu of the AssignUsersToClasses-Submodule
	 */
	protected function assignUsersToClassesMainmenu() {

		$this->_smarty->assign('tableExists',
			$this->assignUsersToClassesTableExists());
		$this->displayTpl('AssignUsersToClasses/mainmenu.tpl');
	}

	/**
	 * Changes the Status of a Temporary Request Entry
	 *
	 * Dies with Json on Error
	 */
	protected function temporaryRequestChangeStatus(
		$userId, $classId, $statusId) {

		try {
			$stmt = $this->_pdo->prepare('UPDATE KuwasysTemporaryRequestsAssign
				SET statusId = :statusId
				WHERE classId = :classId AND userId = :userId');

			$stmt->execute(array(
				'statusId' => $statusId,
				'classId' => $classId,
				'userId' => $userId
			));

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not change the Status of the User'))));
		}
	}

	/**
	 * Fetches the Status with the given Name
	 *
	 * Returns false if Status not found
	 *
	 * @param  string $statusName The Name of the Status to fetch
	 * @return array              The Fetched data of the Status
	 * @throws PDOException If Status could not be fetched
	 */
	protected function statusIdGetByName($statusName) {

		$stmt = $this->_pdo->prepare('SELECT ID FROM KuwasysUsersInClassStatuses
			WHERE name = :name');

		$stmt->execute(array('name' => $statusName));

		return $stmt->fetchColumn();
	}

	/**
	 * Sets the Header of the Templates to allow the User a better overview
	 */
	protected function assignUsersToClassesSetHeader() {

		$siteHeaderPath = $this->_smartyModuleTemplatesPath .
			'AssignUsersToClasses/header.tpl';
		$this->_smarty->assign('inh_path', $siteHeaderPath);
	}


	protected function assignUsersToClassesApplyChangesExecute() {

		$this->_pdo->beginTransaction();
		$this->usersInClassJointDeleteByNewAssignments();
		$this->newAssignmentsAddToJoints();
		$this->assignUsersToClassesParticipationConfirmation();
		// $this->assignUsersToClassesTableDrop();
		$this->_pdo->commit();
		$this->assignUsersToClassesParticipationConfirmation();
	}

	/**
	 * Lets the Admin Download Participation Confirmations for the Assignments
	 */
	protected function assignUsersToClassesParticipationConfirmation() {

		require_once 'AssignUsersInClassParticipationConfirmation.php';

		AssignUsersInClassParticipationConfirmation::init($this->_interface);
		AssignUsersInClassParticipationConfirmation::execute(NULL);
	}

	/**=========================================**
	 * Allows the Admin to add a User to a Class *
	 **=========================================**/
	protected function submoduleAddUserToClassExecute() {

		$userId = $this->userIdGetByUsername($_POST['username']);

		try {
			$this->userAssignToClass(
				$userId, $_POST['classId'], $_POST['statusId']);

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not newly assign the User to the Class!'))));
		}

		die(json_encode(array('value' => 'success',
			'message' => _g('The User was successfully assigned to the Class.'))));
	}

	/**
	 * Returns a Userid found by the Username
	 *
	 * Dies displaying a Nessage on Not found or Error
	 *
	 * @return int    The ID of found
	 */
	protected function userIdGetByUsername($username) {

		try {
			$stmt = $this->_pdo->prepare('SELECT ID FROM SystemUsers
				WHERE username = :username');

			$stmt->execute(array('username' => $username));

			if($id = $stmt->fetchColumn()) {
				return $id;
			}
			else {
				die(json_encode(array('value' => 'error',
					'message' => _g('No User found by the Username!'))));
			}

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g(
					'Error while fetching the User from the Database!'))));
		}
	}

	/**
	 * Assigns the User to a Class
	 *
	 * @param  int    $userId   The ID of the User to assign
	 * @param  int    $classId  The ID of the Class to assign the User to
	 * @param  int    $statusId The ID of the Status
	 * @throws PDOException If Things didnt work out
	 */
	private function userAssignToClass($userId, $classId, $statusId) {

		$stmt = $this->_pdo->prepare('INSERT INTO KuwasysUsersInClasses
			(UserID, ClassID, statusId) VALUES
			(:userId, :classId, :statusId);');

		$stmt->execute(array(
			'userId' => $userId,
			'classId' => $classId,
			'statusId' => $statusId,
		));
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Handy functions to display things to the User
	 * @var AdminInterface
	 */
	protected $_interface;

}

?>

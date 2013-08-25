<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * Allows the User to change Kuwasys-specific Userdata
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class KuwasysUsers extends Module {

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

		if($execReq = $dataContainer->getSubmoduleExecutionRequest()) {
			$this->submoduleExecute($execReq);
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

		$this->displayTpl('mainmenu.tpl');
	}

	/********************************************************************
	 * Allows the User to Print Participation-Confirmations
	 */
	protected function submodulePrintParticipationConfirmationExecute() {

		$this->_interface->dieError('Modul wird momentan überarbeitet...');
	}

	/********************************************************************
	 * Allows the User to Assign Users to Classes
	 */
	protected function submoduleAssignUsersToClassesExecute() {

		if($this->execPathHasSubmoduleLevel(
			2, $this->_submoduleExecutionpath)) {

			$this->submoduleExecute($this->_submoduleExecutionpath, 2,
				'assignUsersToClasses');
		}
		else {
			$this->assignUsersToClassesMainmenu();
		}
	}

	/**
	 * Creates the new Assignments from the data and deletes the old, if exists
	 */
	protected function assignUsersToClassesResetExecute() {

		if($this->assignUsersToClassesTableExists()) {
			$this->assignUsersToClassesTableDrop();
		}
		$this->assignUsersToClassesTableCreate();
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
				'SHOW TABLES LIKE TemporaryUsersToClassesAssign');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not check if the UsersToClasses-Table exists!'));
		}

		return (boolean) $stmt->fetch();
	}

	/**
	 * Drops the UsersToClasses-Table
	 *
	 * Dies displaying a Message when the Query could not be executed
	 */
	protected function assignUsersToClassesTableDrop() {

		try {
			$this->_pdo->exec('DROP TABLE TemporaryUsersToClassesAssign');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not delete the UsersToClasses-Table!'));
		}
	}

	/**
	 * Drops the UsersToClasses-Table
	 *
	 * Dies displaying a Message when the Query could not be executed
	 */
	protected function assignUsersToClassesTableCreate() {

		try {
			$this->_pdo->exec('CREATE TABLE IF NOT EXISTS
				`TemporaryUsersToClassesAssign` (
					`userId` int(11) unsigned NOT NULL,
					`classId` int(11) unsigned NOT NULL,
					`statusId` int(11) unsigned NOT NULL,
					`origUserId` int(11) unsigned NOT NULL,
					`origClassId` int(11) unsigned NOT NULL,
					`origStatusId` int(11) unsigned NOT NULL,
					PRIMARY KEY(`userId`, `classId`)
				);');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not create the UsersToClasses-Table!'));
		}
	}

	/**
	 * Calculates which User goes into which Class and fills the Table
	 *
	 * Dies displaying a Message on Error.
	 * If more Users want to go into one Class than maxRegistrations allows,
	 * it prefers primary registrations before secondary registrations.
	 * Also, it randomizes the selections when multiple users have the same
	 * registration-status at the Class.
	 */
	protected function assignUsersToClassesTableFill() {

		$data = $this->assignUsersToClassesFetchRelevantChoices();
		var_dump($data);
	}

	/**
	 * Fetches the Entries to be processed and filled into the Temp Table
	 * @return Array The connections between the users and the Classes of the
	 *               active Schoolyear
	 */
	protected function assignUsersToClassesFetchRelevantChoices() {

		$statusIds = $this->assignUsersToClassesStatusIdsGet();

		try {
			$stmt = $this->_pdo->query(
				"SELECT FROM jointUsersInClass uic
				JOIN class c ON uic.ClassID = c.ID
				WHERE c.schoolyearId = @activeSchoolyear
					AND (
						uic.statusId = $statusIds[request1] OR
						uic.statusId = $statusIds[request2]
					)
			");

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Error fetching the Choices of the Users!'));
		}
	}

	/**
	 * Fetches the StatusIds of specific UsersInClass-Statuses needed
	 *
	 * Dies displaying a Message if Query could not be executed
	 *
	 * @return Array The Statuses as an Array
	 */
	protected function assignUsersToClassesStatusIdsGet() {

		// These Statuses are needed to Process the Class-Choices of the Users
		$statusNames = array('active', 'waiting', 'request1', 'request2');
		$statuses = array();

		try {
			$stmt = $this->_pdo->prepare('SELECT ID FROM usersInClassStatus
				WHERE name = :name');

			foreach($statusNames as $name) {
				$statuses[$name] = $this->assignUsersToClassesStatusIdGet(
					$stmt, $name);
			}

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the StatusIds!'));
		}

		return $statuses;
	}

	/**
	 * Fetches the Status-ID of a Status by a name
	 *
	 * Dies displaying a Message when the Status could not be found
	 *
	 * @param  PDOStatement $stmt The prepare-Statement for fetching the data
	 * @param  string $name The name of the Status
	 * @return int          The ID of the Status
	 */
	protected function assignUsersToClassesStatusIdGet($stmt, $name) {

		$stmt->execute(array('name' => $name));

		if($id = $stmt->fetch()) {
			return $id;
		}
		else {
			$this->_interface->dieError(_g('The User-in-Class-Status %1$s is missing! Cannot process the data without it!'));
		}
	}

	/**
	 * Displays the Mainmenu of the AssignUsersToClasses-Submodule
	 */
	protected function assignUsersToClassesMainmenu() {

		$this->_smarty->assign('tableExists',
			$this->assignUsersToClassesTableExists());
		$this->displayTpl('AssignUsersToClasses/mainmenu.tpl');
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

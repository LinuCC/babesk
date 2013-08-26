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

	/**------------------------------------------------------------------
	 * Creates the new Assignments from the data and deletes the old, if exists
	 */
	protected function assignUsersToClassesResetExecute() {

		if($this->assignUsersToClassesTableExists()) {
			$this->assignUsersToClassesTableDrop();
		}
		$this->assignUsersToClassesTableCreate();
		$this->assignUsersToClassesTableFill();
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
				'SHOW TABLES LIKE "TemporaryUsersToClassesAssign";');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not check if the UsersToClasses-Table exists!') .
				$e->getMessage());
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

		$requests = RequestsOfClass::requestsGet($this->_pdo);

		if(count($requests)) {

		}
		else {
			$this->_interface->dieError(_g('No Requests of Users found!'));
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





class RequestsOfClass {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($classId) {

		$this->_id = $classId;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the ID of the Class
	 *
	 * @return int   The ID of the Class
	 */
	public function idGet() {
		return $this->_id;
	}

	/**
	 * Returns the Primary Requests of this Class
	 *
	 * @return array  The Primary Requests
	 */
	public function primaryRequestsGet() {
		return $this->_primaryRequests;
	}

	/**
	 * Returns the Secondary Requests of this Class
	 *
	 * @return array  The Secondary Requests
	 */
	public function secondaryRequestsGet() {
		return $this->_secondaryRequests;
	}

	/**
	 * Returns all Requests made for Classes in this Year
	 *
	 * @param  PDO    $pdo The PDO-Object necessary to fetch the Data
	 * @return array       An Array of RequestsOfClass
	 */
	public static function requestsGet($pdo) {

		self::$_statusIds = self::assignUsersToClassesStatusIdsGet($pdo);
		$requests = self::requestsFromDatabaseGet($pdo);
		$classRequests = self::requestDataToClasses($requests);
		$classRequests[2]->usersToClassAssign($classRequests[2]->_primaryRequests);
		return ;
	}

	/**
	 * Assigns the Users depending on the Requests to the Classes
	 */
	public function usersToClassAssign() {

		if(!isset(self::$_statusIds)) {
			self::$_statusIds = self::assignUsersToClassesStatusIdsGet($pdo);
		}

		$count = count($this->_primaryRequests);
		var_dump($this->_remainingRegistrations);
		$this->_remainingRegistrations = 4;
		if($count > $this->_remainingRegistrations) {
			$this->requestsOverflowRandomAssignment($this->_primaryRequests);
		}
		else {
			/********************************************************
			 * NEEEEEEEEEEEEEEEEEEEEDS MOAR HERE
			 *********************************************************/
		}

		foreach($this->_primaryRequests as $request) {

		}
	}

	/**
	 * Assigns Users to the Class at Random
	 *
	 * @param  array  $requests The Requests of one Status
	 * @return array            The randomized Requests
	 */
	public function requestsOverflowRandomAssignment($requests) {

		if(shuffle($requests)) {

			$active = array_slice(
				$requests, 0, $this->_remainingRegistrations, true);
			$active = $this->statusIdOfRequestsChangeTo(
				self::$_statusIds['active'], $active);

			//leftover users go to the waiting-list
			$waiting = array_slice(
				$requests, $this->_remainingRegistrations, NULL, true);
			$waiting = $this->statusIdOfRequestsChangeTo(
				self::$_statusIds['waiting'], $waiting);

			$this->_changedRequests = array_merge($active, $waiting);
			$this->_remainingRegistrations -= count($randRequests);
		}
		else {
			$this->_interface->dieError(_g('Could not Shuffle the Requests!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the Entries to be processed and filled into the Temp Table
	 *
	 * Dies displaying a Message if Query could not be executed successfully
	 */
	protected static function requestsFromDatabaseGet($pdo) {

		try {
			$statusIds = self::$_statusIds;

			$stmt = $pdo->query(
				"SELECT uic.*, c.maxRegistration AS maxRegistration
				FROM jointUsersInClass uic
				JOIN class c ON uic.ClassID = c.ID
				WHERE c.schoolyearId = @activeSchoolyear
					AND (
						uic.statusId = {$statusIds['request1']} OR
						uic.statusId = {$statusIds['request2']}
					)
			");

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Error fetching the Choices of the Users!') . $e->getMessage());
		}
	}

	/**
	 * Fetches the StatusIds of specific UsersInClass-Statuses needed
	 *
	 * Dies displaying a Message if Query could not be executed
	 *
	 * @return Array The Statuses as an Array
	 */
	protected static function assignUsersToClassesStatusIdsGet($pdo) {

		// These Statuses are needed to Process the Class-Choices of the Users
		$statusNames = array('active', 'waiting', 'request1', 'request2');
		$statuses = array();

		try {
			$stmt = $pdo->prepare('SELECT ID FROM usersInClassStatus
				WHERE name = :name');

			foreach($statusNames as $name) {
				$statuses[$name] = self::assignUsersToClassesStatusIdGet(
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
	protected static function assignUsersToClassesStatusIdGet($stmt, $name) {

		$stmt->execute(array('name' => $name));

		if($id = $stmt->fetchColumn()) {
			return $id;
		}
		else {
			$this->_interface->dieError(_g('The User-in-Class-Status %1$s is missing! Cannot process the data without it!'));
		}
	}

	/**
	 * Rearranges the Data given to Objects
	 *
	 * @param  array  $data The data fetched from the Database
	 * @return array        The rearranged data
	 */
	protected static function requestDataToClasses($data) {

		$classesRequests = array();

		foreach($data as $request) {

			if($class = self::classGetById(
				$request['ClassID'], $classesRequests)) {
				$class->requestAdd($request);
			}
			else {
				$class = new RequestsOfClass($request['ClassID']);
				$class->requestAdd($request);
				$classesRequests[] = $class;
			}
		}

		return $classesRequests;
	}

	/**
	 * Checks if a Class by the ID $id exists and returns it
	 *
	 * @param  int    $id      The ID of the Class to search
	 * @param  array  $classes The Classes to search in
	 * @return RequestsOfClass The Class if found, else false
	 */
	protected static function classGetById($id, $classes) {

		foreach($classes as $class) {
			if($class->_id == $id) {
				return $class;
			}
		}

		return false;
	}

	/**
	 * Adds a Request to the ClassRequests
	 *
	 * @param  array  $request  A request fetched from the Db
	 * @param  array  $statuses Contains data of the Request-statuses
	 * @throws Exception If the Request has no processable StatusId
	 */
	protected function requestAdd($request) {

		$this->maxRegistrationsSetByRequestIfNotSet($request);

		if($request['statusId'] == self::$_statusIds['request1']) {
			$this->_primaryRequests[] = $request;
		}
		else if($request['statusId'] == self::$_statusIds['request2']) {
			$this->_secondaryRequests[] = $request;
		}
		else {
			throw new Exception('Request has no Status that could be used!');
		}
	}

	/**
	 * Sets the Maximum Amount of Registrations for this class if not set yet
	 *
	 * @param  array  $request The data fetched from Db containing the
	 * maxRegistration-Value
	 */
	protected function maxRegistrationsSetByRequestIfNotSet($request) {

		if(!isset($this->_maxRegistrations)) {
			$this->_maxRegistrations = $request['maxRegistration'];
			$this->_remainingRegistrations = $request['maxRegistration'];
		}
	}

	/**
	 * Changes the StatusIds of all the given Requests
	 *
	 * @param  int    $statusId The Status-ID to change to
	 * @param  array  $requests The Requests where the Statuses should be
	 * changed
	 * @return array            The Requests-Array with the StatusId changed
	 */
	protected function statusIdOfRequestsChangeTo($statusId, $requests) {

		foreach($requests as $request) {
			$request['statusId'] = $statusId;
		}

		return $requests;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The ID of the Class
	 * @var int
	 */
	protected $_id;

	/**
	 * The primary Requests of Users for this Class
	 * @var Array
	 */
	protected $_primaryRequests;

	/**
	 * The secondary Requests of Users for this Class
	 * @var Array
	 */
	protected $_secondaryRequests;

	/**
	 * The allowed maximum of Registrations for this Class
	 * @var Int
	 */
	protected $_maxRegistrations;

	/**
	 * To count how many Registrations are allowed to be added
	 * @var int
	 */
	protected $_remainingRegistrations;

	/**
	 * Contains which StatusIds belongs to which Statusname
	 * @var array
	 */
	protected static $_statusIds;

	/**
	 * The Requests changed by the UsersToClass-Assignment
	 * @var array
	 */
	protected $_changedRequests;
}

?>

<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/Kuwasys.php';

/**
 * Allows the User to change Kuwasys-specific Userdata
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
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

		if($execReq = $dataContainer->getModuleExecutionRequest()) {
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

	/**====================================================**
	 * Allows the User to Print Participation-Confirmations *
	 **====================================================**/
	protected function submodulePrintParticipationConfirmationExecute() {

		/**
		 * @todo  this is old and outdated stuff, rework it
		 */
		require_once 'KuwasysUsersCreateParticipationConfirmation.php';

		$gradeId = $_GET['gradeId'];
		$query = "SELECT u.ID as userId
			FROM users u
				JOIN usersInGradesAndSchoolyears uigsy ON uigsy.UserID = u.ID
			WHERE uigsy.schoolyearId = @activeSchoolyear AND
				uigsy.gradeId = {$gradeId}
			";
		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Es wurden keine Schüler gefunden, für die man die Dokumente hätte drucken können');
		} catch (Exception $e) {
			$this->_interface->dieError ('konnte die Daten der Schüler nicht abrufen' . $e->getMessage ());
		}
		$userIds = array ();
		foreach ($data as $row) {
			$userIds [] = $row ['userId'];
		}
		KuwasysUsersCreateParticipationConfirmationPdf::init ($this->_interface);
		KuwasysUsersCreateParticipationConfirmationPdf::execute ($userIds);

		$this->_interface->dieError('Modul wird momentan überarbeitet...');
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
				FROM Grades');

			return $stmt->fetchAll();

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the Grades') . $e->getMessage());
		}
	}

	/**==========================================**
	 * Allows the User to Assign Users to Classes *
	 **==========================================**/
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

	/*********************************************************************
	 * Creates the new Assignments from the data and deletes the old, if exists
	 */
	protected function assignUsersToClassesResetExecute() {

		if($this->assignUsersToClassesTableExists()) {
			$this->assignUsersToClassesTableDrop();
		}
		$this->assignUsersToClassesTableCreate();
		$this->assignUsersToClassesTableFill();
		$this->_smarty->assign('backlink', 'javascript:history.back()');
		$this->_interface->dieSuccess(_g('The Data was successfully Assigned. You can now go back and view and edit the temporary changes'));
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
	 * Drops the UsersToClasses-Table
	 *
	 * Dies displaying a Message when the Query could not be executed
	 */
	protected function assignUsersToClassesTableDrop() {

		try {
			$this->_pdo->exec('DROP TABLE KuwasysTemporaryRequestsAssign');

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
				`KuwasysTemporaryRequestsAssign` (
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
			$this->_pdo->beginTransaction();
			foreach($requests as $request) {
				$request->usersToClassAssign();
				$request->assignedDataToTemporaryTable($this->_pdo);
			}
			$this->_pdo->commit();
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

	/*********************************************************************
	 * Allows the User to view, change and upload the temporary Assignments
	 */
	protected function assignUsersToClassesOverviewExecute() {

		$classes = $this->temporaryAssignmentsClassdataGet();
		$this->_smarty->assign('classes', $classes);
		$this->assignUsersToClassesSetHeader();
		$this->displayTpl('AssignUsersToClasses/classlist.tpl');
	}

	/**
	 * Fetches the Temporary Assignments Grouped by Classes
	 *
	 * @return array  The Classes and some more information
	 */
	protected function temporaryAssignmentsClassdataGet() {

		try {
			$data = $this->_pdo->query('SELECT cu.translatedName AS weekday,
					COUNT(*) - (
						SELECT COUNT(*) FROM KuwasysTemporaryRequestsAssign rad
						WHERE ra.classId = rad.classId AND (rad.statusId = 0 OR rad.statusId = 2)
					) AS usercount, c.label AS classlabel,
					c.ID AS classId
				FROM KuwasysTemporaryRequestsAssign ra
				JOIN class c ON ra.classId = c.ID
				JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
				GROUP BY ra.classId ORDER BY cu.ID');

			return $data;

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the Temporary Assignments!'));
		}
	}

	/*********************************************************************
	 * Allows the User to view and edit the Requests of one Class
	 */
	protected function assignUsersToClassesClassdetailsExecute() {

		$class = $this->classGet($_GET['classId']);
		$classes = $this->classesGetAllOfActiveSchoolyear();
		$this->assignUsersToClassesSetHeader();
		$this->_smarty->assign('classId', $_GET['classId']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('classes', $classes);
		$this->displayTpl('AssignUsersToClasses/classdetails.tpl');
	}

	/*********************************************************************
	 * Allows JS to fill its tables with the Data
	 */
	protected function assignUsersToClassesClassdetailsGetExecute() {

		try {
			$data = $this->temporaryAssignmentsRequestsOfClassGet(
				$_POST['classId']);

		} catch(PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not fetch the User-Assignments') . $e->getMessage())));
		}

		die(json_encode($data));
	}

	/**
	 * Fetches all Userrequests of a Class
	 *
	 * @param  int    $classId The ID of the Class
	 * @return array           The Userrequests
	 * @throws PDOException If Error happened when fetching the Data
	 */
	protected function temporaryAssignmentsRequestsOfClassGet($classId) {

		$stmt = $this->_pdo->prepare(
			'SELECT IF(ra.statusId <> 0, uics.name, "removed") statusname,
				ra.statusId AS statusId, ra.classId AS classId,
				ra.userId AS userId,
				IF(origuics.ID, origuics.translatedName, "N/A") AS origStatusname,
				CONCAT(u.forename, " ", u.name) AS username,
				CONCAT(g.gradelevel, "-", g.label) AS grade,
				(SELECT c2.ID FROM class c2
					JOIN KuwasysTemporaryRequestsAssign ra2
						ON ra2.classId = c2.ID
					WHERE c2.unitId = c.unitId AND ra2.userId = ra.userId AND ra2.classId <> ra.classId
				) AS otherClassId,
			(SELECT c2.label FROM class c2
				JOIN KuwasysTemporaryRequestsAssign ra2
					ON ra2.classId = c2.ID
				WHERE c2.unitId = c.unitId AND ra2.userId = ra.userId AND ra2.classId <> ra.classId
			) AS otherClassLabel
			FROM KuwasysTemporaryRequestsAssign ra
			JOIN users u ON ra.userId = u.ID
			LEFT JOIN usersInGradesAndSchoolyears uigsy
				ON ra.userId = uigsy.userId
					AND uigsy.schoolyearId = @activeSchoolyear
			LEFT JOIN Grades g ON uigsy.gradeId = g.ID
			LEFT JOIN usersInClassStatus uics ON ra.statusId = uics.ID
			LEFT JOIN class c ON ra.classId = c.ID
			LEFT JOIN usersInClassStatus origuics
				ON ra.origStatusId = origuics.ID
			WHERE ra.classId = :classId
		');

		$stmt->execute(array('classId' => $classId));

		return $stmt->fetchAll(PDO::FETCH_GROUP);
	}

	/**
	 * Fetches a Class from the Database
	 *
	 * @param  int    $classId The ID of the Class to fetch
	 * @return array           The Class-Data
	 */
	protected function classGet($classId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class
				WHERE ID = :classId');

			$stmt->execute(array('classId' => $classId));
			return $stmt->fetch();

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not fetch the Class'))));
		}
	}

	/*********************************************************************
	 * Allows the Admin to change the Status of UserToClass-Assignments
	 */
	protected function assignUsersToClassesChangeStatusOfUserExecute() {

		try {
			$statusId = ($_POST['statusname'] != 'removed') ?
				$this->statusIdGetByName($_POST['statusname']) : 0;

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not fetch the Status'))));
		}

		$this->temporaryRequestChangeStatus($_POST['userId'],
			$_POST['classId'], $statusId);

		die(json_encode(array('value' => 'success',
			'message' => _g('The Status of the User was successfully changed')
		)));
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

		$stmt = $this->_pdo->prepare('SELECT ID FROM usersInClassStatus
			WHERE name = :name');

		$stmt->execute(array('name' => $statusName));

		return $stmt->fetchColumn();
	}

	/*********************************************************************
	 * Allows the Admin to change the Class of a UserToClass-Assignment
	 */
	protected function assignUsersToClassesChangeClassOfUserExecute() {

		try {
			$stmt = $this->_pdo->prepare('UPDATE KuwasysTemporaryRequestsAssign
				SET classId = :newClassId
				WHERE userId = :userId AND classId = :classId');

			$stmt->execute(array(
				'userId' => $_POST['userId'],
				'classId' => $_POST['classId'],
				'newClassId' => $_POST['newClassId']
			));

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not move the User to the other Class!')
			)));
		}

		die(json_encode(array('value' => 'success',
			'message' => _g('The User was successfully moved.'))));
	}

	/**
	 * Fetches all Classes that are in the active Schoolyear
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array  The Classes
	 */
	protected function classesGetAllOfActiveSchoolyear() {

		try {
			$classes = $this->_pdo->query('SELECT * FROM class
				WHERE schoolyearId = @activeSchoolyear');

			return $classes;

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not fetch the User-Assignments'));
		}
	}

	/**
	 * Sets the Header of the Templates to allow the User a better overview
	 */
	protected function assignUsersToClassesSetHeader() {

		$siteHeaderPath = $this->_smartyModuleTemplatesPath .
			'AssignUsersToClasses/header.tpl';
		$this->_smarty->assign('inh_path', $siteHeaderPath);
	}

	/*********************************************************************
	 * Allows the Admin to Add a User to the Class to the Temp-Table
	 */
	protected function assignUsersToClassesAddUserToClassExecute() {

		$userId = $this->userIdGetByUsername($_POST['username']);
		$statusId = $this->statusIdGetByName($_POST['statusname']);

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO KuwasysTemporaryRequestsAssign
				(userId, classId, statusId, origUserId, origClassId, origStatusId) VALUES
				(:userId, :classId, :statusId, 0, 0, 0)');

			$stmt->execute(array(
				'userId' => $userId,
				'classId' => $_POST['classId'],
				'statusId' => $statusId,
			));

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not add the User to the Class'))));
		}

		die(json_encode(array('value' => 'success',
			'message' => _g('The User was successfully added'))));
	}

	/*********************************************************************
	 * This Submodule applys the changes temp. made to the UsersInClass-Table
	 */
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

	/**
	 * Deletes all UsersInClass-Joints which got changed by the new Assignments
	 *
	 * Dies displaying a Message on Error
	 */
	protected function usersInClassJointDeleteByNewAssignments() {

		try {
			$this->_pdo->exec('DELETE uic.* FROM jointUsersInClass uic
				JOIN KuwasysTemporaryRequestsAssign ra
					ON uic.ClassID = ra.origClassId
						AND uic.userId = ra.origUserId');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not delete the old Joints!'));
		}
	}

	/**
	 * Adds the temporary assignments to the jointUsersInClass-Table
	 *
	 * Dies displaying a Message on Error.
	 * Only Entries with an StatusId that is not Zero will be added.
	 */
	protected function newAssignmentsAddToJoints() {

		try {
			$this->_pdo->exec('INSERT INTO jointUsersInClass
				(UserID, ClassID, statusId)
				SELECT userId, classId, statusId
					FROM KuwasysTemporaryRequestsAssign re
					WHERE statusId <> 0');

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not add the new Joints!') . $e->getMessage());
		}
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
			$stmt = $this->_pdo->prepare('SELECT ID FROM users
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
	protected function userAssignToClass($userId, $classId, $statusId) {

		$stmt = $this->_pdo->prepare('INSERT INTO jointUsersInClass
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
		return $classRequests;
	}

	/**
	 * Assigns the Users depending on the Requests to the Classes
	 */
	public function usersToClassAssign() {

		$this->_changedRequests = array();

		if(!isset(self::$_statusIds)) {
			self::$_statusIds = self::assignUsersToClassesStatusIdsGet($pdo);
		}

		$this->requestsAssign($this->_primaryRequests);
		$this->requestsAssign($this->_secondaryRequests);
	}

	/**
	 * Uploads the Assigned data to the Database
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  PDO    $pdo The PDO-Object for uploading stuff
	 */
	public function assignedDataToTemporaryTable($pdo) {

		$stmt = $pdo->prepare(
			'INSERT INTO KuwasysTemporaryRequestsAssign
			(`userId`, `classId`, `statusId`, `origUserId`, `origClassId`,
				`origStatusId`) VALUES
			(:userId, :classId, :statusId, :userId, :classId,
				:origStatusId);
		');

		foreach($this->_changedRequests as $request) {
			$stmt->execute($request);
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
				"SELECT uic.ClassID AS classId,uic.statusId AS statusId,
					uic.UserID AS userId, c.maxRegistration AS maxRegistration
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
				$request['classId'], $classesRequests)) {
				$class->requestAdd($request);
			}
			else {
				$class = new RequestsOfClass($request['classId']);
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
	 * Assigns the Given Userrequests either as Waiting or Active
	 *
	 * @param  array  $requests The Requests to assign
	 */
	protected function requestsAssign($requests) {

		if(count($requests) > 0) {
			if(count($requests) > $this->_remainingRegistrations) {
				$this->requestsOverflowRandomAssignment($requests);
			}
			else {
				$this->requestsAllAssignableAssignment($requests);
			}
		}
	}

	/**
	 * Assigns Users to the Class at Random
	 *
	 * @param  array  $requests The Requests of one Status
	 * @return array            The randomized Requests
	 */
	protected function requestsOverflowRandomAssignment($requests) {

		$active = array();
		$waiting = array();

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

			$this->_changedRequests = array_merge(
				$this->_changedRequests, $active, $waiting);

			$this->_remainingRegistrations -= count($active);
		}
		else {
			$this->_interface->dieError(_g('Could not Shuffle the Requests!'));
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

		foreach($requests as &$request) {
			$request['origStatusId'] = $request['statusId'];
			$request['statusId'] = $statusId;
		}

		return $requests;
	}

	/**
	 * Assigns all of the Requests to the Class as active
	 *
	 * Subtracts the Count of the Assigned Requests from remainingRegistrations
	 *
	 * @param  array  $requests The Requests to add
	 */
	protected function requestsAllAssignableAssignment($requests) {

		$active = $this->statusIdOfRequestsChangeTo(
			self::$_statusIds['active'], $requests);
		$this->_changedRequests = array_merge(
			$this->_changedRequests, $active);
		$this->_remainingRegistrations -= count($active);
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

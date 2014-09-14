<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/gump.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/Kuwasys.php';

/**
 * Allows the User to use Classes. Classes as in the Workgroups in Schools
 */
class Classes extends Kuwasys {

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
	 *
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
	 *
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);

		$this->_interface = $dataContainer->getInterface();
		$this->_acl = $dataContainer->getAcl();
		$this->_pdo = $dataContainer->getPdo();
		$this->_smarty = $dataContainer->getSmarty();
		$this->_logger->categorySet('administrator/Babesk/Classes');

		$this->initSmartyVariables();
	}

	/**
	 * Displays a MainMenu to the User
	 *
	 * Dies displaying the Main Menu
	 */
	protected function mainMenu() {

		$this->_smarty->assign('isClassRegistrationGloballyEnabled',
			$this->globalClassRegistrationGet());

		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'mainmenu.tpl');
	}

	protected function submoduleAddClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->addClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully added.'));
		}
		else {
			$this->addClassDisplay();
		}
	}

	/**
	 * Fetches all Schoolyears and returns them
	 *
	 * @return array The Schoolyears as an Array
	 */
	protected function schoolyearsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM SystemSchoolyears');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Schoolyears!'));
		}
	}

	protected function schoolyearsIdNamePairsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT ID, label FROM SystemSchoolyears');

			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the Schoolyear-ID-Name-Pairs',
				'Notice', Null,
				json_encode(array('Exception' => $e->getMessage()
			)));
			$this->_interface->dieError(
				_g('Could not fetch the Schoolyears!'));
		}
	}

	/**
	 * Fetches all Classunits (usually days) and returns them
	 *
	 * @return array The Classunits
	 */
	protected function classunitsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM KuwasysClassCategories');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classunits!'));
		}
	}

	/**
	 * Checks the given Input of the ChangeClass and AddClass Dialog
	 */
	protected function classInputCheck() {

		$gump = new GUMP();
		$gump->rules(array(
			'label' => array(
				'required|min_len,2|max_len,64',
				'',
				_g('Classname')
			),
			'description' => array(
				'max_len,1024',
				'',
				_g('Classdescription')
			),
			'maxRegistration' => array(
				'required|min_len,1|max_len,4|numeric',
				'',
				_g('Max Amount of Registrations for this Class')
			),
			'classunit' => array(
				'required|numeric',
				'',
				_g('Classunit')
			),
			'schoolyear' => array(
				'required|numeric',
				'',
				_g('Schoolyear-ID')
			),
			'allowRegistration' => array(
				'boolean',
				'',
				_g('Allow registration')
			)
		));

		if(!($_POST = $gump->run($_POST))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Adds all necessary data to the Database
	 */
	protected function addClassUpload() {

		$this->_pdo->beginTransaction();
		$this->newClassUpload();
		$this->_pdo->commit();
	}

	/**
	 * Adds a new Row to the class-Table
	 *
	 * Dies displaying a Message on Error
	 */
	protected function newClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO KuwasysClasses (label, description, maxRegistration,
					registrationEnabled, unitId, schoolyearId)
				VALUES (:label, :description, :maxRegistration,
					:registrationEnabled, :unitId, :schoolyearId)');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not add the new Class!'));
		}
	}

	/**
	 * Displays a Form to the User which allows him to add a Class
	 */
	protected function addClassDisplay() {

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'addClass.tpl');
	}

	/**
	 * Allows the User to change a Class
	 */
	protected function submoduleChangeClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->changeClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully changed.'));
		}
		else {
			$this->changeClassDisplay();
		}
	}

	/**
	 * Uploads the Change of the Class
	 *
	 * Dies with a Message on Error
	 */
	protected function changeClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE KuwasysClasses SET label = :label, description = :description,
					maxRegistration = :maxRegistration,
					registrationEnabled = :registrationEnabled,
					unitId = :unitId, schoolyearId = :schoolyearId
					WHERE ID = :id');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
				':id' => $_GET['ID'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g("Could not change the Class with the ID {$_GET[ID]}!"));
		}
	}

	/**
	 * Displays a form allowing the User to Change the Class
	 */
	protected function changeClassDisplay() {

		try {
			$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
			$this->_smarty->assign('classunits', $this->classunitsGetAll());
			$this->_smarty->assign('class', $this->classGet($_GET['ID']));
			$this->_smarty->display(
				$this->_smartyModuleTemplatesPath . 'changeClass.tpl');

		} catch (PDOException $e) {
			$this->_logger->log(__METHOD__ . ': ' . $e->getMessage(),
				'Moderate');
			$this->_interface->dieError(_g('Could not fetch the data!'));
		}
	}

	/**
	 * Deletes a Class
	 */
	protected function submoduleDeleteClassExecute() {

		if(isset($_POST['confirmed'])) {
			$this->classDeletionRun();
		}
		else if(isset($_POST['declined'])) {
			$this->_interface->dieMsg(_g('The Class was not deleted.'));
		}
		else {
			$this->classDeletionConfirmation();
		}
	}

	/**
	 * Displays a Confirmation asking wether the user wants to delete the class
	 *
	 * Dies Displaying the Form
	 */
	protected function classDeletionConfirmation() {

		try {
			$class = $this->classGet($_GET['ID']);
			if(count($class)) {
				$this->_smarty->assign('class', $class);
				$this->displayTpl('deleteClassConfirmation.tpl');
			}
			else {
				$this->_interface->dieError(_g('Class to delete not found!'));
			}

		} catch (PDOException $e) {
			$this->_logger->log("Error fetching Class with Id $_GET[ID] " .
				'in ' . __METHOD__, 'Moderate');
			$this->_interface->dieError(_g('Could not fetch the data!'));
		}
	}

	/**
	 * Checks the given ID before starting the Deletion-process of the Class
	 *
	 * Dies displaying a message when Input not correct
	 */
	protected function classDeletionInputCheck() {

		$gump = new GUMP();
		$gump->rules(array('ID' => array(
			'required|min_len,1|max_len,11|numeric', '', _g('Class-ID')
		)));
		if(!($_GET = $gump->run($_GET))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Checks the Input and deletes the Class
	 *
	 * Dies displaying a Message
	 */
	protected function classDeletionRun() {

		$this->classDeletionInputCheck();
		$this->classDeletionUpload();
		$this->_interface->backlink(
			'administrator|Kuwasys|Classes|DisplayClasses'
		);
		$this->_interface->dieSuccess(_g(
			'The Class was successfully deleted'));
	}

	/**
	 * Deletes the Class with the given ID from the Database
	 *
	 * Dies displaying a Message on Error
	 */
	protected function classDeletionUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE c.*, uic.*
				FROM KuwasysClasses c
				LEFT JOIN KuwasysUsersInClasses uic ON c.ID = uic.ClassID
				WHERE c.ID = :id');

			$stmt->execute(array(':id' => $_GET['ID']));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not delete the Class!') . $e->getMessage());
		}
	}

	/**
	 * Display all Classes to the User
	 */
	protected function submoduleDisplayClassesExecute() {

		$schoolyearId = $this->displayClassesDesiredSchoolyearGet();
		$classes = $this->displayClassesClassesGet($schoolyearId);
		$schoolyears = $this->schoolyearsIdNamePairsGetAll();

		$this->_smarty->assign('classes', $classes);
		$this->_smarty->assign('schoolyears', $schoolyears);
		$this->_smarty->assign('activeSchoolyearId', $schoolyearId);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'displayClasses.tpl');
	}

	/**
	 * Returns the SchoolyearId the classes being displayed are in
	 *
	 * @return string The SchoolyearId
	 */
	protected function displayClassesDesiredSchoolyearGet() {

		if(isset($_GET['schoolyearId'])) {
			$schoolyearId = $_GET['schoolyearId'];
		}
		else {
			$schoolyearId = $this->activeSchoolyearGet();
		}

		return $schoolyearId;
	}

	/**
	 * Fetches the ID of the active Schoolyear from the Server
	 *
	 * @return string the ID of the Active Schoolyear
	 */
	protected function activeSchoolyearGet() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT ID FROM SystemSchoolyears WHERE active = 1');

			$stmt->execute();
			return $stmt->fetchColumn();

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not fetch the active Schoolyear!'));
		}
	}

	/**
	 * Returns the Classes to be displayed
	 *
	 * @return array The Classes
	 */
	protected function displayClassesClassesGet($schoolyearId) {

		if($this->schoolyearIdCheck($schoolyearId)) {
			$classes = $this->classesGetAllBySchoolyearId($schoolyearId);
		}
		else {
			$classes = array();
		}

		return $classes;
	}

	/**
	 * Checks if the Fetched SchoolyearId has a correct value
	 *
	 * @param  int    $id The SchoolyearID
	 * @return bool       If the ID is a correct value true, else false
	 */
	protected function schoolyearIdCheck($id) {

		if(empty($id)) {
			$this->_interface->showError(
				_g('There is no active Schoolyear set!'));
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Fetches all Classes with additional Data that are in the Schoolyear
	 *
	 * @param  int    $schoolyearId The ID of the Schoolyear
	 * @return array                The Fetched Classes
	 */
	protected function classesGetAllBySchoolyearId($schoolyearId) {

		try {
			$subQueryCountUsers = '(SELECT Count(*)
					FROM KuwasysUsersInClassesAndCategories uic
					JOIN SystemUsers ON SystemUsers.ID = uic.UserID
					WHERE uic.statusId = (
							SELECT ID FROM KuwasysUsersInClassStatuses
							WHERE name="%s"
						)
						AND c.ID = uic.ClassID
						AND uic.categoryId = cu.ID
					)
				';

			$stmt = $this->_pdo->prepare(
				'SELECT c.*, sy.label As schoolyearLabel,
					GROUP_CONCAT(cu.translatedName) AS unitTranslatedName,
					(SELECT GROUP_CONCAT(
							CONCAT(ct.forename, " ", ct.name) SEPARATOR "; "
						)
						FROM KuwasysClassteachers ct
						JOIN KuwasysClassteachersInClasses ctic
							ON ct.ID = ctic.ClassTeacherID
						WHERE c.ID = ctic.ClassID
						GROUP BY c.ID
					) AS classteacherName,
					'. sprintf ($subQueryCountUsers, 'active') . ' AS activeCount,
					'. sprintf ($subQueryCountUsers, 'waiting') . ' AS waitingCount,
					'. sprintf ($subQueryCountUsers, 'request1') . ' AS request1Count,
					'. sprintf ($subQueryCountUsers, 'request2') . ' AS request2Count
				FROM KuwasysClasses c
				LEFT JOIN SystemSchoolyears sy ON c.schoolyearId = sy.ID
				LEFT JOIN KuwasysClassesInCategories cic ON cic.classId = c.ID
				LEFT JOIN KuwasysClassCategories cu ON cu.ID = cic.categoryId
				WHERE sy.ID = :schoolyearId
				GROUP BY c.ID, cu.ID');

			$stmt->execute(array('schoolyearId' => $schoolyearId));

			return $stmt->fetchAll();

		} catch (PDOException $e) {
			$this->_logger->log('error fetching the classes by schoolyearId',
				'Notice', Null, json_encode(array(
					'msg' => $e->getMessage(),
					'schoolyearId' => $schoolyearId)));
			$this->_interface->dieError(
				_g('Could not fetch the Classes by SchoolyearId $1%s',
					$schoolyearId));
		}
	}

	/**
	 * Fetches one/all Classes from the Database and linked data
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  $classId If Set, only the Data for the Class will be fetched -
	 * else all classes will be fetched
	 * @return array The Classes
	 */
	protected function classesGetWithAdditionalReadableData(
		$classId = false, $filterBySchoolyear = false) {

		$whereStr = '';

		if($classId) {
			$whereStr = 'WHERE c.ID = :id';
		}
		else if($filterBySchoolyear) {
			$whereStr = 'WHERE sy.ID = :id';
		}

		$subQueryCountUsers = '(SELECT Count(*)
				FROM KuwasysUsersInClasses uic
				JOIN SystemUsers ON SystemUsers.ID = uic.UserID
				WHERE uic.statusId = (SELECT ID FROM KuwasysUsersInClassStatuses
					WHERE name="%s") AND c.ID = uic.ClassID
				)
			';

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.*, cc.translatedName AS unitTranslatedName,
					sy.ID AS schoolyearId, sy.label AS schoolyearLabel,
					cc.ID AS categoryId,
					GROUP_CONCAT(DISTINCT ct.name SEPARATOR "; ") AS classteacherName,
					'. sprintf ($subQueryCountUsers, 'active') . ' AS activeCount,
					'. sprintf ($subQueryCountUsers, 'waiting') . ' AS waitingCount,
					'. sprintf ($subQueryCountUsers, 'request1') . ' AS request1Count,
					'. sprintf ($subQueryCountUsers, 'request2') . ' AS request2Count
				FROM KuwasysClasses c
				LEFT JOIN SystemSchoolyears sy ON c.schoolyearId = sy.ID
				LEFT JOIN KuwasysClassCategories cc ON c.unitId = cc.ID
				LEFT JOIN (
						SELECT ctic.ClassID AS classId,
							CONCAT(ct.forename, " ", ct.name) AS name
						FROM KuwasysClassteachers ct
						JOIN KuwasysClassteachersInClasses ctic
							ON ct.ID = ctic.ClassTeacherID
					) ct ON c.ID = ct.classId
				' . $whereStr . '
				GROUP BY c.ID');

			if($classId !== false) {
				$stmt->execute(array(':id' => $classId));
				return $stmt->fetch();
			}
			else if($filterBySchoolyear !== false) {
				$stmt->execute(array(':id' => $filterBySchoolyear));
				return $stmt->fetch();
			}
			else {
				$stmt->execute();
				return $stmt->fetchAll();
			}


		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Class(es)!'));
		}
	}

	protected function submoduleDisplayClassDetailsExecute() {

		if(!isset($_GET['ID'])) {
			$this->_interface->dieError('Keine ID angegeben!');
		}

		$class = $this->classesGetWithAdditionalReadableData($_GET['ID']);
		$users = $this->usersByClassIdGet($_GET['ID']);
		$users = $this->assignClassesOfSameClassunitToUsers(
			$users, $class['unitId']
		);
		$statuses = $this->statusesGetAll();
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('users', $users);
		$this->_smarty->assign('statuses', $statuses);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'display-class-details.tpl'
		);
	}

	/**
	 * Returns the Users that are in the ClassId
	 * @param  string $classId The ID of the Class
	 * @return array           The Users that are in the Class and the Status
	 *                         of this connection
	 */
	protected function usersByClassIdGet($classId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT u.*, g.gradename AS gradename,
					uics.translatedName AS statusTranslated,
					uics.ID AS statusId,
					uic.ID as jointId
				FROM SystemUsers u
				JOIN KuwasysUsersInClasses uic ON u.ID = uic.UserID
				JOIN KuwasysUsersInClassStatuses uics ON uic.statusId = uics.ID
				LEFT JOIN (
						SELECT CONCAT(gradelevel, "-", label) AS gradename,
							uigs.UserID AS userId
						FROM SystemGrades g
						JOIN SystemUsersInGradesAndSchoolyears uigs ON
							uigs.gradeId = g.ID
						WHERE uigs.schoolyearId = @activeSchoolyear
					) g ON g.userId = u.ID
				WHERE uic.ClassID = :id'
			);

			$stmt->execute(array(':id' => $classId));
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Users by Class') . $e->getMessage());
		}
	}

	/**
	 * Fetches the Classes that has the UnitId and one of the User in it
	 *
	 * @param  string $userIds The User-IDs of the User
	 * @param  string $unitId The Unit-ID of the Class
	 * @return array          Returns the Classes
	 */
	protected function assignClassesOfSameClassunitToUsers($users, $unitId) {

		$userIdString = $this->idStringGetFromUsers($users);
		if(!empty($userIdString)) {
			$userIdPart = "uic.UserID IN($userIdString) AND";
		}
		else {
			$userIdPart = '';
		}

		try {
			$stmt = $this->_pdo->prepare(
				"SELECT c.*, uic.UserID AS userId FROM KuwasysClasses c
				JOIN KuwasysUsersInClasses uic ON c.ID = uic.ClassID
				WHERE $userIdPart c.unitId = :unitId
					AND c.ID <> :classId
					AND c.schoolyearId = @activeSchoolyear"
			);

			$stmt->execute(
				array(':unitId' => $unitId, ':classId' => $_GET['ID']));

			while($row = $stmt->fetch()) {
				$users = $this->classOfSameDayAssignToUser($row, $users);
			}
			return $users;

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classes of the User at the same day') . $e->getMessage());
		}
	}

	/**
	 * Fetches all existing Statuses
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array  All existing Statuses
	 */
	protected function statusesGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM KuwasysUsersInClassStatuses');
			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Error fetching the Statuses!'));
		}
	}

	/**
	 * Creates a comma-separated String from the IDs of the given users
	 *
	 * @param  array  $users The Users with an ID-Key
	 * @return string        The String containing all IDs
	 */
	protected function idStringGetFromUsers($users) {

		$userIdString = '';
		foreach($users as &$user) {
			$userIdString .= $this->_pdo->quote($user['ID']) . ', ';
		}
		$userIdString = trim($userIdString, ', ');

		return $userIdString;
	}

	/**
	 * Assigns the Class to the fitting User in Users
	 *
	 * @param  array  $class The Class to Assign
	 * @param  array  $users The users to which to assign a Class
	 * @return array         The changed Users-Array
	 */
	protected function classOfSameDayAssignToUser($class, $users) {

		foreach($users as &$user) {
			if($user['ID'] == $class['userId']) {
				$user['classesOfSameDay'][] = $class;
			}
		}

		return $users;
	}

	/**
	 * Lets the User toggle the Global Classregistration on and off
	 *
	 * Dies displaying a Message
	 */
	protected function submoduleGlobalClassRegistrationExecute() {

		if(isset($_GET['toggleFormSend'])) {
			$this->_pdo->beginTransaction();
			$this->globalClassRegistrationChange();
			if(isset($_POST['activateIndividualClassregistrations'])) {
				$this->individalClassRegistrationsEnable();
			}
			$this->_pdo->commit();
			$this->_interface->dieSuccess(_g('The Global Classregistration ' .
				'was successfully changed.'));
		}
		else {
			$this->globalClassRegistrationFormDisplay();
		}
	}

	/**
	 * Changes the global Classregistration in the Database
	 *
	 * Dies displaying a Message on Error
	 */
	protected function globalClassRegistrationChange() {

		$toggle = (isset($_POST['toggleGlobalClassregistration'])) ? 1 : 0;

		try {
			$stmt = $this->_pdo->prepare('UPDATE SystemGlobalSettings
				SET value = :toggle
				WHERE name = "isClassRegistrationEnabled"');

			$stmt->execute(array(':toggle' => $toggle));

		} catch (PDOException $e) {
			$this->_logger->log('error changing global classregistrations',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not change the Global ' .
				'Classregistration!'));
		}
	}

	/**
	 * Enables class-registrations for all classes of the active schoolyear
	 * Dies displaying a message on PDOException
	 */
	protected function individalClassRegistrationsEnable() {

		try {
			$this->_pdo->exec(
				'UPDATE KuwasysClasses SET registrationEnabled = 1
					WHERE schoolyearId = @activeSchoolyear'
			);

		} catch (PDOException $e) {
			$this->_logger->log('error changing individual classregistrations',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not change the individual ' .
				'classregistrations!'));
		}
	}

	/**
	 * Displays a form to the User to change the Global Classregistration
	 */
	protected function globalClassRegistrationFormDisplay() {

		$this->_smarty->assign('enabled', $this->globalClassRegistrationGet());
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath .
			'toggleGlobalClassRegistrationEnabled.tpl');
	}

	/**
	 * Fetches the setting for global Classregistration and returns it
	 *
	 * @return bool  If Global Classregistration is enabled or not
	 */
	protected function globalClassRegistrationGet() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM SystemGlobalSettings
				WHERE name = "isClassRegistrationEnabled"');

			$data = $stmt->fetch();

			if($data === false) {
				return $this->globalClassRegistrationAdd();
			}

			return (boolean) $data['value'];

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not check whether the global Classregistration is' .
					'enabled or not.'));
		}
	}

	/**
	 * Adds the Global Classregistration Setting to the Database
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return  Returns the Value of the newly created Setting
	 */
	protected function globalClassRegistrationAdd() {

		try {
			$this->_pdo->exec('INSERT INTO SystemGlobalSettings (name, value)
				VALUES ("isClassRegistrationEnabled", "0")');

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not add the Global ' .
				'Classregistration setting!'));
		}

		return 0;
	}

	protected function submoduleAssignUsersToClassesExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleCreateClassSummaryExecute() {

		require_once 'SummaryOfClassesPdf.php';
		SummaryOfClassesPdf::init($this->_interface);
		SummaryOfClassesPdf::execute($_GET['startdate'], $_GET['enddate']);
	}

	protected function submoduleCsvImportExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Handy functions to display things to the User
	 * @var AdminInterface
	 */
	protected $_interface;

	/**
	 * The AccessControlLayer used for getting the Submodules
	 * @var Acl
	 */
	protected $_acl;

	/**
	 * The Database-Connection
	 * @var PDO
	 */
	protected $_pdo;

}

?>

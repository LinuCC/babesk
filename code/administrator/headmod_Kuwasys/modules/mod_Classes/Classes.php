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

		$execReq = $dataContainer->getModuleExecutionRequest();
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
			$stmt = $this->_pdo->query('SELECT * FROM schoolYear');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Schoolyears!'));
		}
	}

	protected function schoolyearsIdNamePairsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT ID, label FROM schoolYear');

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
			$stmt = $this->_pdo->query('SELECT * FROM kuwasysClassUnit');
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
				'INSERT INTO class (label, description, maxRegistration,
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
				'UPDATE class SET label = :label, description = :description,
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

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->assign('class', $this->classGet($_GET['ID']));
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'changeClass.tpl');
	}

	/**
	 * Fetches and returns the Class with the ID $id
	 *
	 * @param  string $id The ID of the Class
	 * @return array      The Class as an Array
	 */
	protected function classGet($id) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class WHERE ID = :id');
			$stmt->execute(array(':id' => $id));
			return $stmt->fetch();

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Class'));
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

		$class = $this->classGet($_GET['ID']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'deleteClassConfirmation.tpl');
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
				FROM class c
				LEFT JOIN jointUsersInClass uic ON c.ID = uic.ClassID
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
				'SELECT ID FROM schoolYear WHERE active = 1');

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
					FROM jointUsersInClass uic
					JOIN users ON users.ID = uic.UserID
					WHERE uic.statusId = (SELECT ID FROM usersInClassStatus
						WHERE name="%s") AND c.ID = uic.ClassID
					)
				';

			$stmt = $this->_pdo->prepare(
				'SELECT c.*, sy.label As schoolyearLabel,
					cu.translatedName AS unitTranslatedName,
					GROUP_CONCAT(DISTINCT ct.name SEPARATOR "; ") AS classteacherName,
					'. sprintf ($subQueryCountUsers, 'active') . ' AS activeCount,
					'. sprintf ($subQueryCountUsers, 'waiting') . ' AS waitingCount,
					'. sprintf ($subQueryCountUsers, 'request1') . ' AS request1Count,
					'. sprintf ($subQueryCountUsers, 'request2') . ' AS request2Count
				FROM class c
				LEFT JOIN schoolYear sy ON c.schoolyearId = sy.ID
				LEFT JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
				LEFT JOIN (
						SELECT ctic.ClassID AS classId,
							CONCAT(ct.forename, " ", ct.name) AS name
						FROM classTeacher ct
						JOIN jointClassTeacherInClass ctic
							ON ct.ID = ctic.ClassTeacherID
					) ct ON c.ID = ct.classId
				WHERE sy.ID = :schoolyearId
				GROUP BY c.ID');

			$stmt->execute(array('schoolyearId' => $schoolyearId));

			return $stmt->fetchAll();

		} catch (PDOException $e) {
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
				FROM jointUsersInClass uic
				JOIN users ON users.ID = uic.UserID
				WHERE uic.statusId = (SELECT ID FROM usersInClassStatus
					WHERE name="%s") AND c.ID = uic.ClassID
				)
			';

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.*, sy.label As schoolyearLabel,
					cu.translatedName AS unitTranslatedName,
					GROUP_CONCAT(DISTINCT ct.name SEPARATOR "; ") AS classteacherName,
					'. sprintf ($subQueryCountUsers, 'active') . ' AS activeCount,
					'. sprintf ($subQueryCountUsers, 'waiting') . ' AS waitingCount,
					'. sprintf ($subQueryCountUsers, 'request1') . ' AS request1Count,
					'. sprintf ($subQueryCountUsers, 'request2') . ' AS request2Count
				FROM class c
				LEFT JOIN schoolYear sy ON c.schoolyearId = sy.ID
				LEFT JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
				LEFT JOIN (
						SELECT ctic.ClassID AS classId,
							CONCAT(ct.forename, " ", ct.name) AS name
						FROM classTeacher ct
						JOIN jointClassTeacherInClass ctic
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

		$class = $this->classesGetWithAdditionalReadableData($_GET['ID']);
		$users = $this->usersByClassIdGet($_GET['ID']);
		$users = $this->assignClassesOfSameClassunitToUsers(
			$users, $class['unitId']);
		$statuses = $this->statusesGetAll();
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('users', $users);
		$this->_smarty->assign('statuses', $statuses);
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'displayClassDetails.tpl');
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
					uics.translatedName AS statusTranslated
				FROM users u
				JOIN jointUsersInClass uic ON u.ID = uic.UserID
				JOIN usersInClassStatus uics ON uic.statusId = uics.ID
				LEFT JOIN (
						SELECT CONCAT(label, "-", gradelevel) AS gradename,
							uigs.UserID AS userId
						FROM Grades g
						JOIN usersInGradesAndSchoolyears uigs ON
							uigs.gradeId = g.ID
						WHERE uigs.schoolyearId = @activeSchoolyear
					) g ON g.userId = u.ID
				WHERE uic.ClassID = :id'
			);

			$stmt->execute(array(':id' => $classId));
			return $stmt->fetchAll();

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
				"SELECT c.*, uic.UserID AS userId FROM class c
				JOIN jointUsersInClass uic ON c.ID = uic.ClassID
				WHERE $userIdPart c.unitId = :unitId
					AND c.ID <> :classId"
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
			$stmt = $this->_pdo->query('SELECT * FROM usersInClassStatus');
			$stmt->execute();
			return $stmt->fetchAll();

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
			$this->globalClassRegistrationChange();
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

		$toggle = (isset($_POST['toggle'])) ? 1 : 0;

		try {
			$stmt = $this->_pdo->prepare('UPDATE global_settings
				SET value = :toggle
				WHERE name = "isClassRegistrationEnabled"');

			$stmt->execute(array(':toggle' => $toggle));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not change the Global Classregistration!'));
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
			$stmt = $this->_pdo->query('SELECT * FROM global_settings
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
			$this->_pdo->exec('INSERT INTO global_settings (name, value)
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

	protected function submoduleUnregisterUserExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
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

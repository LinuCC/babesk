<?php

require_once 'AdminUserInterface.php';
require_once 'AdminUserProcessing.php';
require_once 'UserDelete.php';
require_once 'UserDisplayAll.php';
require_once 'UsernameAutoCreator.php';
require_once PATH_ACCESS . '/CardManager.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/ArrayFunctions.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class User extends System {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	///////////////////////////////////////////////////////////////////////
	//Getters and Setters
	///////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		$execReq = $dataContainer->getExecutionCommand()->pathGet();
		if($this->submoduleCountGet($execReq)) {
			$this->submoduleExecuteAsMethod($execReq);
		}
		else {
			// $this->actionSwitch();
			$this->userInterface->ShowSelectionFunctionality();
		}
	}
	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');
		$this->userManager = new UserManager();
		$this->userInterface = new AdminUserInterface($this->relPath);
		$this->_interface = $this->userInterface;
		$this->userProcessing = new AdminUserProcessing($this->userInterface);
		$this->messages = array('error' => array(
			'no_id' => 'ID nicht gefunden.'));
		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();
		$this->_dataContainer = $dataContainer;
	}

	protected function submoduleDisplayAllExecute() {

		$displayer = new UserDisplayAll($this->_dataContainer);
		$displayer->displayAll();
	}

	protected function submoduleFetchUserdataExecute() {

		$displayer = new UserDisplayAll($this->_dataContainer);
		$displayer->fetchUsersOrganized();
	}

	protected function submoduleFetchUsercolumnsExecute() {

		$displayer = new UserDisplayAll($this->_dataContainer);
		$displayer->fetchShowableColumns();
	}

	protected function submoduleDeleteExecute() {

		$deleter = new UserDelete($this->_smarty);
		$deleter->deleteFromDb();
	}

	/**
	 * Registers a user. Requests should come from Ajax
	 *
	 * Either shows the Register-form or, if POST-Data send, tries to add the
	 * Data-input to the Database as a new User
	 */
	protected function submoduleRegisterExecute() {

		if (isset($_POST['forename'], $_POST['name'])) {
			$this->registerCheck(); //Form filled out
			$this->registerUpload();
			die(json_encode(array('value' => 'success',
				'message' => array("Der Benutzer $_POST[forename] $_POST[name] wurde erfolgreich hinzugefügt"))));
		}
		else { //show Form
			$this->registerForm();
		}
	}

	/**
	 * Displays the Register-a-User-Form to the Administrator
	 */
	protected function registerForm() {

		try {
			//---fetch data
			try { //Babesk-specific, dont crash when table not exist
				$priceGroups = $this->arrayGetFlattened(
					'SELECT ID, name FROM priceGroups');

			} catch (Exception $e) {
				$priceGroups = array();
			}

			//---display
			$this->_smarty->assign('grades', $this->gradesGetAllFlattened());
			$this->_smarty->assign('schoolyears',
				$this->schoolyearsGetAllFlattened());
			$this->_smarty->assign('usergroups',
				$this->usergroupsGetAllFlattened());

			$this->displayTpl('register.tpl');

		} catch (Exception $e) {
			$this->_interface->dieError('Ein Fehler ist beim Abrufen der Daten aufgetreten!');
		}
	}

	/**
	 * Checks the Inputdata of the registerform for correct Format and stuff
	 */
	protected function registerCheck() {

		require_once PATH_INCLUDE . '/gump.php';

		$gump = new GUMP();

		$_POST['isSoli'] = (isset($_POST['isSoli'])
			&& $_POST['isSoli'] == 'true');

		try {
			$gump->rules(self::$registerRules);
			// $_POST = $gump->input_preprocess_by_ruleset($_POST,
				// self::$registerRules);
			//Set none-filled-out formelements to be at least a void string,
			//for easier processing
			$gump->voidVarsToStringByRuleset($_POST, self::$registerRules);

			//validate and MySQL-Escape the elements
			if($gump->run($_POST)) {
				//Is PasswordRepeat the same as Password
				if($_POST['password'] != $_POST['passwordRepeat']) {
					die(json_encode(array(
						'value' => 'inputError',
						'message' => array('Wiederholtes Passwort stimmt nicht mit dem Passwort überein!'))));
				}
			}
			else {
				die(json_encode(array(
					'value' => 'inputError',
					'message' => $gump->get_readable_string_errors(false)
					)));
			}
		} catch (Exception $e) {
			die(json_encode(array(
				'value' => 'inputError',
				'message' => array('Konnte die Eingaben nicht überprüfen!'))));
		}
	}

	/**
	 * Registers the User by the Inputdata by creating the rows in the database
	 */
	protected function registerUpload() {

		//Standard-Values when adding a new User
		$locked = '0';
		$first_passwd = ($this->isFirstPasswordEnabled()) ? 1 : 0;

		if(!empty($_POST['password'])) {
			$password = hash_password($_POST['password']);
		}
		else {
			$password = $this->presetPasswordGet();
		}

		//Querys
		$cardnumberQuery = '';

		TableMng::getDb()->autocommit(false);

		try {
			$gradeAndSchoolyearQuery =
				$this->schoolyearsAndGradesRegisterQueryCreate();
			$usergroupsQuery = $this->registerUserUsergroupQueryCreate();

			$_POST = ArrayFunctions::arrayKeysCreateIfNotExist(
				$_POST, array('pricegroupId', 'credits', 'isSoli'));

			if(!empty($_POST['cardnumber'])) {
				$cardnumberQuery = "INSERT INTO cards (cardnumber, UID)
					VALUES ('$_POST[cardnumber]', @uid);";
			}

			TableMng::queryMultiple("INSERT INTO users
				(forename, name, username, password, email, telephone, birthday,
					first_passwd, locked, GID, credit, soli)
				VALUES ('$_POST[forename]', '$_POST[name]', '$_POST[username]',
					'$password', '$_POST[email]', '$_POST[telephone]',
					'$_POST[birthday]', $first_passwd, $locked,
					'$_POST[pricegroupId]', '$_POST[credits]', '$_POST[isSoli]'
					);
				SET @uid = LAST_INSERT_ID();
				$gradeAndSchoolyearQuery
				$cardnumberQuery
				$usergroupsQuery
				");

		} catch (Exception $e) {
			die($e->getMessage());
		}

		TableMng::getDb()->autocommit(true);
	}

	/**
	 * Checks if First Password in GlobalSettings enabled
	 *
	 * Dies when Error occured during fetching
	 *
	 * @return boolean If the User should input a new Password on First Login
	 */
	protected function isFirstPasswordEnabled() {

		try {
			$data = TableMng::querySingleEntry('SELECT value
				FROM global_settings
				WHERE name = "firstLoginChangePassword"');

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not check if first ' .
				'Password on Login is enabled!'));
		}

		if(!count($data)) {
			return false;
		}
		else {
			return (boolean) $data['value'];
		}
	}

	/**
	 * Fetches the presetPassword set in GlobalSettings
	 *
	 * @return string The hashed Password or a void string if no
	 *                PresetPassword is set or it could not be fetched
	 */
	protected function presetPasswordGet() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT value FROM global_settings
				WHERE name = "presetPassword"');
			$stmt->execute();
			$res = $stmt->fetchColumn();

		} catch (PDOException $e) {
			$this->_logger->log(
				'Could not fetch the Preset Password! ' . __METHOD__);
			return '';
		}

		if(empty($res)) {
			return '';
		}
		else {
			return $res;
		}
	}

	protected function schoolyearsAndGradesRegisterQueryCreate() {

		$query = '';

		if(empty($_POST['schoolyearAndGradeData'])) {
			return $query;
		}

		$flatRequestedRows = ArrayFunctions::arrayColumn(
			$_POST['schoolyearAndGradeData'], 'gradeId', 'schoolyearId');

		foreach($flatRequestedRows as $rSyId => $rGradeId) {
			$query .= "INSERT INTO usersInGradesAndSchoolyears
				(userId, gradeId, schoolyearId) VALUES
				(@uid, '$rGradeId', '$rSyId');";
		}

		return $query;
	}

	/**
	 * Creates Queries that adds the newly added User to the Selected Groups
	 */
	protected function registerUserUsergroupQueryCreate() {

		$query = '';

		if(isset($_POST['groups']) && count($_POST['groups'])) {
			foreach($_POST['groups'] as $group) {
				TableMng::sqlEscape($group);
				$query .= "INSERT INTO UserInGroups (userId, groupId)
					VALUES (@uid, '$group');";
			}
		}

		return $query;
	}

	/**
	 * Fetches data from the Database and rearranges them
	 *
	 * This function executed the Query given and rearranges the Elements into
	 * a flat key => value-Array
	 *
	 * @param  String $query The SQL-Query to execute
	 * @param  String $key (Standard: "ID") the column-name of the element of
	 * each row that should be the key for the new array-element
	 * @param  String $value (Standard: "name") the column-name of the element
	 * of each row that should be the value for the rearranged Array-Element
	 * @return Array The rearranged Array or a void array if SQL-Query returned
	 * nothing
	 */
	protected function arrayGetFlattened($query, $key = 'ID',
		$value = 'name') {

		$rows = TableMng::query($query);
		return ArrayFunctions::arrayColumn($rows, $value, $key);
	}

	protected function submoduleCreateUsernamesExecute() {
		if (isset($_POST['confirmed'])) {
			$creator = new UsernameAutoCreator();
			$scheme = new UsernameScheme();
			$scheme->templateAdd(UsernameScheme::Forename);
			$scheme->stringAdd('.');
			$scheme->templateAdd(UsernameScheme::Name);
			$creator->usersSet($this->userManager->getAllUsers());
			$creator->schemeSet($scheme);
			$users = $creator->usernameCreateAll();
			foreach ($users as $user) {
				///@todo: PURE EVIL DOOM LOOP OF LOOPING SQL-USE. Kill it with fire.
				$this->userManager->alterUsername ($user ['ID'], $user ['username']);
			}
			$this->userInterface->dieMsg ('Die Benutzernamen wurden erfolgreich geÃ¤ndert');
		}
		else {
			$this->userInterface->showConfirmAutoChangeUsernames ();
		}
	}

	protected function submoduleRemoveSpecialCharsFromUsernamesExecute () {
		if (isset ($_POST ['removeSpecialChars'])) {
			$users = $this->usersGetAll ();
			$rows = array ();
			foreach ($users as $user) {
				$name = $user ['username'];
				$nameChanged = $this->specialCharsRemove ($name);
				if ($name != $nameChanged) {
					$row = new DbAMRow ();
					$row->searchFieldAdd ('ID', $user ['ID']);
					$row->processFieldAdd ('username', $nameChanged);
					$rows [] = $row;
				}
			}
			$this->userManager->changeUsers ($rows);
		}
		else {
			$this->userInterface->showRemoveSpecialCharsFromUsername();
		}
	}

	/**
	 * Fetches all of the users from the database and returns them
	 *
	 * @return array(array(...)) An Array of Users, each one represented by
	 * another array
	 */
	protected function usersGetAll () {

		try {
			$data = TableMng::query(
				'SELECT u.*,
				(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM usersInGradesAndSchoolyears uigs
					LEFT JOIN Grades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
				FROM users u');

		} catch (Exception $e) {
			$this->userInterface->dieError ('Konnte die Benutzer nicht abrufen');
		}

		return $data;
	}

	protected function specialCharsRemove ($str) {
		$str = str_replace(array_keys (self::$invalid), array_values (self::$invalid), $str);
		return $str;
	}

	/**
	 * This function prepares and shows the ChangeUser-Form
	 *
	 * @param string (numeric) $uid The ID of the User
	 */
	protected function submoduleDisplayChangeExecute() {

		$uid = $_GET['ID'];
		TableMng::sqlEscape($uid);

		try {
			list($user,
				$cardnumber,
				$priceGroups,
				$grades,
				$schoolyears,
				$gradesAndSchoolyears,
				$groups,
				$modsActivated) = $this->changeDisplayDataFetch($uid);

			if($modsActivated['Kuwasys']) {
				list($classes,
					$statuses,
					$classesOfUser
					) = $this->userChangeDisplayKuwasysDataFetch($uid);
			}

		} catch (Exception $e) {
			$this->userInterface->dieError($e->getMessage());
		}

		$this->userInterface->ShowChangeUser(
			$user,
			$cardnumber,
			$priceGroups,
			$grades,
			$schoolyears,
			$gradesAndSchoolyears,
			$groups,
			$modsActivated,
			$classes,
			$statuses,
			$classesOfUser);
	}

	protected function changeDisplayDataFetch($userId) {

		$user = $this->userGet($userId);
		$gradeAndSchoolyears = $this->gradeAndSchoolyearDataOfUserGet(
			$userId);
		$grades = $this->gradesGetAllFlattened();
		$schoolyears = $this->schoolyearsGetAllFlattened();
		$groups = $this->groupsGetAllWithCheckIsUserIn($userId);
		$modsActivated = $this->userChangeModuleActivationGet();

		if($modsActivated['Babesk']) {
			$priceGroups = $this->arrayGetFlattened(
				'SELECT ID, name FROM priceGroups');
			$cardnumber = $this->cardnumberGetByUserId($userId);
			$cardnumber = (!empty($cardnumber)) ?
				$cardnumber[0]['cardnumber'] : '';

		}
		else {
			$cardnumber = '';
			$priceGroups = array();
		}

		return array($user,
			$cardnumber,
			$priceGroups,
			$grades,
			$schoolyears,
			$gradeAndSchoolyears,
			$groups,
			$modsActivated);
	}

	/**
	 * Fetches data specific to the Kuwasys Headmodule
	 *
	 * @return array The Data needed to change the User for Kuwasys
	 */
	protected function userChangeDisplayKuwasysDataFetch($uid) {

		$classes = $this->classesGetAll();
		$statuses = $this->usersInClassStatusGetAll();
		$classesOfUser = $this->classesOfUserGet($uid);

		return array($classes, $statuses, $classesOfUser);
	}

	/**
	 * Fetches and returns all Classes in the Database
	 *
	 * @return Array The Classes as an Array
	 */
	protected function classesGetAll() {

		try {
			$data = TableMng::query('SELECT * FROM class');

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Classes!'));
		}

		return $data;
	}

	/**
	 * Fetches all Statuses of a User-in-Class-Registration and returns them
	 *
	 * @return array The Statuses as an Array
	 */
	protected function usersInClassStatusGetAll() {

		try {
			$data = TableMng::query('SELECT * FROM usersInClassStatus');

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the User-in-Class-Statuses!'));
		}

		return $data;
	}

	/**
	 * Checks if Headmodules are active to display / hide Input-fields
	 *
	 * @return array An Array containing if the Headmodules are activated or
	 * not
	 */
	protected function userChangeModuleActivationGet() {

		$modsActivated = array();
		$modsActivated['Kuwasys'] =
			(boolean) $this->_acl->moduleGet('root/administrator/Kuwasys');

		$modsActivated['Babesk'] =
			(boolean) $this->_acl->moduleGet('root/administrator/Babesk');

		return $modsActivated;
	}

	/**
	 * Fetches all Classes of a User
	 *
	 * @return array The Classes of a user of all Schoolyears
	 */
	protected function classesOfUserGet($id) {

		try {
			$data = TableMng::query("SELECT c.*, uic.statusId AS statusId
				FROM class c
				JOIN jointUsersInClass uic ON c.ID = uic.ClassID
				WHERE uic.UserID = '$id'");

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classes of the User'));
		}

		return $data;
	}

	protected function userGet($uid) {

		$user = TableMng::querySingleEntry(
			"SELECT u.* FROM users u WHERE `ID` = $uid");

		return $user;
	}

	protected function gradeAndSchoolyearDataOfUserGet($uid) {

		$data = TableMng::query(
			"SELECT gradeId, schoolyearId FROM usersInGradesAndSchoolyears
			WHERE userId = $uid");

		return $data;
	}

	protected function cardnumberGetByUserId($userId) {

		$cardnumber = TableMng::query(
			"SELECT cardnumber FROM cards WHERE UID = $userId");

		return $cardnumber;
	}

	protected function gradesGetAllFlattened() {

		$grades = TableMng::query(
			'SELECT ID, CONCAT(gradelevel, "-", label) AS name FROM Grades');

		$flattenedGrades = ArrayFunctions::arrayColumn($grades, 'name', 'ID');

		return $flattenedGrades;
	}

	protected function schoolyearsGetAllFlattened() {

		$schoolyears = TableMng::query(
			'SELECT ID, label AS name FROM schoolYear');

		$flattenedSchoolyears = ArrayFunctions::arrayColumn(
			$schoolyears,
			'name',
			'ID');

		return $flattenedSchoolyears;
	}

	/**
	 * Fetches all Usergroups from the Database
	 *
	 * @return array  The Usergroups as an Array: '<ID>' => '<name>'
	 */
	protected function usergroupsGetAllFlattened() {

		try {
			$stmt = $this->_pdo->query('SELECT ID, name FROM Groups');

			return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Error fetching the Usergroups'));
		}
	}

	protected function schoolyearsGetAllWithCheckIsUserIn($userId) {

		$schoolyears = TableMng::query(
			"SELECT ID, label AS name, (
				SELECT COUNT(*) AS count FROM usersInGradesAndSchoolyears uigs
				WHERE sy.ID = uigs.schoolyearId AND uigs.userId = $userId
			) AS isUserIn
			FROM schoolYear sy
			ORDER BY active DESC;");

		return $schoolyears;
	}

	protected function groupsGetAllWithCheckIsUserIn($userId) {

		$groups = TableMng::query(
			"SELECT ID, name,
			(SELECT COUNT(*) AS count FROM UserInGroups uig
				WHERE g.ID = uig.groupId AND uig.userId = $userId)
					AS isUserIn
			FROM Groups g");

		return $groups;
	}

	/**
	 * Handles the Input from the ChangeUser-Form and changes the data
	 */
	protected function submoduleChangeExecute() {

		$uid = $_POST['ID'];
		TableMng::sqlEscape($uid);
		$this->changeParseInput();
		$this->changeCleanAndCheckInput();
		$this->changeUpload($uid);
		die(json_encode(array('value' => 'success',
			'message' => "Der Benutzer mit der ID '$uid' wurde erfolgreich geändert")));
	}

	/**
	 * Parses the input that the user made, so that the Program can run
	 * without throwing weird errors
	 */
	protected function changeParseInput() {

		$_POST['isSoli'] = (isset($_POST['isSoli']) &&
			$_POST['isSoli'] == 'true') ? 1 : 0;

		$_POST['accountLocked'] = ($_POST['accountLocked'] == 'true') ? 1 : 0;

		//Add Password to the Inputcheck if user wants it to be changed
		if($_POST['passwordChange'] == 'true') {
			self::$_changeRules['password'] = array('min_len,3|max_len,64', '', 'Passwort');
		}

		if(isset($_POST['credits'])) {
			$_POST['credits'] = str_replace(',', '.', $_POST['credits']);
		}
		else {
			$_POST['credits'] = 0;
		}
	}

	/**
	 * Cleans the Input, decodes HTML-entities and mysql-Encapes it and checks
	 * the input
	 */
	protected function changeCleanAndCheckInput() {

		require_once PATH_INCLUDE . '/gump.php';

		$gump = new GUMP();


		try {
			$gump->rules(self::$_changeRules);

			//Set none-filled-out formelements to be at least a void string,
			//for easier processing
			$_POST = $gump->voidVarsToStringByRuleset(
				$_POST, self::$registerRules);

			//validate the elements
			if($validatedData = $gump->run($_POST)) {
				//escapes all of the elements in the ruleset
				// $_POST = $gump->input_preprocess_by_ruleset($_POST,
					// self::$_changeRules);
			}
			else {
				die(json_encode(array(
					'value' => 'inputError',
					'message' => $gump->get_readable_string_errors(false)
					)));
			}

		} catch(Exception $e) {
			die(json_encode(array(
				'value' => 'inputError',
				'message' => array('Konnte die Eingaben nicht überprüfen!' .
					$e->getMessage()))));
		}
	}

	/**
	 * Uploads the changed data of the User
	 *
	 * Never ever use the following function to change the ID of the User,
	 * SQL-Statements used here dont support it and the linked Table-Elements
	 * will point to the wrong ID!
	 *
	 * @param  String $uid The ID of the User to upload the change
	 */
	protected function changeUpload($uid) {

		//Querys
		$cardnumberQuery = '';
		$passwordQuery = '';
		$groupQuery = '';

		TableMng::getDb()->autocommit(false);
		$this->_pdo->beginTransaction();

		try {
			//check for additional Querys needed
			if($this->_acl->moduleGet('root/administrator/Babesk')) {
				$cardnumberQuery = $this->cardsQueryCreate($uid);
			}
			$passwordQuery = $this->passwordQueryCreate($uid);
			$groupQuery = $this->groupQueryCreate($uid);
			$schoolyearsAndGradesQuery =
				$this->schoolyearsAndGradesQueryCreate($uid);
			$this->userChangeKuwasysData($uid);

			TableMng::queryMultiple("UPDATE users
				SET `forename` = '$_POST[forename]',
					`name` = '$_POST[name]',
					`username` = '$_POST[username]',
					`email` = '$_POST[email]',
					$passwordQuery
					`telephone` = '$_POST[telephone]',
					`birthday` = '$_POST[birthday]',
					`locked` = $_POST[accountLocked],
					`GID` = '$_POST[pricegroupId]',
					`credit` = '$_POST[credits]',
					`soli` = '$_POST[isSoli]'
				WHERE `ID` = $uid;
				$cardnumberQuery
				$groupQuery
				$schoolyearsAndGradesQuery
				");

		} catch (Exception $e) {
			die($e->getMessage());
		}

		TableMng::getDb()->autocommit(true);
		$this->_pdo->commit();
	}

	protected function schoolyearsAndGradesQueryCreate($userId) {

		if(empty($_POST['schoolyearAndGradeData'])) {
			$_POST['schoolyearAndGradeData'] = array();
		}

		$query = $this->schoolyearsAndGradesChange(
			$_POST['schoolyearAndGradeData'],
			$userId);

		return $query;
	}

	protected function schoolyearsAndGradesChange($requestedRows, $userId) {

		$query = '';
		$existingRows = $this->gradeAndSchoolyearDataOfUserGet($userId);

		$flatExistingRows = ArrayFunctions::arrayColumn(
			$existingRows, 'gradeId', 'schoolyearId');
		$flatRequestedRows = ArrayFunctions::arrayColumn(
			$requestedRows, 'gradeId', 'schoolyearId');

		$toDelete = $flatExistingRows;

		foreach($flatRequestedRows as $rSyId => $rGradeId) {

			if(!array_key_exists($rSyId, $flatExistingRows) ||
				$flatExistingRows[$rSyId] != $rGradeId) {

				$query .= "INSERT INTO usersInGradesAndSchoolyears
					(userId, gradeId, schoolyearId) VALUES
					('$userId', '$rGradeId', '$rSyId');";
			}
			else {
				unset($toDelete[$rSyId]);
			}
		}

		foreach($toDelete as $schoolyearId => $gradeId) {

			$query .= "DELETE FROM usersInGradesAndSchoolyears
				WHERE userId = $userId AND
					schoolyearId = $schoolyearId AND
					gradeId = $gradeId;";
		}

		return $query;
	}

	/**
	 * Creates a Query changing the Cardnumber of a User depending on the
	 * Userinput given from the Change-User-Dialog
	 * @return String The Query that changes the Data in the Database
	 */
	protected function cardsQueryCreate($uid) {

		$query = '';
		//Fetch the existing cardnumber of the User
		$userCard = TableMng::query(
				"SELECT * FROM cards WHERE UID = $uid");

		if(!empty($_POST['cardnumber'])) {

			if(!count($userCard)) {
				$query = "INSERT INTO cards (cardnumber, UID)
					VALUES ('$_POST[cardnumber]', $uid);";
			}
			else if($userCard[0]['cardnumber'] == $_POST['cardnumber']) {
				//nothing changed
				return '';
			}
			else {
				//Card was changed, add it to the counter
				$countChangedCardId = $userCard[0]['changed_cardID'] + 1;
				$cardnumber = $_POST['cardnumber'];
				$query = "UPDATE cards
					SET cardnumber = '$cardnumber',
						changed_cardID = '$countChangedCardId'
					WHERE UID = $uid;";
			}
		}
		else {
			if(count($userCard)) {
				//cardnumber exists, but user deleted that
				$query = "DELETE FROM cards WHERE UID = $uid";
			}
			else {
				//No Cardnumber exists and User did not enter one
				return '';
			}
		}

		return $query;
	}

	/**
	 * Creates a Query changing the Password of a User depending on
	 * the Userinput given from the Change-User-Dialog
	 * @return String The Query that changes the Data in the Database
	 */
	protected function passwordQueryCreate($uid) {

		$query = '';

		if($_POST['passwordChange'] == 'true') {
			if(!empty($_POST['password'])) {
				$query = sprintf('password = "%s",', hash_password($_POST['password']));
			}
			else {
				$query = sprintf('password = "%s",', hash_password(''));
			}
		}

		return $query;
	}

	/**
	 * Creates a Query changing the Groups of the User to the Input
	 *
	 * Fetches the group-Ids from $_POST['groups']
	 *
	 * @param integer $uid The Userid of which Groups to change
	 */
	protected function groupQueryCreate($uid) {

		$query = '';

		if(!empty($_POST['groups'])) {
			$existingGroups = $this->groupsOfUserGet($uid);
			$query .= $this->groupAddQueryCreate($uid, $existingGroups);
			$query .= $this->groupDeleteQueryCreate($uid, $existingGroups);
		}

		return $query;
	}

	/**
	 * Creates a Query that Updates the Tables belonging to the Kuwasys-Module
	 *
	 * Dies displaying a Message on Error
	 */
	protected function userChangeKuwasysData($userId) {

		if($this->_acl->moduleGet('root/administrator/Kuwasys')) {

			$this->userChangeKuwasysDataInputCheck();

			$classes = $this->classesOfUserGet($userId);
			$flatClasses = ArrayFunctions::arrayColumn(
				$classes, 'unitId', 'ID');

			$this->classesChangeDeleteDeleted($userId, $flatClasses);
			$this->classesChangeAddMissing($userId, $flatClasses);
		}
	}

	/**
	 * Checks if the Kuwasys-Input is correct
	 *
	 * Dies displaying a Message in Error
	 */
	protected function userChangeKuwasysDataInputCheck() {

		if(!isset($_POST['schoolyearAndClassData']) ||
			!count($_POST['schoolyearAndClassData'])) {
			return;
		}
		foreach($_POST['schoolyearAndClassData'] as $key1 => $class1) {
			foreach($_POST['schoolyearAndClassData'] as $key2 =>$class2) {
				if($class1['classId'] == $class2['classId'] &&
					$key1 !== $key2) {
					die(json_encode(array('value' => 'error',
						'message' => 'Der Benutzer kann nicht zweimal gleichzeitig in derselben Klasse sein!')));
				}
			}
		}
	}

	/**
	 * Adds the Classes that were added in the Change-User-dialog
	 *
	 * @param  string $userId          The User-ID
	 * @param  array  $existingClasses A flattened array of classes that the
	 * User already has
	 */
	protected function classesChangeAddMissing($userId, $existingClasses) {

		if(!isset($_POST['schoolyearAndClassData']) ||
				!count($_POST['schoolyearAndClassData'])) {
			return;
		}

		$stmtAdd = $this->_pdo->prepare('INSERT INTO
			jointUsersInClass (UserID, ClassID, statusId) VALUES
			(:id, :classId, :statusId)');

		foreach($_POST['schoolyearAndClassData'] as $join) {
			if(!isset($existingClasses[$join['classId']]) ||
				$existingClasses[$join['classId']] != $join['statusId']) {
				$stmtAdd->execute(array(
					':id' => $userId,
					':classId' => $join['classId'],
					':statusId' => $join['statusId']
				));
			}
		}
	}

	/**
	 * Deletes the Classes that were removed in the Change-User-dialog
	 *
	 * @param  string $userId          The User-ID
	 * @param  array  $existingClasses A flattened array of classes that the
	 * User already has
	 */
	protected function classesChangeDeleteDeleted($userId, $existingClasses) {

		if(isset($_POST['schoolyearAndClassData'])) {
			$flatClassInput = ArrayFunctions::arrayColumn(
				$_POST['schoolyearAndClassData'], 'statusId', 'classId');
		}
		else {
			$flatClassInput = array();
		}

		$stmtDelete = $this->_pdo->prepare('DELETE FROM
			jointUsersInClass WHERE UserID = :id AND ClassID = :classId');

		foreach($existingClasses as $exClassId => $exStatusId) {
			if(!isset($flatClassInput[$exClassId]) ||
				$flatClassInput[$exClassId] != $exStatusId) {
				$stmtDelete->execute(array(
					':id' => $userId,
					':classId' => $exClassId
				));
			}
		}
	}

	/**
	 * Fetches the Groups of one User and returns them
	 *
	 * @param  integer $userId
	 * @return Array
	 */
	protected function groupsOfUserGet($userId) {

		return TableMng::query("SELECT g.ID FROM Groups g
			JOIN UserInGroups uig ON g.ID = uig.groupId
			WHERE uig.userId = $userId");
	}

	/**
	 * Creates a Query that adds Groups to a specific User
	 * @param  integer $userId
	 * @param  Array $existingGroups
	 * @return string contains multiple Queries, SQL-ready separated with
	 * Semicolon
	 */
	protected function groupAddQueryCreate($userId, $existingGroups) {

		$query = '';

		foreach($_POST['groups'] as $group) {
			//if UserInGroup is not already in Db, add it
			if(array_search($group,
				ArrayFunctions::arrayColumn($existingGroups, 'ID'))
					=== false) {
				$query .= "INSERT INTO UserInGroups (userId, groupId)
					VALUES ($userId, $group);";
			}
		}

		return $query;
	}

	/**
	 * Creates a Query that removes the User from specific Groups
	 *
	 * @param  integer $userId
	 * @param  Array $existingGroups
	 * @return string contains multiple Queries separated with semicolon
	 */
	protected function groupDeleteQueryCreate($userId, $existingGroups) {

		$query = '';

		foreach(ArrayFunctions::arrayColumn($existingGroups, 'ID') as
			$exGroup) {
			if(array_search($exGroup, $_POST['groups']) === false) {
				$query .= "DELETE FROM UserInGroups WHERE userId = $userId
					AND groupId = $exGroup;";
			}
		}

		return $query;
	}

	/**
	 * Just a Hotfix, should be refactored later on
	 */
	protected function submoduledeletedUserShowPdfExecute() {

		TableMng::sqlEscape($_GET['pdfId']);
		$fileId = $_GET['pdfId'];
		$deleter = new UserDelete();
		$deleter->showPdfOfDeletedUser($fileId);
	}

	/**
	 * Allows the User to Import the Csv-Files of a User
	 */
	protected function submoduleUserCsvImportExecute() {

		require_once 'UserCsvImport.php';

		if(count($_FILES)) {
			$importer = new UserCsvImport();
			$importer->execute($this->_dataContainer);
		}
		else {
			$this->displayTpl('importCsvFile.tpl');
		}
	}

	/**===============================================================**
	 * Searches for the given username and gives a lsit of suggestions *
	 **===============================================================**/
	protected function submoduleJsSearchForUsernameExecute() {

		$limit = 5;
		$this->levenshteinFunctionAddToSqlIfNotExists();

		$data = array();
		$stmt = $this->_pdo->prepare('SELECT u.username AS username
			FROM users u
			JOIN usersInGradesAndSchoolyears uigs ON u.ID = uigs.userId
			WHERE uigs.schoolyearId = @activeSchoolyear
			ORDER BY levenshtein_ratio(:name, username) DESC
			LIMIT 0, 10');
		$stmt->execute(array('name' => $_GET['term']));

		while($el = $stmt->fetchColumn()) {
			$data[] = $el;
		}

		die(json_encode($data));
	}

	/**
	 * Adds the levenshtein-Function to MySQL if it not exists
	 */
	protected function levenshteinFunctionAddToSqlIfNotExists() {

		try {
			$stmt = $this->_pdo->query('SHOW FUNCTION STATUS WHERE Name = "levenshtein"');

			if($stmt->fetch() === FALSE) {

				$stmt = $this->_pdo->query(
					'CREATE FUNCTION `levenshtein`( s1 text, s2 text) RETURNS int(11)
					    DETERMINISTIC
					BEGIN
					    DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
					    DECLARE s1_char CHAR;
					    DECLARE cv0, cv1 text;
					    SET s1_len = CHAR_LENGTH(s1), s2_len = CHAR_LENGTH(s2), cv1 = 0x00, j = 1, i = 1, c = 0;
					    IF s1 = s2 THEN
					      RETURN 0;
					    ELSEIF s1_len = 0 THEN
					      RETURN s2_len;
					    ELSEIF s2_len = 0 THEN
					      RETURN s1_len;
					    ELSE
					      WHILE j <= s2_len DO
					        SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1;
					      END WHILE;
					      WHILE i <= s1_len DO
					        SET s1_char = SUBSTRING(s1, i, 1), c = i, cv0 = UNHEX(HEX(i)), j = 1;
					        WHILE j <= s2_len DO
					          SET c = c + 1;
					          IF s1_char = SUBSTRING(s2, j, 1) THEN
					            SET cost = 0; ELSE SET cost = 1;
					          END IF;
					          SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost;
					          IF c > c_temp THEN SET c = c_temp; END IF;
					            SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1;
					            IF c > c_temp THEN
					              SET c = c_temp;
					            END IF;
					            SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1;
					        END WHILE;
					        SET cv1 = cv0, i = i + 1;
					      END WHILE;
					    END IF;
					    RETURN c;
  						END;
  						CREATE FUNCTION `levenshtein_ratio`( s1 text, s2 text ) RETURNS int(11)
  						    DETERMINISTIC
  						BEGIN
  						    DECLARE s1_len, s2_len, max_len INT;
  						    SET s1_len = LENGTH(s1), s2_len = LENGTH(s2);
  						    IF s1_len > s2_len THEN
  						      SET max_len = s1_len;
  						    ELSE
  						      SET max_len = s2_len;
  						    END IF;
  						    RETURN ROUND((1 - LEVENSHTEIN(s1, s2) / max_len) * 100);
  						  END
				');
				$stmt->execute();
			}

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not add the levenshtein-Function to' .
					' the Database!') . $e->getMessage())));
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	protected $userManager;
	protected $userInterface;
	protected $userProcessing;
	protected $messages;
	protected $_interface;
	protected $_dataContainer;
	protected $_pdo;

	protected static $invalid = array('Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d',
		'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A',
		'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C',
		'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
		'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O',
		'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'à'=>'a',
		'á'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e',
		'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
		'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ø'=>'o',
		'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
		'Ŕ'=>'R', 'ŕ'=>'r');

	protected static $registerRules = array(
		'forename' => array(
			'required|min_len,2|max_len,64',
			'sql_escape',
			'Vorname'),
		'name' => array(
			'required|min_len,3|max_len,64',
			'sql_escape',
			'Nachname'),
		'username' => array(
			'min_len,3|max_len,64',
			'sql_escape',
			'Benutzername'),
		'password' => array(
			'min_len,3|max_len,64',
			'sql_escape',
			'Passwort'),
		'passwordRepeat' => array(
			'min_len,3|max_len,64',
			'sql_escape','wiederholtes Passwort'),
		'email' => array(
			'valid_email|min_len,3|max_len,64',
			'sql_escape',
			'Email'),
		'telephone' => array(
			'min_len,3|max_len,64',
			'sql_escape',
			'Telefonnummer'),
		'birthday' => array(
			'max_len,10',
			'sql_escape',
			'Geburtstag'),
		'pricegroupId' => array(
			'numeric',
			'sql_escape',
			'PreisgruppenId'),
		'schoolyearId' => array(
			'numeric',
			'sql_escape',
			'SchuljahrId'),
		'gradeId' => array(
			'numeric',
			'sql_escape',
			'KlassenId'),
		'cardnumber' => array(
			'exact_len,10',
			'sql_escape',
			'Kartennummer'),
		'credits' => array(
			'numeric|min_len,1|max_len,5',
			'sql_escape',
			'Guthaben'),
		'isSoli' => array(
			'boolean',
			'sql_escape',
			'ist-Soli-Benutzer')
	);

	protected static $_changeRules = array(
		'ID' => array(
			'required|numeric|min_len,1|max_len,10',
			'sql_escape',
			'ID'),
		'forename' => array(
			'required|min_len,2|max_len,64',
			'sql_escape',
			'Vorname'),
		'name' => array(
			'required|min_len,3|max_len,64',
			'sql_escape',
			'Nachname'),
		'username' => array(
			'min_len,3|max_len,64',
			'sql_escape',
			'Benutzername'),
		'email' => array(
			'valid_email|min_len,3|max_len,64',
			'sql_escape',
			'Email'),
		'telephone' => array(
			'min_len,3|max_len,64',
			'sql_escape',
			'Telefonnummer'),
		'birthday' => array(
			'isodate|max_len,10',
			'sql_escape',
			'Geburtstag'),
		'pricegroupId' => array(
			'numeric',
			'sql_escape',
			'PreisgruppenId'),
		'cardnumber' => array(
			'exact_len,10',
			'sql_escape',
			'Kartennummer'),
		'credits' => array(
			'numeric|min_len,1|max_len,5',
			'sql_escape',
			'Guthaben'),
		'isSoli' => array(
			'boolean',
			'sql_escape',
			'ist-Soli-Benutzer')
	);

}

?>

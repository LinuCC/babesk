<?php

require_once 'AdminUserInterface.php';
require_once 'AdminUserProcessing.php';
require_once 'UsernameAutoCreator.php';
require_once PATH_ACCESS . '/CardManager.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_INCLUDE . '/Module.php';
require_once 'UserDisplayAll.php';
require_once 'UserDelete.php';


class User extends Module {
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

		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 'addUser': //register user
					$this->register();
					break;
				case 'changeUser':
					$this->change();
					break;
				case 2: //show the users
					$displayer = new UserDisplayAll($this->_smarty);
					$displayer->displayAll();
					break;
				case 'fetchUserdata':
					$displayer = new UserDisplayAll($this->_smarty);
					$displayer->fetchUsersOrganized();
					break;
				case 'fetchUsercolumns':
					$displayer = new UserDisplayAll($this->_smarty);
					$displayer->fetchShowableColumns();
					break;
				case 'deleteUser': //delete the user
					$deleter = new UserDelete($this->_smarty);
					$deleter->deleteFromDb();
					break;
				case 4:
					$this->search();
					break;
				case 5:
					$this->userCreateUsernames();
					break;
				case 6:
					$this->usernamesRemoveSpecialChars();
					break;
				case 7:
					$this->deletePdf();
					break;
				default:
					die('wrongActionValue');
			}
		}
		else if ('GET' == $_SERVER['REQUEST_METHOD']
			&& isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 'changeUserDisplay':
					$this->changeDispay();
					break;
				case 'deleteUser': //delete the user
					$deleter = new UserDelete($this->_smarty);
					$deleter->deleteFromDb();
					break;
			}
		}
		else {
			$this->userInterface->ShowSelectionFunctionality();
		}
	}
	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////
	protected function entryPoint ($dataContainer) {
		defined('_AEXEC') or die('Access denied');
		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->userInterface = new AdminUserInterface($this->relPath);
		$this->_interface = $this->userInterface;
		$this->userProcessing = new AdminUserProcessing($this->userInterface);
		$this->messages = array(
				'error' => array('no_id' => 'ID nicht gefunden.'));
		$this->_smarty = $dataContainer->getSmarty();
	}

	/**
	 * Registers a user. Requests should come from Ajax
	 *
	 * Either shows the Register-form or, if POST-Data send, tries to add the
	 * Data-input to the Database as a new User
	 */
	protected function register() {

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
				$priceGroups = $this->dbRowsGetArranged(
					'SELECT ID, name FROM groups');

			} catch (Exception $e) {
				$priceGroups = array();
			}

			$grades = $this->dbRowsGetArranged(
				'SELECT ID, CONCAT(gradeValue, "-", label) AS name
				FROM grade');
			//ORDER BY active: Get active schoolyear to top = selected
			$schoolyears = $this->dbRowsGetArranged(
				'SELECT ID, label AS name FROM schoolYear
				ORDER BY active DESC;');

			//---display
			$this->userInterface->ShowRegisterForm($priceGroups, $grades,
				$schoolyears);

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

		$_POST = $gump->sanitize($_POST);
		$_POST['isSoli'] = (isset($_POST['isSoli'])
			&& $_POST['isSoli'] == 'true');

		try {
			$gump->rules(self::$registerRules);
			$gump->html_decode_sql_escape_by_ruleset(true,
				self::$registerRules);
			//Set none-filled-out formelements to be at least a void string, for
			//easier processing
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
		$first_passwd = '0';

		$password = (!empty($_POST['password']))
			? hash_password($_POST['password']) : '';

		//Querys
		$schoolyearQuery = '';
		$cardnumberQuery = '';
		$gradeQuery = '';

		TableMng::getDb()->autocommit(false);

		try {
			//check for additional Querys needed
			if(!empty($_POST['schoolyearId'])) {
				$schoolyearQuery = "INSERT INTO jointUsersInSchoolYear
				(UserID, SchoolYearID) VALUES (@uid, $_POST[schoolyearId]);";
			}
			if(!empty($_POST['cardnumber'])) {
				$cardnumberQuery = "INSERT INTO cards (cardnumber, UID)
					VALUES ('$_POST[cardnumber]', '@uid');";
			}
			if(!empty($_POST['gradeId'])) {
				$gradeQuery = "INSERT INTO jointUsersInGrade (UserID, GradeID)
					VALUES (@uid, $_POST[gradeId]);";
			}

			TableMng::query("INSERT INTO users
				(forename, name, username, password, email, telephone, birthday,
					first_passwd, locked, GID, credit, soli)
				VALUES ('$_POST[forename]', '$_POST[name]', '$_POST[username]',
					'$password', '$_POST[email]', '$_POST[telephone]',
					'$_POST[birthday]', $first_passwd, $locked,
					'$_POST[pricegroupId]', '$_POST[credits]', '$_POST[isSoli]'
					);
				SET @uid = LAST_INSERT_ID();
				$schoolyearQuery
				$cardnumberQuery
				$gradeQuery
				",false, true);

		} catch (Exception $e) {
			die($e->getMessage());
		}

		TableMng::getDb()->autocommit(true);

	}

	protected function search() {
		if (isset ($_POST['user_search'])) {
			try {
				$userID = $this->cardManager->getUserID($_POST['user_search']);
			} catch (Exception $e) {
				 $userID =  $e->getMessage();
			}
			if ($userID == 'MySQL returned no data!') {
			try {
				$userID = $this->userManager->getUserID($_POST['user_search']);
			} catch (Exception $e) {
				$this->userInterface->dieError("Benutzer nicht gefunden!");
			}

		}

			$this->changeDispay($userID);

			break;
		}

		if (!isset($_POST['id'], $_POST['forename'], $_POST['name'], $_POST['username'], $_POST['credits'], $_POST[
		'gid'])) {
		$this->changeDispay($_GET['ID']);
		} else {
			$soli = 0;
			if (isset($_POST['soliAccount'])) {
				$soli = 1;
			}
			if (isset($_POST['lockAccount'])) {
				$this->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
						'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
						'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 1, @$_POST[
						'cardnumber'], $soli,$_POST['class']);
			} else {
				$this->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
						'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
						'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 0, @$_POST[
						'cardnumber'], $soli,$_POST['class']);
			}
		}
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
	protected function dbRowsGetArranged($query, $key = 'ID',
		$value = 'name') {

		$rearranged = array();
		$rows = TableMng::query($query, true);


		if(!empty($rows)) {
			foreach($rows as $row) {
				$rearranged[$row['ID']] = $row['name'];
			}
		}
		return $rearranged;
	}

	protected function userCreateUsernames () {
		if (isset ($_POST ['confirmed'])) {
			$creator = new UsernameAutoCreator ();
			$scheme = new UsernameScheme ();
			$scheme->templateAdd (UsernameScheme::Forename);
			$scheme->stringAdd ('.');
			$scheme->templateAdd (UsernameScheme::Name);
			$creator->usersSet ($this->userManager->getAllUsers());
			$creator->schemeSet ($scheme);
			$users = $creator->usernameCreateAll ();
			foreach ($users as $user) {
				///@FIXME: PURE EVIL DOOM LOOP OF LOOPING SQL-USE. Kill it with fire.
				$this->userManager->alterUsername ($user ['ID'], $user ['username']);
			}
			$this->userInterface->dieMsg ('Die Benutzernamen wurden erfolgreich geÃ¤ndert');
		}
		else {
			$this->userInterface->showConfirmAutoChangeUsernames ();
		}
	}

	protected function usernamesRemoveSpecialChars () {
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
			$this->userInterface->showRemoveSpecialCharsFromUsername ($rows);
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
				(SELECT CONCAT(g.gradeValue, g.label) AS class
					FROM jointUsersInGrade uig
					LEFT JOIN grade g ON uig.gradeId = g.ID
					LEFT JOIN jointGradeInSchoolYear gisy ON gisy.gradeId = g.ID
					LEFT JOIN schoolYear sy ON gisy.schoolyearId = sy.ID
					WHERE uig.userId = u.ID) AS class
				FROM users u', true);

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
	 * Enter description here ...
	 * @param string (numeric) $uid The ID of the User
	 */
	function changeDispay() {

		$uid = $_GET['ID'];
		TableMng::sqlEscape($uid);

		try {
			TableMng::query('SET @activeSchoolyear :=
				(SELECT ID FROM schoolYear WHERE active = "1");');
			$user = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT g.ID
					FROM jointUsersInGrade uig
					LEFT JOIN grade g ON uig.gradeId = g.ID
					WHERE uig.userId = u.ID) AS gradeId
				FROM users u WHERE `ID` = %s', $uid), true);
			$cardnumber = TableMng::query(
				"SELECT cardnumber FROM cards WHERE UID = $uid
				", true);
			$priceGroups = $this->dbRowsGetArranged(
					'SELECT ID, name FROM groups');
			$grades = $this->dbRowsGetArranged(
				'SELECT ID, CONCAT(gradeValue, "-", label) AS name
				FROM grade', true);
			$schoolyears = TableMng::query(
				"SELECT ID, label AS name, (
					SELECT COUNT(*) AS count FROM jointUsersInSchoolYear uisy
					 WHERE sy.ID = uisy.SchoolYearID
					AND uisy.UserID = $uid) AS isUserIn
				 FROM schoolYear sy
				ORDER BY active DESC;", true);

			$cardnumber = (!empty($cardnumber)) ?
				$cardnumber[0]['cardnumber'] : '';

		} catch (Exception $e) {
			$this->userInterface->dieError($e->getMessage());
		}
		$this->userInterface->ShowChangeUser($user[0], $cardnumber, $priceGroups, $grades, $schoolyears);
	}

	/**
	 * Handles the Input from the ChangeUser-Form and changes the data
	 */
	protected function change() {

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

		if(!empty($_POST['schoolyearIds'])
			&& $_POST['schoolyearIds'] !== 'null') {
			$_POST['schoolyearIds'] =
				json_decode(html_entity_decode($_POST['schoolyearIds']));
		}
		else { //Error; User did not even select "no Schoolyears"
			die(json_encode(array(
				'value' => 'inputError',
				'message' => array('Es wurde nichts bei den Schuljahren ausgewählt; sollen dem Nutzer keine Schuljahre  zugewiesen sein, wählen sie bitte die entsprechende Option aus'))));
		}

		if(count($_POST['schoolyearIds']) > 1 && in_array('NONE',
			$_POST['schoolyearIds'])) {
			die(json_encode(array(
				'value' => 'inputError',
				'message' => array('Schuljahre können nicht gleichzeitig mit "keinem Schuljahr" ausgewählt sein!'))));
		}

		$_POST['credits'] = str_replace(',', '.', $_POST['credits']);
	}

	/**
	 * Cleans the Input, decodes HTML-entities and mysql-Encapes it and checks
	 * the input
	 */
	protected function changeCleanAndCheckInput() {

		require_once PATH_INCLUDE . '/gump.php';

		$gump = new GUMP();

		try {
			$_POST = $gump->sanitize($_POST);
			$gump->rules(self::$_changeRules);

			//Set none-filled-out formelements to be at least a void string,
			//for easier processing
			$_POST = $gump->voidVarsToStringByRuleset(
				$_POST, self::$registerRules);

			//validate the elements
			if($validatedData = $gump->run($_POST)) {
				//escapes all of the elements in the ruleset
				$_POST = $gump->html_decode_sql_escape_by_ruleset($_POST,
					self::$_changeRules);
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
		$schoolyearQuery = '';
		$cardnumberQuery = '';
		$gradeQuery = '';
		$passwordQuery = '';

		TableMng::getDb()->autocommit(false);

		try {
			//check for additional Querys needed
			$schoolyearQuery = $this->schoolyearQueryCreate($uid);
			$cardnumberQuery = $this->cardsQueryCreate($uid);
			$gradeQuery = $this->gradeQueryCreate($uid);
			$passwordQuery = $this->passwordQueryCreate($uid);

			TableMng::query("UPDATE users
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
				$schoolyearQuery
				$cardnumberQuery
				$gradeQuery
				",false, true);


		} catch (Exception $e) {
			die($e->getMessage());
		}

		TableMng::getDb()->autocommit(true);
	}

	/**
	 * Creates a Query changing the schoolyears a User is present in
	 * depending on the Userinput given from the Change-User-Dialog
	 * @return String The Query that changes the Data in the Database
	 */
	protected function schoolyearQueryCreate($uid) {

		$query = '';

		$existingSchoolyears = TableMng::query(
			"SELECT * FROM jointUsersInSchoolYear WHERE UserID = $uid", true);

		foreach($_POST['schoolyearIds'] as $schoolyearId) {
			if($schoolyearId === 'NONE') {
				// User has chosen "no Schoolyear". so delete all entries
				// having UserID $uid
				$query = "DELETE FROM jointUsersInSchoolYear
					WHERE UserID = $uid;";
				return $query; //No need to process other elements
			}
			foreach($existingSchoolyears as $key => $eSchoolyear) {
				if($eSchoolyear['SchoolYearID'] == $schoolyearId) {
					// User is already in Schoolyear
					unset($existingSchoolyears[$key]);
					continue 2;
				}
			}
			//schoolyear does not exist in the Database yet
			$query .= "INSERT INTO jointUsersInSchoolYear
				(UserID, SchoolYearID) VALUES ($uid, $schoolyearId);";
		}

		//Existing Schoolyears that were not unset by the loop before got
		//unselected by the user. Remove them from the Db
		foreach($existingSchoolyears as $key => $eSchoolyear) {
			$query .= "DELETE FROM jointUsersInSchoolYear
				WHERE ID = $eSchoolyear[ID];";
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
				"SELECT * FROM cards WHERE UID = $uid", true);

		if(!empty($_POST['cardnumber'])) {

			if(!count($userCard)) {
				$query = "INSERT INTO cards (cardnumber, UID)
					VALUES ($_POST[cardnumber], $uid);";
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
	 * Creates a Query changing the Grade a User is present in depending on
	 * the Userinput given from the Change-User-Dialog
	 * @return String The Query that changes the Data in the Database
	 */
	protected function gradeQueryCreate($uid) {

		$query = '';
		//The Grade of the User before the change
		$userGrade = TableMng::query(
			"SELECT * FROM jointUsersInGrade WHERE UserID = $uid", true);

		if(!empty($_POST['gradeId'])) {

			if(!count($userGrade)) {
				//User is in no grade
				$query = "INSERT INTO jointUsersInGrade (UserID, GradeID)
					VALUES ($uid, $_POST[gradeId]);";
			}
			else if($userGrade[0]['GradeID'] == $_POST['gradeId']) {
				//nothing changed
				return '';
			}
			else {
				//User got switched to another grade
				$query = "UPDATE jointUsersInGrade
					SET GradeID = $_POST[gradeId]
					WHERE UserID = $uid;";
			}
		}
		else {
			if(count($userGrade)) {
				//grade exists, but user deleted that
				$query = "DELETE FROM jointUsersInGrade WHERE UserID = $uid";
			}
			else {
				//No Grade exists for the User and User did not enter one
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

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	protected $cardManager;
	protected $userManager;
	protected $userInterface;
	protected $userProcessing;
	protected $messages;
	protected $_interface;

	protected static $invalid = array('Å '=>'S', 'Å¡'=>'s', 'Ä�'=>'D', 'Ä‘'=>'d', 'Å½'=>'Z', 'Å¾'=>'z', 'ÄŒ'=>'C', 'Ä�'=>'c', 'Ä†'=>'C', 'Ä‡'=>'c', 'Ã€'=>'A', 'Ã�'=>'A', 'Ã‚'=>'A', 'Ãƒ'=>'A', 'Ã„'=>'A', 'Ã…'=>'A', 'Ã†'=>'A', 'Ã‡'=>'C', 'Ãˆ'=>'E', 'Ã‰'=>'E', 'ÃŠ'=>'E', 'Ã‹'=>'E', 'ÃŒ'=>'I', 'Ã�'=>'I', 'ÃŽ'=>'I', 'Ã�'=>'I', 'Ã‘'=>'N', 'Ã’'=>'O', 'Ã“'=>'O', 'Ã”'=>'O', 'Ã•'=>'O', 'Ã–'=>'O', 'Ã˜'=>'O', 'Ã™'=>'U', 'Ãš'=>'U', 'Ã›'=>'U', 'Ã�'=>'Y', 'Ãž'=>'B', 'Ã '=>'a', 'Ã¡'=>'a', 'Ã¢'=>'a', 'Ã£'=>'a', 'Ã¥'=>'a', 'Ã¦'=>'a', 'Ã§'=>'c', 'Ã¨'=>'e', 'Ã©'=>'e', 'Ãª'=>'e', 'Ã«'=>'e', 'Ã¬'=>'i', 'Ã­'=>'i', 'Ã®'=>'i', 'Ã¯'=>'i', 'Ã°'=>'o', 'Ã±'=>'n', 'Ã²'=>'o', 'Ã³'=>'o', 'Ã´'=>'o', 'Ãµ'=>'o', 'Ã¸'=>'o', 'Ã¹'=>'u', 'Ãº'=>'u', 'Ã»'=>'u', 'Ã½'=>'y', 'Ã½'=>'y', 'Ã¾'=>'b', 'Ã¿'=>'y', 'Å”'=>'R', 'Å•'=>'r');

	protected static $registerRules = array(
		'forename' => array('required|min_len,2|max_len,64', '', 'Vorname'),
		'name' => array('required|min_len,3|max_len,64', '', 'Nachname'),
		'username' => array('min_len,3|max_len,64', '', 'Benutzername'),
		'password' => array('min_len,3|max_len,64', '', 'Passwort'),
		'passwordRepeat' => array('min_len,3|max_len,64', '',
			'wiederholtes Passwort'),
		'email' => array('valid_email|min_len,3|max_len,64', '', 'Email'),
		'telephone' => array('min_len,3|max_len,64', '', 'Telefonnummer'),
		'birthday' => array('max_len,10', '', 'Geburtstag'),
		'pricegroupId' => array('numeric', '', 'PreisgruppenId'),
		'schoolyearId' => array('numeric', '', 'SchuljahrId'),
		'gradeId' => array('numeric', '', 'KlassenId'),
		'cardnumber' => array('exact_len,10', '', 'Kartennummer'),
		'credits' => array('numeric|min_len,1|max_len,5', '', 'Guthaben'),
		'isSoli' => array('boolean', '', 'ist-Soli-Benutzer'));

	protected static $_changeRules = array(
		'ID' => array('required|numeric|min_len,1|max_len,10', '', 'ID'),
		'forename' => array('required|min_len,2|max_len,64', '', 'Vorname'),
		'name' => array('required|min_len,3|max_len,64', '', 'Nachname'),
		'username' => array('min_len,3|max_len,64', '', 'Benutzername'),
		'email' => array('valid_email|min_len,3|max_len,64', '', 'Email'),
		'telephone' => array('min_len,3|max_len,64', '', 'Telefonnummer'),
		'birthday' => array('isoBirthday|max_len,10', '', 'Geburtstag'),
		'pricegroupId' => array('numeric', '', 'PreisgruppenId'),
		'schoolyearId' => array('numeric', '', 'SchuljahrId'),
		'gradeId' => array('numeric', '', 'KlassenId'),
		'cardnumber' => array('exact_len,10', '', 'Kartennummer'),
		'credits' => array('numeric|min_len,1|max_len,5', '', 'Guthaben'),
		'isSoli' => array('boolean', '', 'ist-Soli-Benutzer'));

}

?>

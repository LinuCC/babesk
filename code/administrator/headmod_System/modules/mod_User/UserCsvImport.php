<?php

require_once PATH_INCLUDE . '/CsvImportTableData.php';

class UserCsvImport extends CsvImportTableData {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the UserCsvImport
	 *
	 * @param  DataContainer $dataContainer Contains data needed by the Class
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->arrayDataInit();
		$this->_presetPassword = $this->presetPasswordGet();
		parent::execute($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * The Entrypoint of the Execution of the Importer
	 *
	 * @param  DataContainer $dataContainer Contains data needed by the Class
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->_acl = $dataContainer->getAcl();
		list($this->_isBabeskEnabled) = $this->enabledHeadmodulesCheck(
			array('Babesk'));
	}

	protected function arrayDataInit() {

		$this->_targetColumns = array(
			'forename' => _g('Forename'),
			'name' => _g('Surname'),
			'username' => _g('Username'),
			'birthday' => _g('Birthday'),
			'email' => _g('Email-Address'),
			'password' => _g('Password'),
			'schoolyear' => _g('Schoolyear'),
			'grade' => _g('Grade'),
			'credits' => _g('Credits'),
			'pricegroupId' => _g('PricegroupId'),
			'soli' => _g('is Soli')
		);

		$this->_gumpRules = array(
			'forename' => array(
				'required|alpha_dash_space|min_len,2|max_len,64',
				'', _g('Forename')
			),
			'name' => array(
				'required|alpha_dash_space|min_len,2|max_len,64',
				'', _g('Surname')
			),
			'username' => array('min_len,2|max_len,64', '', _g('Username')),
			'birthday' => array('isodate', '', _g('Birthday')),
			'email' => array('email', '', _g('Email-Address')),
			'password' => array('', '', _g('Password')),
			'schoolyear' => array('min_len,2|max_len,64', '', _g('Schoolyear')),
			'grade' => array('min_len,2|max_len,24', '', _g('Grade')),
			'credits' => array(
				'numeric|min_len,1|max_len,5', '', _g('Credits')
			),
			'soli' => array('boolean', '', _g('is Soli'))
		);

		$this->inputRulesBabeskActivationCheck();
	}

	protected function inputRulesBabeskActivationCheck() {

		if(!$this->_isBabeskEnabled) {
			$deact = _g('Babesk is deactivated!');
			$this->_gumpRules['pricegroup'] = array(
				'disallowed,' . $deact, '', _g('Pricegroups'));
			$this->_gumpRules['credits'] = array(
				'disallowed,' . $deact, '', _g('Credits'));
			$this->_gumpRules['soli'] = array(
				'disallowed,' . $deact, '', _g('Soli'));
		}
	}

	protected function check() {

		parent::check();
	}

	protected function dataPrepare() {

		$this->missingValuesAddAsVoidString();
		$this->schoolyearIdsAppendToColumns();
		$this->usergroupIdsAppendToColumns();
		$this->gradeIdsAppendToColumns();
		if($this->_isBabeskEnabled) {
			$this->pricegroupIdsAppendToColumns();
		}
	}

	/**
	 * Uploads the UserCsv-Data to the Server.
	 */
	protected function dataCommit() {

		$this->dataPrepare();

		$stmt = $this->_pdo->prepare(
			'INSERT INTO SystemUsers (forename, name, username, birthday, email,
				GID, credit, soli, password)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
			');

		$this->additionalUserQuerysInit();

		foreach($this->_contentArray as $con) {
			extract($con);
			if($password == '') {
				if($this->_presetPassword != '') {
					$password = $this->_presetPassword;
				}
			}
			else {
				$password = hash_password($password);
			}
			$stmt->execute(array($forename, $name, $username, $birthday, $email, $pricegroupId, $credits, $soli, $password));
			$this->additionalUserQueriesRun($con, $this->_pdo->lastInsertId());
		}
	}

	protected function additionalUserQuerysInit() {

		$this->_stmtSchoolyearAndGrade = TableMng::getDb()->prepare(
			'INSERT INTO SystemUsersInGradesAndSchoolyears
				(userId, gradeId, schoolyearId) VALUES (?, ?, ?)');
		$this->_stmtUsergroups = $this->_pdo->prepare('INSERT INTO SystemUsersInGroups
			(userId, groupId) VALUES (:userId, :groupId);');
		$this->_noGradeId = $this->noGradeIdGet();
	}

	/**
	 * Runs additional Queries to fill other Tables with user-related data
	 *
	 * @param  array  $entry  One Row of the parsed Input of the CSV-File
	 * @param  string $userId The ID of the added User
	 */
	protected function additionalUserQueriesRun($entry, $userId) {

		$this->usersInGradesAndSchoolyearsAddQuery($entry, $userId);
		$this->userInUsergroupsAddQuery($entry, $userId);
	}

	/**
	 * Adds the newly added User to his Grade and his Schoolyear
	 *
	 * @param  array  $entry  One Row of the parsed Input of the CSV-File
	 * @param  string $userId The ID of the added User
	 */
	protected function usersInGradesAndSchoolyearsAddQuery($entry, $userId) {

		if(!empty($entry['schoolyearId'])) {

			$entry['gradeId'] = (!empty($entry['gradeId'])) ?
				$entry['gradeId'] : $this->_noGradeId;

			$this->_stmtSchoolyearAndGrade->bind_param(
				'sss', $userId, $entry['gradeId'], $entry['schoolyearId']);

			if($this->_stmtSchoolyearAndGrade->execute()) {

			}
			else {
				$this->errorDie(
					_g('Could not add the Schoolyear and the Grade to the User "%1$s" "%2$s"', $entry['forename'], $entry['name']));
			}
		}
		else if(!empty($entry['grade'])) {
			$this->errorAdd(array('type' => 'missingColumn',
				'message' => _g('The User "%1$s %2$s" has a Grade, but no Schoolyear defined! Define a Schoolyear for Users with a Grade!')));
		}
	}

	/**
	 * Adds the User to the Usergroups chosen
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  array  $entry  One Row of the parsed Input of the CSV-File
	 * @param  string $userId The ID of the added User
	 */
	protected function userInUsergroupsAddQuery($entry, $userId) {

		if(!empty($entry['usergroupIds'])) {
			$groupIds = explode('|', $entry['usergroupIds']);

			foreach($groupIds as $groupId) {
				$res = $this->_stmtUsergroups->execute(
					array('userId' => $userId, 'groupId' => $groupId));
				if(!$res) {
					$this->errorDie(
						'Could not add the User "%1$s %2$s" to the ' .
						'Usergroup with the ID "%3$s"',
						$entry['forename'],
						$entry['name'],
						$groupId);
				}
			}
		}
	}

	/**
	 * Adds Grade-IDs to the elements of they contain grade-names
	 *
	 * Dies displaying a Message on Error
	 * Uses 'grade' => <gradevalue>
	 * Adds 'gradeId' => <gradeId>
	 */
	protected function gradeIdsAppendToColumns() {

		$allGrades = TableMng::query('SELECT ID,
			CONCAT(g.gradelevel, "-", LOWER(g.label)) AS name FROM SystemGrades g');
		$flatGrades = ArrayFunctions::arrayColumn($allGrades, 'name', 'ID');

		foreach($this->_contentArray as &$con) {

			if(!empty($con['grade'])) {
				$grade = $con['grade'];

				$id = array_search(strtolower($grade), $flatGrades);
				if($id !== false) {
					$con['gradeId'] = $id;
				}
				else {
					$con['gradeId'] = $this->newGradeAddGetId($grade);
				}
			}
		}
	}

	/**
	 * Adds the not yet existing Grade to the Database and returns its ID
	 *
	 * @param  string $givenName The Gradename given (includes gradelevel and
	 *                           gradelabel)
	 * @return int               The ID of the newly added Grade
	 */
	protected function newGradeAddGetId($givenName) {

		$elements = explode('-', $givenName);

		if(isset($this->_gradesToAdd[$givenName])) {
			return $this->_gradesToAdd[$givenName];
		}
		else {
			$newGradeId = $this->gradeAddUpload($elements[0], $elements[1]);
			$this->_gradesToAdd[$givenName] = $newGradeId;
			return $newGradeId;
		}
	}

	/**
	 * Adds a Grade to the Database; Uses Prepare
	 *
	 * @param  int    $gradelevel The Gradelevel
	 * @param  string $label      The Label of the Grade
	 * @return int                The ID of the newly added Grade
	 */
	protected function gradeAddUpload($gradelevel, $label) {

		if(!isset($this->_stmtAddGrade)) {
			$this->_stmtAddGrade =$this->_pdo->prepare('INSERT INTO SystemGrades
				(gradelevel, label) VALUES (:gradelevel, :label);');
		}

		$this->_stmtAddGrade->execute(array(
			'gradelevel' => $gradelevel,
			'label' => $label
		));

		return $this->_pdo->lastInsertId();
	}

	/**
	 * Returns the ID of the "NoGrade"-Grade
	 *
	 * Dies if Grade not found or multiple Entries returned
	 *
	 * @return string The ID of the Grade
	 */
	protected function noGradeIdGet() {

		try {
			$row = TableMng::querySingleEntry('SELECT ID FROM SystemGrades
				WHERE gradelevel = 0');

		} catch(MultipleEntriesException $e) {
			$this->errorDie(_g('Multiple Grades with gradelevel "0" found!'));

		} catch (Exception $e) {
			$this->errorDie(
				_g('Could not find the ID of the "NoGrade"-Grade'));
		}

		if(!isset($row) || !count($row)) {
			$this->errorDie(
				_g('Could not find the ID of the "NoGrade"-Grade'));
		}

		return $row['ID'];
	}

	/**
	 * Adds Pricegroup-IDs to the elements if they contain pricegroup-names
	 *
	 * Dies displaying a Message on Error
	 * Uses 'pricegroup' => <pricegroupName>
	 * Adds 'pricegroupId' => <pricegroupId>
	 */
	protected function pricegroupIdsAppendToColumns() {

		$allPricegroups = TableMng::query('SELECT ID, LOWER(name) AS name
			FROM BabeskPriceGroups pg');
		$flatPricegroups = ArrayFunctions::arrayColumn(
			$allPricegroups, 'name', 'ID');

		foreach($this->_contentArray as &$con) {

			if(!empty($con['pricegroup'])) {
				$pricegroup = $con['pricegroup'];

				$id = array_search(
					strtolower($pricegroup), $flatPricegroups);
				if($id !== false) {
					$con['pricegroupId'] = $id;
				}
				else {
					$this->errorDie(_g('Could not find the Pricegroup %1$s!', $pricegroup));
				}
			}
		}
	}

	/**
	 * Adds Usergroup-IDs to the elements of they contain UsergroupPaths
	 *
	 * Dies displaying a Message on Error
	 * Uses 'usergroup' => <usergroup-Path(s)>
	 * Adds 'usergroupIds' => <usergroup-ID(s)>
	 * Multiple Paths can be separated with '|'
	 */
	protected function usergroupIdsAppendToColumns() {

		$grouproot = $this->_acl->getGrouproot();

		foreach($this->_contentArray as &$con) {

			if(!empty($con['usergroup'])) {

				$groupPaths = explode('|', $con['usergroup']);

				$con['usergroupIds'] = $this->usergroupIdsByPathsGet(
					$groupPaths, $grouproot);
			}
		}
	}

	/**
	 * Gets the Usergroup-Ids by the Paths and returns them
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  array  $paths     An array containing the Paths to the
	 * Usergroups
	 * @param  Group  $grouproot The Root-Element of the Usergroups
	 * @return string            An String containing the GroupIds separated
	 * with '|'
	 */
	protected function usergroupIdsByPathsGet($paths, $grouproot) {

		$ids = array();

		foreach($paths as $path) {

			if($group = $grouproot->groupByPathGet($path)) {
				$ids[] = $group->getId();
			}
			else {
				$this->errorDie(_g('Could not find the Usergroup by Path "%1$s"', $con['usergroup']));
			}
		}

		return implode('|', $ids);
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
				'SELECT value FROM SystemGlobalSettings
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


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_isBabeskEnabled;

	/**
	 * A Prepared Statement to add Grades And schoolyears to the User
	 * @var mysqli_stmt
	 */
	protected $_stmtSchoolyearAndGrade;

	protected $_noGradeId;

	protected $_gradesToAdd;

	protected $_presetPassword;
}


?>

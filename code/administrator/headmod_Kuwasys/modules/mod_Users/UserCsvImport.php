<?php

require_once PATH_INCLUDE . '/CsvImport.php';

class UserCsvImport extends CsvImport {

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

		$moduleroot = $dataContainer->getAcl()->getModuleroot();
		$this->enabledHeadmodulesCheck($moduleroot);
		$this->arrayDataInit();
	}

	protected function enabledHeadmodulesCheck($mr) {

		$this->_isBabeskEnabled = false;
		if(($babesk = $mr->moduleByPathGet('administrator/Babesk'))) {
			if($babesk->isEnabled()) {
				$this->_isBabeskEnabled = true;
			}
		}
	}

	protected function arrayDataInit() {

		$this->_targetColumns = array(
			'forename' => _g('Forename'),
			'name' => _g('Surname'),
			'username' => _g('Username'),
			'birthday' => _g('Birthday'),
			'email' => _g('Email-Adresse'),
			'schoolyear' => _g('Schoolyear'),
			'grade' => _g('Grade'),
			'priceclass' => _g('Priceclass'),
			'credits' => _g('Credits'),
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
			'email' => array('isodate', '', _g('Email-Adresse')),
			'schoolyear' => array('min_len,2|max_len,64', '', _g('Schoolyear')),
			'grade' => array('min_len,2|max_len,24', '', _g('Grade')),
			'pricegroup' => array('min_len,2|max_len,64', '', _g('Priceclass')),
			'credits' => array(
				'numeric|min_len,1|max_len,5', '', _g('Credits')
			),
			'soli' => array('boolean', '', _g('is Soli'))
		);

		$this->inputRulesBabeskActivationCheck();
	}

	protected function inputRulesBabeskActivationCheck() {

		if(!$this->_isBabeskEnabled) {
			$deact = 'Could not add Pricegroups, %s are deactivated!';
			$this->_gumpRules['pricegroup'][0] = 'disallowed,' .
				_g($deact, _('Pricegroups'));
			$this->_gumpRules['credits'][0] = 'disallowed,' .
				_g($deact, _('Credits'));
			$this->_gumpRules['soli'][0] = 'disallowed,' .
				_g($deact, _('Soli'));
		}
	}

	protected function check() {

		parent::check();
	}

	protected function dataPrepare() {

		$this->missingValuesAddAsVoidString();
		$this->schoolyearIdsAppendToColumns();
		$this->pricegroupIdsAppendToColumns();
		$this->gradeIdsAppendToColumns();
	}

	protected function schoolyearIdsAppendToColumns() {

		$schoolyears = $this->schoolyearsGetAll();
		foreach($this->_contentArray as &$con) {

			if(!empty($con['schoolyear'])) {
				$id = $this->schoolyearIdGetByLabel(
					$con['schoolyear'], $schoolyears);

				if($id !== false) {
					$con['schoolyearId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Schoolyear "%1$s"',
							$con['schoolyear']));
				}
			}
		}
	}

	protected function schoolyearIdGetByLabel($name, $schoolyears) {

		foreach ($schoolyears as $schoolyear) {
			if($schoolyear['label'] == $name) {
				return $schoolyear['ID'];
			}
		}

		return false;
	}

	/**
	 * adds 'pricegroupId' to the Array
	 */
	protected function pricegroupIdsAppendToColumns() {

		$allPricegroups = TableMng::query('SELECT * FROM groups');
		foreach($this->_contentArray as &$con) {

			if(!empty($con['pricegroup'])) {

				$id = $this->pricegroupIdGetByName(
					$con['pricegroup'], $allPricegroups);

				if($id !== false) {
					$con['pricegroupId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Pricegroup "%1$s"',
							$con['pricegroup']));
				}
			}
			else {
				// Field GID in Table users is required
				$con['pricegroupId'] = 0;
			}
		}
	}

	/**
	 * Returns the ID of the Pricegroup-Name given
	 *
	 * @param  string $name        The Name of the Pricegroup
	 * @param  Array  $pricegroups The Pricegroups in which to search
	 * @return string              The ID of the pricegroup or false if not
	 * found
	 */
	protected function pricegroupIdGetByName($name, $pricegroups) {

		foreach($pricegroups as $pricegroup) {
			if($schoolyear['label'] == $name) {
				return $schoolyear['ID'];
			}
		}

		return false;
	}

	/**
	 * If grades set, it adds 'gradeID' to the Array
	 */
	protected function gradeIdsAppendToColumns() {

		$allGrades = TableMng::query('SELECT ID,
			CONCAT(g.gradelevel, "-", LOWER(g.label)) AS name FROM Grades g');
		$flatGrades = ArrayFunctions::arrayColumn($allGrades, 'name', 'ID');

		foreach($this->_contentArray as &$con) {

			$grade = $con['grade'];
			if(!empty($grade)) {

				$id = array_search(strtolower($grade), $flatGrades);
				if($id !== false) {
					$con['gradeId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Grade "%1$s"', $grade));
				}
			}
		}
	}

	/**
	 * Uploads the UserCsv-Data to the Server.
	 */
	protected function dataCommit() {

		$this->dataPrepare();

		// var_dump($this->_contentArray);

		$stmt = TableMng::getDb()->prepare(sprintf(
			'INSERT INTO users (forename, name, username, birthday, email, GID, credit, soli)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?);
			'));

		$this->additionalUserQuerysInit();

		foreach($this->_contentArray as $con) {
			extract($con);
			$stmt->bind_param('ssssssss', $forename, $name, $username, $birthday, $email, $pricegroupId, $credits, $soli);
			if($stmt->execute()) {
				$this->additionalUserQueriesRun($con, $stmt->insert_id);
			}
			else {
				$this->errorDie(
					_g('Could not parse the data to the Database!') . $stmt->error);
			}
		}
		$stmt->close();
	}

	protected function additionalUserQuerysInit() {

		$this->_stmtSchoolyearAndGrade = TableMng::getDb()->prepare(
			'INSERT INTO usersInGradesAndSchoolyears
				(userId, gradeId, schoolyearId) VALUES (?, ?, ?)');
		$this->_noGradeId = $this->noGradeIdGet();
	}

	/**
	 * Runs additional Queries to fill other Tables with user-related data
	 *
	 * @param  array  $entry  One Row of the parsed Input of the CSV-File
	 * @param  string $userId The ID of the added User
	 */
	protected function additionalUserQueriesRun($entry, $userId) {

		if(!empty($entry['schoolyearId'])) {

			$entry['grade'] = (!empty($entry['grade'])) ?
				$entry['grade'] : $this->_noGradeId;

			$this->_stmtSchoolyearAndGrade->bind_param(
				'sss', $userId, $entry['grade'], $entry['schoolyearId']);

			if($this->_stmtSchoolyearAndGrade->execute()) {

			}
			else {
				$this->errorDie(
					_g('Could not add the Schoolyear and the Grade to the User "%1$s" "%2$s"', $entry['forename'], $entry['name']));
			}
		}
	}

	protected function schoolyearsGetAll() {

		$schoolyears = TableMng::query('SELECT * FROM schoolYear');

		return $schoolyears;
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
			$row = TableMng::querySingleEntry('SELECT ID FROM Grades
				WHERE gradelevel = 0');

		} catch(MultipleEntriesException $e) {
			$this->errorDie(_g('Multiple Grades with gradelevel "0" found!'));

		} catch (Exception $e) {
			$this->errorDie(
				_g('Could not find the ID of the "NoGrade"-Grade'));
		}

		return $row['ID'];
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
}


?>

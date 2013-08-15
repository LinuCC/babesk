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
		list($this->_isBabeskEnabled) = $this->enabledHeadmodulesCheck(
			array('Babesk'));
		$this->arrayDataInit();
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
			$deact = 'Could not add %1$s, %1$s are deactivated!';
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

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

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		parent::execute($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		$this->arrayDataInit();
		$mr = $dataContainer->getAcl()->getModuleroot();
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
			'username' => array('alpha_dash', '', _g('Username')),
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
	}

	protected function check() {

		parent::check();
	}

	protected function dataPrepare() {

		$this->missingValuesAddAsVoidString();
	}

	/**
	 * Uploads the UserCsv-Data to the Server.
	 */
	protected function dataCommit() {

		$this->dataPrepare();

		$stmt = TableMng::getDb()->prepare(sprintf(
			'INSERT INTO users (forename, name, username, birthday, email)
				VALUES (?, ?, ?, ?, ?);
			'));

		foreach($this->_contentArray as $con) {
			extract($con);
			$stmt->bind_param('sssss', $forename, $name, $username, $birthday, $email);
			if($stmt->execute()) {
			}
			else {
				$this->errorDie(
					_g('Could not parse the data to the Database!'));
			}
		}
		$stmt->close();
	}

	protected function schoolyearIdsAppendToColumns() {

		$schoolyears = $this->schoolyearsGetAll();
		foreach($this->_contentArray as &$con) {

			$id = $this->schoolyearIdGetByLabel(
				$con['schoolyear'], $schoolyears);

			if($id !== false) {
				$con['schoolyearId'] = $id;
			}
			else {

			}
		}
	}

	protected function schoolyearsGetAll() {

		$schoolyears = TableMng::query('SELECT * FROM schoolYear');

		return $schoolyears;
	}

	protected function schoolyearIdGetByLabel($name, $schoolyears) {

		foreach ($schoolyears as $schoolyear) {
			if($schoolyear['label'] == $name) {
				return $schoolyear['ID'];
			}
		}

		return false;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_targetColumns;

	protected $_gumpRules;

	protected $_isBabeskEnabled;
}


?>

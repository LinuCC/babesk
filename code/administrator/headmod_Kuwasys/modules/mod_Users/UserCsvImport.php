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
			'forename' => _('Forename'),
			'name' => _('Surname'),
			'username' => _('Username'),
			'birthday' => _('Birthday'),
			'email' => _('Email-Adresse'),
			'schoolyear' => _('Schoolyear'),
			'grade' => _('Grade'),
			'priceclass' => _('Priceclass'),
			'credits' => _('Credits'),
			'soli' => _('is Soli')
		);

		$this->_gumpRules = array(
			'forename' => array(
				'required|alpha_dash_space|min_len,2|max_len,64',
				'', _('Forename')
			),
			'name' => array(
				'required|alpha_dash_space|min_len,2|max_len,64',
				'', _('Surname')
			),
			'username' => array('alpha_dash', '', _('Username')),
			'birthday' => array('isodate', '', _('Birthday')),
			'email' => array('isodate', '', _('Email-Adresse')),
			'schoolyear' => array('min_len,2|max_len,64', '', _('Schoolyear')),
			'grade' => array('min_len,2|max_len,24', '', _('Grade')),
			'pricegroup' => array('min_len,2|max_len,64', '', _('Priceclass')),
			'credits' => array(
				'numeric|min_len,1|max_len,5', '', _('Credits')
			),
			'soli' => array('boolean', '', _('is Soli'))
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
					_('Could not parse the data to the Database!'));
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

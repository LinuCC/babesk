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

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function check() {

		parent::check();

	}

	protected function schoolyearsCheck() {

	}

	protected function schoolyearsGetAll() {

		$schoolyears = TableMng::query('SELECT * FROM schoolYear');
		return $schoolyears;
	}

	/**
	 * Uploads the UserCsv-Data to the Server.
	 */
	protected function dataCommit() {

		$schoolyears = $this->schoolyearsGetAll();


		$stmt = TableMng::getDb()->prepare(sprintf(
			'INSERT INTO users (forename, name, username, birthday, email)
				VALUES (?, ?);
			'));

		foreach($this->_contentArray as $con) {
			$stmt->bind_param('ss', $con['forename'], $con['name']);
			if($stmt->execute()) {
			}
			else {
				$this->errorAdd($con, 'dbUpload');
				$this->_errors['dbUpload'][] = $con;
			}
		}
		$stmt->close();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_targetColumns = array(
		'forename' => _('Vorname'),
		'name' => _('Nachname'),
		'username' => _('Benutzername'),
		'birthday' => _('Geburtstag'),
		'schoolyear' => _('Schuljahr'),
		'grade' => _('Klasse'),
		'priceclass' => _('Preisklasse'),
		'credits' => _('Guthaben'),
		'soli' => _('Ist Soli')
	);

	protected $_gumpRules = array(
		'forename' => array(
			'required|alpha_dash_space|min_len,2|max_len,64',
			'', _('Vorname')),
		'name' => array(
			'required|alpha_dash_space|min_len,2|max_len,64',
			'', _('Nachname')),
		'username' => array('alpha_dash', '', _('Benutzername')),
		'birthday' => array('isodate', '', _('Geburtstag')),
		'schoolyear' => array('isodate', '', _('Schuljahr')),
		'grade' => array('min_len,2|max_len,24', '', _('Klasse')),
		'pricegroup' => array('min_len,2|max_len,64', '', _('Preisklasse')),
		'credits' => array('numeric|min_len,1|max_len,5', '', _('Guthaben')),
		'soli' => array('boolean', '', _('Ist Soli')
	);
}


?>

<?php

require_once PATH_INCLUDE . '/CsvImportTableData.php';

class ClassesCsvImport extends CsvImportTableData {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs this Class
	 */
	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the ClassesCsvImport
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

		$this->arrayDataInit();
	}

	/**
	 * Initializes the array-Data needed describing how to handle the Columns
	 */
	protected function arrayDataInit() {

		$this->_targetColumns = array(
			'label' => _g('Label'),
			'description' => _g('Description'),
			'unit' => _g('Day'),
			'schoolyear' => _g('Schoolyear'),
			'maxRegistration' => _g('Maximum Amount of Registrations'),
			'registrationEnabled' => _g('Registration Enabled for this Class')
		);

		$this->_gumpRules = array(
			'label' => array(
				'required|alpha_dash_space|min_len,2|max_len,255',
				'', _g('Label')
			),
			'description' => array(
				'min_len,2|max_len,1024',
				'', _g('Description')
			),
			'unit' => array('min_len,2|max_len,64', '', _g('Day')),
			'schoolyear' => array(
				'min_len,2|max_len,64', '', _g('Schoolyear')),
			'maxRegistration' => array(
				'numeric|min_len,1|max_len,10', '', _g('Email-Adresse')),
			'registrationEnabled' => array(
				'numeric|min_len,1|max_len,1', '',
				_g('Registration Enabled for this Class'))
		);
	}

	/**
	 * Prepares the Data so it can be uploaded to the Database
	 */
	protected function dataPrepare() {

		$this->missingValuesAddAsVoidString();
		$this->schoolyearIdsAppendToColumns();
	}

	/**
	 * Tries to get the ID of given unitnames allowing to upload it
	 *
	 * Dies displaying a message on Error
	 * Adds the pair 'ID' => <schoolyearId> to each array-Element
	 */
	protected function unitIdsAddToColumns() {

		$units = $this->unitsGetAll();
		foreach($this->_contentArray as &$con) {

			if(!empty($con['day'])) {
				$id = $this->unitIdGetByName(
					$con['day'], $units);

				if($id !== false) {
					$con['unitId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Unit "%1$s"',
							$con['day']));
				}
			}
		}
	}

	/**
	 * Fetches all Units and returns them
	 *
	 * @return array  The fetched Units
	 */
	private function unitsGetAll() {

		$units = TableMng::query('SELECT * FROM kuwasysClassUnit');

		return $units;
	}

	/**
	 * Returns the Unit-ID of the Unit that has the Label
	 *
	 * @param  string $name  The Label of the Unit to search for
	 * @param  array  $units The Units to search in
	 * @return string        The ID if found, else false
	 */
	private function unitIdGetByName($name, $units) {

		foreach ($units as $unit) {
			if($unit['translatedName'] == $name) {
				return $unit['ID'];
			}
		}

		return false;
	}

	protected function dataCommit() {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>

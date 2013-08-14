<?php

require_once PATH_INCLUDE . '/CsvImport.php';

class ClassesCsvImport extends CsvImport {

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

		$moduleroot = $dataContainer->getAcl()->getModuleroot();
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

	protected function dataCommit() {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>

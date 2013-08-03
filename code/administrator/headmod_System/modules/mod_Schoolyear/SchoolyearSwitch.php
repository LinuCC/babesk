<?php

class SchoolyearSwitch {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($interface) {

		$this->_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($newSchoolyearId) {

		try {
			$this->uploadStart();
			$this->upload();
			$this->uploadFinish();

		} catch (Exception $e) {

			$this->_interface->dieError(_('Could not switch the Schoolyear!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function uploadStart() {

		TableMng::getDb()->autocommit(false);
	}

	protected function upload() {

		$this->incrementGradeValues();
	}

	protected function incrementGradeValues() {

		$toUpgrade = TableMng::query(
			'SELECT * FROM usersInGradesAndSchoolyears
			WHERE schoolyearId = @activeSchoolyear');

		foreach($toUpgrade as $join) {

		}
	}

	protected function uploadFinish() {

		TableMng::getDb()->commit();
		TableMng::getDb()->autocommit(true);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;
}

?>

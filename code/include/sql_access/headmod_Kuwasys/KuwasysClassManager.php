<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysClassManager extends TableManager {

		////////////////////////////////////////////////////////////////////////////////
		//Attributes
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Constructor
		////////////////////////////////////////////////////////////////////////////////
		public function __construct($interface = NULL) {
			parent::__construct('class');
		}

		////////////////////////////////////////////////////////////////////////////////
		//Getters and Setters
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Methods
		////////////////////////////////////////////////////////////////////////////////
		public function addClass ($label, $description, $maxRegistration, $regEnabled, $weekday) {
			$this->addEntry('label', $label, 'description', $description, 'maxRegistration', $maxRegistration,
					'registrationEnabled', $regEnabled, 'unitId', $weekday);
		}

		public function deleteClass ($ID) {
			$this->delEntry($ID);
		}

		public function alterClass ($ID, $label, $description, $maxRegistration, $regEnabled, $weekday) {
			$this->alterEntry($ID, 'label', $label, 'description', $description, 'maxRegistration', $maxRegistration, 'registrationEnabled', $regEnabled, 'unitId', $weekday);
		}

		public function getAllClasses () {
			return $this->getTableData();
		}

		public function getLabelOfClass ($ID) {
			$label = $this->getEntryValue($ID, 'label');
			return $label;
		}

		/**
		 * Returns the maxRegistration-Value of the class with the $classId
		 */
		public function getMaxRegOfClass ($classId) {
			$maxReg = $this->getEntryValue ($classId, 'maxRegistration');
			return $maxReg;
		}

		public function getClass ($ID) {
			$class = $this->searchEntry('ID =' . $ID);
			return $class;
		}

		public function getLastClassID () {
			$lastID = $this->getLastInsertedID();
			return $lastID;
		}

		public function getClassesByClassIdArray ($classIdArray) {

			$classes = $this->getMultipleEntriesByArray('ID', $classIdArray);
			return $classes;
		}
		////////////////////////////////////////////////////////////////////////////////
		//Implementations
		////////////////////////////////////////////////////////////////////////////////

}
?>
<?php

require_once PATH_ACCESS . '/TableManager.php';

/** This Class acts like a struct, it allows to abstract from the MySQL-table Variable names
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
abstract class KuwasysClassUnit {
	const id = 'ID';
	const name = 'name';
	const translatedName = 'translatedName';
}

/** Manages the Access to the Table kuwasysClassUnitManager of the Babesk-MySQL-Database
 *@author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class KuwasysClassUnitManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////
	public function __construct($interface = NULL) {
		parent::__construct('kuwasysClassUnit');
	}

	////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////
	/** Returns a Class-Unit with the given ID
	 * @param $id The ID of the ClassUnit to search for
	 * @return An Array with the Data of the ClassUnit
	 */
	public function unitGet ($id) {
		return $this->searchEntry (sprintf('%s = %s',KuwasysClassUnit::id, $id));
	}

	/** Returns all ClassUnits in the Table
	 * @return An array of an array, The Units and their Data
	 */
	public function unitGetAll () {
		return $this->getTableData ();
	}

	/** Returns the first found ClassUnit with the given name
	 * @param $name The Name of the Unit to search for
	 * @return An Array with the Data of the ClassUnit
	 */
	public function unitGetByName ($name) {
		return $this->searchEntry (sprintf('%s = "%s"',KuwasysClassUnit::name, $name));
	}

	/** Adds an ClassUnit-Entry to the table
	 * @param $name the Name of the ClassUnit
	 * @param $translatedName the translatedName (shown to the User) of the ClassUnit
	 */
	public function unitAdd ($name, $translatedName) {
		$this->addEntry ('name', $name, 'translatedName', $translatedName);
	}

	/** Deletes an ClassUnit from the Table
	 * @param $id The ID of the ClassUnit to delete
	 */
	public function unitDelete ($id) {
		$this->delEntry ($id);
	}

	public function unitGetMultiple ($unitIds) {
		return $this->getMultipleEntriesByArray ('ID', $unitIds);
	}
	////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////

}

?>
<?php

require_once PATH_INCLUDE . '/CsvImport.php';

/**
 * A CSV-Importer. Contains various useful methods to convert names to IDs
 *
 * Allows to convert for example schoolyear-names to schoolyearIds
 */
abstract class CsvImportTableData extends CsvImport {

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

		$this->_acl = $dataContainer->getAcl();
		parent::execute($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Tries to get the ID of given Schoolyearnames allowing to upload it
	 *
	 * Dies displaying a message on Error
	 * Adds the pair 'ID' => <schoolyearId> to each array-Element
	 */
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

	/**
	 * Fetches all Schoolyears and returns them
	 *
	 * @return array  The fetched Schoolyears
	 */
	private function schoolyearsGetAll() {

		$schoolyears = TableMng::query('SELECT * FROM SystemSchoolyears');

		return $schoolyears;
	}

	/**
	 * Returns the Schoolyear-ID of the Schoolyear that has the Label
	 *
	 * @param  string $name        The Label of the Schoolyear to search for
	 * @param  array  $schoolyears The Schoolyears to search in
	 * @return string              The ID if found, else false
	 */
	private function schoolyearIdGetByLabel($name, $schoolyears) {

		foreach ($schoolyears as $schoolyear) {
			if($schoolyear['label'] == $name) {
				return $schoolyear['ID'];
			}
		}

		return false;
	}

	/**
	 * Checks if the given Headmodules are enabled or not
	 *
	 * @param  array  $headmodules The Headmodules to check for
	 * @return array               The given Array, but each element has a
	 *     boolean value given to it stating if the Heamodule exists & is
	 *     activated or not
	 */
	protected function enabledHeadmodulesCheck(array $headmodules) {

		foreach($headmodules as $name => $mod) {
			$act = $this->_acl->moduleGet('root/administrator/' . $mod);
			$headmodules[$name] = (boolean) $act;
		}

		return $headmodules;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The AccessControlLayer used to check if the Headmodules are enabled
	 * @var Acl
	 */
	protected $_acl;

}

?>

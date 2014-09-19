<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows the User to view, change and upload the temporary Assignments
 */
class Overview extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$classes = $this->assignmentsGet();
		$this->_smarty->assign('classes', $classes);
		$this->displayTpl('classlist.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the Temporary Assignments Grouped by Classes
	 * @return array  The Classes and some more information
	 */
	private function assignmentsGet() {

		try {
			$data = $this->_pdo->query('SELECT cu.translatedName AS weekday,
					COUNT(*) - (
						SELECT COUNT(*) FROM KuwasysTemporaryRequestsAssign rad
						WHERE ra.classId = rad.classId AND (rad.statusId = 0 OR rad.statusId = 2)
					) AS usercount, c.label AS classlabel,
					c.ID AS classId
				FROM KuwasysTemporaryRequestsAssign ra
				INNER JOIN KuwasysClasses c ON ra.classId = c.ID
				INNER JOIN KuwasysClassCategories cu ON cu.ID = ra.categoryId
				GROUP BY ra.classId, ra.categoryId
				ORDER BY usercount DESC, ra.classId'
			);

			return $data;

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the Temporary Assignments!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>
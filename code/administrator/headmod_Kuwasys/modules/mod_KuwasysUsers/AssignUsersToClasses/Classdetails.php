<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows the User to view and edit the Requests of one Class
 */
class Classdetails extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		try {
			$class = $this->classGet($_GET['classId']);
			$classes = $this->classesGetAllOfActiveSchoolyear();

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the data of class' .
				"$_GET[classId] in " . __METHOD__, 'Moderate');
			$this->_interface->dieError(_g('Error while fetching the data!'));
		}
		$this->_smarty->assign('classId', $_GET['classId']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('classes', $classes);
		$this->displayTpl('classdetails.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches all Classes that are in the active Schoolyear
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array  The Classes
	 */
	private function classesGetAllOfActiveSchoolyear() {

		try {
			$classes = $this->_pdo->query('SELECT * FROM class
				WHERE schoolyearId = @activeSchoolyear');

			return $classes;

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not fetch all classes of the active schoolyear'));
			$this->_logger->log('Error fetching all classes of the active ' .
				'schoolyear', 'Notice', Null,
				json_encode(array('msg' => $e->getMessage())));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>
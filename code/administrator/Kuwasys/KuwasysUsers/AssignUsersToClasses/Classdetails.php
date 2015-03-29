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
			$class = $this->classWithCategoryGet(
				$_GET['classId'], $_GET['categoryId']
			);
			$classes = $this->classesGetAllOfActiveSchoolyear();

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the data of class' .
				"$_GET[classId] in " . __METHOD__, 'Moderate');
			$this->_interface->dieError(_g('Error while fetching the data!'));
		}
		$this->_smarty->assign('classId', $_GET['classId']);
		$this->_smarty->assign('categoryId', $_GET['categoryId']);
		$this->_smarty->assign('class', $class);
		$this->_smarty->assign('classes', $classes);
		$this->displayTpl('classdetails.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches a Class from the Database
	 *
	 * @param  int    $classId The ID of the Class to fetch
	 * @return array           The Class-Data
	 */
	protected function classWithCategoryGet($classId, $categoryId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.*, cc.translatedName AS categoryName
				FROM KuwasysClasses c
				INNER JOIN KuwasysClassesInCategories cic
					ON cic.classId = c.ID
				INNER JOIN KuwasysClassCategories cc
					ON cic.categoryId = cc.ID
				WHERE c.ID = :classId AND cic.categoryId = :categoryId
			');
			$stmt->execute(array(
				'classId' => $classId, 'categoryId' => $categoryId
			));
			return $stmt->fetch();

		} catch (PDOException $e) {
			$msg = "Could not fetch the Class with Id $classId.";
			$this->_logger->log(__METHOD__ . ": $msg", 'Moderate', NULL,
				json_encode(array('error' => $e->getMessage())));
			throw new PDOException($msg, 0, $e);
		}
	}

	/**
	 * Fetches all Classes that are in the active Schoolyear
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array  The Classes
	 */
	private function classesGetAllOfActiveSchoolyear() {

		try {
			$classes = $this->_pdo->query(
				'SELECT c.*, ca.translatedName AS categoryName,
					ca.ID AS categoryId
				FROM KuwasysClasses c
				INNER JOIN KuwasysClassesInCategories cic
					ON cic.classId = c.ID
				INNER JOIN KuwasysClassCategories ca
					ON ca.ID = cic.categoryId
				WHERE c.schoolyearId = @activeSchoolyear'
			);

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
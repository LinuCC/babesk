<?php

namespace web\Kuwasys\ClassList;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/modules/mod_ClassList/ClassList.php';

class Show extends \web\Kuwasys\ClassList {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		try {
			$regEnabled = $this->globalClassRegistrationsAllowed();
			if($regEnabled) {
				$classes = $this->classesFetch();
				$units = $this->unitsFetch();
				$classAppliance = $this->appliancesFetch();
				$this->_smarty->assign('classCategories', $units);
				$this->_smarty->assign('classes', $classes);
				$this->_smarty->assign('classAppliance', $classAppliance);
				$this->displayTpl('classList.tpl');
			}
			else {
				$this->_interface->dieError(
					_g('Registrations are not allowed for the moment!')
				);
			}
		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not display the list!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$subprogram = $this->_modExecCommand->subprogramGet();
		$this->_smartyModuleTemplatesPath =
			PATH_SMARTY_TPL . '/' . $subprogram . $this->relPath;
	}

	/**
	 * Fetches the data necessary to display the class-list
	 * @return array  the fetched data
	 */
	private function classesFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT c.*, GROUP_CONCAT(
						DISTINCT ct.forename, " ", ct.name SEPARATOR ", "
					) AS classteacher, cic.categoryId AS unitId
					FROM KuwasysClasses c
					JOIN KuwasysClassesInCategories cic
						ON cic.classId = c.ID
					LEFT JOIN KuwasysClassteachersInClasses ctic
						ON ctic.ClassID = c.ID
					LEFT JOIN KuwasysClassteachers ct ON ct.ID = ctic.ClassTeacherID
					WHERE c.schoolyearId = @activeSchoolyear
					GROUP BY c.ID'
			);
			$data = $res->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the data to display',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Fetches the class-units from the database
	 * @return array  The class-units
	 */
	private function unitsFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT cc.*, COUNT(voted.categoryId) AS votedCount
				FROM KuwasysClassCategories cc
					-- If the user has voted on this category already
					LEFT JOIN (SELECT cic.categoryId FROM KuwasysClasses c
						INNER JOIN KuwasysClassesInCategories cic
							ON cic.classId = c.ID
						INNER JOIN KuwasysUsersInClassesAndCategories uic ON
							uic.ClassID = c.ID AND
							uic.UserID = ? AND
							c.schoolyearId = @activeSchoolyear
						WHERE c.isOptional = 0
					) voted ON voted.categoryId = cc.ID
					GROUP BY cc.ID
					ORDER BY cc.ID
			');
			$stmt->execute(array($_SESSION['uid']));
			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the units to display',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	private function appliancesFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.ID AS classId, COUNT(*) AS hasApplied,
					uics.name AS statusName, uicc.categoryId AS categoryId
				FROM KuwasysUsersInClassesAndCategories uicc
				INNER JOIN KuwasysClasses c ON c.ID = uicc.ClassID
				INNER JOIN SystemSchoolyears sy
					ON sy.ID = c.schoolyearId AND sy.active = 1
				INNER JOIN KuwasysUsersInClassStatuses uics
					ON uics.ID = uicc.statusId
				WHERE uicc.userId = :userId
				GROUP BY c.ID, uicc.categoryId
			');
			$stmt->execute(array('userId' => $_SESSION['uid']));
			$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			$appliedAr = array();
			if(count($res)) {
				//We want classId and then categoryId to be the Index
				foreach($res as $row) {
					$appliedAr[$row['classId']][$row['categoryId']] = $row;
				}
			}
			return $appliedAr;

		} catch (Exception $e) {
			$this->_logger->log('Could not fetch already existing appliances',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>
<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class MainMenu extends Kuwasys {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	///////////////////////////////////////////////////////////////////////
	//Getters and Setters
	///////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(!isset($_POST['unregisterFromAllClassesOfUnit'])) {
			$classes = $this->getAllClassesOfUser();
			$this->displayMainMenu($classes);
		}
		else {
			$this->unregisterAllClassesOfUserAtCategory();
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////
	protected function entryPoint($dataContainer) {

		defined('_WEXEC') or die("Access denied");
		parent::entryPoint($dataContainer);
		$this->_smarty = $dataContainer->getSmarty();
		$this->_pdo = $dataContainer->getPdo();
		$this->_interface = $dataContainer->getInterface();
	}

	/**
	 * Fetches all Classes of the User in this Schoolyear from the Database
	 *
	 * @return array  All Classes with additional useful information such as
	 * Classstatus and ClassUnit
	 */
	private function getAllClassesOfUser() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT cu.translatedName AS unitname, c.*,
					uics.translatedName AS translatedStatus,
					uics.name AS statusName, uics.name AS status,
					cu.ID AS unitId
				FROM KuwasysClasses c
				JOIN KuwasysUsersInClasses uic ON uic.ClassID = c.ID
				JOIN KuwasysClassCategories cu ON c.unitId = cu.ID
				JOIN KuwasysUsersInClassStatuses uics ON uic.statusId = uics.ID
				WHERE uic.UserID = :userId AND c.schoolyearId = @activeSchoolyear
				ORDER BY cu.ID, uic.statusId
				-- The ID of the ClassUnits states the Order of the Units');

			$stmt->execute(array('userId' => $_SESSION['uid']));

			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$units = array();
			foreach($data as $row) {
				if(isset($units[$row['unitId']]['classes'])) {
					$classes = $units[$row['unitId']]['classes'];
					$classes[] = $row;
				}
				else {
					$classes = array($row);
				}
				$units[$row['unitId']] = array(
					'name' => $row['unitname'],
					'id' => $row['unitId'],
					'classes' => $classes
				);
			}
			return $units;

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch your Classes!'));
		}
	}

	private function displayMainMenu($classes) {

		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'mainMenu.tpl');
	}

	/**
	 * Removes the user from all classes of a unit at the active schoolyear
	 * Dies sending ajax back to the client
	 */
	private function unregisterAllClassesOfUserAtCategory() {

		$this->_interface->setAjax(true);
		$deletedCount = $this->unregisterAllClassesOfUserAtCategoryCommit(
			$_SESSION['uid'], $_POST['categoryId']
		);
		if($deletedCount == 0) {
			$this->_interface->dieMessage(
				_g('You were not unregistered from any classes.')
			);
		}
		else {
			$this->_interface->dieSuccess(
				_g('Unregistered you from %1$s classes.', $deletedCount)
			);
		}
	}

	private function unregisterAllClassesOfUserAtCategoryCommit(
		$userId, $catId) {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE uic FROM KuwasysUsersInClasses uic
					INNER JOIN KuwasysClasses c ON c.ID = uic.ClassID
					WHERE c.schoolyearId = @activeSchoolyear AND
					uic.statusId IN(
							(SELECT ID FROM KuwasysUsersInClassStatuses
								WHERE name="request1"),
							(SELECT ID FROM KuwasysUsersInClassStatuses
								WHERE name="request2")
						) AND
					c.unitId = ? AND
					uic.UserID = ?
			');
			$stmt->execute(array($catId, $userId));

			return $stmt->rowCount();

		} catch (\PDOException $e) {
			$this->_logger->log('error unregistering user from classes of day',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not unregister you from your classes!')
			);
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	private $_classManager;
	private $_userManager;
	private $_jointUsersInClassManager;
	protected $_classUnitManager;
	private $_usersInClassStatusManager;
	protected $_smarty;
	private $_smartyPath;
	protected $_pdo;
}

class SortedClassesByUnits {
	public function __construct($unit, $class) {
		$this->unit = $unit;
		$this->classes [] = $class;
	}
	public $classes;
	public $unit;
}

?>

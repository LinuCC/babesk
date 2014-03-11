<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class MainMenu extends Kuwasys {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	///////////////////////////////////////////////////////////////////////
	//Getters and Setters
	///////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		$classes = $this->getAllClassesOfUser();
		$this->displayMainMenu($classes);
	}

	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////
	protected function entryPoint($dataContainer) {

		defined('_WEXEC') or die("Access denied");
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
					uics.translatedName AS status
				FROM KuwasysClasses c
				JOIN KuwasysUsersInClasses uic ON uic.ClassID = c.ID
				JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
				JOIN usersInClassStatus uics ON uic.statusId = uics.ID
				WHERE uic.UserID = :userId AND c.schoolyearId = @activeSchoolyear
				ORDER BY cu.ID
				-- The ID of the ClassUnits states the Order of the Units');

			$stmt->execute(array('userId' => $_SESSION['uid']));

			return $stmt->fetchAll(PDO::FETCH_GROUP);

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch your Classes!'));
		}
	}

	private function addErrorMsg($str) {

		$this->_smarty->append('error', $str . '<br>');
	}

	private function dieErrorMsg($str) {

		$this->_smarty->append('error', $str . '<br>');
		$this->displayMainMenu(NULL);
	}

	private function displayMainMenu($classes) {

		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'mainMenu.tpl');
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

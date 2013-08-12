<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/gump.php';

/**
 * Allows the User to change Classteacher-data
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class Classteachers extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 *
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
	 *
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if($execReq = $dataContainer->getSubmoduleExecutionRequest()) {
			$this->submoduleExecute($execReq);
		}
		else {
			$this->mainMenu();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 *
	 * @param  DataContainer $dataContainer Contains data needed by
	 *                                      Classteacher
	 */
	protected function entryPoint($dataContainer) {

		$this->_interface = $dataContainer->getInterface();
		$this->_acl = $dataContainer->getAcl();
		$this->_pdo = $dataContainer->getPdo();
		$this->_smarty = $dataContainer->getSmarty();

		$this->initSmartyVariables();
	}

	/**
	 * Displays a MainMenu to the User
	 *
	 * Dies displaying the Main Menu
	 */
	protected function mainMenu() {

		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'mainmenu.tpl');
	}

	/**
	 * Allows the User to add a Classteacher
	 */
	protected function submoduleAddExecute() {

		if(isset($_POST['forename'], $_POST['name'])) {
			$this->classteacherAddRun();
		}
		else {
			$this->_smarty->assign('classes',
				$this->classesOfActiveSchoolyearGet());
			$this->displayTpl('addClassteacher.tpl');
		}
	}

	/**
	 * Fetches all Classes that are in the active Schoolyear
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array The fetched Classes
	 */
	protected function classesOfActiveSchoolyearGet() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM class
				WHERE schoolyearId = @activeSchoolyear');

			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classes of the Active Schoolyear'));
		}
	}

	/**
	 * Checks the Input and adds the Classteacher
	 */
	protected function classteacherAddRun() {

		$this->classteacherAddInputCheck();

		$this->_pdo->beginTransaction();

		$classteacherId = $this->classteacherAddUpload();
		if(count($_POST['class'])) {
			$this->classteacherAddClassesAdd($classteacherId);
		}

		$this->_pdo->commit();

		$this->_interface->dieSuccess(
			_g('The Classteacher was successfully added.'));

	}

	/**
	 * Checks the Input of the AddClassteacher-Form
	 *
	 * Dies displaying a Message on wrong Input
	 */
	protected function classteacherAddInputCheck() {

		$gump = new GUMP();
		$gump->rules(array(
			'forename' => array(
				'min_len,2|max_len,64',
				'',
				_g('Forename'),
			),
			'name' => array(
				'required|min_len,2|max_len,64',
				'',
				_g('Surname'),
			),
			'address' => array(
				'min_len,2|max_len,255',
				'',
				_g('Address'),
			),
			'telephone' => array(
				'min_len,2|max_len,64',
				'',
				_g('Telephone Number'),
			),
		));

		if(!($_POST = $gump->run($_POST))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
		if(count($_POST['class'])) {
			$this->classteacherAddInputInClassesCheck();
		}
	}

	/**
	 * Checks if the given Classteacher-In-Class-Input looks correct
	 *
	 * Dies displaying a Message on wrong Input
	 */
	protected function classteacherAddInputInClassesCheck() {

		if(array_search('NoClass', $_POST['class']) !== false &&
			count($_POST['class']) > 1) {

			$this->_interface->dieError(_g('You cant choose that the ' .
				'Classteacher has No Classes AND select classes!'));
		}
	}

	/**
	 * Adds the Classteacher to the Database
	 *
	 * Dies displaying a Message on Error
	 */
	protected function classteacherAddUpload() {

		try {
			$stmt = $this->_pdo->prepare('INSERT INTO classTeacher
				(forename, name, address, telephone) VALUES
				(:forename, :name, :address, :telephone)');

			$stmt->execute(array(
				':forename' => $_POST['forename'],
				':name' => $_POST['name'],
				':address' => $_POST['address'],
				':telephone' => $_POST['telephone']
			));

			return $this->_pdo->lastInsertId();

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not add the Classteacher!'));
		}
	}

	/**
	 * Adds the selected Classes to the Classteacher
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  $id The ID of the newly added Classteacher
	 */
	protected function classteacherAddClassesAdd($id) {

		try {
			$stmt = $this->_pdo->prepare('INSERT INTO jointClassTeacherInClass
				(ClassTeacherID, ClassID) VALUES (:classteacherId, :classId)');

		foreach($_POST['class'] as $class) {

			if($class != 'NoClass') {
				$stmt->execute(array(
					':classteacherId' => $id, ':classId' => $class));
			}
		}

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not add the Classes to the Classteacher!'));
		}
	}

	protected function submoduleChangeExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleDeleteExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleDisplayExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleCsvImportExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Handy functions to display things to the User
	 * @var AdminInterface
	 */
	protected $_interface;

	/**
	 * The AccessControlLayer used for getting the Submodules
	 * @var Acl
	 */
	protected $_acl;

	/**
	 * The Database-Connection
	 * @var PDO
	 */
	protected $_pdo;

}

?>

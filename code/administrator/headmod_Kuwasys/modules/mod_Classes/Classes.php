<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/gump.php';

/**
 * Allows the User to use Classes. Classes as in the Workgroups in Schools
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class Classes extends Module {

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
	 * @param  DataContainer $dataContainer Contains data needed by Classes
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

	protected function submoduleAddClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->addClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully added.'));
		}
		else {
			$this->addClassDisplay();
		}
	}

	/**
	 * Fetches all Schoolyears and returns them
	 *
	 * @return array The Schoolyears as an Array
	 */
	protected function schoolyearsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM schoolYear');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Schoolyears!'));
		}
	}

	/**
	 * Fetches all Classunits (usually days) and returns them
	 *
	 * @return array The Classunits
	 */
	protected function classunitsGetAll() {

		try {
			$stmt = $this->_pdo->query('SELECT * FROM kuwasysClassUnit');
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classunits!'));
		}
	}

	/**
	 * Checks the given Input of the ChangeClass and AddClass Dialog
	 */
	protected function classInputCheck() {

		$gump = new GUMP();
		$gump->rules(array(
			'label' => array(
				'required|min_len,2|max_len,64',
				'',
				_g('Classname')
			),
			'description' => array(
				'max_len,1024',
				'',
				_g('Classdescription')
			),
			'maxRegistration' => array(
				'required|min_len,1|max_len,4|numeric',
				'',
				_g('Max Amount of Registrations for this Class')
			),
			'classunit' => array(
				'required|numeric',
				'',
				_g('Classunit')
			),
			'schoolyear' => array(
				'required|numeric',
				'',
				_g('Schoolyear-ID')
			),
			'allowRegistration' => array(
				'boolean',
				'',
				_g('Allow registration')
			)
		));

		if(!($_POST = $gump->run($_POST))) {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Adds all necessary data to the Database
	 */
	protected function addClassUpload() {

		$this->_pdo->beginTransaction();
		$this->newClassUpload();
		$this->_pdo->commit();
	}

	/**
	 * Adds a new Row to the class-Table
	 *
	 * Dies displaying a Message on Error
	 */
	protected function newClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO class (label, description, maxRegistration,
					registrationEnabled, unitId, schoolyearId)
				VALUES (:label, :description, :maxRegistration,
					:registrationEnabled, :unitId, :schoolyearId)');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not add the new Class!'));
		}
	}

	/**
	 * Displays a Form to the User which allows him to add a Class
	 */
	protected function addClassDisplay() {

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'addClass.tpl');
	}

	/**
	 * Allows the User to change a Class
	 */
	protected function submoduleChangeClassExecute() {

		if(isset($_POST['label'], $_POST['description'])) {
			$_POST['allowRegistration'] =
				(isset($_POST['allowRegistration'])) ? 1 : 0;
			$this->classInputCheck();
			$this->changeClassUpload();
			$this->_interface->dieSuccess(
				_g('The Class was successfully changed.'));
		}
		else {
			$this->changeClassDisplay();
		}
	}

	/**
	 * Uploads the Change of the Class
	 *
	 * Dies with a Message on Error
	 */
	protected function changeClassUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE class SET label = :label, description = :description,
					maxRegistration = :maxRegistration,
					registrationEnabled = :registrationEnabled,
					unitId = :unitId, schoolyearId = :schoolyearId
					WHERE ID = :id');

			$stmt->execute(array(
				':label' => $_POST['label'],
				':description' => $_POST['description'],
				':maxRegistration' => $_POST['maxRegistration'],
				':registrationEnabled' => $_POST['allowRegistration'],
				':unitId' => $_POST['classunit'],
				':schoolyearId' => $_POST['schoolyear'],
				':id' => $_GET['ID'],
			));

		} catch (Exception $e) {
			$this->_interface->dieError(_g("Could not change the Class with the ID {$_GET[ID]}!"));
		}
	}

	/**
	 * Displays a form allowing the User to Change the Class
	 */
	protected function changeClassDisplay() {

		$this->_smarty->assign('schoolyears', $this->schoolyearsGetAll());
		$this->_smarty->assign('classunits', $this->classunitsGetAll());
		$this->_smarty->assign('class', $this->classGet($_GET['ID']));
		$this->_smarty->display(
			$this->_smartyModuleTemplatesPath . 'changeClass.tpl');
	}

	protected function classGet($id) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class WHERE ID = :id');
			$stmt->execute(array(':id' => $id));
			return $stmt->fetch();

		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not fetch the Class'));
		}
	}

	protected function submoduleDeleteClassExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleDisplayClassesExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleDisplayClassDetailsExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleGlobalClassRegistrationExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleAssignUsersToClassesExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleCreateClassSummaryExecute() {

		$this->_interface->dieError(
			'Dieses Modul ist noch in Überarbeitung...');
	}

	protected function submoduleUnregisterUserExecute() {

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

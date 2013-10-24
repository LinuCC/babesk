<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/gump.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/Kuwasys.php';

/**
 * Allows the User to change Classteacher-data
 */
class Classteachers extends Kuwasys {

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

		$execReq = $dataContainer->getExecutionCommand()->pathGet();
		if($this->submoduleCountGet($execReq)) {
			$this->submoduleExecuteAsMethod($execReq);
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

		$this->classteacherInputCheck();

		$this->_pdo->beginTransaction();

		$classteacherId = $this->classteacherAddUpload();
		if(count($_POST['classes'])) {
			$this->classteacherAddClassesAdd($classteacherId);
		}

		$this->_pdo->commit();

		$this->_interface->dieSuccess(
			_g('The Classteacher was successfully added.'));

	}

	/**
	 * Checks the Input of the AddClassteacher-Form and ChangeClassteacher-Form
	 *
	 * Dies displaying a Message on wrong Input
	 */
	protected function classteacherInputCheck() {

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
		if(count($_POST['classes'])) {
			$this->classteacherAddInputInClassesCheck();
		}
	}

	/**
	 * Checks if the given Classteacher-In-Class-Input looks correct
	 *
	 * Dies displaying a Message on wrong Input
	 */
	protected function classteacherAddInputInClassesCheck() {

		if(array_search('NoClass', $_POST['classes']) !== false &&
			count($_POST['classes']) > 1) {

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

		foreach($_POST['classes'] as $class) {

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

	/**
	 * Allows the User to change a Classteacher
	 */
	protected function submoduleChangeExecute() {

		if(isset($_GET['ID'])) {
			if(isset($_POST['name'])) {
				$this->classteacherChangeRun();
			}
			else {
				$this->classteacherChangeDisplay();
			}
		}
		else {
			$this->_interface->dieError(_g('No ID given!'));
		}
	}

	/**
	 * Changes the Classteacher
	 *
	 * Dies displaying a Message
	 */
	protected function classteacherChangeRun() {

		$this->classteacherInputCheck();
		$this->_pdo->beginTransaction();
		$this->classteacherChangeUpload();
		$this->classteacherChangeClassesUpload();
		$this->_pdo->commit();

		$this->_interface->dieSuccess(
			_g('The Classteacher was successfully changed.'));
	}

	/**
	 * Changes a Classteacher-Entry in the Database based on userinput
	 *
	 * Dies displaying a message on Error
	 */
	protected function classteacherChangeUpload() {

		try {
			$stmt = $this->_pdo->prepare('UPDATE classTeacher SET
				forename = :forename,
				name = :name,
				address = :address,
				telephone = :telephone
				WHERE ID = :id'
			);

			$stmt->execute(array(
				':id' => $_GET['ID'],
				':forename' => $_POST['forename'],
				':name' => $_POST['name'],
				':address' => $_POST['address'],
				':telephone' => $_POST['telephone']
			));

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not change the Classteacher!'));
		}
	}

	/**
	 * Uploads the Changes to the Classes a Classteacher is on
	 *
	 * Dies displaying a Message on Error
	 */
	protected function classteacherChangeClassesUpload() {

		try {
			$classes = $this->classesOfActiveSchoolyearAndClassteacherGet(
				$_GET['ID']);
			$flatClasses = ArrayFunctions::arrayColumn($classes, 'ClassID');

			$this->classteacherChangeAddMissingClasses($flatClasses);
			$this->classteacherChangeDeleteClasses($flatClasses);

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not change the Classes of the Classteacher!') . $e->getMessage());
		}
	}

	/**
	 * Adds missing connections between the classteacher and the Clas on Change
	 *
	 * @param   $existingClasses The existing Class-IDs of the Classteacher
	 * @throws  Exception If Error occured while adding the Connections
	 */
	protected function classteacherChangeAddMissingClasses($existingClasses) {

		$stmtAdd = $this->_pdo->prepare('INSERT INTO
			jointClassTeacherInClass
			(ClassTeacherID, ClassID) VALUES (:id, :classId)');

		foreach($_POST['classes'] as $class) {
			if(array_search($class, $existingClasses) === false &&
				$class != 'NoClass') {
				$stmtAdd->execute(
					array(':id' => $_GET['ID'],
					':classId' => $class));
			}
		}
	}

	/**
	 * Deletes the Classteacher from the Classes
	 *
	 * @param   array  $existingClasses The existing Class-IDs
	 * @throws  Exception If The Class-Connection could not be deleted
	 */
	protected function classteacherChangeDeleteClasses($existingClasses) {

		$stmtDelete = $this->_pdo->prepare('DELETE FROM
				jointClassTeacherInClass
			WHERE ClassTeacherId = :id AND classId = :classId');

		foreach($existingClasses as $exClassId) {
			if(array_search($exClassId, $_POST['classes']) === false) {
				$stmtDelete->execute(
					array(':id' => $_GET['ID'],
					':classId' => $exClassId));
			}
		}
	}

	/**
	 * Displays the change Classteacher form to the User
	 *
	 * Dies displaying the Form
	 */
	protected function classteacherChangeDisplay() {

		$this->_smarty->assign('classteacher',
			$this->classteacherGet($_GET['ID']));
		$this->_smarty->assign('classesOfClassteacher',
			ArrayFunctions::arrayColumn(
				$this->classesOfActiveSchoolyearAndClassteacherGet(
					$_GET['ID']), 'ClassID')
		);
		$this->_smarty->assign('classes',
			$this->classesOfActiveSchoolyearGet());
		$this->displayTpl('changeClassteacher.tpl');
	}

	/**
	 * Fetches the Classteacher with the ID from the Database and returns it
	 *
	 * @param  string $id The ID of the Classteacher
	 * @return array      The Data of the Classteacher
	 */
	protected function classteacherGet($id) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM classTeacher
				WHERE ID = :id');

			$stmt->execute(array(':id' => $id));
			return $stmt->fetch();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g("Could not fetch the Classteacher with the Id $id!"));
		}
	}

	/**
	 * Fetches the Classes where the Classteacher is in
	 *
	 * @param  string $id The ID of the Classteacher
	 * @return array      The classes
	 */
	protected function classesOfActiveSchoolyearAndClassteacherGet($id) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class c
				JOIN jointClassTeacherInClass ctic ON c.ID = ctic.ClassID
				WHERE ctic.ClassTeacherID = :id AND
					c.schoolyearId = @activeSchoolyear');

			$stmt->execute(array(':id' => $id));
			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classes of the Classteacher!'));
		}
	}

	/**
	 * Allows the User to delete A Classteacher
	 *
	 * Dies displaying something
	 */
	protected function submoduleDeleteExecute() {

		if(isset($_GET['ID'])) {

			$this->classteacherDeleteUpload();

			$this->_interface->dieSuccess(
				_g('The Classteacher was successfully deleted!'));
		}
		else {
			$this->_interface->dieError(_g('Id is not set!'));
		}
	}

	/**
	 * Deletes a Classteacher-Entry in the Database
	 *
	 * Dies displaying a Message on Error
	 */
	protected function classteacherDeleteUpload() {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE ct, ctic FROM classTeacher ct
				LEFT JOIN jointClassTeacherInClass ctic
					ON ct.ID = ctic.ClassTeacherID
				WHERE ct.ID = :id');

			$stmt->execute(array(':id' => $_GET['ID']));

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not delete the Classteacher!'));
		}
	}

	/**
	 * Displays all Classteachers
	 */
	protected function submoduleDisplayExecute() {

		$this->_smarty->assign('classteachers', $this->classteachersGetAll());
		$this->displayTpl('displayClassteachers.tpl');
	}

	/**
	 * Fetches all Classteachers in the Table
	 *
	 * Dies displaying a Message on Error
	 *
	 * @return array All Classteachers
	 */
	protected function classteachersGetAll() {

		$classlink = '<a href=\"index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID=';

		try {
			$stmt = $this->_pdo->query("SELECT ct.*,
				GROUP_CONCAT(
					CONCAT('{$classlink}', c.ID, '\">', c.label, '</a>')
					SEPARATOR '<hr>') AS classes
				FROM classTeacher ct
				LEFT JOIN jointClassTeacherInClass ctic
					ON ct.ID = ctic.ClassTeacherID
				LEFT JOIN class c ON ctic.ClassID = c.ID
				GROUP BY ct.ID");

			return $stmt->fetchAll();

		} catch (Exception $e) {
			$this->_interface->dieError(
				_g('Could not fetch the Classteachers') . $e->getMessage());
		}
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

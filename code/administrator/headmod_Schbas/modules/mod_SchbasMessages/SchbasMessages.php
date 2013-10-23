<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'SchbasMessagesInterface.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class SchbasMessages extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'createTemplateForm':
					$this->templateCreateForm();
					break;
				case 'addTemplate':
					$this->templateAdd();
					break;
				case 'deleteTemplate':
					$this->templateDelete();
					break;
				default:
					die('Wrong action-value');
					break;
			}
		}
		else {
			$this->mainMenu();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_interface = new MessageTemplateInterface($this->relPath,
			$this->_dataContainer->getSmarty());
	}

	/**
	 * Displays the main Menu of this module to the User
	 */
	protected function mainMenu() {

		$templates = $this->templatesFetchAll();
		$this->_interface->mainMenu($templates);
	}

	/**
	 * Fetches all of the Templates from the database and returns them
	 *
	 * @return array() An Array of Elements represented as arrays themselfs or
	 * a void Array if no elements where found
	 */
	protected function templatesFetchAll() {

		$data = array();

		try {
			$data = TableMng::query('SELECT * FROM MessageTemplate WHERE GID=(SELECT ID FROM messagegroups WHERE name="Schbas");');

		} catch (MySQLVoidDataException $e) {
			return array();

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Vorlagen nicht abrufen');
		}

		return $data;
	}

	/**
	 * Displays a form to the user which allows creating a template
	 */
	protected function templateCreateForm() {

		$this->_interface->templateCreateForm();
	}

	/**
	 * Adds the template created by the User with the formular to the Database
	 */
	protected function templateAdd() {

		$templateTitle = TableMng::getDb()->real_escape_string(
			$_POST['templateTitle']);
		$templateText = TableMng::getDb()->real_escape_string(
			$_POST['templateText']);

		$this->templateAddToDb($templateTitle, $templateText);

		$this->_interface->dieMsg(
			'Die Vorlage wurde erfolgreich hinzugefügt.');
	}

	/**
	 * Adds a new Template to the Database
	 *
	 * Dies if an Error occured while processing the data
	 *
	 * @param  string $title the title of the Template
	 * @param  string $text the text of the Template
	 */
	protected function templateAddToDb($title, $text) {

		try {
			$gid = TableMng::query('SELECT ID FROM messagegroups WHERE name LIKE "Schbas"');

			TableMng::query(sprintf('INSERT INTO MessageTemplate
				(`title`, `text`,`GID`) VALUES ("%s", "%s", "%d");', $title, $text,$gid[0]['ID']));

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Vorlage nicht speichern!' . $e->getMessage());
		}
	}

	/**
	 * Deletes a Template from the Database based on the ID the user has given
	 */
	protected function templateDelete() {

		$id = TableMng::getDb()->real_escape_string($_GET['id']);
		$this->templateDeleteFromDb($id);

		$this->_interface->dieMsg('Die Vorlage wurde erfolgreich gelöscht');
	}

	/**
	 * Deletes a Template from the Database
	 *
	 * Dies if an Error occured in the Process
	 *
	 * @param  int $id the ID of the Template to delete
	 */
	protected function templateDeleteFromDb($id) {

		try {
			TableMng::query(sprintf('DELETE FROM MessageTemplate
				WHERE `ID` = %s', $id));

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Nachricht nicht löschen');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_dataContainer;

	protected $_interface;

}

?>

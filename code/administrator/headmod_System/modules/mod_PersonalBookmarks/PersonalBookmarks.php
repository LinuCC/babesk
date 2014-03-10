<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class PersonalBookmarks extends System {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		parent::__construct ($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute ($dataContainer) {

		defined('_AEXEC') or die("Access denied");

		$this->entryPoint($dataContainer);
		if (isset ($_GET['action'])) {
			switch ($_GET['action']) {
				case 'save':
					$this->saveBookmark ();
					die('success');
					break;
				default:
					die('error');
					break;
			}
		}
		else {
			die ('error');
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////


	/**
	 * Generates the executePath from the module name and gets the module ID.
	 * Result is stored in adminBookmarks
	 * @todo: Works with one sublevel only. Have to extend it... (count the pipes, make things relative to that...)
	 * @todo: 4 bookmarks possible atm. more?
	 * @todo: no drag&drop sortable for bookmark list
	 */
	protected function saveBookmark () {


		if (!isset ($_POST['moduleId'])) {
			die('error');
		}

		try {
			TableMng::query("update SystemAdminBookmarks set bmid = '0' WHERE bmid='1' AND uid=".$_SESSION['UID']);
			TableMng::query("update SystemAdminBookmarks set bmid = '1' WHERE bmid='2' AND uid=".$_SESSION['UID']);
			TableMng::query("update SystemAdminBookmarks set bmid = '2' WHERE bmid='3' AND uid=".$_SESSION['UID']);
			TableMng::query("update SystemAdminBookmarks set bmid = '3' WHERE bmid='4' AND uid=".$_SESSION['UID']);
			$stmt = $this->_pdo->prepare('INSERT INTO SystemAdminBookmarks
				(uid, bmid, mid) VALUES (?, "4", ?)');
			$stmt->execute(array($_SESSION['UID'], $_POST['moduleId']));
			TableMng::query("delete from SystemAdminBookmarks WHERE bmid = '0' AND uid=".$_SESSION['UID']);

		} catch (Exception $e) {
			$this->_logger->log('Could not add a bookmark',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			die('error');
		}

	}
}

?>

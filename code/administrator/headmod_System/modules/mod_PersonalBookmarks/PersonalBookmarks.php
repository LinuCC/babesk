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


		if (isset ($_POST['mid'])) {
			$mid = TableMng::getDb()->real_escape_string($_POST['mid']);
			$mid = str_replace("|", "/", $mid);
			$mid = str_replace("root/", "", $mid);
			$firstSlash = strpos($mid, "/");
			$secondSlash = strpos($mid,"/",$firstSlash+1);
			$fileName = substr($mid, $secondSlash);
			$mid = substr_replace($mid, "/modules/mod_",$secondSlash,1 );
			$mid = substr_replace($mid, "/headmod_",$firstSlash,1 );
			$mid .= $fileName.".php";

			$id = TableMng::query("SELECT ID FROM Modules WHERE executablePath LIKE '$mid'");
			TableMng::query("update adminBookmarks set bmid = '0' WHERE bmid='1' AND uid=".$_SESSION['UID']);
			TableMng::query("update adminBookmarks set bmid = '1' WHERE bmid='2' AND uid=".$_SESSION['UID']);
			TableMng::query("update adminBookmarks set bmid = '2' WHERE bmid='3' AND uid=".$_SESSION['UID']);
			TableMng::query("update adminBookmarks set bmid = '3' WHERE bmid='4' AND uid=".$_SESSION['UID']);
			TableMng::query("INSERT INTO adminBookmarks (uid,bmid,mid) VALUES ('".$_SESSION['UID']."','4','".$id[0]['ID']."')");
			TableMng::query("delete from adminBookmarks WHERE bmid = '0' AND uid=".$_SESSION['UID']);

		}
	}
}

?>

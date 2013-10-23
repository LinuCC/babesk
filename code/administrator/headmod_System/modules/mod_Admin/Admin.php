<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class Admin extends System {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $rel_path) {
		parent::__construct($name, $display_name, $rel_path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($dataContainer) {

		defined('_AEXEC') or die("Access denied");

		require_once 'AdminAdminInterface.php';
		require_once 'AdminAdminProcessing.php';

		$adminInterface = new AdminAdminInterface($this->relPath);
		$adminProcessing = new AdminAdminProcessing($adminInterface);

		$action = array('add_admin'			 => 1,
			'show_admins'		 => 2,
			'add_admin_group'	 => 3,
			'show_admin_groups'	 => 4,
			'delete_admin'		 => 5,
			'delete_admin_group' => 6,
			'alter_admin'		 => 7,
			'alter_admingroup'	 => 8);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_GET['action'])) {
			switch ($_GET['action']) {
				case $action['add_admin']:
					if (isset($_POST['adminname'], $_POST['password'], $_POST['admin_groups'])) {
						$adminProcessing->addAdmin($_POST['adminname'], $_POST['password'], $_POST['admin_groups']);
					}
					else {
						$adminProcessing->addAdmin(NULL, NULL, NULL);
					}
					break;
				case $action['show_admins']:
					$adminProcessing->ShowAdmins();
					break;
				case $action['add_admin_group']:
					if (isset($_POST['groupname'], $_POST['modules'])) {
						$adminProcessing->addAdminGroup($_POST['groupname'], $_POST['modules']);
					}
					else {
						$adminProcessing->addAdminGroup(NULL, NULL);
					}
					break;
				case $action['show_admin_groups']:
					$adminProcessing->ShowAdminGroups();
					break;
				case $action['delete_admin']:
					if (isset($_POST['delete'])) {
						$adminProcessing->deleteAdmin($_GET['ID'], 1);
					}
					else {
						$adminProcessing->deleteAdmin($_GET['ID'], 0);
					}
					break;
				case $action['delete_admin_group']:
					if (isset($_POST['delete'])) {
						$adminProcessing->deleteAdminGroup($_GET['ID'], 1);
					}
					else {
						$adminProcessing->deleteAdminGroup($_GET['ID'], 0);
					}
					break;
				case $action['alter_admin']:
					if (isset($_GET['where'], $_POST['ID'], $_POST['name'])) {
						$adminProcessing->ChangeAdmin($_GET['where'], $_POST['ID'], $_POST['name'], @$_POST['password'],
							$_POST['admingroup']);
					}
					else {
						$adminProcessing->ChangeAdmin($_GET['ID'], NULL, NULL, NULL, NULL);
					}
					break;
				case $action['alter_admingroup']:
					die('Not implemented yet!');
					break;
			}
		}
		else {
			$adminInterface->SelectionMenu($action);
		}
	}
}

?>

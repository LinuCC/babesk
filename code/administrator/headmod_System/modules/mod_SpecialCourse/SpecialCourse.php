<?php

require_once PATH_INCLUDE . '/Module.php';

class SpecialCourse extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminSpecialCourseInterface.php';
		require_once 'AdminSpecialCourseProcessing.php';

		$SpecialCourseInterface = new AdminSpecialCourseInterface($this->relPath);
		$SpecialCourseProcessing = new AdminSpecialCourseProcessing($SpecialCourseInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //edit the special course list
					$SpecialCourseProcessing->EditSpecialCourses(0);
				break;
				case 2: //save the special courses list
					$SpecialCourseProcessing->EditSpecialCourses($_POST);
				break;
				case 3: //edit the users
					if (isset($_POST['filter'])) {
						$SpecialCourseProcessing->ShowUsers($_POST['filter']);
					} else {
						$SpecialCourseProcessing->ShowUsers("name");
					};
				break;
				case 4: //save the users
					$SpecialCourseProcessing->SaveUsers($_POST);
				break;
			}
		} elseif  (('GET' == $_SERVER['REQUEST_METHOD'])&&isset($_GET['action'])) {
					$action = $_GET['action'];
					switch ($action) {
						case 3: //show the users
					if (isset($_GET['filter'])) {
						$SpecialCourseProcessing->ShowUsers($_GET['filter']);
					} else {
						$SpecialCourseProcessing->ShowUsers("name");
					}
					}


		} else {
			$SpecialCourseInterface->ShowSelectionFunctionality();
		}
	}
}

?>
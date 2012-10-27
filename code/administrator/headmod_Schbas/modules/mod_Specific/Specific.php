<?php

require_once PATH_INCLUDE . '/Module.php';

class Specific extends Module {

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

		require_once 'AdminSpecificInterface.php';
		require_once 'AdminSpecificProcessing.php';

		$SpecificInterface = new AdminSpecificInterface($this->relPath);
		$SpecificProcessing = new AdminSpecificProcessing($SpecificInterface);

		$action_arr = array('show_Specific' => 1,);

		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show Specific
					$SpecificProcessing->ShowSpecific(false);
				break;
				case 2: //edit Specific
					if (!isset ($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle'])){
						$SpecificProcessing->editSpecific($_GET['ID']);
					}else{
						$SpecificProcessing->changeSpecific($_GET['ID'],$_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				break;


			}
		} else {
			$SpecificInterface->ShowSelectionFunctionality($action_arr);
		}
	}
}

?>
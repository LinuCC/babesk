<?php

require_once PATH_INCLUDE . '/Module.php';

class Booklist extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		
		defined('_AEXEC') or die('Access denied');
		
		require_once 'AdminBooklistInterface.php';
		require_once 'AdminBooklistProcessing.php';
		
		$BookInterface = new AdminBooklistInterface($this->relPath);
		$BookProcessing = new AdminBooklistProcessing($BookInterface);
		
		$action_arr = array('show_booklist' => 1,);
		
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show booklist
					$BookProcessing->ShowBooklist(false);
				break;
				case 2: //edit a book
					if (!isset ($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle'])){
						$BookProcessing->editBook($_GET['ID']);
					}else{
						$BookProcessing->changeBook($_GET['ID'],$_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				break;
				
					
			}
		} else {
			$BookInterface->ShowSelectionFunctionality($action_arr);
		}
	}
}

?>
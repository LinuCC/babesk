<?php

require_once PATH_INCLUDE . '/Module.php';

class Search extends Module {

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

		require_once 'AdminSearchInterface.php';
		require_once 'AdminSearchProcessing.php';

		$searchInterface = new AdminSearchInterface($this->relPath);
		$searchProcessing = new AdminSearchProcessing($searchInterface);
		
		if (isset($_POST['search'])){
			$searchProcessing->ShowBooks($_POST['search']);
		}else{
			$searchInterface->showSearchForm();
		}
				
	}
}
?>
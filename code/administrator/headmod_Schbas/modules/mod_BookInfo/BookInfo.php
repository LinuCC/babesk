<?php

require_once PATH_INCLUDE . '/Module.php';

class BookInfo extends Module {

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
		//no direct access
		defined('_AEXEC') or die("Access denied");

		require_once 'AdminBookInfoProcessing.php';
		require_once 'AdminBookInfoInterface.php';

		$BookInfoInterface = new AdminBookInfoInterface($this->relPath);
		$BookInfoProcessing = new AdminBookInfoProcessing($BookInfoInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['barcode'])) {
			$uid = $BookInfoProcessing->GetUser($_POST['barcode']);
			$userData = $BookInfoProcessing->GetUserData($uid);
			$bookData = $BookInfoProcessing->GetBookData($_POST['barcode']);
			$BookInfoInterface->ShowBookInfo($userData,$bookData);
		}
		else{
			$BookInfoInterface->BookId();
		}
	}
}

?>
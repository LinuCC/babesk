<?php

class AdminBookInfoProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $BookManager;
	private $userManager;
	private $BookInfoInterface;
	private $msg;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($BookInfoInterface) {

		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/LoanManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
		
		require_once 'AdminBookInfoInterface.php';

		$this->BookManager = new BookManager();
		$this->LoanManager = new LoanManager();
		$this->InventoryManager = new InventoryManager();
		$this->userManager = new UserManager();
		$this->BookInfoInterface = $BookInfoInterface;

		$this->msg = array(
			'err_get_user_by_Book'	 => 'Anhand der Kartennummer konnte kein Benutzer gefunden werden.',
			'err_connection'		 => 'Ein Fehler ist beim Verbinden zum MySQL-Server aufgetreten',);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	
	/**
	 * Looks the user for the given BookID up, checks if the Book is locked and returns the UserID
	 * @param string $Book_id The ID of the Book
	 * @return string UserID
	 */
	public function GetUser ($barcode) {
		$invID = $this->InventoryManager->getInvIDByBarcode($barcode);
		$uid = $this->LoanManager->getUserIDByInvID($invID);
		return $uid;
	}
	
	/**
	 * Returns some generic user data for identifying a Book
	 */
	public function GetUserData($uid) {
		return $this->userManager->getUserdata($uid);
	}
	
	/**
	 * Returns some generic book data for identifying a Book
	 */
	public function GetBookData($barcode) {
		$bookData = $this->BookManager->getBookDataByBarcode($barcode);
		return $bookData;
	}
}

?>
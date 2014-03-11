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
			'err_get_user_by_Book'	 => 'Anhand des Buchcodes konnte kein Benutzer gefunden werden.',
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
		try {
			$invID = $this->InventoryManager->getInvIDByBarcode($barcode);
			$uid = $this->LoanManager->getUserIDByInvID($invID);
			return $uid;
		}catch (Exception $e){
			$this->BookInfoInterface->dieError(sprintf($this->msg['err_get_user_by_Book']));
		}
	}

	/**
	 * Returns some generic user data for identifying a Book
	 */
	public function GetUserData($uid) {

		$data = TableMng::query(sprintf(
			'SELECT u.*,
			(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM usersInGradesAndSchoolyears uigs
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
			FROM SystemUsers u WHERE ID = %s', $uid), true);

		return $data[0];
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

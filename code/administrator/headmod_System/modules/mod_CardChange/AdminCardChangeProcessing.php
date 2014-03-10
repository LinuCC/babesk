<?php

class AdminCardChangeProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $cardManager;
	private $cardChangeInterface;
	private $msg;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($cardChangeInterface) {

		require_once PATH_ACCESS . '/CardManager.php';


		require_once 'AdminCardChangeInterface.php';

		$this->cardManager = new CardManager();

		$this->cardChangeInterface = $cardChangeInterface;

		$this->msg = array(
			);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 *
	 */
	public function GetSumOfCardChanges () {


		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init ();
		$temp =  TableMng::query('SELECT SUM(changed_cardID) FROM BabeskCards');
		return $temp[0]["SUM(changed_cardID)"];

	}


}

?>

<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';
require_once PATH_INCLUDE . '/Schbas/Book.php';

class Loan extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		require_once 'AdminLoanInterface.php';
		require_once 'AdminLoanProcessing.php';

		$LoanInterface = new AdminLoanInterface($this->relPath);
		$LoanProcessing = new AdminLoanProcessing($LoanInterface);

		if(isset($_POST['barcode']) && isset($_POST['userId'])) {
			$this->bookLoanToUserByBarcode(
				$_POST['barcode'], $_POST['userId']
			);
		}
		else if(isset($_POST['card_ID'])) {
			$this->loanDisplay($_POST['card_ID']);
		}
		else {
			$this->displayTpl('form.tpl');
		}

		//if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['inventarnr'])) {
		//	if (!$LoanProcessing->LoanBook(urldecode($_GET['inventarnr']),$_GET['uid'])) {
		//		$LoanInterface->LoanEmpty();
		//	} else {
		//		$LoanProcessing->LoanAjax($_GET['card_ID']);
		//	}
		//}
		//else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
		//	$LoanProcessing->Loan($_POST['card_ID']);
		//}
		//else{
		//	// Scan the card-id
		//	$this->displayTpl('form.tpl');
		//}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	private function loanDisplay($cardnumber) {

		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$user = $this->userByCardnumberGet($cardnumber);
		$formSubmitted = $this->userFormSubmittedCheck($user);
		$loanChoice = false;
		if($user->getSchbasAccounting() !== Null) {
			$userPaid = $this->userPaidForLoanCheck($user);
			$userSelfpayer = $this->selfpayerCheck($user);
			$loanChoice = $user->getSchbasAccounting()
				->getLoanChoice()
				->getAbbreviation();
		}
		else {
			$userPaid = false;
			$userSelfpayer = false;
		}
		$exemplarsLent = $this->exemplarsStillLendByUserGet($user);
		$booksSelfpaid = $user->getSelfpayingBooks();
		$booksToLoan = $loanHelper->loanBooksGet($user->getId());

		foreach($booksToLoan as $key => $loan) {
			foreach($booksSelfpaid as $selfpaid) {
				if($loan['id'] == $selfpaid->getId()) {
					unset($booksToLoan[$key]);
				}
			}
		}

		$this->_smarty->assign('user', $user);
		$this->_smarty->assign('formSubmitted', $formSubmitted);
		$this->_smarty->assign('loanChoice', $loanChoice);
		$this->_smarty->assign('userPaid', $userPaid);
		$this->_smarty->assign('userSelfpayer', $userSelfpayer);
		$this->_smarty->assign('exemplarsLent', $exemplarsLent);
		$this->_smarty->assign('booksSelfpaid', $booksSelfpaid);
		$this->_smarty->assign('booksToLoan', $booksToLoan);
		$this->displayTpl('user-loan-list.tpl');
	}

	private function userByCardnumberGet($cardnumber) {

		$card = $this->_em->getRepository('DM:BabeskCards')
			->findOneByCardnumber($cardnumber);
		if($card) {
			if(!$card->getLost()) {
				$user = $card->getUser();
				if($user->getLocked()) {
					$this->_interface->dieError('Der Benutzer ist gesperrt!');
				}
				else {
					return $user;
				}
			}
			else {
				$this->_interface->dieError(
					'Diese Karte ist verloren gegangen!'
				);
			}
		}
		else {
			$this->_interface->dieError('Die Karte wurde nicht gefunden!');
		}
	}

	private function userFormSubmittedCheck($user) {

		$acc = $user->getSchbasAccounting();
		return isset($acc);
	}

	private function userPaidForLoanCheck($user) {

		$acc = $user->getSchbasAccounting();
		$loanChoice = $acc->getLoanChoice();
		return (
			(
				$loanChoice->getAbbreviation() == 'ln' ||
				$loanChoice->getAbbreviation() == 'lr'
			) &&
			$acc->getPayedAmount() >= $acc->getAmountToPay()
		);
	}

	private function selfpayerCheck($user) {

		$abbr = $user->getSchbasAccounting()
			->getLoanChoice()
			->getAbbreviation();
		return $abbr == 'ls';
	}

	private function exemplarsStillLendByUserGet($user) {

		$exemplars = $this->_em->createQuery(
			'SELECT i FROM DM:SchbasInventory i
				INNER JOIN i.book b
				INNER JOIN i.usersLent u
				WHERE u.id = :userId
				ORDER BY b.subject
		')->setParameter('userId', $user->getId())
			->getResult();
		return $exemplars;
	}

	private function booksSelfpaidByUserGet($user) {

		$books = $user->getSelfpayingBooks();
		return $books;
	}

	private function bookLoanToUserByBarcode($barcode, $userId) {

		$exemplar = $this->exemplarByBarcodeGet($barcode);
		if($exemplar) {
			//Check if book is lent to someone
			if($exemplar->getUsersLent()->count() == 0) {
				if($this->bookLoanToUserToDb($exemplar, $userId)) {
					die(json_encode(array(
						'bookId' => $exemplar->getBook()->getId(),
						'exemplarId' => $exemplar->getId(),
						'title' => $exemplar->getBook()->getTitle()
					)));
				}
				else {
					http_response_code(500);
					die(json_encode(array(
						'message' => 'Ein Fehler ist beim Eintragen der ' .
							'Ausleihe aufgetreten.'
					)));
				}
			}
			else {
				http_response_code(500);
				//Exemplar should not be lent to two users at the same time
				$user = $exemplar->getUsersLent()->first();
				die(json_encode(array(
					'message' => 'Dieses Exemplar ist im System bereits an ' .
					$user->getForename() . ' ' . $user->getName() .
					' verliehen!'
				)));
			}
		}
		else {
			$this->_logger->log('Book not found by barcode',
				'Notice', Null, json_encode(array('barcode' => $barcode)));
			http_response_code(500);
			die(json_encode(array(
				'message' => 'Das Exemplar konnte nicht anhand des Barcodes ' .
					'gefunden werden!'
			)));
		}
	}

	/**
	 * Checks if the book-exemplar is already lent to a user
	 * @param  string $barcode The Barcode of the exemplar
	 * @return bool            true if it is lent
	 */
	private function exemplarByBarcodeGet($barcodeStr) {

		$bookHelper = new \Babesk\Schbas\Book($this->_dataContainer);
		$barcode = $bookHelper->barcodeParseToArray($barcodeStr);
		//$barcodeStr = $this->barcodeNormalize($barcodeStr);
		//$barcode = $this->barcodeParseToArray($barcodeStr);
		//Delimiter not used in Query
		unset($barcode['delimiter']);
		$query = $this->_em->createQuery(
			'SELECT i, b FROM DM:SchbasInventory i
				INNER JOIN i.book b
					WITH b.class = :class AND b.bundle = :bundle
				INNER JOIN b.subject s
					WITH s.abbreviation = :subject
				WHERE i.yearOfPurchase = :purchaseYear
					AND i.exemplar = :exemplar
		')->setParameters($barcode);
		try {
			$lent = $query->getSingleResult();
		}
		catch(\Doctrine\ORM\NoResultException $e) {
			return false;
		}
		return $lent;
	}

	private function bookLoanToUserToDb($exemplar, $userId) {

		try {
			$lending = new \Babesk\ORM\SchbasLending();
			$user = $this->_em->find(
				'DM:SystemUsers', $userId
			);
			$lending->setUser($user);
			$lending->setInventory($exemplar);
			$lending->setLendDate(new \DateTime());
			$this->_em->persist($lending);
			$this->_em->flush();

		} catch (Exception $e) {
			$this->_logger->log('Error loaning a book-exemplar to a user',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage())));
			return false;
		}
		return true;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>

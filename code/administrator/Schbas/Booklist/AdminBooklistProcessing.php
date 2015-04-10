<?php

use Doctrine\Common\Collections\ArrayCollection;

class AdminBooklistProcessing {


	function __construct($dataContainer, $BookInterface) {

		$this->_dataContainer = $dataContainer;
		$this->BookInterface = $BookInterface;
		$this->messages = array(
				'error' => array('no_books' => 'Keine B&uuml;cher gefunden.','notFound' => 'Buch nicht gefunden!'));
	}

	public $messages = array();
	private $bookInterface;

	/**
	 * Show list of books which students can keep for next schoolyear, ordered by schoolyear.
	 */
	function showBooksForNextYear() {

		require_once 'AdminBooklistInterface.php';
		if (isset($_POST['grade'])) {
			require_once PATH_INCLUDE . '/Schbas/Loan.php';
			require_once PATH_ACCESS . '/BookManager.php';

			$gradelevel = $_POST['grade'];
			$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
			$booksThisYear = $loanHelper->booksInGradelevelToLoanGet(
				$gradelevel
			);
			$booksNextYear = $loanHelper->booksInGradelevelToLoanGet(
				$gradelevel + 1
			);
			//Use ArrayCollection for filter() and contains()
			$booksThisYear = new ArrayCollection($booksThisYear);
			$booksNextYear = new ArrayCollection($booksNextYear);
			// Only books that are both in this gradelevel and next gradelevel
			// will be displayed
			$books = $booksThisYear->filter(
				function($book) use ($booksNextYear) {
					return $booksNextYear->contains($book);
				}
			);
			$this->showPdf($books->toArray());
		}
		else {
			$this->BookInterface->ShowSelectionForBooksToKeep();
		}
	}

	/**
	 * Show list of books by topics.
	 */
	function showBooksByTopic() {

		require_once 'AdminBooklistInterface.php';
		if (isset($_POST['topic'])) {
			require_once PATH_ACCESS . '/BookManager.php';
			$booklistManager = new BookManager();
			$booklist = $booklistManager->getBooksByTopic($_POST['topic']);
			$this->showPdfFT($booklist);
		}
		else {
			$this->BookInterface->ShowSelectionForBooksByTopic();
		}
	}

	function serialize_array_values($arr){
		foreach($arr as $key=>$val){
			//sort($val);
			$arr[$key]=serialize($val);
		}

		return $arr;
	}

	private function showPdf($booklist) {
		$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">

			<tr style="font-weight:bold; text-align:center;"><th>Fach</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';
		foreach ($booklist as $book) {
			// $bookPrices += $book['price'];
			$books .= '<tr><td>' . $book->getSubject()->getName() . '</td><td>' . $book->getTitle() . '</td><td>' . $book->getPublisher() . '</td><td>' . $book->getIsbn() . '</td><td align="right">' . $book->getPrice() . ' &euro;</td></tr>';
		}
		//$books .= '<tr><td></td><td></td><td></td><td style="font-weight:bold; text-align:center;">Summe:</td><td align="right">'.$bookPrices.' &euro;</td></tr>';
		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);
		$this->createPdf("Lehrb&uuml;cher, die f&uuml;r Jahrgang ".($_POST['grade']+1)." behalten werden k&ouml;nnen",$books);
	}


        private function showPdfFT($booklist) {
            require_once 'LoanSystemPdf.php';
			$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">

				<tr style="font-weight:bold; text-align:center;"><th>Klasse</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';
		 $classAssign = array(
				'5'=>'05,56',			// hier mit assoziativem array
										// arbeiten, in der wertzuw.
				'6'=>'56,06,69,67',		// alle kombinationen auflisten
								// sql-abfrage:
				'7'=>'78,07,69,79,67',	// SELECT * FROM `schbas_books` WHERE `class` IN (werte-array pro klasse)
				'8'=>'78,08,69,79,89',
				'9'=>'90,91,09,92,69,79,89',
				'10'=>'90,91,10,92',
				'11'=>'12,92,13',
				'12'=>'12,92,13');

                        foreach ($booklist as $book) {
                               $classKey="";
                            foreach ($classAssign as $key => $value) {
                               if (strpos($value,$book["class"]) !== false) $classKey.=$key."/";
                            }


                    $classKey = rtrim($classKey, "/");
			$books.= '<tr><td>'.$classKey.'</td><td>'.$book["title"].'</td><td>'.$book["publisher"].'</td><td>'.$book["isbn"].'</td><td align="right">'.$book["price"].' &euro;</td></tr>';
		}

		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);
                try {
			$pdfCreator = new LoanSystemPdf("Lehrb&uuml;cher f&uuml;r Fach ".($_POST['topic']),$books,"");
			$pdfCreator->create();
			$pdfCreator->output("Buchliste_Fach_".$_POST['topic']);

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte das PDF nicht erstellen!');
		}


	}


	/**
	 * Creates a PDF for the Participation Confirmation and returns its Path
	 */
	private function createPdf ($page1Title,$page1Text) {

		require_once 'LoanSystemPdf.php';

		try {
			$pdfCreator = new LoanSystemPdf($page1Title,$page1Text,$_POST['grade']);
			$pdfCreator->create();
			$pdfCreator->output("Buchliste_Folgejahr_".$_POST['grade']);

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte das PDF nicht erstellen!');
		}
	}

	/**
	 * Returns the book ID by a given ISBN
	 */
	function getBookIdByISBN($isbn_search) {
		require_once PATH_ACCESS . '/BookManager.php';
		$bookManager = new BookManager();
		try {
			$book_id = $bookManager->getBookIDByISBN($isbn_search);
		} catch (Exception $e) {
			$this->BookInterface->dieError($this->messages['error']['notFound'] . $e->getMessage());
		}
		return $book_id['id'];
	}

	/**
	 *
	 * @var unknown
	 */
	function ScanForDeleteEntry() {
		$this->BookInterface->ShowScanforDeleteEntry();
	}
}

?>

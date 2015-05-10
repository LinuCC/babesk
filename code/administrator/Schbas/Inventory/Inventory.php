<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/Schbas/Barcode.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';

class Inventory extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(isset($_GET['getBooksForBarcodes'])) {
			if(isset($_GET['barcodes']) && count($_GET['barcodes'])) {
				$this->booksForBarcodesSend($_GET['barcodes']);
			}
		}
		if(isset($_GET['ajax'])) {
			$this->index();
		}
		else {
			$this->indexHtml();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the barcodes and, if it fits to more than one book, the books
	 * Dies with the following structure: {
	 *     unique: [ {barcode: <barcodeString>, bookId: <bookId> } ],
	 *     duplicated: [
	 *         {
	 *             books: [
	 *                 {id: <bookId>, title: <bookTitle> }
	 *             ],
	 *             barcodes: [ <barcodeString> ]
	 *         }
	 *     ]
	 *
	 * }
	 *
	 * @param  array  $barcodes An array of strings representing barcodes
	 */
	protected function booksForBarcodesSend($barcodeStrings) {

		$extractIdsFromBooks = function($book) {
			return $book->getId();
		};

		$uniqueBarcodes = [];
		$duplicatedBarcodes = [];
		foreach($barcodeStrings as $barcodeStr) {
			$barcode = new \Babesk\Schbas\Barcode();
			if($barcode->initByBarcodeString($barcodeStr)) {
				// SQL-Request for every barcode, dont request too many :P
				$books = $barcode->getMatchingBooks($this->_em);
				if(count($books) == 1) {
					$uniqueBarcodes[] = [
						'barcode' => $barcodeStr,
						'bookId' => $books[0]->getId()
					];
				}
				else {
					// Make the combined book-ids the key of the array to group
					// the duplicated barcodes
					$bookIds = array_map($extractIdsFromBooks, $books);
					asort($bookIds);
					$key = implode('_', $bookIds);
					if(!isset($duplicatedBarcodes[$key])) {
						$duplicatedBarcodes[$key] = [
							'books' => [],
							'barcodes' => []
						];
						foreach($books as $book) {
							$duplicatedBarcodes[$key]['books'][] = [
								'id' => $book->getId(),
								'title' => $book->getTitle()
							];
						}
					}
					$duplicatedBarcodes[$key]['barcodes'][] = $barcodeStr;
				}
			}
			else {
				dieHttp("Barcode '$barcodeStr' inkorrekt", 400);
			}
		}
		// Remove the keys so that it is a json-array
		$duplicatedBarcodes = array_values($duplicatedBarcodes);
		dieJson( [
			'unique' => $uniqueBarcodes,
			'duplicated' => $duplicatedBarcodes
		] );
	}

	/**
	 * Send the basic HTML-page to the user
	 */
	protected function indexHtml() {

		$this->moduleTemplatePathSet();
		$this->displayTpl('index.tpl');
	}

	/**
	 * Show the inventory-list
	 */
	protected function index() {

		$sort = filter_input(INPUT_GET, 'sort');
		$filter = filter_input(INPUT_GET, 'filter');
		$activePage = filter_input(INPUT_GET, 'activePage',
			FILTER_VALIDATE_INT);
		$entriesPerPage = filter_input(INPUT_GET, 'entriesPerPage',
			FILTER_VALIDATE_INT);
		$displayColumns = [];
		if(isset($_GET['displayColumns']) && count($_GET['displayColumns'])) {
			$displayColumns = array_map(function($columnString) {
				return filter_var($columnString, FILTER_SANITIZE_STRING);
			}, $_GET['displayColumns']);
		}
		if(
			!isset($sort) || !isset($filter) || !isset($activePage) ||
			!isset($entriesPerPage) || !count($displayColumns)
		) {
			dieHttp('Fehlende Parameter', 400);
		}
		else {
			$this->sendIndex(
				$sort, $filter, $activePage, $entriesPerPage, $displayColumns
			);
		}
	}

	protected function sendIndex(
		$sort, $filter, $activePage, $entriesPerPage, $displayColumns
	) {
		$this->sendIndexCheckInput($entriesPerPage);
		$qb = $this->_em->createQueryBuilder()
			->select(['i', 'b', 's'])
			->from('DM:SchbasInventory', 'i')
			->leftJoin('i.book', 'b')
			->leftJoin('b.subject', 's')
			->leftJoin('i.lending', 'l')
			->leftJoin('l.user', 'u');
		$rowCountQb = $this->_em
			->createQueryBuilder()
			->select('COUNT(DISTINCT i.id)')
			->from('DM:SchbasInventory', 'i')
			->leftJoin('i.book', 'b')
			->leftJoin('b.subject', 's')
			->leftJoin('i.lending', 'l')
			->leftJoin('l.user', 'u');
		if(!empty($filter)) {
			$this->sendIndexApplyFilter($filter, $qb, $displayColumns);
			$this->sendIndexApplyFilter($filter, $rowCountQb, $displayColumns);
		}
		$qb->setFirstResult($activePage * $entriesPerPage)
			->setMaxResults($entriesPerPage);
		$result = $qb->getQuery()->getResult();
		$rowCount = $rowCountQb->getQuery()->getSingleScalarResult();


		$data = [];
		foreach($result as $row) {
			$rowData = [];
			$rowData['id'] = $row->getId();
			if($row->getLending() && count($row->getLending()) > 0) {
				if(count($row->getLending()) > 1) {
					$this->_logger->log('Inventory is lend multiple times!',
						'warning');
				}
				$user = $row->getLending()->first()->getUser();
				$rowData['lentUser'] = [
					'id' => $user->getId(),
					'username' => $user->getUsername()
				];
				$rowData['lentUserId'] = $user->getId();
				$rowData['lentUserUsername'] = $user->getUsername();
			}
			if($row->getBook()) {
				if($row->getBook()->getSubject()) {
					$barcode = Babesk\Schbas\Barcode::createByInventory($row);
					$rowData['barcode'] = ($barcode) ?
						$barcode->getAsString() : '???';
					$rowData['subjectName'] = $row->getBook()->
						getSubject()->getName();
				}
				else {
					$this->_logger->logO('Subject for inventory not found',
						['sev' => 'error', 'moreJson' => ['invId' =>
						$row->getId()]]);
					$rowData['barcode'] = 'Fach nicht gefunden!';
				}
				$rowData['bookTitle'] = $row->getBook()->getTitle();
				$rowData['bookIsbn'] = $row->getBook()->getIsbn();
				$rowData['bookAuthor'] = $row->getBook()->getAuthor();
			}
			else {
				$this->_logger->logO('Book for inventory not found', ['sev' =>
					'error', 'moreJson' => ['invId' => $row->getId()]]);
				$rowData['barcode'] = 'Buch nicht gefunden!';
			}
			$data['data'][] = $rowData;
		}
		$data['pageCount'] = $rowCount / $entriesPerPage;
		dieJson($data);
	}

	protected function sendIndexCheckInput($entriesPerPage) {

		if($entriesPerPage < 0 || $entriesPerPage > 1000) {
			dieHttp('Inkorrekte Eingabe: Einträge pro Seite', 400);
		}
	}

	protected function sendIndexApplyFilter(
		$filter, $queryBuilder, $displayColumns
	) {
		if(in_array('barcode', $displayColumns)) {
			$queryBuilder->where('i.yearOfPurchase LIKE :filter')
				->orWhere('b.class LIKE :filter')
				->orWhere('b.bundle LIKE :filter')
				->orWhere('i.exemplar LIKE :filter')
				->orWhere('s.abbreviation LIKE :filter');
		}
		if(in_array('lentUser', $displayColumns)) {
			$queryBuilder->orWhere('u.username LIKE :filter');
		}
		if(in_array('bookTitle', $displayColumns)) {
			$queryBuilder->orWhere('b.title LIKE :filter');
		}
		if(in_array('bookIsbn', $displayColumns)) {
			$queryBuilder->orWhere('b.isbn LIKE :filter');
		}
		if(in_array('bookAuthor', $displayColumns)) {
			$queryBuilder->orWhere('b.author LIKE :filter');
		}
		if(in_array('subjectName', $displayColumns)) {
			$queryBuilder->orWhere('s.name LIKE :filter');
		}
		$queryBuilder->setParameter('filter', "%$filter%");
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>

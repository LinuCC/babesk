<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/Schbas/Barcode.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';

class Inventory extends Schbas {

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

		$this->entryPoint($dataContainer);

		require_once 'AdminInventoryInterface.php';
		require_once 'AdminInventoryProcessing.php';

		$inventoryInterface = new AdminInventoryInterface($this->relPath);
		$inventoryProcessing = new AdminInventoryProcessing($inventoryInterface);

		/**
		 * @todo  Remove this in the future when the old methods of this class
		 * are not used anymore
		 */
		if(isset($_GET['index'])) {
			if(isset($_GET['ajax'])) {
				$this->index();
			}
			else {
				$this->indexPage();

			}
			die();
		}

		$action_arr = array('show_inventory' => 1,
							'add_inventory' => 4,
							'del_inventory' => 5);

			if (isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show the inventory
					$inventoryProcessing->ShowInventory(false);
					break;

				case 2: //edit an entry
					if (!isset ($_POST['purchase'], $_POST['exemplar'])){
						$inventoryProcessing->editInventory($_GET['ID']);
					}else{
						$inventoryProcessing->changeInventory($_GET['ID'], $_POST['purchase'], $_POST['exemplar']);
					}
					break;

				case 3: //delete an entry

					if (isset($_POST['barcode'])) {
						try {
							$inventoryProcessing->DeleteEntry($inventoryProcessing->GetIDFromBarcode($_POST['barcode']));
						} catch (Exception $e) {
						}
					}

					else if (isset($_POST['delete'])) {
						$inventoryProcessing->DeleteEntry($_GET['ID']);
					} else if (isset($_POST['not_delete'])) {
						$inventoryInterface->ShowSelectionFunctionality($action_arr);
					} else {
						$inventoryProcessing->DeleteConfirmation($_GET['ID']);
					}
					break;
				case 4: //add an entry
					if (!isset($_POST['bookcodes'])) {
						$inventoryProcessing->AddEntry();
					} else {
                            $barcodes = explode( "\r\n", $_POST['bookcodes'] );
                            if(in_array("",$barcodes)){
                                $pos=array_search("",$barcodes);
                                unset($barcodes[$pos]);
                            }
                            $barcodes = array_values($barcodes);


                        $succ_list = "";
                        foreach ($barcodes as $barcode) {
						    $succ_list .= "<li>".$inventoryProcessing->AddEntryFin($barcode)."</li>";
                    }
                        $inventoryInterface->ShowAddEntryFin($succ_list);
					}
					break;
				case 5: //search an entry for deleting

						$inventoryProcessing->ScanForDeleteEntry();

					break;
			}
		} else {
			// Check for Ajax-Requests
			if(isset($_GET['getBooksForBarcodes'])) {
				if(isset($_GET['barcodes']) && count($_GET['barcodes'])) {
					$this->booksForBarcodesSend($_GET['barcodes']);
				}
			}
			$inventoryInterface->ShowSelectionFunctionality($action_arr);
		}
	}

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
	protected function indexPage() {

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

		$qb = $this->_em->createQueryBuilder()
			->select(['i', 'b', 's'])
			->from('DM:SchbasInventory', 'i')
			->leftJoin('i.book', 'b')
			->leftJoin('b.subject', 's')
			->leftJoin('i.lending', 'l')
			->leftJoin('l.user', 'u');
		$rowCountQb = $this->_em
			->createQueryBuilder()
			->select('COUNT(i)')
			->from('DM:SchbasInventory', 'i');
		if(!empty($filter)) {
			$this->sendIndexApplyFilter($filter, $qb);
			$this->sendIndexApplyFilter($filter, $rowCountQb);
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
			if(!$row->getBook()) {
				$this->_logger->logO('Book for inventory not found', ['sev' =>
					'error', 'moreJson' => ['invId' => $row->getId()]]);
				$rowData['barcode'] = 'Buch nicht gefunden!';
			}
			else {
				$barcode = Babesk\Schbas\Barcode::createByInventory($row);
				$rowData['barcode'] = $barcode->getAsString();
				if($row->getBook()->getSubject()) {
					$rowData['subject'] = $row->getBook()->
						getSubject()->getName();
				}
			}
			$data['data'][] = $rowData;
		}
		$data['pageCount'] = $rowCount;
		dieJson($data);
	}

	protected function sendIndexApplyFilter($filter, $queryBuilder) {
		$queryBuilder->where('i.yearOfPurchase LIKE :filter')
			->orWhere('i.exemplar LIKE :filter')
			// ->orWhere('b.title LIKE :filter')
			// ->orWhere('s.name LIKE :filter')
			// ->orWhere('u.username LIKE :filter')
			// ->orWhere('s.abbreviation LIKE :filter')
			->setParameter('filter', "%$filter%");
	}
}

?>

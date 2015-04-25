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
}

?>

<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

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

		defined('_AEXEC') or die('Access denied');

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
			$inventoryInterface->ShowSelectionFunctionality($action_arr);
		}
	}
}

?>

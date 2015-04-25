<?php

namespace administrator\Schbas\Inventory\Delete;

require_once PATH_ADMIN . '/Schbas/Inventory/Inventory.php';
require_once PATH_INCLUDE . '/Schbas/Barcode.php';

class Delete extends \Inventory {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_POST['barcodes']) && count($_POST['barcodes'])) {
			$this->barcodesDelete($_POST['barcodes']);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function barcodesDelete($barcodeStrings) {

		foreach($barcodeStrings as $barcodeStr) {
			$barcode = new \Babesk\Schbas\Barcode();
			if(!$barcode->initByBarcodeString($barcodeStr)) {
				dieHttp("Der Barcode '$barcodeStr' ist nicht korrekt", 400);
			}
			$bookCopy = $barcode->getMatchingBookExemplar($this->_em);
			if($bookCopy) {
				foreach($bookCopy->getLending() as $lending) {
					$this->_em->remove($lending);
				}
				$this->_em->remove($bookCopy);
			}
			else {
				echo "<p>Kein Buchexemplar zu Barcode $barcodeStr gefunden. " .
					"</p>";
			}
		}
		$this->_em->flush();
		die('Die Exemplare wurden erfolgreich gelöscht');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
<?php

namespace Babesk\Schbas;

class Barcode {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

	}

	public static function createByBarcodeString($barcodeStr) {

		$barcode = new Barcode();
		$barcode->initByBarcodeString($barcodeStr);
		return $barcode;
	}

	/**
	 * Creates the barcode by reading the data from the inventory-object
	 * Note that it also reads from the book and subject of the $inventory, so
	 * be sure to fetch those with a query beforehand if you dont want
	 * additional queries to be executed.
	 * @param  DM:SchbasInventory $inventory
	 */
	public static function createByInventory($inventory) {

		$barcode = new Barcode();
		$book = $inventory->getBook();
		if(!$book || !$book->getSubject()) {
			return false;
		}
		$barcode->_subject = $book->getSubject()->getAbbreviation();
		$barcode->_class = $book->getClass();
		$barcode->_bundle = $book->getBundle();
		$barcode->_purchaseYear = $inventory->getYearOfPurchase();
		$barcode->_exemplar = $inventory->getExemplar();
		$barcode->_delimiter = '/';
		return $barcode;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function getSubject() {
		return $this->_subject;
	}

	public function getPurchaseYear() {
		return $this->_purchaseYear;
	}

	public function getClass() {
		return $this->_class;
	}

	public function getBundle() {
		return $this->_bundle;
	}

	public function getDelimiter() {
		return $this->_delimiter;
	}

	public function getExemplar() {
		return $this->_exemplar;
	}

	public function getAsString() {
		return "$this->_subject $this->_purchaseYear $this->_class " .
			"$this->_bundle $this->_delimiter $this->_exemplar";
	}


	public function initByBarcodeString($barcode) {

		$barcode = $this->barcodeStringNormalize($barcode);
		$elements = explode(' ', $barcode);
		if(count($elements) != 6) {
			return false;
		}
		list(
				$this->_subject,
				$this->_purchaseYear,
				$this->_class,
				$this->_bundle,
				$this->_delimiter,
				$this->_exemplar
			) = $elements;
		return true;
	}

	public function getMatchingBookExemplar($em) {

		$query = $em->createQuery(
			'SELECT i, l FROM DM:SchbasInventory i
			LEFT JOIN i.lending l
			INNER JOIN i.book b WITH b.class = :class AND b.bundle = :bundle
			INNER JOIN b.subject s WITH s.abbreviation = :subject
			WHERE i.yearOfPurchase = :yearOfPurchase AND i.exemplar = :exemplar
		');
		$query->setParameter('class', $this->_class)
			->setParameter('bundle', $this->_bundle)
			->setParameter('subject', $this->_subject)
			->setParameter('yearOfPurchase', $this->_purchaseYear)
			->setParameter('exemplar', $this->_exemplar);
		return $query->getOneOrNullResult();
	}

	public function getMatchingBooks($em) {

		$query = $em->createQuery(
			'SELECT b FROM DM:SchbasBook b
			INNER JOIN b.subject s WITH s.abbreviation = :subject
			WHERE b.class = :class AND b.bundle = :bundle
		');
		$query->setParameter('class', $this->_class)
			->setParameter('bundle', $this->_bundle)
			->setParameter('subject', $this->_subject);
		return $query->getResult();
	}

	/**
	 * Checks if the given barcode has the same book-data as this instance
	 * @param  Barcode $compareBarcode The barcode to compare to
	 * @return bool                    true if it has the same data, else false
	 */
	public function sameBookDataAs($compareBarcode) {
		return (
			$this_subject == $compareBarcode->getSubject() &&
			$this->_class == $compareBarcode->getClass() &&
			$this->_bundle == $compareBarcode->getBundle()
		);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	private function barcodeStringNormalize($barcode) {

		$barcode = str_replace("-", "/", $barcode);
		//add space after / when it's missing
		$barcode = preg_replace("/\/([0-9])/", "/ $1", $barcode);
		$barcode = str_replace("  ", " ", $barcode);
		return $barcode;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_subject;
	protected $_purchaseYear;
	protected $_class;
	protected $_bundle;
	protected $_delimiter;
	protected $_exemplar;



}

?>
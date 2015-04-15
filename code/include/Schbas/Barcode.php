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
			'SELECT i FROM DM:SchbasInventory i
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
<?php

class MealToDisplay {

	public function __construct($id, $date, $name, $price, $priceclassId,
		$priceclassName, $description) {

		$this->id = $id;
		$this->date = $date;
		$this->name = $name;
		$this->price = $price;
		$this->priceclassId = $priceclassId;
		$this->priceclassName = $priceclassName;
		$this->description = $description;
	}

	public $id;
	public $date;
	public $name;
	public $price;
	public $priceclassId;
	public $priceclassName;
	public $description;
}

?>

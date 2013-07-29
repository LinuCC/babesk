<?php

require_once 'MealToDisplay.php';


class Mealweek {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Mealweek-Object
	 *
	 * @param String $date A date that is in the Mealweek
	 */
	public function __construct($date) {

		$this->_weeknumber = date('W', strtotime($date));
		$this->yearSet($date);
		$this->weekdayDataInit();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the Weeknumber of this Mealweek
	 *
	 * @return int The Weeknumber
	 */
	public function mealweeknumberGet() {

		return $this->_weeknumber;
	}

	/**
	 * Adds a meal to the Mealweek
	 *
	 * @param  MealToDisplay $meal The Meal to add
	 * @throws Exception If Date of Meal is not in the Mealweeks Datespan
	 */
	public function mealAdd($meal) {

		foreach($this->_weekdayData as &$weekday) {
			if($weekday['date'] == $meal->date) {
				$weekday['meals'][] = $meal;
				return;
			}
		}

		throw new Exception('The Meals date is not in this Mealweek');
	}

	/**
	 * Returns the data of the weekdays containing the meals
	 *
	 * @return Array data usable to create Tables
	 */
	public function weekdayDataGet() {

		return $this->_weekdayData;
	}

	/**
	 * Extracts all priceclasses from the Meals this Object has
	 *
	 * @return Array Ań Array of all priceclasses used by the meals
	 */
	public function priceclassesGet() {

		$priceclasses = array();

		foreach($this->_weekdayData as $day) {
			foreach($day['meals'] as $meal) {
				if(!(array_key_exists($meal->priceclassId, $priceclasses))) {
					$priceclasses[$meal->priceclassId] =
						$meal->priceclassName;
				}
			}
		}

		return $priceclasses;
	}

	/**
	 * Gets the Meals with the given Priceclass and Date
	 *
	 * @param  int $priceclassId The Id of the Priceclass
	 * @param  string $date The date of the Meal
	 * @return Array An Array of MealToDisplay-Objects
	 */
	public function mealsByPriceclassAndDateGet($priceclassId, $date) {

		$meals = array();

		foreach($this->_weekdayData as $day) {
			foreach($day['meals'] as $meal) {
				if($meal->priceclassId == $priceclassId &&
					$meal->date == $date) {
					$meals[] = $meal;
				}
			}
		}

		return $meals;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function yearSet($date) {

		$this->_year = date('Y', strtotime($date));
	}

	protected function weekdayDataInit() {

		$weeknum = $this->_weeknumber;
		$year = $this->_year;

		/**
		 * @todo  Only Monday to Friday for now, maybe make it customisable?
		 */
		for($i = 1; $i <= 5; $i++) {
			$addDays = $i - 1;
			$timestamp = strtotime("{$year}W{$weeknum} +$addDays days");
			$daynameKey = date('w', $timestamp);

			$this->_weekdayData[$daynameKey]['date'] =
				date('Y-m-d', $timestamp);
			$this->_weekdayData[$daynameKey]['dayname'] =
				self::$weekdayNames[$daynameKey];
			$this->_weekdayData[$daynameKey]['meals'] = array();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_weekdayData;
	protected $_year;
	protected $_meals;
	protected $_weeknumber;

	protected static $weekdayNames = array(
		0 => 'Sonntag',
		1 => 'Montag',
		2 => 'Dienstag',
		3 => 'Mittwoch',
		4 => 'Donnerstag',
		5 => 'Freitag',
		6 => 'Samstag'
	);
}

?>

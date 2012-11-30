<?php

class AdminMenuProcessing {
	public function __construct ($menuInterface) {

		require_once PATH_ACCESS . '/MealManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		require_once 'AdminMenuInterface.php';

		$this->gsManager = new GlobalSettingsManager();
		$this->mealManager = new MealManager();
		$this->menuInterface = $menuInterface;
		$this->menuInterface->AdditionalHeader();

		$this->msg = array(
			'err_no_meals'	 => 'Es sind keine Mahlzeiten vorhanden',
			'err_infotext'	 => 'Ein Fehler ist beim abrufen der Infotexte entstanden.',
		);
	}

	/**
	 * Shows the Meal-Menu
	 */
	public function ShowMenu () {

		$meallist = $this->FetchMeallist();
		$meallist_sorted = $this->SortMeallist($meallist);
		$weekdates = $this->InitWeekdayArray();
		$infotexts = $this->FetchInfoTexts();

		$this->menuInterface->Menu($infotexts[0], $infotexts[1], $meallist_sorted, $weekdates);

	}

	/**
	 * fetches the meallist for this and next week from the MySQL-table and returns it
	 */
	private function FetchMeallist () {

		$meallist = array();
		try {
			$meallist = $this->mealManager->get_meals_between_two_dates($this->GetWeekday(0), $this->GetWeekday(14));
		} catch (MySQLVoidDataException $e) {
			$this->menuInterface->dieError($this->msg['err_no_meals']);
		}
		return $meallist;
	}

	/**
	 * fills an array with the fitting dates to display in menu and returns it
	 */
	private function InitWeekdayArray () {

		$weekdate = array();
		for ($i = 0; $i < 12; $i++) {
			if ($i <> 5 && $i <> 6)
				$weekdate[] = $this->DateToEuropeanDate($this->GetWeekday($i));
		}
		return $weekdate;
	}

	/**
	 *Converts a Y-m-d date to a good readable date
	 *@param date the date that should be converted
	 *@return returns the converted date as a string
	 */
	private function DateToEuropeanDate ($date) {

		if (!$date)
			return false;
		$date_parts = explode('-', $date);
		$fin_date = $date_parts[2] . '.' . $date_parts[1] . '.' . $date_parts[0];
		return $fin_date;
	}

	/**
	 *gets the date of this week's day
	 *@param day what day in this week is meant. Format is like 'w' in date(), so 0 like Sunday up to 6 for Saturday
	 *@return $dateday string YYYY-MM-DD
	 */
	private function GetWeekday ($day) {

		$weekdaynow = date('w');
		$timestampnow = time();

		$weekday = $timestampnow - (($weekdaynow - $day - 1) * 60 * 60 * 24);
		$dateday = date("Y-m-d", $weekday);
		return $dateday;
	}

	/**
	 * Fetches the Infotexts for the menu from the MySQLtable and returns it
	 * @return array(0 => infotext1, 1 => infotext2)
	 */
	private function FetchInfoTexts () {
		//get the Information-texts
		try {
			$itxt_arr = $this->gsManager->getInfoTexts();
		} catch (Exception $e) {
			$this->menuInterface->dieError($this->msg['err_infotext']);
		}
		return $itxt_arr;
	}

	/**
	 *reorganizes the meallist to: meallistweeksorted [Menu] [day]
	 *@param meallist the meallist that should be reorganized
	 *@return returns the reorganized meallist
	 */
	private function SortMeallist ($meallist) {

		require_once PATH_ACCESS . '/PriceClassManager.php';

		$priceclassmanager = new PriceClassManager();

		if (!$meallist)
			return false;

		$weekday_name = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'monday2',
			'tuesday2', 'wednesday2', 'thursday2', 'friday2');

		$weekday_date = array(); //at which day which date is, 0 is Monday, 1 Tuesday...
		//initialize weekdays
		for ($i = 0; $i < 12; $i++) {
			if ($i <> 5 && $i <> 6)
				$weekday_date[$i] = $this->GetWeekday($i);
		}
		//[A Row of meals(One week)] [day] [specific variable]
		$meallistweeksorted = array();
		foreach ($meallist as & $meal) {
			for ($i = 0; $i < 12; $i++) {
				//Saturday and Sunday shall not be shown
				if ($i <> 5 && $i <> 6 && $meal["date"] == $weekday_date[$i]) {

					$pcn = $priceclassmanager->getPriceClassName($meal["price_class"]);
					$counter = 0;

					$countedPriceclassName = $this->addCounterToPriceclassName($meal["price_class"], $meallistweeksorted,
						$i, $weekday_name);
					$meallistweeksorted[$countedPriceclassName][$weekday_name[$i]]["description"] = $meal["description"];
					$meallistweeksorted[$countedPriceclassName][$weekday_name[$i]]["title"] = $meal["name"];
					$meallistweeksorted[$countedPriceclassName][$weekday_name[$i]]["priceclass"] = $pcn[0]["name"];
				}
			}
		}
		return $meallistweeksorted;
	}

	/**
	 * prevents a bug when mutliple Priceclasses at the same day should be shown; Counter makes it possible to distinguish between them
	 * @param unknown_type $priceClassName
	 * @param unknown_type $meallistweeksorted
	 */
	private function addCounterToPriceclassName ($priceClassName, $meallistweeksorted, $dayCounter, $weekdayName) {

		$counter = 0;
		while (isset($meallistweeksorted[$priceClassName . '(' . $counter . ')'][$weekdayName[$dayCounter]]["title"])) {
			$counter++;
		}
		$priceClassName = $priceClassName . '(' . $counter . ')';
		return $priceClassName;
	}

	protected $gsManager;
	protected $mealManager;
	protected $menuInterface;
	protected $msg;
}

?>

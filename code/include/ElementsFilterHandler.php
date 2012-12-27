<?php

/**
 * Defines static variables to allow the class ElementsFilter to distinguish
 * between comparison-Types
 */
class FilterComparisonType {
	public static $Bigger = 0;
	public static $Smaller = 1;
	public static $Same = 2;
	public static $BiggerSame = 3;
	public static $SmallerSame = 4;
	public static $StringContains = 5;
}

/**
 * Allows to Filter Elements
 * use self::elementsFilter to filter them
 * all functions of this class are static
 */
class ElementsFilterHandler {
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	/**
	 * Filters the value of the key $toFilter in the given $elements by $filter
	 * Note that this class does no type-conversion at all, so you may have to
	 * change the values of the elements beforehand
	 * @param $elements array(array()) an array of elements. Each element is an
	 * array.
	 * @param $toFilter the Key of the value of each Element to check, example: 'name'
	 * @param $filter the Variable to base the Comparison on.
	 * @param $mod Chooses the type of comparison, use a static Var of
	 * FilterComparisonType, for Example FilterComparisonType::$Bigger
	 * @return Array of Elements without those that didnt pass the test
	 */
	public static function elementsFilter ($elements, $toFilter, $filter, $mod) {
		$filtered = array ();
		foreach ($elements as $element) {
			self::elementHasKey ($element, $toFilter); //throws Exception if error
			if (self::elementCheck ($element [$toFilter], $filter, $mod)) {
				$filtered [] = $element;
			}
		}
		return $filtered;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////
	/**
	 * Compars $toCheck with $filter based on $mod and returns boolean
	 * @param $toCheck The Variable to compare with $filter
	 * @param $filter the variable that is compared with $toCheck
	 * @param $mod Chooses the type of comparison, use a static Var of
	 * FilterComparisonType like FilterComparisonType::$Bigger
	 * @return boolean If the Comparison went well
	 */
	protected static function elementCheck ($toCheck, $filter, $mod) {
		switch ($mod) {
			case FilterComparisonType::$Bigger:
				return ($toCheck > $filter);
				break;
			case FilterComparisonType::$Smaller:
				return ($toCheck < $filter);
				break;
			case FilterComparisonType::$Same:
				return ($toCheck == $filter);
				break;
			case FilterComparisonType::$BiggerSame:
				return ($toCheck >= $filter);
				break;
			case FilterComparisonType::$SmallerSame:
				return ($toCheck <= $filter);
				break;
			case FilterComparisonType::$StringContains:
				return (strpos ($toCheck, $filter) !== False);
				break;
			default:
				throw new Exception ('Wrong parameter $mod given');
		}
	}

	/**
	 * Checks if an $element has the $key, throws Exception if not
	 * @param $element the Element to check
	 * @param $key the key of the element
	 * @throws Exception of element has no such key
	 */
	protected static function elementHasKey ($element, $key) {
		if (!isset ($element [$key])) {
			throw new Exception (sprintf('Could not find Key %s in element', $toFilter));
		}
	}
}

?>
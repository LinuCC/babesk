<?php

require_once PATH_INCLUDE . '/ElementsFilterHandler.php';

/**
 * Some functions to filter and sort elements
 * Used by the modules directly
 * Uses specific Post-values
 * The functions check if the Post-values are existing, and only if they
 * exist they sort / filter the elements.
 */
class KuwasysFilterAndSort {
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sorts elements by specific Post-values
	 * @param $elements the elements to sort
	 * @var $_POST ['keyToSortAfter'] the key to sort after
	 */
	public static function elementsSort ($elements) {
		if (isset ($_POST ['keyToSortAfter']) && $_POST ['keyToSortAfter'] != '') {
			self::$sortKey = $_POST ['keyToSortAfter'];
			foreach ($elements as $element) {
				self::elementHasSortKey ($element);//check if key exists
			}
			usort ($elements, array('KuwasysFilterAndSort', 'elementsCompare'));
		}
		return $elements;
	}

	/**
	 * Filters elements by a specific Post-value
	 * @param $elements the elements to filter
	 * @var $_POST ['keyToFilterAfter'] Which of the values of the elements to filter
	 * @var $_POST ['filterValue'] Filters the elements by the value
	 */
	public static function elementsFilter ($elements) {
		if (isset ($_POST ['keyToFilterAfter']) && isset ($_POST ['filterValue']) && $_POST ['filterValue'] != '') {
			$elements = ElementsFilterHandler::elementsFilter ($elements, $_POST ['keyToFilterAfter'], $_POST ['filterValue'], FilterComparisonType::$StringContains);
		}
		return $elements;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////
	protected static function elementsCompare ($classA, $classB) {
		if (is_string($classA [self::$sortKey]) && is_string ($classB [self::$sortKey])) {
			return strcmp ($classA [self::$sortKey], $classB [self::$sortKey]);
		}
		if ($classA [self::$sortKey] === $classB [self::$sortKey]) {
			return 0;
		}
		return -1;
	}

	protected static function elementHasSortKey ($element) {
		if (!isset ($element [self::$sortKey])) {
			throw new Exception (sprintf('Class has not "%s" as a key', self::$sortKey));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
	protected static $sortKey;
}

?>
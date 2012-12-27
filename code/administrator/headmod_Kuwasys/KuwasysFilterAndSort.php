<?php

require_once PATH_INCLUDE . '/ElementsFilterHandler.php';

/**
 * Some functions to filter and sort elements
 * Used by the modules directly
 * Uses specific Post-values
 */
class KuwasysFilterAndSort {
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
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

	public static function elementsFilter ($elements) {
		if (isset ($_POST ['keyToFilterAfter']) && isset ($_POST ['filterValue']) && $_POST ['filterValue'] != '') {
			$elements = ElementsFilterHandler::elementsFilter ($elements, $_POST ['keyToFilterAfter'], $_POST ['filterValue'], FilterComparisonType::$Same);
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
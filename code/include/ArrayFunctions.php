<?php

class ArrayFunctions {

	/**
	 * The Wonderful array_column-function, in core only availabe in PHP 5.5+,
	 * implemented for our needs
	 */
	public static function arrayColumn(
		array $input,
		$columnKey,
		$indexKey = null) {

		$array = array();

		foreach ($input as $value) {
			if(!isset($value[$columnKey])) {
				trigger_error("Key \"$columnKey\" does not exist in array");
				return false;
			}

			if(is_null($indexKey)) {
				$array[] = $value[$columnKey];
			}
			else {
				if(!isset($value[$indexKey])) {
					trigger_error("Key \"$indexKey\" does not exist in array");
					return false;
				}
				if(!is_scalar($value[$indexKey])) {
					trigger_error("Key \"$indexKey\" does not contain scalar value");
					return false;
				}
				$array[$value[$indexKey]] = $value[$columnKey];
			}
		}

		return $array;
	}

	/**
	 * Checks if the Keys exist in the Container-Array, when not adds them
	 *
	 * @param  array  $container The Container with (or without) the Keys
	 * @param  array  $keys      An Array containing the Keys to check for
	 * @param  string $value     The Value of the newly added Keys
	 * @return array             The changed Container-Array
	 */
	public static function arrayKeysCreateIfNotExist(
		array $container, array $keys, $value = '') {

		foreach($keys as $key) {
			if(!isset($container[$key])) {
				$container[$key] = $value;
			}
		}

		return $container;
	}
}

?>

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

	/**
	 * Converts a nested set to a multidimensional array
	 * For this function to work properly it is important that the nodes are
	 * sorted by their left value in ascending order!
	 * @param  array  $arrData  The nested set to convert
	 * '<index>' = [
	 *     '<opening tag name>' => '<opening tag number>',
	 *     '<closing tag name>' => '<closing tag number>',
	 *     '<user-defined key>' => '<user-defined value>', ...
	 * ]
	 * @param  string $tagOpen  default 'lft', the name of the opening tag
	 * @param  string $tagClose default 'rgt', the name of the closing tag
	 * @return array            a hierarchically ordered array
	 * '<index>' = [
	 *     'item'              => [
	 *         '<opening tag name>' => '<opening tag number>',
	 *         '<closing tag name>' => '<closing tag number>',
	 *         '<user-defined key>' => '<user-defined value>', ...
	 *     ],
	 *     'children'           => [
	 *         (The children of the node, with the same structure)
	 *     ]
	 * ]
	 */
	public static function nestedSetToArray(
		array $arrData, $tagOpen = 'lft', $tagClose = 'rgt'
	) {
		$stack = array();
		$arraySet = array();

		foreach( $arrData as $intKey=>$arrValues) {
			$stackSize = count($stack); //how many opened tags?
			while(
				$stackSize > 0 &&
				$stack[$stackSize-1][$tagClose] < $arrValues[$tagOpen]
			) {
				array_pop($stack); //close sibling and his childrens
				$stackSize--;
			}

			$link =& $arraySet;
			for($i=0;$i<$stackSize;$i++) {
				$link =& $link[$stack[$i]['index']]["children"]; //navigate to the proper children array
			}
			$tmp = array_push($link,  array ('item'=>$arrValues,'children'=>array()));
			array_push($stack, array('index' => $tmp-1, $tagClose => $arrValues[$tagClose]));
		}

		return $arraySet;
	}

	/**
	 * Returns the first value of the array
	 * @return mixed the first value, or false if not exists
	 */
	public static function firstValue(array $arr) {

		if(!count($arr)) {
			return false;
		}
		else {
			foreach($arr as $el) {
				return $el;
			}
		}
	}

	/**
	 * If value with the $key in array $container is blank, set it to $toSet
	 * @param array  $container The array which (maybe) contains an element with
	 *                          the key $key
	 * @param mixed  $key       The key of the value to check for blank-ness
	 * @param mixed  $toSet     The value to set when the original value is blank
	 */
	public static function setOnBlank(array &$container, $key, $toSet) {

		if(empty($container[$key]) || isBlank($container[$key])) {
			$container[$key] = $toSet;
		}
	}
}

?>

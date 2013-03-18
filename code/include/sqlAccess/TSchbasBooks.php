<?php


require_once 'TTable.php';

class TSchbasBooks {

	public static function toObject ($elements) {
		$obj = self::__construct ();
		foreach ($elements as $key => $value) {
			foreach (self::$el as $elKey => $elValue) {
				if ($key == $elValue) {
					// set the values of the instance
					$obj->$elKey = $value;
				}
			}
		}
		return $obj;
	}

	public static function el ($name) {
		if (isset (self::$el [$name])) {
			return self::$el [$name];
		}
		else {
			throw new Exception (sprintf(
				'Could not find Element "%s" of table "%s"', $name, self::$tablename));
		}
	}

	public static function elT ($name) {
		if (isset (self::$el [$name])) {
			return self::$tablename . '.' . $el [$name];
		}
		else {
			throw new Exception (sprintf(
				'Could not find Element "%s" of table "%s"', $name, self::$tablename));
		}
	}

	public static $el = array (
		'id' => 'id',
		'subject' => 'subject',
		'class' => 'class',
		'title' => 'title',
		'author' => 'author',
		'publisher' => 'publisher',
		'isbn' => 'isbn',
		'price' => 'price',
		'bundle' => 'bundle',
		);

	public static $tablename = 'schbas_books';

}


?>
<?php
/**
 * Provides a class to manage the booklist of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the booklist, provides methods to add/modify the booklist or to get information from the booklist.
 */
class BookManager extends TableManager{

	public function __construct() {
		parent::__construct('schbas_books');
	}
	
	/**
	 * Sorts the booklist it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getBooklistSorted() {
		require_once PATH_ACCESS . '/dbconnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY id', $this->tablename));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
	
	/**
	 * Gives the informations about a book by ID.
	 * 
	 * 
	 */
	function getBookDataByID($id) {
		require_once PATH_ACCESS . '/dbconnect.php';
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE id = %s', $this->tablename, $id));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array = $buffer;
		return $res_array;
	}
	
	/**
	 * Gives the book ID from a given barcode
	 */
	function getBookIDByBarcode($barcode) {
		require_once PATH_ACCESS . '/dbconnect.php';
		try {
			$barcode_exploded = explode(' ', $barcode);
		} catch (Exception $e) {
		}
		$query = sql_prev_inj(sprintf('subject = "%s" AND class = "%s" AND bundle = %s' , $barcode_exploded[0], $barcode_exploded[2], $barcode_exploded[3]));
		//$result = $this->db->query($query);	
		$result = parent::searchEntry($query);
		return $result;
	}
	
	/**
	 * edit a book entry by given id
	 */
	function editBook($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle){
		parent::alterEntry($id, 'subject', $subject, 'class', $class, 'title', $title, 'author', $author, 'publisher', $publisher, 'isbn', $isbn, 'price', $price, 'bundle', $bundle);
	}
	
	/**
	 * Gives all books for a class.
	 * @todo: add an editor in admin area for the associative array !!
	 */
	function getBooksByClass($class) {
		$class = preg_replace('/[^0-9]/i', '', $class); // keep numbers only
		$classAssign = array(
				'5'=>'05,56',			// hier mit assoziativem array
				'6'=>'06',				// arbeiten, in der wertzuw.
				'7'=>'07',				// alle kombinationen auflisten
				'8'=>'08',				// sql-abfrage: 
				'9'=>'09,90,92',				// SELECT * FROM `schbas_books` WHERE `class` IN (werte-array pro klasse)
				'10'=>'10,90,92',				
				'11'=>'01,12,92,13,23',
				'12'=>'02,12,92,13,23');
		require_once PATH_ACCESS . '/dbconnect.php';
		//$query = sql_prev_inj(sprintf("SELECT * FROM %s WHERE class LIKE '%%%s%%'", $this->tablename, $class));
		$query = sql_prev_inj(sprintf("SELECT * FROM %s WHERE class IN (%s)", $this->tablename, $classAssign[$class]));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		$res_array = NULL;
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
}
?>
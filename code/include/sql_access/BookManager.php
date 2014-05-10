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
		parent::__construct('SchbasBooks');
	}

	/**
	 * Sorts the booklist it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getBooklistSorted($pagePointer, $orderBy) {
		require_once PATH_ACCESS . '/DBConnect.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY `%s` LIMIT %s,10', $this->tablename,$orderBy,$pagePointer));
		$result = $this->db->query($query);
		if (!$result) {
			/**
			 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
			 * throw DB_QUERY_ERROR.$this->db->error;
			 */
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		foreach ($res_array as &$book){
			$book['lastNumber'] = $inventoryManager->getHighestNumberByBookId($book['id']);
		}

		return $res_array;
	}

	/**
	 * Gives the informations about a book by ID.
	 *
	 *
	 */
	function getBookDataByID($id) {
		require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE id = %s', $this->tablename, $id));
		$result = $this->db->query($query);
		if (!$result) {
			/**
			 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
			 * throw DB_QUERY_ERROR.$this->db->error;
			 */
		}
		while($buffer = $result->fetch_assoc())
			$res_array = $buffer;
		return $res_array;
	}

	/**
	 * Gives the book ID from a given inventory (!) barcode
	 */
	function getBookDataByBarcode($barcode) {
		require_once PATH_ACCESS . '/DBConnect.php';
		try {
			$barcode_exploded = explode(' ', $barcode);
		} catch (Exception $e) {
		}
		if (isset ($barcode_exploded[5])){
			$query = sql_prev_inj(sprintf('subject = "%s" AND class = "%s" AND bundle = %s' , $barcode_exploded[0], $barcode_exploded[2], $barcode_exploded[3]));
			$result = parent::searchEntry($query);
			if (!$result) {
				/**
				 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
				 * throw DB_QUERY_ERROR.$this->db->error;
				 */
			}
			return $result;
		}
	}

	/**
	 * Gives the book ID from a given isbn (!) barcode
	 */
	function getBookIDByISBN($isbn) {
		require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf('isbn = "%s"' , $isbn));
		$result = parent::searchEntry($query);
		if (!$result) {
			/**
			 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
			 * throw DB_QUERY_ERROR.$this->db->error;
			 */
		}
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
										// arbeiten, in der wertzuw.
				'6'=>'56,06,69,67',		// alle kombinationen auflisten
								// sql-abfrage:
				'7'=>'78,07,69,79,67',	// SELECT * FROM `schbas_books` WHERE `class` IN (werte-array pro klasse)
				'8'=>'78,08,69,79,89',
				'9'=>'90,91,09,92,69,79,89',
				'10'=>'90,91,10,92',
				'11'=>'12,92,13',
				'12'=>'12,92,13');
		require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf("SELECT * FROM %s WHERE class IN (%s)", $this->tablename, $classAssign[$class]));
		$result = $this->db->query($query);
		if (!$result) {
			die(_g('Error occured while fetching the books by class'));
		}
		$res_array = NULL;
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}

        function getBooksByTopic($topic) {
            require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf("SELECT * FROM %s WHERE `subject` LIKE '%s' ORDER BY `class`", $this->tablename, $topic));

                $result = $this->db->query($query);
		if (!$result) {
			die(_g('Error occured while fetching the books by topic'));
		}
		$res_array = NULL;
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;

        }

}
?>
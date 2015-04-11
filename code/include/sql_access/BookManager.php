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
	 * Gives the informations about a book by ID.
	 *
	 *
	 */
	function getBookDataByID($id) {
		require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf(
			'SELECT b.*, ss.abbreviation AS subject FROM %s b
				LEFT JOIN `SystemSchoolSubjects` ss ON ss.ID = b.subjectId
				WHERE b.id = %s',
			$this->tablename, $id
		));
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
			$query = sql_prev_inj(sprintf(
				'subjectId = (
					SELECT ID FROM SystemSchoolSubjects ss
						WHERE ss.abbreviation = "%s"
				) AND class = "%s" AND bundle = %s',
				$barcode_exploded[0],
				$barcode_exploded[2],
				$barcode_exploded[3])
			);
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
	function editBook($id, $subjectId, $class, $title, $author, $publisher, $isbn, $price, $bundle){
		parent::alterEntry($id, 'subjectId', $subjectId, 'class', $class, 'title', $title, 'author', $author, 'publisher', $publisher, 'isbn', $isbn, 'price', $price, 'bundle', $bundle);
	}

        function getBooksByTopic($topic) {
            require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf(
			'SELECT * FROM %s WHERE subjectId = (
					SELECT ID FROM SystemSchoolSubjects
						WHERE abbreviation = "%s"
				) ORDER BY `class`',
			$this->tablename, $topic
		));

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
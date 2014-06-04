<?php

require_once 'Booklist.php';

class ShowBooklist extends Booklist {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['ajax'])) {
			$this->ajaxBooklist();
		}
		else {
			$this->displayTpl('show-booklist.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	protected function ajaxBooklist() {

		$query = $this->booklistQueryGet();
		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);
		$books = $this->bookArrayPopulate($paginator);
		$pagecount = $this->pagecountGet($paginator);
		die(json_encode(array(
			'pagecount' => $pagecount, 'books' => $books
		)));
	}

	/**
	 * Creates and returns a query that fetches the book-variables
	 * @return QueryBuilder A Doctrine-Query-Builder
	 */
	protected function booklistQueryGet() {

		$query = $this->_entityManager->createQueryBuilder()
			->select(array('b, s'))
			->from('Babesk\ORM\SchbasBooks', 'b')
			->leftJoin('b.subject', 's');
		if(isset($_POST['filterFor']) && !isBlank($_POST['filterFor'])) {
			$query->where('b.title LIKE :filterVar')
			->orWhere('b.author LIKE :filterVar')
			->orWhere('b.class LIKE :filterVar')
			->orWhere('b.bundle LIKE :filterVar')
			->orWhere('b.price LIKE :filterVar')
			->orWhere('b.isbn LIKE :filterVar')
			->orWhere('b.publisher LIKE :filterVar')
			->orWhere('s.name LIKE :filterVar')
			->orWhere('s.abbreviation LIKE :filterVar')
			->setParameter('filterVar', '%' . $_POST['filterFor'] . '%');
		}
		$query->setFirstResult($_POST['pagenumber'] * $_POST['booksPerPage'])
			->setMaxResults($_POST['booksPerPage']);
		return $query;
	}

	/**
	 * Populates the array of books to be returned to the client
	 * @param  Paginator $paginator doctrines paginator to fetch the data
	 * @return array                An array of bookdata
	 */
	protected function bookArrayPopulate($paginator) {

		$books = array();
		foreach($paginator as $book) {
			$bookAr = array(
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'author' => $book->getAuthor(),
				'gradelevel' => $book->getClass(),
				'bundle' => $book->getBundle(),
				'price' => $book->getPrice(),
				'isbn' => $book->getIsbn(),
				'publisher' => $book->getPublisher()
			);
			$bookAr['subject'] = ($book->getSubject()) ?
				$book->getSubject()->getName() : '';
			$books[$book->getId()] = $bookAr;
		}
		$invData = $this->booksInventoryDataGet($paginator);
		$books = $this->bookArrayMerge($books, $invData);
		return $books;
	}

	/**
	 * Calculates the pagecount of the booklist
	 * @param  Paginator $paginator doctrines paginator fed with the bookquery
	 * @return int                  The count of showable pages
	 */
	protected function pagecountGet($paginator) {

		$bookcount = count($paginator);
		// No division by zero, never show zero sites
		if($_POST['booksPerPage'] != 0 && $bookcount > 0) {
			$pagecount = ceil($bookcount / (int)$_POST['booksPerPage']);
		}
		else {
			$pagecount = 1;
		}
		return $pagecount;
	}

	/*====================================================
	=            Additional Bookdata to fetch            =
	====================================================*/

	protected function booksInventoryDataGet($paginator) {

		require_once PATH_INCLUDE . '/orm-entities/SchbasBooks.php';

		$booksLent = $this->booksLentGet($paginator);
		return $booksLent;
	}

	/**
	 * Gets the count of exemplars of the given books that are lent
	 * @param  Paginator $paginator a doctrine-paginator containing the books
	 *                              which lent exemplars (inventory) to count
	 * @return array                '<bookId>' => [
	 *                                  'exemplarsLent' => '<lentCount>'
	 *                              ]
	 */
	protected function booksLentGet($paginator) {

		$booksLent = array();
		$query = $this->_entityManager->createQuery(
			'SELECT COUNT(l) FROM \Babesk\ORM\SchbasBooks b
				JOIN b.exemplars e
				JOIN e.lending l
				WHERE b.id = :id
		');
		foreach($paginator as $book) {
			$res = $query->setParameter('id', $book->getId())->getResult();
			$booksLent[$book->getId()]['exemplarsLent'] = $res[0][1];
		}

		return $booksLent;
	}

	/*-----  End of Additional Bookdata to fetch  ------*/

	/**
	 * Combines two multi-dimensional arrays
	 * Combines something like
	 *     [ '1' => ['A' => '5', 'B' => '6'],
	 *         '2' => ['A' => '9', 'B' => '3'] ]
	 *     and
	 *     [ '1' => ['F' => '8']
	 *         '2' => ['F' => '4'] ]
	 *     to
	 *     [ '1' => ['A' => '5', 'B' => '6', 'F' => '8'],
	 *         '2' => ['A' => '9', 'B' => '3', 'F' => '4'] ]
	 * @param  array  $ar1 The first array
	 * @param  array  $ar2 The second array
	 * @return array       The combined array
	 */
	protected function bookArrayMerge($ar1, $ar2) {

		foreach($ar1 as $bookId1 => $book1) {
			if(!empty($ar2[$bookId1])) {
				foreach($ar2[$bookId1] as $name => $val) {
					$ar1[$bookId1][$name] = $val;
				}
			}
		}

		return $ar1;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
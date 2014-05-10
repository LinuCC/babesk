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

		// 	// $pagenum = $_POST['pagenumber'];
		// 	// $usersPerPage = $_POST['usersPerPage'];
		// 	// $sortFor = $_POST['sortFor'];
		// 	// $filterFor = $_POST['filterFor'];

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
			->setMaxResults($_POST['booksPerPage']) ;

		// 	// if($_POST['sortFor']) {
		// 	// 	$query->orderBy($_POST['sortFor']);
		// 	// }
		// 	// if($_POST['filterFor']) {
		// 	// 	$query->where()
		// 	// }

		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);

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
			$books[] = $bookAr;
		}

		$bookcount = count($paginator);
		// No division by zero, never show zero sites
		if($_POST['booksPerPage'] != 0 && $bookcount > 0) {
			$pagecount = ceil($bookcount / (int)$_POST['booksPerPage']);
		}
		else {
			$pagecount = 1;
		}

		die(json_encode(array(
			'pagecount' => $pagecount, 'books' => $books
		)));
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
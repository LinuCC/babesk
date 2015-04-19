<?php

namespace administrator\Schbas\Books\Search;

require_once PATH_ADMIN . '/Schbas/Books/Books.php';

class Search extends \administrator\Schbas\Books\Books {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$by = filter_input(INPUT_GET, 'searchBy');
		if($by == 'title') {
			$title = filter_input(INPUT_GET, 'title');
			if($title) {
				dieJson($this->searchByTitle($title, 20));
			}
			else {
				dieHttp('Namens-parameter fehlt', 500);
			}
		}
		else {
			dieHttp('Parameter zum Suchtyp fehlt', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function searchByTitle($title, $entryCount) {

		try {
			$query = $this->_em->createQuery(
				'SELECT b FROM DM:SchbasBook b
				WHERE b.title LIKE :title
			');
			$query->setParameter('title', "%$title%");
			$query->setMaxResults($entryCount);
			$books = $query->getResult();
			$bookArray = [];
			foreach($books as $book) {
				$bookArray[] = [
					'id' => $book->getId(),
					'title' => $book->getTitle()
				];
			}
			return $bookArray;
		}
		catch(\Exception $e) {
			$this->_logger->logO('Could not search the books by title', [
				'sev' => 'error', 'moreJson' => ['title' => $title,
				'msg' => $e->getMessage()]]);
			dieHttp('Konnte nicht nach dem Buch suchen', 500);
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
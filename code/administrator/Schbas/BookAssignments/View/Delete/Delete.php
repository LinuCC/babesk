<?php

namespace administrator\Schbas\BookAssignments\View\Delete;

require_once PATH_ADMIN . '/Schbas/BookAssignments/View/View.php';

class Delete extends \administrator\Schbas\BookAssignments\View\View {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$delEntity = filter_input(INPUT_GET, 'deleteEntity');
		$bookId = filter_input(INPUT_GET, 'bookId');
		$entityId = filter_input(INPUT_GET, 'entityId');
		$schoolyearId = filter_input(INPUT_GET, 'schoolyearId');
		if(
			$delEntity && $entityId && $bookId && $schoolyearId &&
			in_array($delEntity, $this->_validDeleteEntities)
		) {
			try {
				$count = $this->assignmentsDeleteFor(
					$delEntity, $bookId, $entityId, $schoolyearId
				);
				die("Es wurden $count Zuweisungen gelöscht.");
			}
			catch(\Exception $e) {
				$this->_logger->logO('Could not delete book-assignments',
					['sev' => 'error', 'moreJson' => ['entityId' => $entityId,
						'bookId' => $bookId, 'msg' => $e->getMessage()]]);
				dieHttp('Ein Fehler ist beim Löschen der Zuweisungen ' .
					'aufgetreten', 500);
			}
		}
		else {
			dieHttp('Parameter fehlen', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	protected function assignmentsDeleteFor(
		$delEntity, $bookId, $entityId, $schoolyearId
	) {

		$schoolyear = $this->_em->find('DM:SystemSchoolyears', $schoolyearId);
		$book = $this->_em->find('DM:SchbasBook', $bookId);
		if(!$schoolyear) { dieHttp('Schuljahr nicht gefunden', 400); }
		if(!$book) { dieHttp('Buch nicht gefunden', 400); }

		// DQL does not support delete with joins, so select them first
		// and delete them after that
		$qb = $this->_em->createQueryBuilder()
			->select('usb')
			->from('DM:SchbasUserShouldLendBook', 'usb');

		switch($delEntity) {
			case 'book':
				// We want to delete all assignments for the book, no filtering
				// necessary
				break;
			case 'gradelevel':
				$qb->innerJoin('usb.user', 'u')
					->innerJoin('u.attendances', 'a')
					->innerJoin('a.schoolyear', 's', 'WITH', 's = :schoolyear')
					->innerJoin(
						'a.grade', 'g', 'WITH', 'g.gradelevel = :gradelevel'
					)->setParameter('gradelevel', $entityId);
				break;
			case 'grade':
				$grade = $this->_em->getReference(
					'DM:SystemGrades', $entityId
				);
				$qb->innerJoin('usb.user', 'u')
					->innerJoin('u.attendances', 'a')
					->innerJoin('a.schoolyear', 's', 'WITH', 's = :schoolyear')
					->innerJoin('a.grade', 'g', 'WITH', 'g = :grade');
				$qb->setParameter('grade', $grade);
				break;
			case 'user':
				$user = $this->_em->getReference('DM:SystemUsers', $entityId);
				$qb->andWhere('usb.user = :user');
				$qb->setParameter('user', $user);
				break;
		}
		$qb->andWhere('usb.book = :book');
		$qb->andWhere('usb.schoolyear = :schoolyear');
		$qb->setParameter('schoolyear', $schoolyear);
		$qb->setParameter('book', $book);
		$query = $qb->getQuery();
		$entries = $query->getResult();
		foreach($entries as $entry) {
			$this->_em->remove($entry);
		}
		$this->_em->flush();
		return count($entries);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_validDeleteEntities = ['book', 'gradelevel', 'grade', 'user'];
}

?>
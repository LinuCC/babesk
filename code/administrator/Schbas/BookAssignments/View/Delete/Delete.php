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
		if(
			$delEntity &&
			in_array($delEntity, $this->_validDeleteEntities) &&
			$entityId && $bookId
		) {
			try {
				$count = $this->assignmentsDeleteFor(
					$delEntity, $bookId, $entityId
				);
				die("Es wurden $count Zuweisungen gelöscht.");
			}
			catch(Exception $e) {
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

	// protected function assignmentsDeleteFor($delEntity, $bookId, $entityId) {

	// 	$entityParam = false;
	// 	$qb = $this->_em->createQueryBuilder()
	// 		->delete('DM:SchbasUserShouldLendBook', 'usb');
	// 	switch($delEntity) {
	// 		case 'book':
	// 			// We want to delete all assignments for the book, no filtering
	// 			// necessary
	// 			$entityParam = false;
	// 			break;
	// 		case 'gradelevel':
	// 			$qb->innerJoin('usb.user', 'u')
	// 				->innerJoin('u.attendances', 'a')
	// 				->innerJoin('a.schoolyear', 's', 'WITH', 's.active = 1')
	// 				->innerJoin(
	// 					'a.grade', 'g', 'WITH', 'g.gradelevel = :entity'
	// 				);
	// 			$entityParam = $entityId;
	// 			break;
	// 		case 'grade':
	// 			$grade = $this->_em->getReference(
	// 				'DM:SystemGrades', $entityId
	// 			);
	// 			$qb->innerJoin('usb.user', 'u')
	// 				->innerJoin('u.attendances', 'a')
	// 				->innerJoin('a.schoolyear', 's', 's.active = 1')
	// 				->innerJoin('a.grade', 'g');
	// 			$qb->andWhere('g = :entity');
	// 			$entityParam = $grade;
	// 			break;
	// 		case 'user':
	// 			$user = $this->_em->getReference('DM:SystemUsers', $entityId);
	// 			$qb->andWhere('usb.user = :entity');
	// 			$entityParam = $user;
	// 			break;
	// 	}
	// 	$book = $this->_em->getReference('DM:SchbasBook', $bookId);
	// 	$qb->andWhere('usb.book = :book');
	// 	$query = $qb->getQuery();
	// 	var_dump($query->getSql());
	// 	if($entityParam) {
	// 		$query->setParameter('entity', $entityParam);
	// 	}
	// 	$query->setParameter('book', $book);
	// 	$delCount = $query->getResult();
	// 	return $delCount;
	// }

	protected function assignmentsDeleteFor($delEntity, $bookId, $entityId) {

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
					->innerJoin('a.schoolyear', 's', 'WITH', 's.active = 1')
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
					->innerJoin('a.schoolyear', 's', 's.active = 1')
					->innerJoin('a.grade', 'g');
				$qb->andWhere('g = :grade');
				$qb->setParameter('grade', $grade);
				break;
			case 'user':
				$user = $this->_em->getReference('DM:SystemUsers', $entityId);
				$qb->andWhere('usb.user = :user');
				$qb->setParameter('user', $user);
				break;
		}
		$book = $this->_em->getReference('DM:SchbasBook', $bookId);
		$qb->andWhere('usb.book = :book');
		$qb->setParameter('book', $book);
		$query = $qb->getQuery();
		$entries = $query->getResult();
		foreach($entries as $entry) {
			$this->_em->remove($entry);
		}
		$this->_em->flush();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_validDeleteEntities = ['book', 'gradelevel', 'grade', 'user'];
}

?>
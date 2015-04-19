<?php

namespace administrator\Schbas\BookAssignments\Add;

require_once PATH_ADMIN . '/Schbas/BookAssignments/BookAssignments.php';

class Add extends \administrator\Schbas\BookAssignments\BookAssignments {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		var_dump($_POST);
		var_dump($_GET);
		$bookId = filter_input(INPUT_POST, 'bookId');
		$entityType = filter_input(INPUT_POST, 'entityType');
		$entityId = filter_input(INPUT_POST, 'entityId');
		$schoolyearId = filter_input(INPUT_POST, 'schoolyearId');
		var_dump($_POST);
		var_dump($_GET);

		if(
			$bookId && $entityType && $entityId &&
			in_array($entityType, $this->_supportedEntities)
		) {
			$this->assignmentsToEntityAdd(
				$bookId, $entityType, $entityId, $schoolyearId
			);
		}
		else {
			dieHttp('Parameter fehlen / sind inkorrekt', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Adds the new book-assignments to the given entity
	 * @param int    $bookId       The book-id of the book to assign
	 * @param string $type         The type of the entity to assign the books
	 *                             to
	 * @param int    $id           The identifier of the entity
	 * @param int    $schoolyearId The schoolyear-id of the entities and
	 *                             assignments
	 */
	protected function assignmentsToEntityAdd(
		$bookId, $type, $id, $schoolyearId
	) {
		$book = $this->_em->getReference('DM:SchbasBook', '$bookId');
		$schoolyear = $this->_em->getReference(
			'DM:SystemSchoolyears', $schoolyearId
		);
		try {
			$users = $this->usersGetByEntity($type, $id, $schoolyear);
			if($users) {
				foreach($users as $user) {
					$entry = new \Babesk\ORM\SchbasUserShouldLendBook();
					$entry->setUser($user);
					$entry->setBook($book);
					$entry->setSchoolyear($schoolyear);
					$this->_em->persist($entry);
				}
				$this->_em->flush();
				die('Die Zuweisungen wurden erfolgreich hinzugefügt');
			}
			else {
				dieHttp('Konnte die Benutzer zum Hinzufügen nicht abrufen',
					500);
			}
		}
		catch(\Exception $e) {
			$this->_logger->logO('Could not add the assignments', [
				'sev' => 'error', 'moreJson' => ['bookId' => $bookId,
					'entityType' => $type, 'entityId' => $id,
					'schoolyearId' => $schoolyearId, 'msg' => $e->getMessage()]
			]);
			dieHttp('Ein Fehler ist beim Hinzufügen der Zuweisungen ' .
				'aufgetreten', 500);
		}
	}

	protected function usersGetByEntity($type, $id, $schoolyear) {

		// Get all users to which the assignments should be added
		$qb = $this->_em->createQueryBuilder()
			->select('u')
			->from('DM:SystemUsers', 'u');
		switch($type) {
			case 'user':
				$user = $this->_em->getReference('DM:SystemUsers', $id);
				$qb->innerJoin('u.attendances', 'a')
					->andWhere('a.schoolyear = :schoolyear')
					->andWhere('u = :user')
					->setParameter('user', $user);
				break;
			case 'grade':
				$grade = $this->_em->getReference('DM:SystemGrades', $id);
				$qb->innerJoin('u.attendances', 'a')
					->innerJoin(
						'a.schoolyear', 'sy', 'WITH', 'sy = :schoolyear'
					)->andWhere('a.grade = :grade')
					->setParameter('grade', $grade);
				break;
			case 'gradelevel':
				$qb->innerJoin('u.attendances', 'a')
					->innerJoin(
						'a.schoolyear', 'sy', 'WITH', 'sy = :schoolyear'
					)->innerJoin('a.grade', 'g')
					->andWhere('g.gradelevel = :gradelevel')
					->setParameter('gradelevel', $id);
				break;
			default:
				return false;
		}
		$query = $qb->getQuery();
		$users = $query->getResult();
		return $users;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_supportedEntities = [
		'user',
		'grade',
		'gradelevel'
	];

}

?>
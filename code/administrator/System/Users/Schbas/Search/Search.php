<?php

namespace administrator\System\Users\Schbas\Search;

require_once PATH_ADMIN . '/System/Users/Schbas/Schbas.php';

class Search extends \administrator\System\Users\Schbas\Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$selectors = $this->selectorQueryPartGet($_GET);
		$activeSchoolyear = $this->_em->getRepository('DM:SystemSchoolyears')
			->findOneByActive(true);
		if(!$activeSchoolyear) {
			dieHttp('Konnte aktives Schuljahr nicht finden.', 500);
		}
		try {
			$qb = $this->_em->createQueryBuilder()
				->select($selectors)
				->from('DM:SystemUsers', 'u');

			//Additional data
			if(isset($_GET['includeShouldLendBooks'])) {
				$qb->leftJoin('u.booksToLend', 'btl');
				$qb->leftJoin('btl.book', 'usb');
			}
			if(isset($_GET['includeLendingBooks'])) {
				$qb->leftJoin('u.bookLending', 'bl');
				$qb->leftJoin('bl.book', 'blb');
			}

			//Filters
			if(isset($_GET['grade'])) {
				$grade = $this->_em->find('DM:SystemGrades', $_GET['grade']);
				if(!$grade) {
					dieHttp("Klasse mit Id $_GET[grade] nicht gefunden", 400);
				}
				$qb->innerJoin(
					'u.attendances', 'grade_a',
					'WITH', 'grade_a.schoolyear = :activeSchoolyear'
				);
				$qb->innerJoin('grade_a.grade', 'gg', 'WITH', 'gg = :grade');
				$qb->setParameter('activeSchoolyear', $activeSchoolyear);
				$qb->setParameter('grade', $grade);
			}
			if(isset($_GET['gradelevel'])) {
				$gradelevel = filter_input(
					INPUT_GET, 'gradelevel', FILTER_VALIDATE_INT
				);
				if($gradelevel === false || $gradelevel === null) {
					dieHttp("Klassenstufe '$gradelevel' nicht korrekt", 400);
				}
				$qb->innerJoin(
					'u.attendances', 'gl_a',
					'WITH', 'gl_a.schoolyear = :activeSchoolyear'
				);
				$qb->innerJoin(
					'gl_a.grade', 'gl_g', 'WITH',
					'gl_g.gradelevel = :gradelevel'
				);
				$qb->setParameter('activeSchoolyear', $activeSchoolyear);
				$qb->setParameter('gradelevel', $gradelevel);
			}

			$query = $qb->getQuery();
			$users = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
			echo '<pre>';
			var_dump($users);

		} catch(\Exception $e) {
			$this->_logger->logO('Could not search for users',
				['sev' => 'error', 'moreJson' => $e->getMessage()]);
			dieHttp('Fehler beim Suchen.', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function selectorQueryPartGet($params) {

		$selectors = ['partial u.{id, forename, name}'];
		if(isset($params['includeShouldLendBooks'])) {
			$selectors[] = 'partial btl.{id}';
			$selectors[] = 'partial usb.{id, title}';
		}
		if(isset($params['includeLendingBooks'])) {
			$selectors[] = 'partial bl.{id}';
			$selectors[] = 'partial blb.{id, title}';
		}
		return $selectors;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}
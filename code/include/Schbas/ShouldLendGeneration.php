<?php

namespace Babesk\Schbas;

require_once PATH_INCLUDE . '/Schbas/Loan.php';

/**
 * Generates the entries for the table SchbasUsersShouldLendBooks
 */
class ShouldLendGeneration {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($dataContainer) {

		$this->_dataContainer = $dataContainer;
		$this->_em = $dataContainer->getEntityManager();
		$this->_logger = clone($dataContainer->getLogger());
		$this->_logger->categorySet(
			'Babesk/Schbas/Loan/ShouldLendGeneration'
		);
		$this->_loanHelper = new Loan($this->_dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function generate() {

		$this->generateInit();
		$entries = [];
		foreach($this->_users as $user) {
			$gradelevel = $this->gradelevelGet($user);
			if(!$gradelevel) { continue; }
			$subjects = $this->userSubjectsCalc($user, $gradelevel);
			$classes = $this->_loanHelper->gradelevel2IsbnIdent(
				$gradelevel
			);
			if(!$classes) { continue; }
			foreach($this->_books as $book) {
				$bookSubject = $book->getSubject();
				if(!$bookSubject) { continue; }
				$bookSubjectAbbreviation = $bookSubject->getAbbreviation();
				// Book is for other gradelevels than the user is in
				if(!in_array($book->getClass(), $classes)) { continue; }
				if($this->isBookFiltered(
					$bookSubjectAbbreviation, $subjects, $gradelevel)
				) {
					$this->entryAdd($user, $book);
				}
			}
		}
		// Commit changes
		$this->_em->flush();
		return true;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function generateInit() {

		$this->preparationSchoolyearFetch();
		$this->usersFetch();
		$this->booksFetch();
		$this->filtersFetch();
		$this->specialCourseTriggerFetch();
	}

	protected function preparationSchoolyearFetch() {

		$this->_preparationSchoolyear = $this->_loanHelper
			->schbasPreparationSchoolyearGet();
		if(!$this->_preparationSchoolyear) {
			$this->_logger->log(
				'Vorbereitungsschuljahr nicht gesetzt.', 'error'
			);
			throw new \Exception('schbasPreparationSchoolyear not found');
		}
	}

	protected function usersFetch() {

		try {
			$userQuery = $this->_em->createQuery(
				'SELECT partial u.{
						id, religion, foreign_language, special_course
					},
					uigs, partial g.{id, gradelevel}
				FROM DM:SystemUsers u
				INNER JOIN u.attendances uigs
				INNER JOIN uigs.schoolyear sy WITH sy = :schoolyear
				INNER JOIN uigs.grade g
			');
			$userQuery->setParameter(
				'schoolyear', $this->_preparationSchoolyear
			);
			//Silly doctrine, dont lazy-load oneToOne-entries automatically
			$userQuery->setHint(
				\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true
			);
			$this->_users = $userQuery->getResult();
		}
		catch (\Doctrine\ORM\Query\QueryException $e) {
			$this->_logger->logO('Could not fetch the users',
				['sev' => 'error', 'moreJson' => $e->getMessage()]);
			throw new \Exception('Could not fetch the users');
		}
	}

	protected function booksFetch() {

		// Fetch the books
		try {
			$bookQuery = $this->_em->createQuery(
				'SELECT partial b.{id, class}, partial s.{id, abbreviation}
				FROM DM:SchbasBook b
				INNER JOIN b.subject s
			');
			$this->_books = $bookQuery->getResult();
		}
		catch (\Doctrine\ORM\Query\QueryException $e) {
			$this->_logger->logO('Could not fetch the books',
				['sev' => 'error', 'moreJson' => $e->getMessage()]);
			throw new \Exception('Could not fetch the books');
		}
	}

	protected function filtersFetch() {

		// Init the filter-array
		// It filters the books based on their subject.
		// The filter-list define the special subjects to which the user must
		// explicitly attend, else books of this subject will not be assigned
		// to the user.
		list($lang, $rel, $course) = $this->_loanHelper
			->bookSubjectFilterArrayGet();
		$this->_bookFilterWithCourse = array_merge($lang, $rel, $course);
		$this->_bookFilter = array_merge($lang, $rel);
	}

	protected function specialCourseTriggerFetch() {

		$triggerObj = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('special_course_trigger');
		$this->_specialCourseTrigger = (int)$triggerObj->getValue();
	}

	protected function gradelevelGet($user) {

		$grade = $user->getAttendances()
			->first()
			->getGrade();
		if(!$grade) {
			return false;
		}
		$gradelevel = $grade->getGradelevel();
		return $gradelevel;
	}

	protected function userSubjectsCalc($user, $gradelevel) {

		$userSubjects = array_merge(
			explode('|', $user->getReligion()),
			explode('|', $user->getForeignLanguage())
		);
		if($this->_specialCourseTrigger >= $gradelevel) {
			$userSubjects = array_merge(
				$userSubjects,
				explode('|', $user->getSpecialCourse())
			);
		}
		return $userSubjects;
	}

	/**
	 * Checks if the book gets filtered out or not.
	 *
	 * The conditions are:
	 * - If the user has a subject which the book also has, he needs to lend
	 *   the book.
	 * - If he has not, but the books subject is also not in the filter, he
	 *   needs to lend it regardles.
	 * - If he has not, but the books subject is in the filter, he does not
	 *   need to lend it.
	 *
	 * @return boolean             Returns true if the book does not get
	 *                             filtered.
	 */
	protected function isBookFiltered($bookSubject, $subjects, $gradelevel) {
		// Does the user explicitly participate to the subject of the book?
		if(in_array($bookSubject, $subjects)) {
			return true;
		}
		else {
			$bookFilter = [];
			if($this->_specialCourseTrigger > $gradelevel) {
				$bookFilter = $this->_bookFilter;
			}
			else {
				// Check for filtering with special_course
				$bookFilter = $this->_bookFilterWithCourse;
			}
			if(in_array($bookSubject, $bookFilter)) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	protected function entryAdd($user, $book) {

		$entry = new \Babesk\ORM\SchbasUserShouldLendBook();
		$entry->setUser($user);
		$entry->setBook($book);
		$entry->setSchoolyear($this->_preparationSchoolyear);
		$this->_em->persist($entry);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_dataContainer;
	protected $_em;
	protected $_logger;

	protected $_loanHelper;

	protected $_preparationSchoolyear;
	protected $_users;
	protected $_books;

	/**
	 * Contains school-subjects with which to filter the books.
	 *
	 * The book-filter consists of entries of the SystemGlobalSettings-table,
	 * namely 'religion' and 'foreign_language'.
	 */
	protected $_bookFilter;

	/**
	 * Contains school-subjects with which to filter the books.
	 *
	 * The book-filter consists of entries of the SystemGlobalSettings-table,
	 * namely 'religion', 'foreign_language' and 'special_course'.
	 */
	protected $_bookFilterWithCourse;

	protected $_specialCourseTrigger;
}

?>
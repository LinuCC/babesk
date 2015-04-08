<?php

namespace Babesk\Schbas;

/**
 * Contains operations useful for the loan-process of Schbas
 */
class Loan {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($dataContainer) {

		$this->entryPoint($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns an array of possible isbn-identifiers for the given gradelevel
	 * @param  int    $gradelevel The Gradelevel
	 * @return array              The array of possible isbn-identifiers of the
	 *                            gradelevel, or false if gradelevel not found
	 */
	public function gradelevel2IsbnIdent($gradelevel) {

		if(!empty($this->_gradelevelIsbnIdentAssoc[$gradelevel])) {
			return $this->_gradelevelIsbnIdentAssoc[$gradelevel];
		}
		else {
			return false;
		}
	}

	/**
	 * Returns an array of gradelevels associated with the given isbnIdentifier
	 * @param  string $ident The identifier (like '69' or '05')
	 * @return array         The array of gradelevels or false if none found
	 */
	public function isbnIdent2Gradelevel($ident) {

		$gradelevels = array();
		//Extract possible gradelevels
		foreach($this->_gradelevelIsbnIdentAssoc as $gradelevel => $idents) {
			if(in_array($ident, $idents)) {
				$gradelevels[] = $gradelevel;
			}
		}
		if(count($gradelevels)) {
			return $gradelevels;
		}
		else {
			return false;
		}
	}

	/**
	 * Calculates the loan-price of a book by its full price and its class
	 * @param  float  $flatPrice The full price of the book
	 * @param  string $class     The class of the book (like "05" or "92")
	 * @return float             The resulting loan-price
	 */
	public function bookLoanPriceCalculate($flatPrice, $class) {

		if(isset($this->_classToPriceFactor[$class])) {
			$factor = $this->_classToPriceFactor[$class];
			$loanPrice = $flatPrice / $factor / 3;
			return $loanPrice;
		}
		else {
			throw new Exception('No book-class "' . $class . '" found.');
		}
	}

	/**
	 * Calculates the reduced loan-price of a book by its price and its class
	 * @param  float  $flatPrice The full price of the book
	 * @param  string $class     The class of the book (like "05" or "92")
	 * @return float             The resulting reduced loan-price
	 */
	public function bookReducedLoanPriceCalculate($flatPrice, $class) {

		if(isset($this->_classToPriceFactor[$class])) {
			$factor = $this->_classToPriceFactor[$class];
			$loanPrice = $flatPrice / $factor / 3 * 0.8;
			return $loanPrice;
		}
		else {
			throw new Exception('No book-class "' . $class . '" found.');
		}
	}

	public function loanPriceOfAllBookAssignmentsForUserCalculate($user) {

		$books = $this->loanBooksGet($user);
		$feeNormal = 0.00;
		$feeReduced = 0.00;
		foreach($books as $book) {
			$normalPrice = $this->bookLoanPriceCalculate(
				$book->getPrice(), $book->getClass()
			);
			$reducedPrice = $this->bookReducedLoanPriceCalculate(
				$book->getPrice(), $book->getClass()
			);
			$feeNormal += $normalPrice;
			$feeReduced += $reducedPrice;
		}
		$feeNormal = round($feeNormal);
		$feeReduced = round($feeReduced);
		return array($feeNormal, $feeReduced);
	}

	/**
	 * Fetches the books the user should lend but has not done so yet
	 * Filters out the books the user has already lent and those that the user
	 * will buy by himself.
	 *
	 * @param  Object $user The \Babesk\ORM\SystemUsers object
	 * @return array        An array of Doctrine-Objects representing the books
	 */
	public function loanBooksGet($user) {

		try {
			$schoolyear = $this->schbasPreparationSchoolyearGet();
			// We want all entries where the book has _not_ been lend
			// and will _not_ be bought by the user himself, so we check for
			// null
			$query = $this->_em->createQuery(
				'SELECT b, usb FROM DM:SchbasBook b
				INNER JOIN b.usersShouldLend usb
				INNER JOIN usb.user u
				LEFT JOIN u.bookLending l
				LEFT JOIN l.book bLend WITH bLend = b
				LEFT JOIN u.selfpayingBooks sb WITH sb = b
				WHERE usb.schoolyear = :schoolyear
					AND usb.user = :user
					AND bLend IS NULL
					AND sb IS NULL
			');
			$query->setParameter('schoolyear', $schoolyear);
			$query->setParameter('user', $user);
			$books = $query->getResult();
			return $books;

		} catch (Exception $e) {
			$this->_logger->log('Could not fetch the loanBooks',
				['sev' => 'error', 'moreJson' => $e->getMessage()]);
		}
	}

	/**
	 * Calculates the books the users should lend.
	 * @param  bool   $isNextYear If true, it will be assumed that all users
	 *                            move one grade up
	 */
	public function loanBooksCalculate($isNextYear) {

		$preparationSchoolyear = $this->schbasPreparationSchoolyearGet();
		if(!$preparationSchoolyear) {
			$this->_logger->log('Vorbereitungsschuljahr nicht gesetzt.');
			return false;
		}
		// Fetch the users
		try {
			$userQuery = $this->_em->createQuery(
				'SELECT partial u.{
						id, religion, foreign_language, special_course
					},
					uigs, partial g.{id, gradelevel}
				FROM DM:SystemUsers u
				INNER JOIN u.attendances uigs
				INNER JOIN uigs.schoolyear sy WITH sy.active = true
				INNER JOIN uigs.grade g
			');
			//Silly doctrine, dont lazy-load oneToOne-entries automatically
			$userQuery->setHint(
				\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true
			);
			$users = $userQuery->getResult();
		} catch (\Doctrine\ORM\Query\QueryException $e) {
			$this->_logger->log('Could not fetch the users');
			return false;
		}
		// Fetch the books
		try {
			$bookQuery = $this->_em->createQuery(
				'SELECT partial b.{id, class} FROM DM:SchbasBook b'
			);
			$books = $bookQuery->getResult();
		} catch (\Doctrine\ORM\Query\QueryException $e) {
			$this->_logger->log('Could not fetch the books', 'error');
			return false;
		}

		// Init the filter-array
		// It filters the books based on their subject.
		// The filter-list define the special subjects to which the user must
		// explicitly attend, else books of this subject will not be assigned
		// to the user.
		list($lang, $rel, $course) = $this->bookSubjectFilterArrayGet();
		$filterWithCourse = array_merge($lang, $rel, $course);
		$filter = array_merge($lang, $rel);
		$triggerObj = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('special_course_trigger');
		$courseTrigger = (int)$triggerObj->getValue();

		// Create new entries
		$entries = array();

		foreach($users as $user) {
			$grade = $user->getAttendances()
				->first()
				->getGrade();
			if(!$grade) {
				continue;
			}
			$gradelevel = $grade->getGradelevel();
			$gradelevel += ($isNextYear) ? 1 : 0;
			$userSubjects = array_merge(
				explode('|', $user->getReligion()),
				explode('|', $user->getForeignLanguage())
			);
			if($courseTrigger >= $gradelevel) {
				$userSubjects = array_merge(
					$userSubjects,
					explode('|', $user->getSpecialCourse())
				);
			}
			if(!empty($this->_gradelevelIsbnIdentAssoc[$gradelevel])) {
				foreach($books as $book) {
					$validClasses =
						$this->_gradelevelIsbnIdentAssoc[$gradelevel];
					if(
						in_array($book->getClass(), $validClasses) &&
						(
							// Filter the non-needed books
							(
								$courseTrigger >= $gradelevel &&
								!in_array($book->getClass(), $filterWithCourse)
							) || (
								!in_array($book->getClass(), $filter)
							)
						)
					) {
						$entry = new \Babesk\ORM\SchbasUserShouldLendBook();
						$entry->setUser($user);
						$entry->setBook($book);
						$entry->setSchoolyear($preparationSchoolyear);
						$this->_em->persist($entry);
					}
				}
			}
		}
		$this->_em->flush();
		return true;
	}

	/**
	 * Returns the schoolyear for which schbas is getting prepared
	 * @return \Babesk\ORM\SystemSchoolyears on success or a false value
	 */
	public function schbasPreparationSchoolyearGet() {

		$sySetting = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('schbasPreparationSchoolyearId');
		//Add entry if not existing
		if(!$sySetting) {
			$sySetting = new \Babesk\ORM\SystemGlobalSettings();
			$sySetting->setName('schbasPreparationSchoolyearId');
			$sySetting->setValue('');
			$this->_em->persist($sySetting);
			$this->_em->flush();
		}
		$syEntry = $this->_em->getRepository('DM:SystemSchoolyears')
			->findOneById($sySetting->getValue());
		return $syEntry;
	}

	public function booksAssignedToGradelevelsGet() {

		$books = $this->_em->getRepository('DM:SchbasBook')
			->findAll();
		$booksInGradelevels = array();
		foreach($books as $book) {
			$gradelevels = $this->isbnIdent2Gradelevel($book->getClass());
			$booksInGradelevels[] = array(
				'book' => $book,
				'gradelevels' => $gradelevels
			);
		}
		return $booksInGradelevels;
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		$this->_pdo = $dataContainer->getPdo();
		$this->_em = $dataContainer->getEntityManager();
		$this->_logger = clone($dataContainer->getLogger());
		$this->_logger->categorySet('Babesk/Schbas/Loan');
	}

	protected function bookSubjectFilterArrayGet() {

		$gsRepo = $this->_em->getRepository(
			'DM:SystemGlobalSettings'
		);
		$lang   = $gsRepo->findOneByName('foreign_language')->getValue();
		$rel    = $gsRepo->findOneByName('religion')->getValue();
		$course = $gsRepo->findOneByName('special_course')->getValue();
		$langAr = explode('|', $lang);
		$relAr = explode('|', $rel);
		$courseAr = explode('|', $course);
		return [$langAr, $relAr, $courseAr];
	}

	/**
	 * Returns all Books assigned to the user by gradelevel and not lend by him
	 * @param  int    $userId The ID of the user
	 * @return array          Books not lend by user, but at the gradelevel
	 */
	protected function booksNotLendToUserByHisGradelevelGet($userId) {

		$gradelevel = $this->activeGradelevelOfUserGet($userId);
		if(empty($gradelevel)) {
			$this->_logger->log('User missing an active gradelevel',
				'Notice', Null, json_encode(array('uid' => $userId)));

		}
		if(empty($this->_gradelevelIsbnIdentAssoc[$gradelevel])) {
			$this->_logger->log('Not found bookclasses for gradelevel',
				'Notice', Null, json_encode(array('uid' => $userId)));
			return array();
		}
		$classes = $this->_gradelevelIsbnIdentAssoc[$gradelevel];
		$classesStr = '"' . implode('", "', $classes) . '"';
		$stmt = $this->_pdo->prepare(
			"SELECT b.*, ss.abbreviation AS subject FROM SchbasBook b
				LEFT JOIN SystemSchoolSubjects ss ON ss.ID = b.subjectId
				LEFT JOIN (
					SELECT book_id
						FROM SchbasInventory i
						INNER JOIN SchbasLending l ON l.inventory_id = i.id
						WHERE l.user_id = :userId
				) lendings ON lendings.book_id = b.id
				WHERE b.class IN(${classesStr})
					-- We want only entries that are _not_ lend to the user
					AND lendings.book_id IS NULL
		");
		$stmt->execute(array('userId' => $userId));
		$books = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $books;
	}

	/**
	 * Returns the users gradelevel of the grade being in the active schoolyear
	 * @param  int    $userId The ID of the user
	 * @return int            The gradelevel
	 * @todo   Probably want to extract this function to System/SystemUsers
	 */
	protected function activeGradelevelOfUserGet($userId) {

		$stmt = $this->_pdo->prepare(
			'SELECT g.gradelevel FROM SystemUsers u
				INNER JOIN SystemAttendances a
					ON a.userId = u.ID
				INNER JOIN SystemGrades g ON g.ID = a.gradeId
				WHERE a.schoolyearId = @activeSchoolyear
					AND u.ID = :userId
		');
		$stmt->execute(array('userId' => $userId));
		return $stmt->fetchColumn();
	}

	/**
	 * Removes the books the user does not have to lend from the booklist
	 * @param  int    $userId The ID of the user
	 * @param  array  $books  The list of books from which to remove the ones
	 *                        that dont need to be lend by the user
	 * @return array          The booklist with some books removed
	 */
	protected function optionalBooksNotNeededByUserRemove($userId, $books) {

		//Get global values stating which booktypes generally exists
		$settings = $this->_pdo->query(
			'SELECT name, value FROM SystemGlobalSettings
				WHERE name IN(
						"foreign_language", "religion",
						"special_course", "special_course_trigger"
					)
		')->fetchAll(\PDO::FETCH_KEY_PAIR);
		//Split all settings that are string-lists into arrays
		foreach($settings as $key => $setting) {
			if($key == 'special_course_trigger') {
				continue;
			}
			$settings[$key] = explode('|', $setting);
		}
		//Get values for user which booktypes he needs to lend
		$userStmt = $this->_pdo->prepare(
				'SELECT foreign_language, religion, special_course
					FROM SystemUsers u
					WHERE u.ID = :userId
			');
		$userStmt->execute(array('userId' => $userId));
		$userSettings = $userStmt->fetch(\PDO::FETCH_ASSOC);
		foreach($userSettings as $key => $setting) {
			$userSettings[$key] = explode('|', $setting);
		}
		$gradelevel = $this->activeGradelevelOfUserGet($userId);
		$languagesToRemove = array_diff(
			$settings['foreign_language'], $userSettings['foreign_language']
		);
		$religionsToRemove = array_diff(
			$settings['religion'], $userSettings['religion']
		);
		$coursesToRemove = array_diff(
			$settings['special_course'], $userSettings['special_course']
		);
		//special_course can contain the same keys as religions, like RE
		//Make sure that the user gets the RE-Book even if he is RE in
		//special_course, but not in religion
		$religionsToRemove = array_diff(
			$religionsToRemove, $userSettings['special_course']
		);
		//Remove books with the unneeded booktypes
		$filteredBooks = array();
		foreach($books as $book) {
			if(
				!in_array($book['subject'], $languagesToRemove) &&
				!in_array($book['subject'], $religionsToRemove) &&
				!(
					(int) $gradelevel >= $settings['special_course_trigger'] &&
					in_array($book['subject'], $coursesToRemove)
				)
			) {
				$filteredBooks[] = $book;
			}
		}
		return $filteredBooks;
	}

	/**
	 * Fetches the book-ids the user buys by himself
	 * @param  int    $userId The ID of the user
	 * @return array          An array of book-ids
	 */
	protected function selfpaidBooksByUserGet($userId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT BID FROM SchbasSelfpayer WHERE UID = :userId
			');
			$stmt->execute(array('userId' => $userId));
			return $stmt->fetchAll(\PDO::FETCH_COLUMN);

		} catch (Exception $e) {
			$this->_logger->log('Error fetching the selfpaid books by user',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Subtracts the books the user buys himself from the given booklist
	 * @param  int    $userId The ID of the user to fetch the selfpaid books
	 * @param  array  $books  The array of books to subtract from
	 * @return array          The resulting array of books that has been
	 *                        subtracted
	 */
	protected function selfpaidBooksOfUserSubtract($userId, $books) {

		$selfpaidBooks = $this->selfpaidBooksByUserGet($userId);
		foreach($books as $key => $book) {
			if(in_array($book['id'], $selfpaidBooks)) {
				unset($books[$key]);
			}
		}
		return $books;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_gradelevelIsbnIdentAssoc = array(
		'5'  => array('05', '56'),
		'6'  => array('56', '06', '69', '67'),
		'7'  => array('78', '07', '69', '79', '67'),
		'8'  => array('78', '08', '69', '79', '89'),
		'9'  => array('90', '91', '09', '92', '69', '79', '89'),
		'10' => array('90', '91', '10', '92'),
		'11' => array('12', '92', '13'),
		'12' => array('12', '92', '13')
	);

	//Maps the book-classes to the pricefactor with which the flatPrice to
	//divide. Corresponds to the amount of years the user is lend the book.
	protected $_classToPriceFactor = array(
		"05" => 1,
		"06" => 1,
		"07" => 1,
		"08" => 1,
		"09" => 1,
		"10" => 1,
		"56" => 2,
		"67" => 2,
		"78" => 2,
		"89" => 2,
		"90" => 2,
		"12" => 2,
		"13" => 2,
		"79" => 3,
		"91" => 3,
		"69" => 4,
		"92" => 4
	);

	protected $_pdo;
	protected $_em;
	protected $_logger;
}

?>

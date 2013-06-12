<?php

/**
 * Shows the display-User-Dialog and handles the data
 */
class UserDisplayAll {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($smarty) {

		$this->_smarty = $smarty;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Displays all of the Users
	 */
	public function displayAll() {
		$this->_smarty->display(PATH_SMARTY_ADMIN_TEMPLATES .
			'/headmod_System/modules/mod_User/displayAll.tpl');
	}

	/**
	 * Fetches the appropriate Userdata neede to Display all of them
	 *
	 * This function gets invoked by an Javascript-Script. It parses the data
	 * and fetches userdata in one Query. It sends JSON-encoded data back.
	 *
	 * @uses  $_POST['pagenumber'] The pagenumber to be displayed
	 * @uses  $_POST['usersPerPage'] How many Users are displayed per Page
	 * @uses  $_POST['sortFor'] What column should be sorted
	 * @uses  $_POST['filterForCol'] What Column should be filtered
	 * @uses  $_POST['filterForVal'] The value to filter for
	 */
	public function fetchUsersOrganized() {


		$pagenumber = $_POST['pagenumber'];
		$usersPerPage = $_POST['usersPerPage'];
		$sortFor = $_POST['sortFor'];
		$filterForCol = $_POST['filterForCol'];
		$filterForVal = $_POST['filterForVal'];
		$toEscape = array(&$pagenumber, &$usersPerPage, &$sortFor, &$filterForCol, &$filterForVal);
		TableMng::sqlEscapeByArray($toEscape);
		$userToStart = $pagenumber * $usersPerPage;
		$filterForQuery = '';

		if(empty($_POST['columnsToFetch'])) {
			$columnsToFetch = array();
		}
		else {
			$columnsToFetch = $_POST['columnsToFetch'];
			foreach($columnsToFetch as &$col) {
				TableMng::sqlEscape($col);
			}
		}

		//When user didnt select anything to sort For, default to name
		if(empty($sortFor)) {
			$sortFor = 'name';
		}

		try {
			$queryCreator = new UserDisplayAllQueryCreator($filterForQuery,
				$sortFor, $userToStart, $usersPerPage);
			$query = $queryCreator->createQuery($columnsToFetch, $sortFor, $filterForCol);
			$countQuery = $queryCreator->createCountOfQuery($columnsToFetch, $sortFor, $filterForCol);

			//Fetch the Userdata
			TableMng::query('SET @activeSy :=
				(SELECT ID FROM schoolYear WHERE active = "1");');
			$data = TableMng::query($query, true);
			$usercount = TableMng::query($countQuery, true);

			// No division by zero!
			if($usersPerPage != 0) {
				$pagecount = floor((int)$usercount[0]['count'] / (int)$usersPerPage);
			}
			else {
				$pagecount = 1;
			}

			$data = $this->fetchedDataToReadable($data, $columnsToFetch);

		} catch (Exception $e) {
			die(json_encode(array('value' => 'error',
							'message' => 'Ein Fehler ist bei der Datenverarbeitung aufgetreten.' . $e->getMessage())));
		}

		die(json_encode(array('value' => 'data',
						'users' => $data,
						'pagecount' => $pagecount)));
	}

	public function fetchShowableColumns() {

		$columns = array();

		$userdata = TableMng::query("SELECT * FROM users LIMIT 1, 1", true);

		foreach($userdata[0] as $key => $data) {
			if(!empty($this->_userColumnTranslations[$key])) {
				$columns[$key] = $this->_userColumnTranslations[$key];
			}
		}
		$columns['schoolyears'] = 'Schuljahre';
		$columns['grades'] = 'Klassen';
		$columns['activeGrade'] = 'aktive Klasse';

		// //Messages-Module existing
		// if(count(TableMng::query("SHOW TABLES LIKE 'Message';", true))) {
		// 	$columns['countMessageReceived'] = 'Nachrichten empfangen';
		// 	$columns['countMessageSend'] = 'Nachrichten abgeschickt';
		// }
		// //Kuwasys existing
		// if(count(TableMng::query("SHOW TABLES LIKE 'class';", true))) {
		// 	$columns['countClass'] = 'Kurse';
		// }
		//Cards existing
		if(count(TableMng::query("SHOW TABLES LIKE 'cards';", true))) {
			$columns['cardnumber'] = 'Kartennummer';
		}
		//Babesk existing
		// if(count(TableMng::query("SHOW TABLES LIKE 'orders';", true))) {
			// $columns['countOrders'] = 'Bestellungen';
		// }

		die(json_encode(array('value' => 'data', 'message' => $columns)));
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Converts boolean data and other stuff to user-friendly readable data
	 *
	 * For Example, the boolean '1' and '0' get converted to false or true
	 *
	 * @param  Array $data The data to search for
	 * @return Array The converted data
	 */
	protected function fetchedDataToReadable($data, $columnsToFetch) {

		$yes = 'Ja';
		$no = 'Nein';

		foreach($data as &$user) {

			if(isset($user['soli'])) {
				$user['soli'] = ($user['soli']) ? $yes : $no;
			}
			if(isset($user['first_passwd'])) {
				$user['first_passwd'] = ($user['first_passwd']) ? $yes : $no;
			}
			if(isset($user['locked'])) {
				$user['locked'] = ($user['locked']) ? $yes : $no;
			}
			if(isset($user['credit'])) {
				$user['credit'] = number_format($user['credit'], 2, '.', '');
			}
			if(in_array('cardnumber', $columnsToFetch) &&
				!isset($user['cardnumber'])) {
				$user['cardnumber'] = 'Keine';
			}
		}

		return $data;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;

	protected $_userColumnTranslations = array(
		'ID' => 'ID',
		'forename' => 'Vorname',
		'name' => 'Name',
		'username' => 'Benutzername',
		'password' => 'Passwort',
		'email' => 'Emailadresse',
		'telephone' => 'Telefonnummer',
		'GID' => 'Preisgruppe',
		'birthday' => 'Geburtstag',
		'first_passwd' => 'ist erstes Passwort',
		'credit' => 'Guthaben',
		'soli' => 'ist Soli');
}

class UserDisplayAllQueryCreator {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($filterForQuery, $sortFor, $userToStart,
		$usersPerPage) {

		$this->_filterForQuery = $filterForQuery;
		$this->_sortFor = $sortFor;
		$this->_userToStart = $userToStart;
		$this->_usersPerPage = $usersPerPage;
	}


	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function createQuery($columns, $toSortFor, $toFilterFor) {

		foreach($columns as $col) {
			$this->addSubquery($col);
		}
		if(!empty($toSortFor)) {
			$this->addSubquery($toSortFor);
		}
		if(!empty($toFilterFor)) {
			$this->addSubquery($toFilterFor);
		}

		$this->concatQuery();

		return $this->_query;
	}

	public function createCountOfQuery($columns, $toSortFor, $toFilterFor) {

		foreach($columns as $col) {
			$this->addSubquery($col);
		}
		if(!empty($toSortFor)) {
			$this->addSubquery($toSortFor);
		}
		if(!empty($toFilterFor)) {
			$this->addSubquery($toFilterFor);
		}

		$this->concatCountQuery();

		return $this->_countQuery;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function addSubquery($col) {
		switch($col) {
			case 'grades':
				$this->gradeQuery();
				break;
			case 'cardnumber':
				$this->cardsQuery();
				break;
			case 'activeGrade':
				$this->gradeQuery();
				break;
			case 'schoolyears':
				$this->schoolyearQuery();
				break;
		}
	}

	protected function concatQuery() {

		$this->_querySelect = rtrim($this->_querySelect, ', ');
		if($this->_querySelect != '') {
			$this->_querySelect = ", $this->_querySelect";
		}

		$this->_query = "SELECT u.*, u.ID AS userId $this->_querySelect
			FROM users u
				$this->_queryJoin
			$this->_filterForQuery
			GROUP BY u.ID
			ORDER BY $this->_sortFor
			LIMIT $this->_userToStart, $this->_usersPerPage";
	}

	protected function concatCountQuery() {

		$this->_countQuery = "SELECT COUNT(*) AS count
			FROM users u
				$this->_queryJoin
			$this->_filterForQuery";
	}

	protected function cardsQuery() {

		if(!$this->_cardsQueryDone) {
			$this->addSelectStatement('cards.cardnumber AS cardnumber');
			$this->addJoinStatement('LEFT JOIN
				(SELECT UID, cardnumber FROM cards) cards
				ON cards.UID = u.ID');
			$this->_cardsQueryDone = true;
		}
	}

	protected function schoolyearQuery() {

		if(!$this->_schoolyearQueryDone) {
			$this->addSelectStatement('GROUP_CONCAT(sy.label
					SEPARATOR "<br />")
				AS schoolyears');
			$this->addJoinStatement('LEFT JOIN jointUsersInSchoolYear uisy
				ON uisy.UserID = u.ID
			LEFT JOIN schoolYear sy ON sy.ID = uisy.SchoolYearID');
			$this->_schoolyearQueryDone = true;
		}
	}

	protected function gradeQuery() {

		if(!$this->_gradeQueryDone) {
			$this->addSelectStatement('GROUP_CONCAT(
				CONCAT(g.gradeValue, "-", g.label)
				SEPARATOR "<br />") AS grades,
				activeGrade.activeGrade AS activeGrade');
			$this->addJoinStatement('
				LEFT JOIN jointUsersInGrade uig ON uig.UserID = u.ID
				LEFT JOIN grade g ON uig.GradeID = g.ID
				LEFT JOIN (
					SELECT CONCAT(gradeValue, "-", label)
						AS activeGrade, uig.UserID AS userId
					FROM grade g
					JOIN jointGradeInSchoolYear gisy
						ON gisy.GradeID = g.ID
					JOIN jointUsersInGrade uig ON g.ID = uig.GradeID
					WHERE gisy.SchoolYearID = @activeSy) activeGrade
						ON u.ID = activeGrade.userId');
			$this->_gradeQueryDone = true;
		}
	}

	protected function addSelectStatement($st) {

		$this->_querySelect .= "$st, ";
	}

	protected function addJoinStatement($st) {

		$this->_queryJoin .= " $st ";
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_querySelect = '';

	protected $_queryJoin = '';

	protected $_query = '';
	protected $_countQuery = '';

	protected $_gradeQueryDone = false;
	protected $_schoolyearQueryDone = false;
	protected $_cardsQueryDone = false;

	protected $_filterForQuery;
	protected $_sortFor;
	protected $_userToStart;
	protected $_usersPerPage;
}

?>

<?php

/**
 * Shows the display-User-Dialog and handles the data
 */
class UserDisplayAll {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($dataContainer) {

		$this->_pdo = $dataContainer->getPdo();
		$this->_smarty = $dataContainer->getSmarty();
		$this->_acl = $dataContainer->getAcl();
		$this->activeModulesAddSelectableData();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Displays all of the Users
	 */
	public function displayAll() {
		$this->_smarty->display(PATH_SMARTY_TPL . '/administrator' .
			'/headmod_System/modules/mod_User/display-all.tpl');
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
		$userToStart = ($pagenumber - 1) * $usersPerPage;
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

		//When joining multiple tables, we have multiple IDs
		if($filterForVal == 'ID') {
			$filterForVal = 'u.ID';
		}

		//When user didnt select anything to sort For, default to name
		if(empty($sortFor)) {
			$sortFor = 'name';
		}

		try {
			$queryCreator = new UserDisplayAllQueryCreator(
				$this->_pdo,
				$filterForQuery,
				$sortFor,
				$userToStart,
				$usersPerPage
			);
			$query = $queryCreator->createQuery($columnsToFetch, $sortFor,
				$filterForCol, $filterForVal);
			$countQuery = $queryCreator->createCountOfQuery($columnsToFetch,
				$sortFor, $filterForCol, $filterForVal);

			//Fetch the Userdata
			TableMng::query('SET @activeSy :=
				(SELECT ID FROM SystemSchoolyears WHERE active = "1");');
			$data = TableMng::query($query);
			$usercount = TableMng::query($countQuery);

			// var_dump($usercount);

			// No division by zero, never show zero sites
			if($usersPerPage != 0 && $usercount[0]['count'] > 0) {
				$pagecount = ceil((int)$usercount[0]['count'] / (int)$usersPerPage);
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

		// $columns = array();

		// $userdata = TableMng::query("SELECT *
		// 	FROM SystemUsers LIMIT 1, 1");

		// foreach($userdata[0] as $key => $data) {
		// 	if(!empty($this->_selectableColumns[$key])) {
		// 		$columns[$key] = $this->_selectableColumns[$key];
		// 	}
		// }

		// // //Messages-Module existing
		// // if(count(TableMng::query("SHOW TABLES LIKE 'MessageMessages';"))) {
		// // 	$columns['countMessageReceived'] = 'Nachrichten empfangen';
		// // 	$columns['countMessageSend'] = 'Nachrichten abgeschickt';
		// // }
		// // //Kuwasys existing
		// // if(count(TableMng::query("SHOW TABLES LIKE 'KuwasysClasses';"))) {
		// // 	$columns['countClass'] = 'Kurse';
		// // }
		// //Cards existing
		// if(count(TableMng::query("SHOW TABLES LIKE 'BabeskCards';"))) {
		// 	$columns['cardnumber'] = 'Kartennummer';
		// }
		// //Babesk existing
		// // if(count(TableMng::query("SHOW TABLES LIKE 'BabeskOrders';"))) {
		// 	// $columns['countOrders'] = 'Bestellungen';
		// // }

		die(json_encode(array('value' => 'data',
			'message' => $this->_selectableColumns)));
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

	/**
	 * Adds various Options for the User depending on Headmodule-Activation
	 */
	protected function activeModulesAddSelectableData() {

		//Columns for Kuwasys
		if($this->_acl->moduleGet('root/administrator/Kuwasys')) {
			$this->selectableAdd(array(
				'classes' => 'diesjährige Kurswahlen',
			));
		}

		//Columns for Babesk
		if($this->_acl->moduleGet('root/administrator/Babesk')) {

			$this->selectableAdd(array(
					'GID' => 'Preisgruppe',
					'credit' => 'Guthaben',
					'cardnumber' => 'Kartennummer',
					'soli' => 'ist Soli'
			));
		}
	}

	/**
	 * Adds the Options given so the User can select the Columns, too
	 *
	 * @param  array  $columns The Options to add
	 */
	protected function selectableAdd(array $columns) {

		$this->_selectableColumns = array_merge(
			$this->_selectableColumns,
			$columns);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;

	protected $_selectableColumns = array(
		'ID' => 'ID',
		'forename' => 'Vorname',
		'name' => 'Name',
		'username' => 'Benutzername',
		'password' => 'Passwort',
		'email' => 'Emailadresse',
		'telephone' => 'Telefonnummer',
		'schoolyears' => 'Schuljahre',
		'grades' => 'Klassen',
		'activeGrade' => 'aktive Klasse',
		'birthday' => 'Geburtstag',
		'first_passwd' => 'ist erstes Passwort'
	);

	protected $_pdo;
}



/******************************************************************************
 * Creates the Query to Display the Users
 *****************************************************************************/

class UserDisplayAllQueryCreator {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($pdo, $filterForQuery, $sortFor, $userToStart,
		$usersPerPage) {

		$this->_pdo = $pdo;
		$this->_filterForQuery = $filterForQuery;
		$this->_sortFor = $sortFor;
		$this->_userToStart = $userToStart;
		$this->_usersPerPage = $usersPerPage;
		$this->_userElementsToFetch = array();
	}


	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function createQuery($columns, $toSortFor, $toFilterColumn, $toFilterValue) {

		foreach($columns as $col) {
			$this->addSubquery($col);
		}
		if(!empty($toSortFor)) {
			$this->addSubquery($toSortFor);
		}
		if(!empty($toFilterColumn)) {
			$this->addSubquery($toFilterColumn);
		}

		$filterQuery = $this->filterForQuery($toFilterColumn, $toFilterValue, 'HAVING');
		$this->concatQuery($filterQuery);

		return $this->_query;
	}

	public function createCountOfQuery($columns, $toSortFor, $toFilterColumn, $toFilterValue) {

		foreach($columns as $col) {
			$this->addSubquery($col);
		}
		if(!empty($toSortFor)) {
			$this->addSubquery($toSortFor);
		}
		if(!empty($toFilterColumn)) {
			$this->addSubquery($toFilterColumn);
		}

		$filterQuery = $this->filterForQuery($toFilterColumn, $toFilterValue, 'HAVING');
		$this->concatCountQuery($filterQuery);

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
			case 'classes':
				$this->classesQuery();
				break;
			default:   //Else guess that its a field in the users-table
				$this->_userElementsToFetch[] = $this->quoteIdentifier(
					$col
				);
				break;
		}
	}

	protected function concatQuery($filterQuery) {

		$this->_querySelect = rtrim($this->_querySelect, ', ');
		if($this->_querySelect != '') {
			$this->_querySelect = ", $this->_querySelect";
		}

		$userSelect = implode(', ', $this->_userElementsToFetch);

		$this->_query = "SELECT u.ID AS ID, $userSelect $this->_querySelect
			FROM SystemUsers u
				$this->_queryJoin
			GROUP BY u.ID
			$filterQuery
			ORDER BY $this->_sortFor
			LIMIT $this->_userToStart, $this->_usersPerPage";
	}

	protected function concatCountQuery($filterQuery) {

		$this->_countQuery = "SELECT COUNT(*) AS count FROM
		(SELECT u.ID AS userId, u.forename AS forename, u.name AS name, u.username AS username, u.email AS email, u.telephone AS telephone, u.GID AS GID, u.birthday AS birthday,
			u.soli AS soli, u.first_passwd AS first_passwd
			$this->_querySelect
					FROM SystemUsers u
						$this->_queryJoin
					GROUP BY u.ID
					$filterQuery) counting";
	}

	protected function filterForQuery($toFilterColumn, $toFilterValue,
		$statement) {

		if(!empty($toFilterColumn) && !empty($toFilterValue)) {
			return "$statement $toFilterColumn LIKE '%$toFilterValue%'";
		}
		else {
			return '';
		}
	}

	protected function cardsQuery() {

		if(!$this->_cardsQueryDone) {
			$this->addSelectStatement('BabeskCards.cardnumber AS cardnumber');
			$this->addJoinStatement('LEFT JOIN
				(SELECT UID, cardnumber FROM BabeskCards) BabeskCards
				ON BabeskCards.UID = u.ID');
			$this->_cardsQueryDone = true;
		}
	}

	protected function schoolyearQuery() {

		if(!$this->_schoolyearQueryDone) {
			$this->addSelectStatement('GROUP_CONCAT(DISTINCT sy.label
					SEPARATOR "<br />")
				AS schoolyears');
			$this->addJoinStatement(
				'LEFT JOIN SystemUsersInGradesAndSchoolyears uigsy
				ON uigsy.userId = u.ID
			LEFT JOIN SystemSchoolyears sy ON sy.ID = uigsy.schoolyearId');
			$this->_schoolyearQueryDone = true;
		}
	}

	protected function gradeQuery() {

		if(!$this->_gradeQueryDone) {
			$this->addSelectStatement('GROUP_CONCAT( DISTINCT
				CONCAT(g.gradelevel, "-", g.label)
				SEPARATOR "<br />") AS grades,
				activeGrade.activeGrade AS activeGrade');

			$this->addJoinStatement('
				LEFT JOIN SystemUsersInGradesAndSchoolyears uigsg
					ON uigsg.userId = u.ID
				LEFT JOIN SystemGrades g ON uigsg.gradeId = g.ID
				LEFT JOIN (
					SELECT CONCAT(gradelevel, "-", label)
						AS activeGrade, uigsg.userId AS userId
					FROM SystemGrades g
					JOIN SystemUsersInGradesAndSchoolyears uigsg ON
						uigsg.gradeId = g.ID AND
						uigsg.schoolyearId = @activeSchoolyear
					) activeGrade
						ON u.ID = activeGrade.userId');
			$this->_gradeQueryDone = true;
		}
	}

	protected function classesQuery() {

		if(!$this->_classesQueryDone) {
			$this->addSelectStatement(
				'GROUP_CONCAT(
					kuwasys_c.label, "  <i>(", kuwasys_uics.translatedName,
						")</i>"
					SEPARATOR "<hr/>"
				) AS classes'
				);

			$this->addJoinStatement('
				LEFT JOIN KuwasysUsersInClasses kuwasys_uic
					ON kuwasys_uic.UserID = u.ID
				LEFT JOIN KuwasysClasses kuwasys_c
					ON kuwasys_c.Id = kuwasys_uic.ClassID
					AND kuwasys_c.schoolyearId = @activeSchoolyear
				LEFT JOIN KuwasysUsersInClassStatuses kuwasys_uics
					ON kuwasys_uics.ID = kuwasys_uic.statusId
				');
			$this->_classesQueryDone = true;
		}
	}

	protected function addSelectStatement($st) {

		$this->_querySelect .= "$st, ";
	}

	protected function addJoinStatement($st) {

		$this->_queryJoin .= " $st ";
	}

	/**
	 * Quotes an identifier so that it is not vulnerable to SQL-Injection
	 * @param  string $ident The SQL-identifier to quote
	 * @return string        The quoted (with backticks) identifier
	 */
	protected function quoteIdentifier($ident) {
		return '`' . str_replace('`', '``', $ident) . '`';
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
	protected $_classesQueryDone = false;

	protected $_filterForQuery;
	protected $_sortFor;
	protected $_userToStart;
	protected $_usersPerPage;

	protected $_userElementsToFetch;

	/**
	 * The Accesscontrollayer, To find out what Headmodules are active
	 * @var Acl
	 */
	protected $_acl;

	protected $_pdo;
}

?>

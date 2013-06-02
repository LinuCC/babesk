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

		$pagenumber = mysql_real_escape_string($_POST['pagenumber']);
		$usersPerPage = mysql_real_escape_string($_POST['usersPerPage']);
		$sortFor = mysql_real_escape_string($_POST['sortFor']);
		$filterForCol = mysql_real_escape_string($_POST['filterForCol']);
		$filterForVal = mysql_real_escape_string($_POST['filterForVal']);
		$userToStart = $pagenumber * $usersPerPage;
		$filterForQuery = '';

		//When user didnt select anything to sort For, default to name
		if(empty($sortFor)) {
			$sortFor = 'name';
		}
		//only add a WHERE-clause if User wants to filter something
		if(!empty($filterForVal) && !empty($filterForCol)) {
			$filterForQuery = "WHERE $filterForCol LIKE '%$filterForVal%'";
		}

		try {
			//Fetch the Userdata
			TableMng::query('SET @activeSy :=
				(SELECT ID FROM schoolYear WHERE active = "1");');
			$data = TableMng::query(
				"SELECT u.*, u.ID AS userId, cards.cardnumber AS cardnumber,
					GROUP_CONCAT(sy.label SEPARATOR '<br />') AS schoolyears,
					GROUP_CONCAT( CONCAT(g.gradeValue, '-', g.label)
						SEPARATOR '<br />') AS grades,
					activeGrade.activeGrade AS activeGrade
				FROM users u
					LEFT JOIN (SELECT UID, cardnumber FROM cards) cards
						ON cards.UID = u.ID
					LEFT JOIN jointUsersInSchoolYear uisy
						ON uisy.UserID = u.ID
					LEFT JOIN schoolYear sy ON sy.ID = uisy.SchoolYearID
					LEFT JOIN jointUsersInGrade uig ON uig.UserID = u.ID
					LEFT JOIN grade g ON uig.GradeID = g.ID
					LEFT JOIN (
						SELECT CONCAT(gradeValue, '-', label)
							AS activeGrade, uig.UserID AS userId
						FROM grade g
						JOIN jointGradeInSchoolYear gisy
							ON gisy.GradeID = g.ID
						JOIN jointUsersInGrade uig ON g.ID = uig.GradeID
						WHERE gisy.SchoolYearID = @activeSy) activeGrade
							ON u.ID = activeGrade.userId
				$filterForQuery
				GROUP BY u.ID
				ORDER BY $sortFor
				LIMIT $userToStart, $usersPerPage", true);

			$usercount = TableMng::query(
				"SELECT COUNT(*) AS count FROM users $filterForQuery", true);

			// No division by zero!
			if($usersPerPage != 0) {
				$pagecount = floor((int)$usercount[0]['count'] / (int)$usersPerPage);
			}
			else {
				$pagecount = 1;
			}

			$data = $this->fetchedDataToReadable($data);

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
		if(count(TableMng::query("SHOW TABLES LIKE 'orders';", true))) {
			$columns['countOrders'] = 'Bestellungen';
		}

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
	protected function fetchedDataToReadable($data) {

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

?>
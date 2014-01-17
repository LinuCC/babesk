<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';
require_once PATH_INCLUDE . '/CsvReader.php';

/**
 * Imports Csv-Data and puts them in temporary tables allowing to update users
 *
 * Table-Structure:
 * Table "UserUpdateTempUsers" contains the users that should be updated.
 * Table "UserUpdateTempConflicts" contains the conflicts the importer has to
 * resolve before updating the user.
 * Conflict:
 * A conflict is an unlikely change of a user that the importer has to
 * manually resolve.
 * - The difference of gradelevels of users can be too high to
 * be normal.
 * - Users are only in csv-file, but not already in the database (and this
 * schoolyear). Either user is new, or an csv-file error occured.
 * - Users are only in database (and this schoolyear), but not in csv-file.
 * Either user is not in the school anymore, or error occured.
 */
class CsvImport extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		if(!isset($_SESSION['UserUpdateWithSchoolyearChange']['switchType']) ||
			!isset($_SESSION['UserUpdateWithSchoolyearChange']['schoolyearId'])
			) {
			$this->_interface->dieError(_g('Missing Session-Variables! ' .
				'Please begin again.'));
		}

		$this->entryPoint($dataContainer);
		if(isset($_FILES['csvFile'])) {
			$this->csvParse($_FILES['csvFile']['tmp_name']);
			//Now execute the SessionMenu-Module
			$mod = new \ModuleExecutionCommand('root/administrator/System/' .
				'User/UserUpdateWithSchoolyearChange/SessionMenu');
			$this->_dataContainer->getAcl()->moduleExecute(
				$mod, $this->_dataContainer
			);
		}
		else {
			$this->uploadFormDisplay();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->_delimiter = ',';

		$this->_gumpRules = array(
				'forename' => array(
					'required||alpha_dash_space||min_len,,2||max_len,,64',
					'', 'forename'
				),
				'name' => array(
					'required||alpha_dash_space||min_len,,2||max_len,,64',
					'', 'name'
				),
				'grade' => array(
					'required||min_len,,2||max_len,,24||regex,,' .
					'/^\d{1,2}-\w{1,2}|\d{1,2}[a-zA-Z]{1,2}$/',
					'', 'grade'
				)
			);
	}

	/**
	 * Displays a form allowing the user to upload a csvfile
	 */
	private function uploadFormDisplay() {

		$this->displayTpl('csvImport.tpl');
	}

	/**
	 * Parses the uploaded csvfile
	 * Dies displaying a message on error or success
	 * @param  String $filepath The path to the uploaded csvfile
	 */
	private function csvParse($filepath) {

		$content = $this->csvContentGet($filepath);
		$this->csvCheck($content);
		$this->userExistenceCompare($content);
		$this->gradeConflictsCreate();
		// $this->gimmeDebug();
		$this->dataUpload();
	}

	private function gimmeDebug() {

		echo '<pre>';
		echo '<br /><b>IN BEIDEM:</b><br />';
		var_dump($this->_usersInBoth);
		echo '<br /><b>IN CSV:</b><br />';
		var_dump($this->_usersInCsv);
		echo '<br /><b>IN DATENBANK:</b><br />';
		var_dump($this->_usersInDb);
		echo '<br /><b>GRADECONFLICT:</b><br />';
		var_dump($this->_usersWithGradeConflicts);
		echo '</pre>';
	}

	/**
	 * Extracts the content from the csvfile
	 * @param  String $filepath The path to the csv-file
	 * @return array            The content of the csv-file
	 */
	private function csvContentGet($filepath) {

		try {
			$reader = new \CsvReader($filepath, $this->_delimiter);
			$content = $reader->getContents();
			return $content;

		} catch(\Exception $e) {
			$this->_logger->log('Error while parsing the csvfile', 'Notice',
				NULL, json_encode(array('msg' => $e->getMessage())));
			$this->_smarty->assign('backlink', 'index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|CsvImport');
			$this->_interface->dieError(_g('An error occured while parsing ' .
				'the csvfile: %1$s', $e->getMessage()));
		}
	}

	/**
	 * Checks the csvContent for missing/errornous stuff
	 * Dies displaying a message on error
	 * @param  array  $content The csv-content
	 */
	private function csvCheck($content) {

		require_once PATH_INCLUDE . '/gump.php';

		$this->csvCheckColumns($content);
		$this->csvCheckGump($content);
	}

	/**
	 * Checks if all necessary columns are contained in the csv-file
	 * Dies displaying a message on error
	 * @param  array  $content the content of the csv-file
	 */
	private function csvCheckColumns($content) {

		$errorcol = '';

		if(!isset($content[0]['forename'])) {
			$errorcol = 'forename';
		}
		if(!isset($content[0]['name'])) {
			$errorcol = 'name';
		}
		if(!isset($content[0]['grade'])) {
			$errorcol = 'grade';
		}

		if(!empty($errorcol)) {
			$this->_interface->dieError(_g('An error occured while importing' .
				' the csv-file. The file is missing the column "%1$s"!',
				$errorcol));
		}
	}

	/**
	 * Checks all entries for their correctness. Uses gump
	 * Dies displaying a message on error
	 * @param  array  $content the content of the csv-file
	 */
	private function csvCheckGump($content) {

		$errors = array();

		$gump = new \Gump();
		//workaround problem of pipe and commata in regex in gumpRules
		$gump->set_validation_rule_delimiter('||');
		$gump->set_validation_rule_param_delimiter(',,');
		$gump->rules($this->_gumpRules);
		foreach($content as $row) {
			if(!$gump->run($row)) {
				$errors[] = $gump->get_readable_string_errors(true);
			}
		}

		if(count($errors)) {
			$errormsg = '';
			foreach($errors as $error) {
				$errormsg .= $error;
			}
			$this->_smarty->assign('backlink', 'index.php?module=' .
				'administrator|System|User|UserUpdateWithSchoolyearChange|' .
				'CsvImport');
			$this->_interface->dieError(
				_g('Errors occured while importing the csv-file. ' .
				'Please correct them and try again.<br />%1$s', $errormsg));
		}
	}

	/**
	 * Compares the existence of users in csv-file and database
	 * Allows to see which users should be deleted or added
	 * @param  array  $content The content of the csv-file
	 */
	private function userExistenceCompare($content) {

		require_once PATH_INCLUDE . '/ArrayFunctions.php';

		$dbUsers = $this->usersOfActiveYearGet();

		foreach($dbUsers as $dbUser) {
			foreach($content as $csvUser) {
				if($dbUser['forename'] == $csvUser['forename'] &&
					$dbUser['name'] == $csvUser['name']
					) {
					//user is in both the csv and database
					$entry = array('db' => $dbUser, 'csv' => $csvUser);
					$this->_usersInBoth[] = $entry;
					break;
				}
				else {
					//user is in database but not in csv
					$this->_usersInDb[] = $dbUser;
					break;
				}
			}
		}

		//Indexing for faster searches
		$dbUserForenames = \ArrayFunctions::arrayColumn($dbUsers, 'forename');
		$dbUserSurnames = \ArrayFunctions::arrayColumn($dbUsers, 'name');
		foreach($content as $index => $user) {
			//Check if users are in csv but not in database
			if(!in_array($user['forename'], $dbUserForenames, true) ||
				!in_array($user['name'], $dbUserSurnames, true)
				) {
				$this->_usersInCsv[] = $user;
			}
		}
	}

	/**
	 * Fetches and returns all users of the active schoolyear
	 * @return array the users of the active schoolyear
	 */
	private function usersOfActiveYearGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT u.ID AS userId, u.forename AS forename, u.name AS name,
					g.gradelevel AS gradelevel
				FROM users u
					JOIN usersInGradesAndSchoolyears uigs ON u.ID = uigs.userId
					JOIN Grades g ON g.ID = uigs.gradeId
				WHERE uigs.schoolyearId = @activeSchoolyear'
			);

		} catch(\PDOException $e) {
			$this->_logger->log('Could not fetch the users', 'Notice', Null,
				json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the users!'));
		}

		return $res->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Creates conflicts based on comparisments of gradelevels of csv and db
	 */
	private function gradeConflictsCreate() {

		if($_SESSION['UserUpdateWithSchoolyearChange']['switchType'] == 0) {
			//A full-year change. Normally gradelevels of users then should be
			//one higher in csv than in db.
			$normalGradeChange = 1;
		}
		else {
			//half-year change
			$normalGradeChange = 0;
		}

		//The allowed difference between the gradelevels of the db and the csv
		$noConflictScope = $this->conflictScopeGet();

		$normalDiffMax = $normalGradeChange + $noConflictScope;
		$normalDiffMin = $normalGradeChange - $noConflictScope;

		if(count($this->_usersInBoth)) {
			foreach($this->_usersInBoth as $user) {
				$csvlevel = $this->gradelevelOfGradeStringGet(
					$user['csv']['grade']);
				$dblevel = (int)$user['db']['gradelevel'];
				$diff = $csvlevel - $dblevel;
				if(!($diff <= $normalDiffMax) || !($diff >= $normalDiffMin)) {
					//add conflict
					$this->_usersWithGradeConflicts[] = $user['db']['userId'];
				}
			}
		}
	}

	/**
	 * Extracts the gradelevel from a given grade-string and returns it
	 * @param  string $grade the full grade, like "5a" or "5-1"
	 * @return int           The gradelevel
	 */
	private function gradelevelOfGradeStringGet($grade) {

		if(strpos($grade, '-') !== False) {
			$data = explode('-', $grade);
			return (int)$data[0];
		}
		else {
			$level = preg_replace('/[A-Za-z]+/', '', $grade);
			return (int)$level;
		}
	}

	/**
	 * Splits the grade-String and returns the level and label of the grade
	 * @param  string $grade The whole grade
	 * @return array         <"0"> => <gradelevel>, <"1"> => <gradelabel>
	 */
	private function gradeStringSplit($grade) {

		if(strpos($grade, '-') !== False) {
			$data = explode('-', $grade);
			return array((int)$data[0], $data[1]);
		}
		else {
			$level = preg_replace('/[A-Za-z]+/', '', $grade);
			$label = str_replace($level, '', $grade);
			return array((int)$level, $label);
		}
	}

	/**
	 * Fetches the conflict-scope and returns it
	 * @return int The allowed difference of the gradelevels
	 */
	private function conflictScopeGet() {

		try {
			$res = $this->_pdo->query('SELECT value
				FROM global_settings WHERE
				name = "userUpdateWithSchoolyearChangeGradelevelConflictScope"
			');
			return (int)$res->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log('Could not fetch the conflict scope',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the conflict ' .
				'scope from server!'));
		}
	}

	private function dataUpload() {

		$this->userDbTableCreate();
		$this->conflictDbTableCreate();
		$this->usersDbOnlyUpload();
		$this->usersInBothUpload();
		$this->usersCsvOnlyUpload();
	}

	/**
	 * Uploads the users that were in both the csv and database
	 * Dies displaying a message on error
	 */
	private function usersInBothUpload() {

		if(!count($this->_usersInBoth)) {
			return;
		}

		try {
			$stmtu = $this->_pdo->prepare('INSERT INTO `UserUpdateTempUsers`
				(origUserId, forename, name, gradelevel, label) VALUES
				(?, ?, ?, ?, ?)');

			$stmtc = $this->_pdo->prepare(
				'INSERT INTO `UserUpdateTempConflicts`
					(userId, type, solved) VALUES (?,?,?)
			');

			foreach($this->_usersInBoth as $user) {
				list($level, $label) = $this->gradeStringSplit(
					$user['csv']['grade']
				);
				$stmtu->execute(array(
					$user['db']['userId'], $user['db']['forename'],
					$user['db']['name'], $level, $label
				));
				if(array_search($user['db']['userId'],
					$this->_usersWithGradeConflicts)
					) {   // User has grade-conflict
					$uid = $this->_pdo->lastInsertId();
					$stmtc->execute(array($uid, 'GradelevelConflict', 0));
				}
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the users in both csv and db',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Uploads the users that were only in the csv-file, not in the database
	 * Dies displaying a message on error
	 */
	private function usersCsvOnlyUpload() {

		try {
			$stmtu = $this->_pdo->prepare(
				'INSERT INTO `UserUpdateTempUsers`
					(origUserId, forename, name, gradelevel, label) VALUES
					(?, ?, ?, ?, ?)
			');
			$stmtc = $this->_pdo->prepare(
				'INSERT INTO `UserUpdateTempConflicts`
					(userId, type, solved) VALUES (?,?,?)
			');

			foreach($this->_usersInCsv as $user) {
				list($level, $label) = $this->gradeStringSplit(
					$user['grade']
				);
				//Add user-entry
				$stmtu->execute(array(
					'0', $user['forename'], $user['name'], $level, $label
				));
				$uid = $this->_pdo->lastInsertId();
				//Add conflict
				$stmtc->execute(array($uid, 'CsvOnlyConflict', 0));
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the CsvOnlyConflict-users',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Uploads the users that were only in the database
	 * Dies displaying a message on error
	 */
	private function usersDbOnlyUpload() {

		try {
			$stmtc = $this->_pdo->prepare(
				'INSERT INTO `UserUpdateTempConflicts`
					(userId, type, solved) VALUES (?,?,?)
			');

			foreach($this->_usersInDb as $user) {
				$stmtc->execute(array($user['userId'], 'DbOnlyConflict', 0));
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the DbOnlyConflict-users',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Creates the table of the database containing the temporary user-changes
	 */
	private function userDbTableCreate() {

		$res = $this->_pdo->query(
			'DROP TABLE IF EXISTS `UserUpdateTempUsers`;
			CREATE TABLE `UserUpdateTempUsers` (
				`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`origUserId` int(11) unsigned NOT NULL,
				`forename` varchar(64) NOT NULL,
				`name` varchar(64) NOT NULL,
				`gradelevel` int(3) NOT NULL,
				`label` varchar(255) NOT NULL,
				PRIMARY KEY (`ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;'
		);

		if(!$res) {
			$this->_logger->log('Error creating table UserUpdateTempUsers',
				'Notice', Null);
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Creates the database-table containing the conflicts
	 */
	private function conflictDbTableCreate() {

		//Using MyISAM here because of InnoDBs abysmal performance
		//(190x slower)
		$res = $this->_pdo->query(
			'DROP TABLE IF EXISTS `UserUpdateTempConflicts`;
			CREATE TABLE `UserUpdateTempConflicts` (
				`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`userId` int(11) unsigned NOT NULL,
				`type` enum(
					"CsvOnlyConflict", "DbOnlyConflict", "GradelevelConflict"
				) NOT NULL,
				`solved` int(1) unsigned NOT NULL,
				PRIMARY KEY (`ID`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
		);

		if(!$res) {
			$this->_logger->log('Error creating table UserUpdateTempConflicts',
				'Notice', Null);
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_delimiter;

	/**
	 * Users that are in both the csv-file and in the database
	 * @var array <index> => [<csv> => [<userId>, ...], <db> => <userId>, ...]]
	 */
	private $_usersInBoth;

	/**
	 * Users that are in the csv-file but not in the database
	 * @var array <index> => [<userId>, ...]
	 */
	private $_usersInCsv;

	/**
	 * Users that are not in the csv-file but are existing in the database
	 * @var array <index> => [<userId>, ...]
	 */
	private $_usersInDb;

	/**
	 * Users whose gradelevels-differences are different from expected
	 * @var array <index> => <userId>
	 */
	private $_usersWithGradeConflicts;

	/**
	 * Rules for checking the csv-fields
	 * @var array
	 */
	private $_gumpRules;
}

?>
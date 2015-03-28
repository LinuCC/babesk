<?php
/**
 * Provides a class to manage the booklist of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the lending list, provides methods to add/modify the lending list or to get information from the lending list.
 */
class LoanManager extends TableManager{

	public function __construct() {
		parent::__construct('SchbasLending');
	}


	/** Sorts the lending list for a UserID it gets from MySQL-table and returns them
	 * Used by mod_retour !!
	*/
	function getLoanlistByUID($uid) {

		require_once PATH_ACCESS . '/DBConnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE user_id = "%s"', $this->tablename, $uid));
		$result = $this->db->query($query);
		if (!$result) {
			/**
			 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
			 * throw DB_QUERY_ERROR.$this->db->error;
			 */
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;


	}

	/**
	 * Sorts a list of books, which should lend for a User.
	 * Used by mod_loan!!
	 */
	function getLoanByUID($uid, $ajax) {
		require_once PATH_ACCESS . '/DBConnect.php';
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		$userManager = new UserManager;
		$bookManager = new BookManager;
		$inventoryManager = new InventoryManager;
		$globalSettingsManager = new GlobalSettingsManager;
		$lang_str = $globalSettingsManager->getForeignLanguages();
		$reli_str = $globalSettingsManager->getReligion();
		$course_str = $globalSettingsManager->getCourse();

		$lang = explode('|', $lang_str);
		$reli = explode('|', $reli_str);
		$course = explode('|', $course_str);
		$details = $this->fetchUserDetails($uid);

		$user_lang = explode('|', $details['foreign_language']);
		$lang = array_diff($lang, $user_lang);

		$user_reli = explode('|', $details['religion']);
		$reli = array_diff($reli, $user_reli);

		$user_course = explode('|', $details['special_course']);
		$course = array_diff($course, $user_course);

		//special_course can contain the same keys as religions, like RE
		//Make sure that the user gets the RE-Book even if he is RE in
		//special_course, but not in religion
		$reli = array_diff($reli, $user_course);

		$regex ='/[^0-9]/'; //keep only numbers in class assign
		$sct = intval($globalSettingsManager->valueGet("special_course_trigger"));

		//if () { // filter special_courses if class larger than 10 a.k.a. "Oberstufe"


		$books = $bookManager->getBooksByClass($details['class']);

		//Wenn Buch nicht von Schueler benötigt, aus dem Array löschen
		//Die arrays $lang, $reli und $course enthalten die Einträge die der
		//Benutzer _nicht_ braucht.
		//$course wird hierbei nur betrachtet, wenn der Benutzer in der
		//Oberstufe ist => ist der Benutzer nicht im Oberstufenkurs, werden nur
		//$lang und $reli Bücher herausgefiltert, die restlichen Bücher aus der
		//Klasse kriegt er alle angedreht
		$counter = 0;
		if ($books){
			foreach ($books as &$book){
				if (in_array($book['subject'], $lang) OR
					in_array($book['subject'], $reli) OR ((
						intval(
							preg_replace($regex,'',$details['class'])
						) >= $sct) AND
						in_array($book['subject'], $course)
					)){
					unset($books[$counter]);
				}
				$counter++;
			}
		}
		//Hole alle Verleihungen an den Schüler
		$query = sql_prev_inj(sprintf('SELECT inventory_id FROM %s WHERE user_id=%s', $this->tablename, $uid));
		$result = $this->db->query($query);
		if (!$result) {
			throw new Exception('Konnte die Ausleihen nicht abrufen');
		}
		while($buffer = $result->fetch_assoc())
			$minusbooksinv[] = $buffer['inventory_id'];
		//Entferne die Bücher, die der Benutzer bereits ausgeliehen hat
		if (isset($minusbooksinv)) {

			foreach ($minusbooksinv as &$minusbookinv){
				$minusbooks[] = $inventoryManager->getBookIDByInvID($minusbookinv);
			}
			$counter = 0;
			if ($books) {
				$books = array_values($books); // notwendig, um array neu zu indexieren. verursacht sonst fehler.
				foreach ($books as &$book){
					if (in_array($book['id'], $minusbooks)) {
						unset($books[$counter]);
					}
					$counter++;
				}
			}
		}
		return $books;
	}

	/**
	 * Remove an entry in the loan list by a given user id and inventory id
	 */
	function RemoveLoanByIDs($inventoryID, $uid) {
		require_once PATH_ACCESS . '/DBConnect.php';
		$query = sql_prev_inj(sprintf('user_id = %s AND inventory_id = %s' , $uid, $inventoryID));
		$result = parent::delEntryNoID($query);
	}

	/**
	 * Search, whether an user_id exists.
	 */
	function isUserEntry($uid) {
		$match = parent::existsEntry('user_id', $uid);
		return $match;
		}

	/**
	 * Add an entry in the loan list by a given user id and inventory id
	 */
	function AddLoanByIDs($inventoryID, $uid) {
		require_once PATH_ACCESS . '/DBConnect.php';
		$date = date("Y-m-d");
		$result = parent::addEntry('user_id', $uid, 'inventory_id', $inventoryID, 'lend_date', $date);
		return $result;
	}

	/**
	 * Search, whether an inventory_id is scanned yet.
	 */
	function isEntry($inventory_id) {
		$match = parent::existsEntry('inventory_id', $inventory_id);
		return $match;
		}


	function getUserIDByInvID($invID){
		$query = sql_prev_inj(sprintf('SELECT user_id FROM %s WHERE inventory_id="%s"', $this->tablename, $invID));
		$result = $this->db->query($query);
		$uid_arr = $result->fetch_assoc();
		$uid = $uid_arr['user_id'];
		if(!$uid) {
			throw new MySQLVoidDataException('MySQL returned no data!');
			}
		return $uid;
		}

	public function fetchUserDetails($userId) {

		$userDetails = TableMng::query(sprintf(
			'SELECT u.*,
			(SELECT CONCAT(g.gradelevel, g.label) AS class
				FROM SystemAttendants uigs
				LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
				WHERE uigs.userId = u.ID AND
					uigs.schoolyearId = @activeSchoolyear) AS class
			FROM SystemUsers u WHERE `ID` = %s', $userId));


		return $userDetails[0];
	}
}
?>

<?php

namespace administrator\System\User;

require_once 'User.php';

class SearchForSimilarUsers extends \User {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		//Defaults when parameters not given
		if(!isset($_GET['username']) || $_GET['username'] == '') {
			http_response_code(405); die();
		}
		$username = $_GET['username'];
		$userLimit = (isset($_GET['userLimit'])) ?
			intval($_GET['userLimit']) : $this->_userLimitDefault;
		$onlyActiveSchoolyear = (isset($_GET['onlyActiveSchoolyear'])) ?
			$_GET['onlyActiveSchoolyear'] :
			$this->_onlyActiveSchoolyearDefault;

		try {
			$users = $this->search(
				$username, $userLimit, $onlyActiveSchoolyear
			);
			die(json_encode($users));
		}
		catch(\PDOException $e) {
			http_response_code(500);
			die('Ein Fehler ist beim Suchen aufgetreten.');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	protected function search($username, $userLimit, $onlyActiveSchoolyear) {

		$query = 'SELECT u.ID AS userId, CONCAT(u.username, " (", u.birthday, ")") AS username
				FROM SystemUsers u ';
		if($onlyActiveSchoolyear) {
			$query .= 'INNER JOIN SystemAttendants uigs
				ON u.ID = uigs.userId
				WHERE uigs.schoolyearId = @activeSchoolyear ';
		}
		$query .= 'ORDER BY LEVENSHTEIN_RATIO(u.username, :username) DESC LIMIT :userLimit;';

		try {
			$stmt = $this->_pdo->prepare($query);
			$stmt->bindParam('userLimit', $userLimit, \PDO::PARAM_INT);
			$stmt->bindParam('username', $username);
			$stmt->execute();
			$users = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
		}
		catch(\PDOException $e) {
			$this->_logger->log( 'Error searching for a username', 'Notice',
				Null, json_encode(array('username' => $username,
					'limit' => $userLimit, 'msg' => $e->getMessage())));
			throw $e;
		}
		return $users;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_userLimitDefault = 5;
	protected $_onlyActiveSchoolyearDefault = false;
}

?>
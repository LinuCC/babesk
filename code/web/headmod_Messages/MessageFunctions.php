<?php

/**
 * Contains functions that are used by multiple messages-classes
 */
class MessageFunctions {
	/**
	 * Returns all users of this schoolyear
	 */
	public static function usersFetch () {
		$activeSchoolyearQuery = sprintf (
			'SELECT sy.ID FROM schoolYear sy WHERE sy.active = "%s"', 1);
		$query = sprintf (
			'SELECT u.ID AS userId,
				CONCAT(u.forename, " ", u.name) AS userFullname
			FROM users u
			JOIN jointUsersInSchoolYear uisy ON u.ID = uisy.UserID
			WHERE uisy.SchoolYearID = (%s)', $activeSchoolyearQuery);
		try {
			$users = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->$_interface->DieError ('Konnte keine Benutzer finden');
		} catch (Exception $e) {
			$this->$_interface->DieError ('Ein Fehler ist beim Abrufen der Benutzer aufgetreten' . $e->getMessage ());
		}
		return $users;
	}

	/**
	 * Checks if the user has received the message and is allowed to access it
	 *
	 * @param integer $messageId the Id of the message
	 * @param integer $userId the Id of the user
	 * @return bool true if the user is allowed to access the message, else
	 * false
	 */
	public static function checkHasReceived($messageId, $userId) {
		$db = TableMng::getDb();
		$escMessageId = $db->real_escape_string($messageId);
		$escUserId = $db->real_escape_string($userId);
		$query = sprintf("SELECT COUNT(*) AS count
			FROM MessageReceivers
			WHERE %s = userId AND %s = messageId
			AND SYSDATE() BETWEEN valid_from AND valid_to",
			$escUserId, $escMessageId);
		$isReceiving = TableMng::query($queryRec, true);
		return (bool) $isReceiving[0]['count'];
	}

	/**
	 * Checks if the user is a manager of the message
	 *
	 * @param integer $messageId the Id of the message
	 * @param integer $userId the Id of the user
	 * @return bool true if the user is the manager of the message, else false
	 */
	public static function checkIsManagerOf($messageId, $userId) {
		$db = TableMng::getDb();
		$escMessageId = $db->real_escape_string($messageId);
		$escUserId = $db->real_escape_string($userId);
		$query = sprintf("SELECT COUNT(*) AS count
			FROM MessageManagers
			WHERE %s = userId AND %s = messageId", $escUserId, $escMessageId);
		$isManaging = TableMng::query($query, true);
		return (bool) $isManaging[0]['count'];
	}

	/**
	 * Gets the users taht have a similar name to the $username
	 */
	public static function usersGetSimilarTo ($username, $maxUsersToGet) {
		$bestMatches = array ();
		$users = self::usersFetch ();
		foreach ($users as $key => $user) {
			$per = 0.0;
			similar_text($username, $user ['userFullname'], $per);
			$users [$key] ['percentage'] = $per;
		}
		usort ($users, array ('MessageFunctions', 'userPercentageComp'));
		for ($i = 0; $i < $maxUsersToGet; $i++) {
			$bestMatches [] = $users [$i];
		}
		return $bestMatches;
	}

	/**
	 * Compares two strings, used with usort()
	 */
	protected static function userPercentageComp ($user1, $user2) {
		if ($user1 ['percentage'] == $user2 ['percentage']) {
			return 0;
		}
		else if ($user1 ['percentage'] < $user2 ['percentage']) {
			return 1;
		}
		else if ($user1 ['percentage'] > $user2 ['percentage']) {
			return -1;
		}
	}
}

?>
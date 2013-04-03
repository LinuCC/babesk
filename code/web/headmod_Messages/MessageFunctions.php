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
		$users = TableMng::query ($query, true);
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
	 * @return bool true if the user is the manager of the message, on
	 * error/else false
	 */
	public static function checkIsManagerOf($messageId, $userId) {
		$db = TableMng::getDb();
		$escMessageId = $db->real_escape_string($messageId);
		$escUserId = $db->real_escape_string($userId);
		$query = sprintf("SELECT COUNT(*) AS count
			FROM MessageManagers
			WHERE %s = userId AND %s = messageId", $escUserId, $escMessageId);
		try {
			$isManaging = TableMng::query($query, true);
		} catch (Exception $e) {
			return false;
		}
		return (bool) $isManaging[0]['count'];
	}

	/**
	 * checks if the User is the creator of the message
	 * @param  int $messageId the message-ID
	 * @param  int $userId the User-ID
	 * @return bool true if the User is the creator, false if not or an error occurred
	 */
	public static function checkIsCreatorOf($messageId, $userId) {
		try {
			$res = TableMng::query(sprintf('SELECT originUserId FROM Message
				WHERE `ID` = "%s"', $messageId), true);
		} catch (Exception $e) {
			return false;
		}
		return ($res[0]['originUserId'] == $userId);
	}

	/**
	 * Deletes the Message with the Id $messageId
	 * Also deletes all entries in the tables MessageReceivers and
	 * MessageManagers that are linked to this Message
	 * @param  id $messageId the message to delete
	 * @throws Exception if somethings gone wrong
	 */
	public static function deleteMessage($messageId) {
		$db = TableMng::getDb();
		$db->autocommit(false);
		$query = sprintf(
			'DELETE FROM Message WHERE `ID` = %s;
			DELETE FROM MessageReceivers WHERE `messageId` = %s;
			DELETE FROM MessageManagers WHERE `messageId` = %s;',
			$messageId, $messageId, $messageId);
		TableMng::query($query, false, true);
		$db->autocommit(true);
	}

	public static function removeReceiver($messageId, $receiverId) {
		self::removeFromMessage($messageId, $receiverId, 'MessageReceivers');
	}

	public static function removeManager($messageId, $managerId) {
		self::removeFromMessage($messageId, $managerId, 'MessageManagers');
	}

	public static function removeFromMessage($messageId, $userId, $tablename) {
		TableMng::query(sprintf(
			'DELETE FROM %s
			WHERE `messageId` = %s AND `userId` = %s
			', $tablename, $messageId, $userId));

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
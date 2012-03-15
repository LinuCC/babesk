<?php

require_once PATH_INCLUDE . '/access.php';

class SoliOrderManager extends TableManager {
	function __construct() {
		parent::__construct('soli_orders');
	}

	function addSoliOrder($MID, $UID, $IP, $date) {
		parent::addEntry('MID', $MID, 'UID', $UID, 'IP', $IP, 'ordertime', time(), 'date', $date);
	}

	/**
	 * Returns all Orders of soli_orders sorted by date
	 */
	function getSortedOrders() {
		$orders = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY %s', $this->tablename, 'date'));
		$result = $this->db->query($query);
		if (!$result)
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		while ($order = $result->fetch_assoc()) {
			$orders[] = $order;
		}
		if (!$orders || !count($orders)) {
			throw new MySQLVoidDataException('No Orders found in getSortedOrers');
		}
	}
}

?>
<?php

require_once PATH_INCLUDE . '/access.php';

class SoliOrderManager extends TableManager {
	function __construct() {
		parent::__construct('soli_orders');
	}

	function addSoliOrder($UID, $IP, $date, $mealname, $mealprice, $mealdate, $soliprice) {
		str_replace(',', '.', $soliprice);
		parent::addEntry('UID', $UID, 'IP', $IP, 'ordertime', time(), 'date', $date, 'mealname', $mealname,
						 'mealprice', $mealprice, 'mealdate', $mealdate, 'soliprice', $soliprice);
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
		return $orders;
	}
}

?>
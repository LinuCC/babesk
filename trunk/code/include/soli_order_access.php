<?php

require_once PATH_INCLUDE . '/access.php';

class SoliOrderManager extends TableManager {

	function __construct() {
		parent::__construct('soli_orders');
	}

	function addSoliOrder($orderID, $UID, $IP, $date, $mealname, $mealprice, $mealdate, $soliprice) {
		str_replace(',', '.', $soliprice);
		parent::addEntry('ID', $orderID, 'UID', $UID, 'IP', $IP, 'ordertime', date("Y-m-d h:i:s"), 'date', $date,
						 'mealname', $mealname, 'mealprice', $mealprice, 'mealdate', $mealdate, 'soliprice',
						 $soliprice);
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

	/**
	 * returns the Orders that are dated [mealdate] between the first and the second Date
	 * Format can be timestamp or YYYY-MM-DD
	 */
	function getOrdersBetMealdate($startdate, $enddate) {
		///@todo sicher das Timestamp hier von MySQL automatisch konvertiert wird?
		return parent::getTableData(sprintf('mealdate BETWEEN %s AND %s', $startdate, $enddate));
	}

	function getOrdersByUserandMealdate($uid, $date) {
		return $this->getTableData(sprintf('UID = "%s" AND mealdate = "%s"', $uid, $date));
	}
}

?>
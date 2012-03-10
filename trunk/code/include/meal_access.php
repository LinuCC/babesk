<?php
/**
 * Provides a class to manage the meals of the system
 */

require_once 'constants.php';
require_once 'access.php';

/**
 * Manages the meals, provides methods to add/modify meals or to get meal data
 */
class MealManager extends TableManager {
	
	function __construct() {
		parent::__construct('meals');
	}
	
	/**
	 * Returns all Meals which are dated after the given timestamp and sorts date first and then priceclass
	 *
	 * @return false if error
	 */
	public function getMealAfterDateSortedPcID($timestamp = 0) {
		require 'dbconnect.php';
		$res_array = array();
		if ($timestamp == 0) {
			$timestamp = time();
		}
		$date = date('Y-m-d', $timestamp);
		$query = 'SELECT *
    				FROM
    					meals
    				WHERE
    					date >= "' . $date . '"
					ORDER BY
						date, price_class';
		sql_prev_inj($query);
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException('Problem connecting to MySQL: ' . $this->db->error);
		}
		while ($res_buffer = $result->fetch_assoc()) {
			$res_array[] = $res_buffer;
		}
		if (!count($res_array)) {
			throw new MySQLVoidDataException('MySQL returned void data');
		}
		return $res_array;
	}
	
	/**
	 * returns all entries between date1 and date2
	 * @param string date1 the first date (earlier) Format: Y-m-d
	 * @param string date2 the second date (later) Format: Y-m-d
	 * @param string order_str The string after MySQL's ORDER BY if something should be sorted. Leave String blank
	 * 				if no sortation is needed.
	 */
	public function get_meals_between_two_dates($date1, $date2, $order_str = '') {
		if (!$date1 or !$date2) {
			return false;
		}
		include 'dbconnect.php';
		$res_array = NULL;
		if (!$order_str) {
			$query = sql_prev_inj(
					sprintf('SELECT *  FROM meals
    				 WHERE date between "%s" and "%s"', $date1, $date2));
		}
		else {
			$query = sql_prev_inj(
					sprintf('SELECT *  FROM meals
    				 WHERE date between "%s" and "%s" ORDER BY %s', $date1, $date2, $order_str));
		}
		
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_CONNECT_ERROR . $this->db->error;
			exit;
		}
		$is_void = true;
		while ($buffer = $result->fetch_assoc()) {
			$res_array[] = $buffer;
			$is_void = false;
		}
		if($is_void)
		  throw new MySQLVoidDataException('MySQL returned no data');
		return $res_array;
	}
	
	/**
	 * returns all menu ids (unique) at given date
	 * @param date the date
	 */
	public function GetMealIdsAtDate($date) {
		if (!$date) {
			return false;
		}
		include 'dbconnect.php';
		$res_array = NULL;
		$query = sql_prev_inj(sprintf('SELECT DISTINCT MID FROM
						orders WHERE date="%s";', $date));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_CONNECT_ERROR . $this->db->error;
			exit;
		}
		while ($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
	
	/**
	 * returns name of given menu id
	 * @param id the menu id
	 */
	public function GetMealName($id) {
		if (!$id) {
			return false;
		}
		include 'dbconnect.php';
		$res_array = NULL;
		$query = sql_prev_inj(sprintf('SELECT name FROM
    							meals WHERE id="%s";', $id));
		$result = $this->db->query($query);
		if (!$result) {
			echo DB_CONNECT_ERROR . $this->db->error;
			exit;
		}
		while ($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array[0]['name'];
		;
		
	}
	
	/**
	 * Adds a Meal
	 * Adds a meal into the MySQL-meal-table based on the given parameters...
	 * @param string $name
	 * @param string $description
	 * @param YYYY-MM-DD $date_conv
	 * @param int $price_class
	 * @param int $max_orders
	 */
	public function addMeal($name, $description, $date_conv, $price_class, $max_orders) {
		parent::addEntry('name', $name, 'description', $description, 'date', $date_conv, 'price_class', $price_class,
						 'max_orders', $max_orders);
	}
}

?>
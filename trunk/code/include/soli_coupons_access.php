<?php

require_once PATH_INCLUDE.'/access.php';

class SoliCouponsManager extends TableManager {
	function __construct() {
		parent::__construct('soli_coupons');
	}
	
	/**
	 * Checks if the User of the given UserID has a valid Coupon-Activation
	 * @param numeric_string $UID
	 */
	function HasValidCoupon($UID) {
		try {
			$coupons = $this->getTableData(sprintf('UID = %s', $UID));
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		
		$now = time();
		foreach($coupons as $coupon) {
			$start = strtotime($coupon['startdate']);
			$end = strtotime($coupon['enddate']);
			echo sprintf('Start wird zu: %s<br>End wird zu: %s <br>', date("Y-m-d H:i:s", $start), date("Y-m-d H:i:s", $end));
			if($now > $start && $now < $end) {
				return true;
			}
		}
		return false;
	}
}


?>
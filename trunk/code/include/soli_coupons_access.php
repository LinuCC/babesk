<?php

require_once PATH_INCLUDE.'/access.php';

class SoliCouponsManager extends TableManager {
	function __construct() {
		parent::__construct('soli_coupons');
	}
	
	/**
	 * returns all Coupons of soli_coupons that are in the Table
	 */
	function getAllCoupons() {
		$coupons = $this->getTableData();
		return $coupons;
	}
	
	function addCoupon($startdate, $enddate, $uid) {
		$this->addEntry('startdate', $startdate, 'enddate', $enddate, 'UID', $uid);
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
			if($now > $start && $now < $end) {
				return true;
			}
		}
		return false;
	}
	
}


?>
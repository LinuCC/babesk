<?php

/**
 * A class containing functionality that Copies orders of Soli-using Users
 * from orders to soli_orders, solving conflicts caused by adding soli_coupons
 * afterwards
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class CopyOldOrdersToSoli {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init($interface) {

		self::$_interface = $interface;

		self::$_errors = array();
		self::$_copied = array();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute() {

		self::soliDataFetch();
		self::couponDataFetch();

		self::soliDataProcess();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the data of Soli-Users to be processed
	 *
	 * @return array(...)
	 */
	protected static function soliDataFetch() {

		self::$_soliData = TableMng::query(sprintf(
			'SELECT u.ID AS userId, CONCAT(`u.forename`, " ", `u.name`) AS name,
			-- Does the Meal and the priceclass still exist?
			(SELECT COUNT(*) FROM meals m
				JOIN price_classes pc ON `m.price_class` = `pc.ID`
				WHERE m.ID = o.MID
			) AS existMealAndPriceclass,
			-- Is the order already in soli_orders?
			(SELECT COUNT(*) FROM soli_orders so
				WHERE `o.date` = `so.date` -- Has same Date?
				AND `o.UID` = `so.UID` -- Has same UserId?
				AND (SELECT `m.name` FROM meals m WHERE `o.MID` = `m.ID`)
					= `so.mealname` -- Has same mealname?
			) AS orderedAsSoli
			FROM users u
			JOIN orders o ON `o.UID` = `u.ID`
			WHERE `u.soli` = 1'));
	}

	protected static function couponDataFetch() {

		self::$couponData = TableMng::query('SELECT * FROM soli_coupons');
	}

	protected static function soliDataProcess() {

		foreach($soliData as $order) {

		}
	}

	protected static function soliDataCheck() {

		if($order['existMealAndPriceclass'] == true) {
			if($order['orderedAsSoli'] == true) {

			}
		}
		else {
			self::$_errors[]
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

	protected static $_soliData;
	protected static $_couponData;

	/**
	 * An array of strings telling the user that something went wrong with this
	 * specific order
	 * @var array(string)
	 */
	protected static $_errors;

	/**
	 * An array of strings telling the user what has finished successfully
	 * @var array(string)
	 */
	protected static $_copied;

}

?>

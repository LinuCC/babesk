<?php
//No direct access
defined('_WEXEC') or die("Access denied");
require_once 'order_constants.php';
global $smarty;
global $logger;

if(isset($_GET['order'])) {
	$mealManager = new MealManager('meals');
	$userManager = new UserManager();
	$orderManager = new OrderManager('orders');
	$priceClassManager = new PriceClassManager();

	is_numeric($_GET['order']) OR exit('Error: ID not Numerical!');
	$result = $mealManager->getEntryData($_GET['order'],'name', 'date');
	$result OR exit('ERROR');

	if('POST' == $_SERVER['REQUEST_METHOD']) {
		////////////////////////////////////////////////////
		//Pay for meal
		try {
			//"Pay", substract the price for the menu from the users account
			$payment = $priceClassManager->getPrice($_SESSION['uid'], $_GET['order']);
			if(!$payment){
				//error-checking
				die('Etwas lief falsch mit Payment! Sorry');
			}

			$soli = $userManager->getEntryData($_SESSION['uid'],'soli');

			if ($soli['soli']=="1") {
				$payment = 1;
			}
			if(!$userManager->changeBalance($_SESSION['uid'], -$payment)) {
				$smarty->display('web/modules/mod_order/failed.tpl');
				die();
			}
		} catch (Exception $e) {
			$logger->log('WEB|ORDER', 'MODERATE', sprintf('Error while handling the order for '
						.'UID %s; Order: %s; Error: %s', $_SESSION['uid'],$_GET['order'],$e->getMessage()));
			$smarty->display('web/header.tpl');
			echo ERR_ORDER;
			$smarty->display('web/footer.tpl');
			die();
		}
		//////////////////////////////////////////////////
		//add meal
		try {
			//get the date of the meal which is ordered
			if (!$meal_date = $mealManager->getEntryData($_GET['order'], 'date')) {
				die(DB_QUERY_ERROR.$this->db->error);
			}

			$orderManager->addEntry('MID', $_GET['order'], 'UID', $_SESSION['uid'], 'IP', 
						$_SERVER['REMOTE_ADDR'], 'ordertime',  time(), 'date', $meal_date ['date']);
		} catch (Exception $e) {
			//meal couldn't be ordered so give the user his money back
			$userManager->changeBalance($_SESSION['uid'], 
									$priceClassManager->getPrice($_SESSION['uid'], $_GET['order']));   
			$logger->log('WEB|ORDER', 'MODERATE', sprintf('Error while handling the order for '
						.'UID %s; Order: %s; Error: %s', $_SESSION['uid'],$_GET['order'],$e->getMessage()));
			$smarty->display('web/header.tpl');
			echo ERR_ORDER;
			$smarty->display('web/footer.tpl');
			die();
		}
		//////////////////////////////////////////////////
		//finished
		$smarty->display('web/header.tpl');
		echo 'Am '.formatDate($result['date']).' das Men&uuml; '.$result['name'].' erfolgreich bestellt. <a href="index.php">Weiter</a>';
		$smarty->display('web/footer.tpl');
	}
	else {
		//////////////////////////////////////////////////
		//show confirm-order-form
		$smarty->display('web/header.tpl');
		if (strtotime($result['date']) < strtotime(date('Y-m-d'))) exit('Error: Fehlerhaftes Datum');
		echo 'Am '.formatDate($result['date']).' das Men&uuml; '.$result['name'].' bestellen?<br />';
		echo'<form method="POST" action="index.php?section=order&order='.$_GET['order'].'">
      		    <input type="submit" value="Bestellen">
    		    </form>';
		$smarty->display('web/footer.tpl');
	}
}
else {
	//////////////////////////////////////////////////
	//show order-overview
	$mealManager = new MealManager('meals');

	$hour = date('H', time());
	$date = time();
	$result = array(array());
	$is_void = false;
	//Ordering only possible until 8AM
	if ($hour > $last_order_time) {
		$date += 86400;
	}
	try {
		$result = $mealManager->getMealAfterDateSortedPcID($date);
	} catch (MySQLVoidDataException $e) {
		$is_void = true;
		$smarty->assign('message', NO_MEALS_EXISTING);
		$smarty->assign('meals', '');
		$smarty->display('web/modules/mod_order/order.tpl');
	}
	if(!$is_void) {
		$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
		$meals = array();
		foreach($result as $meal) {
			$tag = date("w",strtotime($meal['date']));
			$meal['date'] = formatDate($meal['date']);
			$meal['wochentag'] = $tage[$tag];
			$meal['kalenderwoche'] = date("W",strtotime($meal['date']));
			$meals[] = $meal;
		}
		$smarty->assign('meals', $meals);
		$smarty->assign('message', '');
		
		
		$year = date("Y");
		$week_number = date("W");
		$smarty->assign('thisMonday',date('d.m.Y', strtotime($year."W".$week_number."1")));
		$smarty->assign('thisTuesday',date('d.m.Y', strtotime($year."W".$week_number."2")));
		$smarty->assign('thisWednesday',date('d.m.Y', strtotime($year."W".$week_number."3")));
		$smarty->assign('thisThursday',date('d.m.Y', strtotime($year."W".$week_number."4")));
		$smarty->assign('thisFriday',date('d.m.Y', strtotime($year."W".$week_number."5")));
		
		$nextkw = (string)(date("W")+1);
		if (strlen($nextkw)==1) $nextkw = "0".$nextkw;
		$week_number = $nextkw;
		$smarty->assign('nextMonday',date('d.m.Y', strtotime($year."W".$week_number."1")));
		$smarty->assign('nextTuesday',date('d.m.Y', strtotime($year."W".$week_number."2")));
		$smarty->assign('nextWednesday',date('d.m.Y', strtotime($year."W".$week_number."3")));
		$smarty->assign('nextThursday',date('d.m.Y', strtotime($year."W".$week_number."4")));
		$smarty->assign('nextFriday',date('d.m.Y', strtotime($year."W".$week_number."5")));
		
		
		$smarty->display('web/modules/mod_order/order.tpl');
	}
}
?>
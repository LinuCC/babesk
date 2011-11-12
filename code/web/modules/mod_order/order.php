<?php
//No direct access
defined('_WEXEC') or die("Access denied");
global $smarty;

if(isset($_GET['order'])) {
	$mealManager = new MealManager('meals');
	$userManager = new UserManager();
	$orderManager = new OrderManager('orders');
	$priceClassManager = new PriceClassManager();

	is_numeric($_GET['order']) OR exit('Error: ID not Numerical!');
	$result = $mealManager->getEntryData($_GET['order'],'name', 'date');
	$result OR exit('ERROR');

	if('POST' == $_SERVER['REQUEST_METHOD']) {
		//"Pay", substract the price for the menu from the users account
		$payment = $priceClassManager->getPrice($_SESSION['uid'], $_GET['order']);
		if(!$payment){
			//error-checking
			die('Etwas lief falsch mit Payment! Sorry');
		}
		if(!$userManager->changeBalance($_SESSION['uid'], -$payment)) {
			$smarty->display('web/modules/mod_order/failed.tpl');
			die();
		}

		//get the date of the meal which is ordered
		if (!$meal_date = $mealManager->getEntryData($_GET['order'], 'date')) {
			#$this->db->query('SELECT date FROM meals WHERE ID = '.$_GET['order'].';')) {
			die(DB_QUERY_ERROR.$this->db->error);
		}

		//add the Entry, errorchecking
		if (!$orderManager->addEntry('MID', $_GET['order'], 'UID', $_SESSION['uid'], 'IP', $_SERVER['REMOTE_ADDR'], 'ordertime',  time(), 'date', $meal_date ['date'])) {
			$userManager->changeBalance($_SESSION['uid'], $priceClassManager->getPrice($_SESSION['uid'], $_GET['order']));   //meal couldn't be ordered so give the user his money back
			$smarty->display('web/header.tpl');
			echo "Bestellung nicht erfolgreich";
			$smarty->display('web/footer.tpl');
		}

		$smarty->display('web/header.tpl');
		echo 'Am '.formatDate($result['date']).' das Men&uuml; '.$result['name'].' erfolgreich bestellt. <a href="index.php">Weiter</a>';
		$smarty->display('web/footer.tpl');
	}
	else {//show order-form
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
	$mealManager = new MealManager('meals');

	$hour = date('H', time());
	$date = time();
	$result = array(array());
	//Ordering only possible until 8AM
	if ($hour > 8) {
		$date += 86400;
	}
	try {
		$result = $mealManager->getMealAfter($date);
	} catch (MySQLVoidDataException $e) {
			
	}

	$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
	$meals = array();
	// 		while ($meal = $result->fetch_assoc()) {
	foreach($result as $meal) {
		$tag = date("w",strtotime($meal['date']));
		$meal['date'] = formatDate($meal['date']);
		$meal['wochentag'] = $tage[$tag];
		$meal['kalenderwoche'] = date("W",strtotime($meal['date']));
		$meals[] = $meal;
	}
	$smarty->assign('meals', $meals);
	$smarty->display('web/modules/mod_order/order.tpl');
}
//gerichte werden angezeigt
//gerichte k�nnen per klick bestellt werden incl. variabler anzahl dann werden gerichte angezeigt und ob andere Preisklasse (Standartm��ig GID angew�hlt aus mysql datenbank[radio buttons])
//hinweisen, dass gerichte f�r eltern mehr kosten
//f�r geringere Preisklasse bestellen zul�ssig??? oder neues feld wodrin festgehalten ist, f�r welche preisklassen man bestellen kann
//bestellung in datenbank eintragen

?>
<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    global $smarty;
    
	if(isset($_GET['order'])) {
	    $mealManager = new MealManager();
        $userManager = new UserManager();
        $orderManager = new OrderManager();
        $priceClassManager = new PriceClassManager();
    	                                            
		is_numeric($_GET['order']) OR exit('Error: ID not Numerical!');
		$result['name'] = implode($mealManager->getMealData($_GET['order'],'name'));
		$result['date'] = implode($mealManager->getMealData($_GET['order'],'date'));
		$result OR exit('ERROR');
		if('POST' == $_SERVER['REQUEST_METHOD']) {
		    //"Pay", substract the price for the menu from the users account
            if(!$userManager->changeBalance($_SESSION['uid'], -$priceClassManager->getPrice($_SESSION['uid'], $_GET['order']))) {
                $smarty->display('web/modules/mod_order/failed.tpl');
                die();
            }
            
            if (!$orderManager->addOrder($_GET['order'], $_SESSION['uid'], time())) {
                $userManager->changeBalance($_SESSION['uid'], $priceClassManager->getPrice($_SESSION['uid'], $_GET['order']));   //meal couldn't be ordered so give the user his money back
		        $smarty->display('web/header.tpl');
			    echo "Bestellung nicht erfolgreich";
			    $smarty->display('web/footer.tpl');
		    }

		    $smarty->display('web/header.tpl');
			echo 'Am '.formatDate($result['date']).' das Men&uuml; '.$result['name'].' erfolgreich bestellt. <a href="index.php">Weiter</a>';
			$smarty->display('web/footer.tpl');
			
		} else {
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
        $mealManager = new MealManager();

        //Ordering only possible until 8AM
        $hour = date('H', time());
        if ($hour > 8) {
            $date = time() + 86400;
            $result = $mealManager->getMealAfter($date);    
        }
        else {
            $result = $mealManager->getMealAfter();    
        }
		
		$meals = array();
		while ($meal = $result->fetch_assoc()) {
		    $meal['date'] = formatDate($meal['date']);
            $meals[] = $meal;
		} 
		$smarty->assign('meals', $meals);
	    $smarty->display('web/modules/mod_order/order.tpl');
	}
    //gerichte werden angezeigt
    //gerichte können per klick bestellt werden incl. variabler anzahl dann werden gerichte angezeigt und ob andere Preisklasse (Standartmï¿½ï¿½ig GID angewï¿½hlt aus mysql datenbank[radio buttons])
    //hinweisen, dass gerichte für eltern mehr kosten
    //für geringere Preisklasse bestellen zulässig??? oder neues feld wodrin festgehalten ist, für welche preisklassen man bestellen kann
    //bestellung in datenbank eintragen
	
?>
<?php
    //No direct access
    defined('_AEXEC') or die("Access denied");
    
    require_once PATH_INCLUDE.'/managers.php';
    
    $num_user_groups = 2;
    $num_users = 4;
    $num_price_classes = 2;
    $num_meals = 10;
    $num_orders = $num_users * $num_meals;
    $num_admin_groups = 2;
    $num_admins = 3;
    
    //-----------------------------------
	// ======= Create user groups =======
	//-----------------------------------
	echo "<p>Filling User Groups Table</p>";
	$user_groups = array();
	for($i = 0; $i < $num_user_groups; $i++) {
        $user_groups[$i] = array(); 
    }
    
    $user_groups[0]["name"] = "schueler";
    $user_groups[0]["max_credit"] = 15.0; 
    
    $user_groups[1]["name"] = "lehrer";
    $user_groups[1]["max_credit"] = 25.0;	
    
    foreach($user_groups as $group) {
        $groupManager->addGroup($group['name'], $group['max_credit']);
    }
	
	
	//-----------------------------
	// ======= Create users =======
	//-----------------------------
	echo "<p>Filling User Table</p>";
	$users = array();
	for($i = 0; $i < $num_users; $i++) {
        $users[$i] = array();
    }

    $users[0]["ID"] = "0000000001";
    $users[0]["name"] = "Gross";
    $users[0]["forename"] = "Samuel";
    $users[0]["username"] = "sam";
    $users[0]["password"] = "pass";
    $users[0]["birthday"] = date("o-m-d");
    $users[0]["credit"] = 42.0*42.0;
    $users[0]["gid"] = 1;

    $users[1]["ID"] = "0000000002";
    $users[1]["name"] = "Elsner-Murawa";
    $users[1]["forename"] = "Jan";
    $users[1]["username"] = "freddy";
    $users[1]["password"] = "pass";
    $users[1]["birthday"] = date("o-m-d");
    $users[1]["credit"] = -17.0;
    $users[1]["gid"] = 1;
    
    $users[2]["ID"] = "0000000003";
    $users[2]["name"] = "Ernst";
    $users[2]["forename"] = "Pascal";
    $users[2]["username"] = "pascal";
    $users[2]["password"] = "pass";
    $users[2]["birthday"] = date("o-m-d");
    $users[2]["credit"] = 0.325678435;
    $users[2]["gid"] = 1;
    
    $users[3]["ID"] = "0000000004";
    $users[3]["name"] = "Schroeder";
    $users[3]["forename"] = "Cedric";
    $users[3]["username"] = "cedric";
    $users[3]["password"] = "pass";
    $users[3]["birthday"] = date("o-m-d");
    $users[3]["credit"] = $user_groups[0]["max_credit"];
    $users[3]["gid"] = 1;

    foreach($users as $user) {
        $userManager->addUser($user["ID"], $user["name"], $user["forename"], $user["username"], $user["password"], $user["birthday"], $user["credit"], $user["gid"]);
    }
	
	
	//-------------------------------------
	// ======= Create price classes =======
	//-------------------------------------
	echo "<p>Filling Price Class Table</p>";
	$price_classes = array();
	for($i = 0; $i < $num_price_classes * $num_user_groups; $i++) {
        $price_classes[$i] = array();
    }
        //price class 1: menu
    $price_classes[0]["name"] = "menue";
    $price_classes[0]["gid"] = 1;
    $price_classes[0]["price"] = 3.0;
    
    $price_classes[1]["name"] = "menue";
    $price_classes[1]["gid"] = 2;
    $price_classes[1]["price"] = 3.5;
        //price class 2: menu vegetarian
    $price_classes[2]["name"] = "menue vegetarisch";
    $price_classes[2]["gid"] = 1;
    $price_classes[2]["price"] = 2.7;

    $price_classes[3]["name"] = "menue vegetarisch";
    $price_classes[3]["gid"] = 2;
    $price_classes[3]["price"] = 3.2;

    foreach($price_classes as $class) {
        $priceClassManager->addPriceClass($class['name'], $class['gid'], $class['price']);
    }
	
	
	//------------------------------
	// ======= Create meals =======
	//------------------------------
	echo "<p>Filling Meals Table</p>";
	
	$meals = array();
	for($i = 0; $i < $num_meals; $i++) {
        $meals[$i] = array();
    }

    $meals[0]["name"] = "Backfisch mit H&auml;nchenkeule";
    $meals[0]["description"] = "-";
    $meals[0]["price_class"] = 1;
    $meals[0]["date"] = date('Y-m-d', strtotime('next monday'));
    $meals[0]["max_orders"] = 200;
    
    $meals[1]["name"] = "Veggieburger mit Tannennadeln";
    $meals[1]["description"] = "-";
    $meals[1]["price_class"] = 2;
    $meals[1]["date"] = date('Y-m-d', strtotime('next monday'));
    $meals[1]["max_orders"] = 200;

    $meals[2]["name"] = "Backfisch mit Hacksteak";
    $meals[2]["description"] = "-";
    $meals[2]["price_class"] = 1;
    $meals[2]["date"] = date('Y-m-d', strtotime('next tuesday'));
    $meals[2]["max_orders"] = 200;

    $meals[3]["name"] = "Veggieburger mit Kleesalat";
    $meals[3]["description"] = "-";
    $meals[3]["price_class"] = 2;
    $meals[3]["date"] = date('Y-m-d', strtotime('next tuesday'));
    $meals[3]["max_orders"] = 200;
    
    $meals[4]["name"] = "Backfisch mit Wiener Schnitzel";
    $meals[4]["description"] = "-";
    $meals[4]["price_class"] = 1;
    $meals[4]["date"] = date('Y-m-d', strtotime('next wednesday'));
    $meals[4]["max_orders"] = 200;

    $meals[5]["name"] = "Veggieburger mit Karotten";
    $meals[5]["description"] = "-";
    $meals[5]["price_class"] = 2;
    $meals[5]["date"] = date('Y-m-d', strtotime('next wednesday'));
    $meals[5]["max_orders"] = 200;
    
    $meals[6]["name"] = "Backfisch mit Hasenr&uuml;cken";
    $meals[6]["description"] = "-";
    $meals[6]["price_class"] = 1;
    $meals[6]["date"] = date('Y-m-d', strtotime('next thursday'));
    $meals[6]["max_orders"] = 200;

    $meals[7]["name"] = "Veggieburger mit Erbsen";
    $meals[7]["description"] = "-";
    $meals[7]["price_class"] = 2;
    $meals[7]["date"] = date('Y-m-d', strtotime('next thursday'));
    $meals[7]["max_orders"] = 200;
    
    $meals[8]["name"] = "Backfisch mit Schweinerippen";
    $meals[8]["description"] = "-";
    $meals[8]["price_class"] = 1;
    $meals[8]["date"] = date('Y-m-d', strtotime('next friday'));
    $meals[8]["max_orders"] = 200;

    $meals[9]["name"] = "Veggieburger mit Rotkohl";
    $meals[9]["description"] = "-";
    $meals[9]["price_class"] = 2;
    $meals[9]["date"] = date('Y-m-d', strtotime('next friday'));
    $meals[9]["max_orders"] = 200;

    foreach($meals as $meal) {
        $mealManager->addMeal($meal['name'], $meal["description"], $meal['date'], $meal['price_class'], $meal['max_orders']);
    }
	
	
	//------------------------------
	// ======= Create orders =======
	//------------------------------
	echo "<p>Filling Order Table</p>";
	
	for($i = 0; $i < $num_users; $i++) {
        for($j = 0; $j < 5; $j++) {
            $orderManager->addOrder(rand(1, 9), $userManager->getCardOwner($users[$i]["ID"]));
        } 
    }
    
    //-----------------------------------
	// ======= Create admin groups =======
	//-----------------------------------
	echo "<p>Filling Admin Groups Table</p>";
	$admin_groups = array();
	for($i = 0; $i < $num_admin_groups; $i++) {
        $admin_groups[$i] = array(); 
    }
    
    $admin_groups[0]["name"] = "kueche";
    $admin_groups[0]["modules"] = "checkout, meals"; 
    
    $admin_groups[1]["name"] = "verwaltung";
    $admin_groups[1]["modules"] = "register, checkout, logs, dummy";
    
    foreach($admin_groups as $group) {
        $adminManager->addAdminGroup($group['name'], $group['modules']);
    }
	
	
	//-----------------------------
	// ======= Create admins =======
	//-----------------------------
	echo "<p>Filling Admin Table</p>";
	$admins = array();
	for($i = 0; $i < $num_admins; $i++) {
        $admins[$i] = array();
    }

    $admins[0]["name"] = "kueche1";
    $admins[0]["password"] = "pass";
    $admins[0]["gid"] = 2;
    
	$admins[1]["name"] = "verwalter1";
    $admins[1]["password"] = "pass";
    $admins[1]["gid"] = 3;
    
    $admins[2]["name"] = "verwalter2";
    $admins[2]["password"] = "pass";
    $admins[2]["gid"] = 3;

    foreach($admins as $admin) {
        $adminManager->addAdmin($admin["name"], $admin["password"], $admin["gid"]);
    }
	
    
    //----------------------------
	// ======= Create logs =======
	//----------------------------
	echo "<p>Filling Logs Table</p>";
	
	require_once PATH_INCLUDE."/logs.php";
	
	for ($i = 0; $i <= 5; $i++) {
        for ($j = 0; $j <= 2; $j++) {
            $logger->log($i, $j, "First test Log with Category ".$i." and Severity ".$j);
            $logger->log($i, $j, "Second test Log with Category ".$i." and Severity ".$j);
            $logger->log($i, $j, "Third test Log with Category ".$i." and Severity ".$j);
        } 
    }
    
    
    echo "<h3>Tables filled successfully!</h3>";
    $logger->printLogs();

?>
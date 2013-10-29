<?php
	/**
	 * @todo Does this file make sense or not??
	 */
    require "user_access.php";
    require "group_access.php";
    require "meal_access.php";
    require "order_access.php";
    require "price_class_access.php";
    require "admin_access.php";

    $userManager = new UserManager();
    $groupManager = new GroupManager('groups');
    $mealManager = new MealManager('meals');
    $orderManager = new OrderManager('orders');
    $priceClassManager = new PriceClassManager();

?>

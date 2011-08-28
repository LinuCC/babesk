<?php
    require "user_access.php";
    require "group_access.php";
    require "meal_access.php";
    require "order_access.php";
    require "price_class_access.php";
    require "admin_access.php";
    
    $userManager = new UserManager();
    $groupManager = new GroupManager();
    $mealManager = new MealManager();
    $orderManager = new OrderManager();
    $priceClassManager = new PriceClassManager();
    $adminManager = new AdminManager();
    
?>
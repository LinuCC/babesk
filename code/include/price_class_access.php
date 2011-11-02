<?php
    /**
     * Provides Functions to manage the price classes of the system
     */
    
	require_once PATH_INCLUDE.'/access.php';

    /**
     * Manages the price classes, provides methods to add/modify price classes or to get price class data
     */
    class PriceClassManager extends TableManager{
        
    	/**
    	 * @todo add such a void construct-function to all access-classes.
    	 */
    	function __construct() {
    		TableManager::__construct('price_classes');
    	}
        
        /**
          *Returns the requested pricefield on the based on the User-ID and the Meal-ID
          *
          *@return: returns false if nothing found, else the priceData
          */
        function getPrice($uid, $mid) {
            require_once 'managers.php';
            require_once 'constants.php';
            $userManager = new UserManager();
            $mealManager = new MealManager('meals');
            
            $gid = $userManager->getUserData($uid, 'GID');
		    $gid = $gid['GID'];
		    $priceclass_ID = $mealManager->getEntryData($mid, 'price_class');
		    if(!$priceclass_ID){
		    	echo 'priceclasses in getPrice returned false!';
		    	return false;
		    }
		    $priceData = $this->getEntryData($priceclass_ID['price_class'], 'price', 'GID');
// 		    while ($row = $priceData->fetch_assoc()) {
//                 if($row['GID'] == $gid) {
//                     return $row['price'];
//                 }
//             }
		    if(!$priceData){
		    	die(PRICECLASS_INVALID_GID);
		    }
		    foreach($priceData as $price){
		    	if($price['GID'] == $gid){
		    		return $price['price'];
		    	}
		    }
		    echo 'Die GID fehlt!';
		    return false;
        }
		
         /**
         * Adds a Price Class to the System
         *
         * The Function creates a new entry in the price_class Table
         * consisting of the given Data
         *
         * If 4 Params ar given, the ID will not be automatically incremented, but
         * will be the 4th param given
         *
         * @param name The name of the priceclass
         * @param GID The group-ID of the priceclass
         * @param price The price
         * @param ID The ID of the price class, this one is optional (else MySQL will autoincrement)
         */
        function addPriceClass($name, $GID, $price, $ID = '') {
        	try {
        		if(!$ID) {//nothing for ID given
        			TableManager::addEntry('name', $name,'GID', $GID,'price', $price);
        		} else {
        			TableManager::addEntry('name', $name,'GID', $GID,'price', $price, 'ID', $ID);
        		}
        	} catch (Exception $e) {
        		echo ERR_ADD_PRICECLASS;
        		throw $e;
        	}
        }
    }
?>
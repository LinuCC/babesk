<?php
    /**
     * Provides Functions to manage the price classes of the system
     */
    
    /**
     * Manages the price classes, provides methods to add/modify price classes or to get price class data
     */
    class PriceClassManager {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
        
        /**
         * Returns the value of the requested fields for the given price class id.
         *
         * The Function takes a variable amount of parameters, the first being the price class id
         * the other parameters are interpreted as being the fieldnames in the price_class table.
         * In addition to that the function allows no parameters at all, too.
         * Then all data in the table will be returned.
         *
         * @return false if error
         */
        function getPriceClassData() {
			$num_args = func_num_args(); 
			
			if($num_args == 0){//get all data
				$query = 'SELECT * FROM price_classes';
			}
			else if($num_args > 1){//get specific data
				$id = func_get_arg(0);
				$fields = "";
				
				for($i = 1; $i < $num_args - 1; $i++) {
					$fields .= func_get_arg($i).', ';
				}
				$fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name
				
				$query = 'SELECT
				    		'.$fields.'
						FROM
	    					price_classes
	    				WHERE
	    					ID = '.$id.'';
			}
			else {//wrong arguments
				return false;
			}
			
			if (!($result = $this->db->query($query))) {
				echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
				return false;
			}
			///@TODO this conversion into an array has to be in the other functions, too!
			$res_array = array();
			while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
			return $res_array;
			
// 			////////////////////////////////////////////////////////////
			
			
// 			//get all data from the table
// 			if($num_args == 1 && func_get_arg(0) == '*'){
// 				$query = 'SELECT 
// 							*
// 						FROM
// 							price_classes';
// 				$result = $this->db->query($query);
// 				if (!$result) {
// 					echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
// 					return false;
// 				}

// 			}
			
//             //at least 2 arguments needed, get some specific data
//             if ($num_args < 2) {
//                 return false;
//             }
//             $id = func_get_arg(0);
//             $fields = "";
            
//             for($i = 1; $i < $num_args - 1; $i++) {
//                 $fields .= func_get_arg($i).', ';
//             }
//             $fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name 
            
//             $query = 'SELECT
//     					'.$fields.'
//     				FROM
//     					price_classes
//     				WHERE
//     					ID = '.$id.'';
//     	    $result = $this->db->query($query);
//         	if (!$result) {
//             	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
//             	return false;
//         	}
//             return $result;
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
            $mealManager = new MealManager();
            
            $gid = $userManager->getUserData($uid, 'GID');
		    $gid = $gid['GID'];
		    $priceClass = $mealManager->getMealData($mid, 'price_class');
		    $priceClass = $priceClass['price_class'];
		    $priceData = $this->getPriceClassData($priceClass, 'price', 'GID');
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
         * @param name The name of the meal
         * @param date The date the meal will be available
         * @param price_class_ID The ID of the price class this meal is in
         * @return false if error
         */
        function addPriceClass($name, $GID, $price) {
        	if(func_num_args() == 4){
        		$ID = func_get_arg(3);
        		$query = 'INSERT INTO
        		        	 price_classes(ID, name, GID, price)
        		          VALUES
                             ('.$ID.', "'.$name.'", '.$GID.', '.$price.');';
        	}
        	else {
        		$query = 'INSERT INTO
        	    	           price_classes(name, GID, price)
                    	  VALUES
                        	   ("'.$name.'", '.$GID.', '.$price.');';
        	}
           $result = $this->db->query($query);
        	if (!$result) {
            	echo "Table Price_Classes: ".DB_QUERY_ERROR.$this->db->error.'<br>';
            	return false;
        	}
        	return true;
        }

        
        /**
         * Deletes a price class from the system
         *
         * Delete the entry from the price_class table with the given ID
         *
         * @param ID The ID of the group
         * @return false if error
         */
        function delPriceClass($ID) {
        	$query = 'DELETE FROM
        	               price_classes
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
    }    
    

?>
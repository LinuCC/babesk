<?php
    /**
     * Provides a class to manage the meals of the system
     */

    require_once "constants.php";

    /**
     * Manages the meals, provides methods to add/modify meals or to get meal data
     */
    class MealManager {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
        
        /**
         * Returns the value of the requested fields for the given meal id.
         *
         * The Function takes a variable amount of parameters, the first being the meal id
         * the other parameters are interpreted as being the fieldnames in the meals table.
         * The data will be returned in an array with the fieldnames being the keys.
         *
         * @return false if error
         */
        function getMealData() {
            //at least 2 arguments needed
            $num_args = func_num_args(); 
            if ($num_args < 2) {
                return false;
            }
            $id = func_get_arg(0);
            $fields = "";
            
            for($i = 1; $i < $num_args - 1; $i++) {
                $fields .= func_get_arg($i).', ';
            }
            $fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name 
            
            $query = 'SELECT
    					'.$fields.'
    				FROM
    					meals
    				WHERE
    					ID = '.$id.'';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
            	return false;
        	}
            return $result->fetch_assoc();
        }
        
		 /**
         * Returns all Meals of today and after it(names and id's)
         *
         * @return false if error
         */
        function getMealAfter($timestamp = 0) {
            if($timestamp == 0) {
                $timestamp = time();
            }
			$date = date('Y-m-d', $timestamp);
        	$query = 'SELECT
						ID,
    			        name,
						date
    				FROM
    					meals
    				WHERE
    					date >= "'.$date.'"
					ORDER BY
						date';
    	    $result = $this->db->query($query);
        	if (!$result) {
            	exit(DB_QUERY_ERROR.$this->db->error."<br />".$query);
        	}
            return $result;
		}
		
		 /**
         * Adds a Meal to the System
         *
         * The Function creates a new entry in the meals Table
         * consisting of the given Data
         *
         * @param name The name of the meal
         * @param date The date the meal will be available
         * @param price_class_ID The ID of the price class this meal is in
         * @param max_orders The maximum number of orders possible for this meal
         * @return false if error
         */
        function addMeal($name, $description, $date, $price_class_ID, $max_orders, $is_vegetarian) {
        	$query = 'INSERT INTO meals(
        	               name,
        	               description,
        	               date,
        	               price_class,
        	               max_orders,
        	               is_vegetarian)
                      VALUES
                           ("'.$name.'", "'
                           .$description.'", "'
                           .$date.'", '
                           .$price_class_ID.', '
                           .$max_orders.', '
                           .$is_vegetarian.');';
    
           $result = $this->db->query($query);
        	if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
        /**
         * Deletes a meal from the system
         *
         * Delete the entry from the meals table with the given ID
         *
         * @param ID The ID of the meal
         * @return false if error
         */
        function delMeal($ID) {
        	$query = 'DELETE FROM
        	               meals
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
            	echo DB_QUERY_ERROR.$this->db->error;
            	return false;
        	}
        	return true;
        }
        
         /**Returns one meal in form of an mySQLi result object
           *@param name The Identifier from which the Datastruct will be selected.
           *@param value The value of the Identifier, to select one Datastruct */
        function pickMeal($name, $value) {
        	$query = 'SELECT
        					*
        				FROM
        					meals
        				WHERE
        					'.$name.'="'.$value.'"';
    	    $result = $this->db->query($query);
    	    if(!$result) {
    	   		echo DB_CONNECT_ERROR.$this->db->error;
    	  	 	exit;
    	    }
    	    else {
				return $result->fetch_assoc();
    	    }
        }
        
        /**returns all meal entries from the database*/
        function getMeals() {
    	    include 'dbconnect.php';
    	    $res_array = array();
    	    $query = 'SELECT
    	    				*
    	    			FROM
    	    				meals';
    	    $result = $this->db->query($query);
    	    if(!$result) {
    	   		echo DB_CONNECT_ERROR.$this->db->error;
    	   		exit;
    	   	}
    	   	while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
    	   	return $res_array;
    	}
    	
    	/**returns all entries between date1 and date2
    	  *@param date1 the first date (earlier)
    	  *@param date2 the second date (later)
    	  */
    	function get_meals_between_two_dates($date1, $date2) {
    		if(!$date1 or !$date2){return false;}
    		include 'dbconnect.php';
    		$res_array = NULL;
    		$query= 'SELECT * 
    				 FROM meals
    				 WHERE date between "'.$date1.'" and "'.$date2.'"';
    		$result = $this->db->query($query);
    		if(!$result) {
    	   		echo DB_CONNECT_ERROR.$this->db->error; exit;
    	   	}
    	   	while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
    	   	return $res_array;
    	}
    }


?>
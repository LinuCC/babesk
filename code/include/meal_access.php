<?php
    /**
     * Provides a class to manage the meals of the system
     */

    require_once 'constants.php';
    require_once 'access.php';

    /**
     * Manages the meals, provides methods to add/modify meals or to get meal data
     */
    class MealManager extends TableManager {
    
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
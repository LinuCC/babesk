<?php
    /**
     * Provides a class to manage the orders of the system
     */
    
    /**
     * Manages the orders, provides methods to add/modify orders or to get order data
     */
    class OrderManager {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
        }
        
        
        /**
         * Returns the value of the requested fields for the given order id.
         *
         * The Function takes a variable amount of parameters, the first being the order id
         * the other parameters are interpreted as being the fieldnames in the orders table.
         * The data will be returned in an array with the fieldnames being the keys.
         *
         * @return false if error
         */
        function getOrderData() {
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
                        orders
                    WHERE
                        ID = '.$id.'';
            $result = $this->db->query($query);
            if (!$result) {
                echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
                return false;
            }
            return $result->fetch_assoc();
        }
        
        function getOrdersOfUser($uid, $date) {
            $query = 'SELECT * 
                        FROM orders 
                        WHERE UID = "'.$uid.'"     
                        AND date = "'.$date.'"
                        ORDER BY date;';
            $result = $this->db->query($query);
            if (!$result) {
                die(DB_QUERY_ERROR.$this->db->error);
            }
            return $result;
        }
        /**
          *Returns all Orders for given User which are newer than the given date
          */
        function getAllOrdersOfUser($uid, $date) {
            $query = "SELECT * 
                      FROM orders 
                      WHERE UID = ".$uid." AND date >= '".$date."' 
                      ORDER BY date";
            $result = $this->db->query($query);
            if (!$result) {
                die(DB_QUERY_ERROR.$this->db->error);
            }
            return $result;
        }
        
        /** 
          *Returns all Orders that are in the Database
          */
        function getAllOrders() {
            $query = 'SELECT
                    *
                    FROM
                    orders';
            $result = $this->db->query($query);
            if(!$result) {
                echo DB_CONNECT_ERROR.mysqli_error();
                exit;
            }
            $orders = array();
            while($order = $result->fetch_assoc())
        		$orders[] = $order;
            return $orders;
        }
        
        /**
          *returns all orders for the given date
          */
        function getAllOrdersAt($date) {
        	$orders = array();
        	$query = 'SELECT * 
        			  FROM orders 
        			  WHERE date = "'.$date.'"';
        	$result = $this->db->query($query);
        	if (!$result) die(DB_QUERY_ERROR.$this->db->error);
        	while($order = $result->fetch_assoc())
        		$orders[] = $order;
        	return $orders;
        }
        
        /** 
          *removes orders which are older then Servertime plus a day
          */
        function RemoveOldOrders ($search_date) {
            require_once PATH_INCLUDE.'/logs.php';
            require_once PATH_INCLUDE.'/constants.php';

            global $logger;
			$orders = OrderManager::getAllOrders();

		if(preg_match('/\A[0-9]{2,4}-[0-9]{2}-[0-9]{2}\z/',$search_date)) {
			$search_array = explode('-', $search_date);
			$search_timestamp = mktime(0, 0, 1, $search_array[1], $search_array[2], $search_array[0]);
			}
		else if(preg_match('/\A[0-9]{1,}\z/',$search_date))
			$search_timestamp = $search_date;
		else if(empty($search_date)) {
			print 'keine Bestellungen wurden gelöscht.<br>';
			return;
		}
		else {
			var_dump($search_date);
			$logger->log(ADMIN,MODERATE,'ORDER_F_ERROR_DATE_FORMAT');
			die(ORDER_F_ERROR_DATE_FORMAT);
		}
		foreach($orders as $order) {
			$o_timearray = explode("-", $order["date"]);
			$o_timestamp = mktime(0, 0, 1, $o_timearray[1], $o_timearray[2], $o_timearray[0]);
			if($o_timestamp < $search_timestamp) {
				if(OrderManager::delOrder($order['ID']));
					//$logger->log(ADMIN,NOTICE,ORDER_DELETED);
				else
					$logger->log(ADMIN,MODERATE,ORDER_ERROR_DELETE.'dump:'.var_dump($order));
			}
		}
        }
        
        /**
         * Adds an Order to the System
         *
         * The Function creates a new entry in the orders Table
         * consisting of the given Data
         *
         * @param meal_ID The ID of the meal that is ordered
         * @param UID The ID of the user the order is from
         * @param date The date the user orders the meal for
         * @return false if error
         */
        function addOrder($meal_ID, $UID) {
           $query = 'SELECT date FROM meals WHERE ID = '.$meal_ID.';';
           $result = $this->db->query($query);
           if (!$result) {
                echo DB_QUERY_ERROR.$this->db->error;
                return false;
            }
            $date = $result->fetch_assoc();
            $IP = $_SERVER['REMOTE_ADDR'];
            $query = 'INSERT INTO
                            orders(MID, UID, IP, date, ordertime)
                      VALUES
                            ('.$meal_ID.', '.$UID.', "'.$IP.'", "'.$date["date"].'", NOW());';
    
           $result = $this->db->query($query);
            if (!$result) {
                echo "Table Orders: ".DB_QUERY_ERROR.$this->db->error;
                return false;
            }
            return true;
        }
         
        
        /**
         * Deletes an order from the system
         *
         * Delete the entry from the orders table with the given ID
         *
         * @param ID The ID of the group
         * @return false if error
         */
        function delOrder($ID) {
            $query = 'DELETE FROM
                           orders
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
                echo DB_QUERY_ERROR.$this->db->error;
                return false;
            }
            return true;
        }
        
        function setOrderFetched($ID) {
            $query = 'UPDATE orders
                        SET fetched = 1
                      WHERE ID = '.$ID.';';
            $result = $this->db->query($query);
            if (!$result) {
                echo DB_QUERY_ERROR.$this->db->error;
                return false;
            }
            return true;
        }
        
        function OrderFetched($ID) {
            $order_data = $this->getOrderData($ID, 'fetched');
            if($order_data['fetched']) {
                return true; 
            }
            else {
                return false;
            }
        }
    }    


?>
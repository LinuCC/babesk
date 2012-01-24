<?php
    /**
     * Provides a class to manage the orders of the system
     */
    
	require_once 'access.php';

    /**
     * Manages the orders, provides methods to add/modify orders or to get order data
     */
    class OrderManager extends TableManager {
    	
    	function __construct () {
    		parent::__construct('orders');
    	}
    	
        /**
          *Returns all Orders for given User which are newer than the given date
          */
        function getAllOrdersOfUser($uid, $date) {
        	try {
        		$result = TableManager::getTableData('UID = "'.$uid.'" AND date >= "'.$date.'" ORDER BY date');
        	} catch (MySQLVoidDataException $e) {
        		throw new MySQLVoidDataException($e->getMessage()); 
        	} catch (Exception $e) {
        		throw new Exception($e->getMessage());
        	}
            return $result;
        }

        /**
          * returns all orders for the given date
          */
        function getAllOrdersAt($date) {
        	try {
        		$orders = TableManager::getTableData('date = "'.$date.'"');
        	} catch (MySQLVoidDataException $e) {
        		$orders = NULL;
        	}
        	return $orders;
        }
        
        /**
         * sets the order possessing the given ID to fetched
         * Enter description here ...
         * @param long/string $ID
         * @return boolean true if everything has gone right
         */
        function setOrderFetched($ID) {
            $query = sql_prev_inj(sprintf('UPDATE orders
                        SET fetched = 1
                      WHERE ID = %s;',$ID));
            $result = $this->db->query($query);
            if (!$result) {
                echo DB_QUERY_ERROR.$this->db->error;
                return false;
            }
            return true;
        }

        /**
         *  looks up if the order possessing the given ID is fetched or not
         * Enter description here ...
         * @param long/string $ID
         * @return boolean true if fetched, false if not fetched
         */
        function OrderFetched($ID) {
            $order_data = $this->getEntryData($ID, 'fetched');
            if($order_data['fetched']) {
                return true; 
            }
            else {
                return false;
            }
        }
        
        /**
         * Adds an order to the MySQL-orders-table
         * Enter description here ...
         * @param unknown_type $MID
         * @param unknown_type $UID
         * @param unknown_type $IP
         * @param unknown_type $date
         */
        function addOrder($MID, $UID, $IP, $date) {
        	parent::addEntry('MID', $MID,
        					'UID', $UID, 
        					'IP', $IP, 
        					'ordertime', time(), 
        					'date', $date);
        }
        
        /**
         * returns all orders of a meal
         * Enter description here ...
         * @param numberic_string $ID the ID of the meal whose orders to return
         * @return array of orders
         */
        function getAllOrdersOfMeal($ID) {
        	return parent::getTableData(sprintf('MID = %s', $ID));
        }
    }   


?>
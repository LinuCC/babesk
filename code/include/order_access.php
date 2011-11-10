<?php
    /**
     * Provides a class to manage the orders of the system
     */
    
	require_once 'access.php';

    /**
     * Manages the orders, provides methods to add/modify orders or to get order data
     */
    class OrderManager extends TableManager {
    
        /**
          *Returns all Orders for given User which are newer than the given date
          */
        function getAllOrdersOfUser($uid, $date) {
        	try {
        		$result = TableManager::getTableData('UID = "'.$uid.'" AND date >= "'.$date.'" ORDER BY date');
        	} catch (MySQLVoidDataException $e) {
        		$result = NULL;
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
    }    


?>
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
        *Returns all Orders for given User at the given date
        */
        function getAllOrdersOfUserAtDate($uid, $date) {
        	try {
        		$result = TableManager::getTableData('UID = "'.$uid.'" AND date = "'.$date.'" ORDER BY date');
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
          *Returns all Orders for given User which are between the given dates
          */
        function getAllOrdersOfUserBetween($uid, $date_start, $date_end) {
        	try {
        		$result = TableManager::getTableData('UID = "'.$uid.'" AND date >= "'.$date_start.'" AND date <= "'.$date_end.'"  ORDER BY date');
        	} catch (MySQLVoidDataException $e) {
        		throw new MySQLVoidDataException($e->getMessage()); 
        	} catch (Exception $e) {
        		throw new Exception($e->getMessage());
        	}
            return $result;
        }
        
        /**
         * sets the order possessing the given ID to fetched
         * Enter description here ...
         * @param long/string $ID
         * @throws MySQLException
         * @return boolean true if everything has gone right
         */
        function setOrderFetched($ID) {
            $query = sql_prev_inj(sprintf('UPDATE orders
                        SET fetched = 1
                      WHERE ID = %s;',$ID));
            $result = $this->db->query($query);
            if (!$result) {
                throw new MySQLException(sprintf('MySQL failed to execute the query; %s', $this->db->error));
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
        
        /**
        * returns all orders of a meal at givne date
        * @param numberic_string $ID the ID of the meal whose orders to return
        * @param date the date
        * @return array of orders
        */
        function getAllOrdersOfMealAtDate($ID,$date) {
        	return parent::getTableData(sprintf('MID = "%s" AND date = "'.$date.'" ORDER BY date', $ID,$date));
        }
       
    }   


?>
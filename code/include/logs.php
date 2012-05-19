<?php
	
	require_once PATH_ACCESS . '/TableManager.php';

    // The log categories
    define('ADMIN', 'ADMIN');     // everything happening in the admin area
    define('WEB', 'WEB');       // everything happening in the web frontend 
    define('USERS', 'USERS');     // everything dealing with the users (registration, etc)                                 
    
    // The log severity
    define('NOTICE', 'NOTICE');     // just a notice, no error
    define('MODERATE', 'MODERATE');   // moderate errors, the system is still functional
    define('CRITICAL', 'CRITICAL');   // critical errors, the system or a part of it can't function correctly
    
       
    class Logger extends TableManager{
    
        public function __construct() {
        	TableManager::__construct('logs');
        }
    
        /**
         * Log a message in the log table
         *
         * @param area The area in the system where the event occured
         * @param severity The severity of the event
         * @param value msg The log message
         *
         * @return false if error occured
         */
        function log($category, $severity, $msg) {
        	$msg = str_replace('"', '\'', $msg);
            $query = sql_prev_inj(sprintf('INSERT INTO logs(category, severity, time, message) 
            			VALUES ("%s", "%s", CURRENT_TIMESTAMP, "%s");', $category, $severity, $msg));
            $result = $this->db->query($query);
            if (!$result) {
        	   echo DB_QUERY_ERROR.$this->db->error;
        	   return false;
        	}
    	    return true;
        }
        
        /**
         * Returns the value of the requested fields for the given log-ID or all logs
         * 
         * The function takes a variable amount of parameters.
         * First of, if you do not give any parameters, it will return all logs with all fields.
         * Else, the first Parameter is interpreted as the ID of the log, ther other parameters
         * as being the fieldnames in the log-table.
         * 
         * @return in an array with the fieldnames being the keys. false if error 
         */
        function getLogData() {
        	$num_args = func_num_args();
        	if($num_args == 0){
        		$query = sql_prev_inj(sprintf('SELECT * FROM logs'));
        	}
        	else if($num_args > 1){
        		$id = func_get_arg(0);
        		$fields = '';
        		for($i = 1; $i < $num_args - 1; $i++) {
        			$fields .= func_get_arg($i).', ';
        		}
        		//query must not contain an ',' after the last field name
        		$fields .= func_get_arg($num_args - 1);
        		$query = sql_prev_inj(sprintf('SELECT %s FROM logs WHERE ID = %s', $fields, $id));
        	}
        	else {
        		return false;
        	}
        	$result = $this->db->query($query);
        	if (!$result) {
        		echo DB_QUERY_ERROR.$this->db->error."<br />".$query;
        		return false;
        	}
        	$res_array = array();
        	while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
        	return $res_array;
        }
        
        /**
         * returns the logs which have the given Category
         * Enter description here ...
         * @param $category a string
         */
        public function getLogDataByCategory($category) {
        	if(!isset($category) || !is_string($category)) {
        		throw new BadMethodCallException('Wrong argument given in '.__METHOD__);
        	}
        	$query = sql_prev_inj(sprintf('SELECT * FROM logs WHERE category = "%s"', $category));
        	$result = $this->db->query($query);
        	if(!$result) {
        		throw new MySQLConnectionException('Problem with getting data from MySQL:'.mysql_error());
        	}
        	$logs = array();
        	try {
        		while($buffer = $result->fetch_assoc())$logs[] = $buffer;
        	} catch (Exception $e) {
        		throw new MySQLConnectionException('Problem with getting data from MySQL:'.mysql_error());
        	}
        	if(!$logs) {
        		throw new MySQLVoidDataException('MySQL returned no data');
        	}
        	return $logs;
        }
        
        /**
         * Fetches all Logs from the MySQL-Table which are from the given category and severity
         * @param string $category The Category of the logs
         * @param string $severity The Severity of the logs
         * @return array() an array of logs
         */
        public function getLogDataByCategoryAndSeverity ($category, $severity) {
        	
        	$logs = $this->getTableData('category = "'.$category.'" AND severity = "'.$severity.'"');
        	return $logs;
        }
        
        /**
         * Clears the log table
         *
         * @return false if error occured
         */
        function clearLogs() {
            $query = 'TRUNCATE TABLE logs';
            $result = $this->db->query($query);
            if (!$result) {
        	   echo DB_QUERY_ERROR.$this->db->error;
        	   return false;
        	}
    	    return true;   
        }
        
        /**
         * Clears all logs that are older than the given date
         *
         * @param timestamp The date vefore which the logs should be cleared
         *
         * @return false if error
         */
        function clearLogsBefore($timestamp) {
            $query = 'DELETE FROM logs WHERE date < '.$timestamp.';';
            $query = sql_prev_inj(sprintf('DELETE FROM logs WHERE date < %s;', $timestamp));
            $result = $this->db->query($query);
            if (!$result) {
        	   echo DB_QUERY_ERROR.$this->db->error;
        	   return false;
        	}
    	    return true;    
        }
    }
    
    $logger = new Logger();

?>
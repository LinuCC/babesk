<?php
    
    // The log categories
    define('ADMIN', 'ADMIN');     // everything happening in the admin area
    define('WEB', 'WEB');       // everything happening in the web frontend 
    define('USERS', 'USERS');     // everything dealing with the users (registration, etc)                                 
    
    // The log severity
    define('NOTICE', 'NOTICE');     // just a notice, no error
    define('MODERATE', 'MODERATE');   // moderate errors, the system is still functional
    define('CRITICAL', 'CRITICAL');   // critical errors, the system or a part of it can't function correctly
    
       
    class Logger {
    
        private $db;
        
        public function __construct() {
            require "dbconnect.php";
            $this->db = $db;
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
            $query = 'INSERT INTO
                	    logs(category, severity, time, message)
                      VALUES
                        ("'.$category.'", "'.$severity.'", CURRENT_TIMESTAMP, "'.$msg.'");';
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
        		$query = 'SELECT * FROM logs';
        	}
        	else if($num_args > 1){
        		$id = func_get_arg(0);
        		$fields = '';
        		for($i = 1; $i < $num_args - 1; $i++) {
        			$fields .= func_get_arg($i).', ';
        		}
        		//query must not contain an ',' after the last field name
        		$fields .= func_get_arg($num_args - 1);
        	
        		$query = 'SELECT
		   					'.$fields.'
           				FROM
           					logs
           				WHERE
           					ID = '.$id.'';
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
         * Creates HTMl output containing the log messages
         *
         * @param area Print only logs that are in this area
         * @param severity Print only logs with this severity
         *
         * @return false if error occured
         */
        function printLogs($category = "", $severity = "") {
            //if not set, use wildcards in sql query
            $category = 'category = "'.$category.'"';    
            $severity = 'severity = "'.$severity.'"';    
            
            $query = 'SELECT * FROM logs WHERE '.$category.' AND '.$severity.';';
            $result = $this->db->query($query);
            if (!$result) {
               die(DB_QUERY_ERROR.$this->db->error);
        	}
        	echo "<table>
                    <tr>
                        <th>Log ID</th>
                        <th>Category</th>
                        <th>Severity</th>
                        <th>Date</th>
                        <th>Message</th>
                    </tr>";
        	for ($i = 0; $i < $result->num_rows; $i++) {
                $row = $result->fetch_assoc();
                // Output logs in HTML Table
                echo "<tr>";
                echo "<td>".$row["ID"]."</td><td>".$row["category"]."</td><td>".$row["severity"]."</td><td>".$row["time"]."</td><td>".$row["message"]."</td>";
                echo "</tr>";
        	}
            echo "</table>";    
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
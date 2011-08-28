<?php
    
    // The log categories
    define('ADMIN', 0);     // everything happening in the admin area
    define('WEB', 1);       // everything happening in the web frontend 
    define('USERS', 2);     // everything dealing with the users (registration, etc)                                 
    
    // The log severity
    define('NOTICE', 0);     // just a notice, no error
    define('MODERATE', 1);   // moderate errors, the system is still functional
    define('CRITICAL', 2);   // critical errors, the system or a part of it can't function correctly
    
       
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
                        ('.$category.', '.$severity.', CURRENT_TIMESTAMP, "'.$msg.'");';
            $result = $this->db->query($query);
            if (!$result) {
        	   echo DB_QUERY_ERROR.$this->db->error;
        	   return false;
        	}
    	    return true;
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
            if(!empty($category)) $category = 'WHERE category = '.$category;    
            if(!empty($severity)) $severity = ' AND severity = '.$severity;    
            
            $query = "SELECT * FROM logs ".$category.$severity.";";
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
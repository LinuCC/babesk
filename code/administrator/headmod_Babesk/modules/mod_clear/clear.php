<?php

    require PATH_ACCESS . "/databaseDistributor.php";
    
    //get the password of the global admin
    $query = 'SELECT
				password
			FROM
				administrators
			WHERE
				ID = 1';
    $result = $db->query($query);
	if (!$result) {
    	echo DB_QUERY_ERROR.$db->error."<br />".$query;
    	return false;
	}
	$row = $result->fetch_assoc();
    
    $globalAdminPassword = $row["password"];
    		  
    
    $queries = array();
    
    $queries[] = "Truncate Table `users`";
    $queries[] = "Truncate Table `groups`"; 
    $queries[] = "Truncate Table `meals`"; 
    $queries[] = "Truncate Table `orders`"; 
    $queries[] = "Truncate Table `price_classes`"; 
    $queries[] = "Truncate Table `logs`"; 
    $queries[] = "Truncate Table `ip`"; 
    $queries[] = "Truncate Table `cards`;";
    $queries[] = "Truncate Table `administrators`;";
    $queries[] = "Truncate Table `admin_groups`;";
    //keep the global admin
    $queries[] = 'INSERT INTO
            	    admin_groups(name, modules)
               VALUES
                    ("global",
                     "_ALL");';
    $queries[] = 'INSERT INTO
            	    administrators(name, password, GID)
               VALUES
                    ("admin",
                     "'.$globalAdminPassword.'",
                     1);'; 
    
    foreach ($queries as $query) {
        $result = $db->query($query);
        if (!$result) {
        	die(DB_QUERY_ERROR.$db->error);
    	}
    }
	
    echo "<h4>Tables have been cleared!<h4>";
?>
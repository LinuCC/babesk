<?php
    require_once 'constants.php';
    
    $host = '';
    $username = '';
    $password = '';
    $database = '';
    
    $db = @new MySQLi($host, $username, $password, $database);
    $db->set_charset('utf8');
    if (mysqli_connect_errno()) {
        exit(DB_CONNECT_ERROR.mysqli_connect_error());
    }

?>
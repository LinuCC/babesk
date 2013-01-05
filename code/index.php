<?php
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
        <head>
            <title>Index</title>
            <meta content="Legeria Index">
        </head>
        <body>
		  <a href="administrator/index.php">Administrator-Frontend</a><br \>
		  <a href="web/index.php">Web-Frontend</a><br \>
		  <a href="installation/index.php">Installation</a><br \><br />
		  <a href="manager_example.php">Manager Beispiel</a><br \>';

    if(file_exists("installation")) {
        echo "<br \><h4>Installations Verzeichnis existiert</h4>";
        //redirect to installation
    }
    else {
        echo "<br \><h4>Installations Verzeichnis existiert nicht</h4>";
        //redirect to web
    }

    echo '</body>
    </html>';
?>

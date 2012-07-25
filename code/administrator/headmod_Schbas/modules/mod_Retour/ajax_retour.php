<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "babesk";
	//Connect to MySQL Server
mysql_connect($dbhost, $dbuser, $dbpass);
	//Select Database
mysql_select_db($dbname) or die(mysql_error());
	// Retrieve data from Query String
$inr = $_GET['inventarnr'];

	// Escape User Input to help prevent SQL Injection
$inr = mysql_real_escape_string($inr);
$inr_exploded = explode(' ', $inr);
//build query EN 2008 10 1 / 31
$query = "SELECT * FROM schbas_lending AS l, schbas_inventory AS i WHERE i.id=l.inventory_id AND '".$inr_exploded[1]."'=i.year_of_purchase";
	//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

	//Build Result String
$display_string = "<table>";
$display_string .= "<tr>";
$display_string .= "<th>Name</th>";
$display_string .= "</tr>";

	// Insert a new row in the table for each person returned
while($row = mysql_fetch_array($qry_result)){
	$display_string .= "<tr>";
	$display_string .= "<td>$row[lend_date]</td>";
	$display_string .= "</tr>";
	
}
echo "Query: " . $query . "<br />";
$display_string .= "</table>";
echo $display_string;
?>
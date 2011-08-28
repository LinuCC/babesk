<?php

	/**
	  *@file register_test.php All in all it is a formular which fill out itself with randomly chosen values to make the testing easier
	  */

	$right_data = array("forename" => array("Peter","Hans","Horst","Der Tod","Johanna","Maria","Josua","Albert","Ichigo","Alexander"),
						"name" => array("Köhler","Enis","xychotl","Müller","Mustermann","bin Laden","Lampe","Siering","Krentz","Kaschub"),
						"password" => array("1234","qwertz","FanVonHitler88","KommunismusIstGuT","password","WhySoSerious","ichbin blubb","9768434","blubb","whaaahaha"),
						"ID" => ("8833883388".rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9)),
						"birthday_d" => rand(1,31),
						"birthday_m" => rand(1,12),
						"birthday_y" => rand(1910,2000),
						"GID" => (rand(0,99999)),
						"credits" => (rand(0,99).'.'.rand(0,99)));

	$wrong_data; //add wrong data
	$random = array(rand(0,9),rand(0,9),rand(0,9));
	$username = "Bitte eingeben";

	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
			<title>Registrierformular</title>
		</head>
		
		<body>
			<br><br>
			<b>Vorausgef&uuml;lltes Formular</b>
			<form action="index.php?section=register" method="post">
			<fieldset>
				<legend>Pers&ouml;nliche Daten</legend>
				<label for="forename">Vorname:</label>
				<input type="text" name="forename" value="'.$right_data["forename"][$random[0]].'"/><br><br>
				<label for="name">Name:</label>
				<input type="text" name="name" value="'.$right_data["name"][$random[1]].'"/><br><br>
				<label for="username">Benutzername:</label>
				<input type="text" name="username" value="'.$username.'"/><br><br>
				<label for="passwd">Passwort:</label>
				<input type="password" name="passwd" value="'.$right_data["password"][$random[2]].'"/><br><br>
				Geburtstag :
				<label for="b_day">Tag:</label>
				<input type="int" name="b_day" size="2" maxlength="2" value="'.$right_data["birthday_d"].'"/>
				<label for="b_month">Monat:</label>
				<input type="int" name="b_month" size="2" maxlength="2" value="'.$right_data["birthday_m"].'"/>
				<label for="b_year">Jahr:</label>
				<input type="int" name="b_year" size="4" maxlength="4" value="'.$right_data["birthday_y"].'"/>
			</fieldset>
			<br>
			<fieldset>
				<legend>Identit&auml;tsinformationen</legend><br><br>
				<label for="id">ID:</legend>
				<input type="text" name="id" size="20" maxlength="20" value="'.$right_data["ID"].'"/><br><br>
				<label for="gid">GID:</label>
				<input type="text" name="gid" size="5" maxlength="5" value="'.$right_data["GID"].'"/>
				<label for="credits">Guthaben:</label>
				<input type="int" name="credits" size="5" maxlength="5" value="'.$right_data["credits"].'"/>
			</fieldset><br>
			<input type="submit" value="Submit" />
		</form>
		</body>
		'
?>
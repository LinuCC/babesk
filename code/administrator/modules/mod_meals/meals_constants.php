<?php
	/**
	 *\file meals_constants.php saves some constants needed for the meals-module, for example Error-messages
	*/
	//messages output in browser (language-depending)
	define('MEAL_ERROR_NAME','<p class="error">Der Name der Mahlzeit wurde nicht korrekt ausgefüllt. Bitte tragen sie ihn richtig ein <br></p>');
	define('MEAL_ERROR_DESCRIPTION','<p class="error">Die Beschreibung der Mahlzeit wurde nicht korrekt ausgefüllt. Bitte tragen sie sie richtig ein <br></p>');
	define('MEAL_ERROR_PRICE_CLASS','<p class="error">De Preisklasse der Mahlzeit wurde nicht korrekt ausgefüllt. Bitte tragen sie sie richtig ein <br></p>');
	define('MEAL_ERROR_MAX_ORDERS','<p class="error">Die maximalen Bestellungen der Mahlzeit wurde nicht korrekt ausgefüllt. Bitte tragen sie sie richtig ein <br></p>');
	define('MEAL_ERROR_TABLE','<p class="error">Konnte die meal-Tabelle nicht erreichen (oder leer)<br></p>');
	define('MEAL_NO_DELETE','Keine Mahlzeit wurde gel&ouml;scht.<br>');
	define('MEAL_ERROR_DATE','<p class="error">Es wurde ein unmögliches Datum eingegeben.<br></p>');
	define('MEAL_F_ERROR_DATE_FORMAT','<p class="error">Ein falsches Format wurde der Funktion remove_old_meals übergeben.<br></p>');
	define('MEAL_DATABASE_PROB_ENTRY','<p class="error"><br>---------------------------------<br>ein Datenbankeintrag enthält fehlerhafte Informationen.<br></p>');
	define('MEAL_DATABASE_PROB_ENTRY_END','<p class="error"><br>---------------------------------<br></p>');
	define('MEAL_ERROR_PARAM','<p class="error">Ein falsches Argument wurde einer Funktion übergeben!</p>');
	define('MEAL_NO_ORDERS_FOUND','<p class="error">Es wurden keine Bestellungen für den angegebenen Zeitraum gefunden</p>');
	define('MEAL_NO_MEALS_FOUND','<p class="error">Es wurden keine Mahlzeiten gefunden</p>');
	
	define('MEAL_ADDED','Mahlzeit wurde erfolgreich hinzugefügt');
	
	//Messages who are getting logged, so english
	define('MEAL_ERROR_DELETE','Could not delete an old meal');
	define('MEAL_DELETED','meal deleted');
		
	//paths
	define('MEAL_SMARTY_TEMPLATE_PATH',PATH_SMARTY.'/templates/administrator/modules/mod_meals');
?>
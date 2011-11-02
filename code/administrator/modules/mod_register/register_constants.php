<?php
    /**
	 *@file register_constants.php some constants for the register-module, at the moment Errorsentences
	*/
    
   define('REG_VER_PATH','<p class="error">code/administrator/registrieren/registrier_verarbeitung.php</p>');
	define('REG_UNMATCHED_PASSWORDS', '<p class="error">Das Passwort stimmt nicht mit der Passwort-wiederholung &uuml;berein.</p>');
	define('REG_ERROR_FORENAME','<p class="error">Der Vorname wurde inkorrekt eingegeben.</p>');
	define('REG_ERROR_NAME','<p class="error">Der Name wurde inkorrekt eingegeben.</p>');
	define('REG_ERROR_ID','<p class="error">Fehlerhafte Zeichen in ID oder inkorrekte L&auml;nge, bitte Formular korrekt ausf&uuml;llen</p>');
	define('REG_ERROR_GID','<p class="error">Fehlerhafte Eingabe der Gruppen-ID (GID)</p>');
	define('REG_ERROR_PASSWORD','<p class="error">Inkorrekte Passwort-Eingabe. Erlaubt sind Minimal 4 Zeichen und maximal 20 Zeichen, normale Buchstaben und Zahlen</p>');
	define('REG_ERROR_BIRTHDAY','<p class="error">Fehlerhafte Eingabe des Geburtsdatums</p>');
	define('REG_ERROR_CREDITS','<p class="error">Fehlerhafte Eingabe des Guthabens. Maximalwert betr&auml;gt 100 Euro, nur Zahlen erlaubt</p>');
	define('REG_ERROR_MYSQL','<p class="error">Problem beim Versuch, den neuen Benutzer in den MySQL-Server einzutragen</p>');
	define('REG_ERROR_USERNAME','<p class="error">Fehlerhafte Eingabe des Benutzernamen</p>');
	
	define('REG_PLEASE_REPEAT','Bitte wiederholen sie den Vorgang');
	define('REG_PLEASE_REPEAT_CARD_ID','Bitte wiederholen sie den Vorgang von Anfang an (die Karte nochmal neu einlesen)');
	
	define('PATH_TEMPLATE_REG',PATH_SMARTY.'/templates/administrator/modules/mod_register/register.tpl');
	define('PATH_TEMPLATE_CARD',PATH_SMARTY.'/templates/administrator/modules/mod_register/register_input_id.tpl');
?>
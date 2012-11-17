<?php
    //Database
    define('DB_CONNECT_ERROR', '<p class="error">Es konnte keine Verbindung zum MySql-Server hergestellt werden. MySQL meldet folgenden Fehler: </p>');
    define('DB_QUERY_ERROR', '<br /><p class="error">Ein Fehler trat in der Datenbank auf. MySQL meldet folgenden Fehler: </p>');

    //Form Evaluation
    define('EMPTY_FORM', '<p class="error">Bitte f&uuml;llen sie das Formular vollst&auml;ndig aus.</p>');
    define('INVALID_FORM', '<p class="error">Benutzen sie bitte nur Formulare von der Homepage.</p>');
    
    //Login
    define('INVALID_LOGIN', '<p class="error">Der Benutzer wurde nicht gefunden oder das Passwort ist falsch.</p>');
    define('INVALID_CHARS', 'Eingegebene Daten wurden falsch eingegeben');
    define('MAX_LOGIN_TIME', '300');
    define('UNMATCHED_PASSWORDS', '<p class="error">Die Passw&ouml;rter stimmen nicht &uuml;berein!</p>');
    define('ACCOUNT_LOCKED','<p class="error">Kennung gesperrt!</p>');
    
    //Registration
    define('USERNAME_EXISTS', '<p class="error">Der angegebene Benutzername existiert bereits, bitte einen anderen w&auml;hlen</p>');
    define('GROUP_EXISTS', '<p class="error">Der angegebene Gruppenname existiert bereits, bitte einen anderen w&auml;hlen</p>');
    
    //Modules
    define('MODULE_NOT_FOUND', '<p class="error">Das Modul konnte nicht gefunden werden.</p>');
    define('MODULE_FORBIDDEN', '<p class="error">Sie besitzen nicht die erforderlichen Rechte f&uuml;r dieses Modul.</p>');

    //Session
    define('INVALID_SESSION', '<p class="error">Die Sitzung ist ung&uuml;ltig, bitte erneut einloggen.</p>');
    
    //Card
    define('CARD_READ_ERROR', '<p class="error">Die Karte konnte nicht gelesen werden, bitte versuchen sie es ernuet</p>');
    define('INVALID_CARD_ID', '<p class="error">Die Karten-ID ist fehlerhaft</p>');
    
    //Orders
    define('ORDER_F_ERROR_DATE_FORMAT', '<p class="error">Falsches Datumsformat für $search_date in delOldOrders!</p>');
    define('ORDER_ERROR_DELETE', '<p class="error">Ein Fehler ist beim löschen einer Bestellung aufgetreten!</p>');
    
    //defensiv programming
    define('ERR_NUMBER_PARAM', '<p class="error">Die Anzahl der übergebenen Parameter ist falsch!</p>');
?>
<?php
//////////////////////////////////////////////////
//Interface-strings
{
	define('NO_MEALS_EXISTING', '<p class="error">Es sind keine Mahlzeiten vorhanden</p>');
	define('ERR_ORDER', '<p class="error">Ein Fehler ist beim bestellen aufgetreten</p>');
	define('TIMEFORMAT_ERROR', 'Uhrzeit falsch angegeben. Administrator informieren!');
	define('LOG_ERR_ORDER', 'Error in mod_order');
}
//////////////////////////////////////////////////
//Constant Variables
//Deadline to order meals. MUST BE in the format hh:mm !!!
{
	$last_order_time = date("08:30");
}
?>
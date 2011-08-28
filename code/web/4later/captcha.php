<?php
error_reporting(E_ALL);

session_start();

$zeichen = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");

shuffle($zeichen);

$session["captcha"] = array_slice($zeichen,0,4);

?>
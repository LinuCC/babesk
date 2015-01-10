BaBeSK
======


Requirements
------------

BaBeSK needs a Server running
 * PHP Version 5.3 or higher
 * MySQL-Server (3.3 or higher)
 * GD library

Modules
-------

Babesk contains multiple modules ("headmodules") providing different
functionalities.
Note that it also contains a module named Babesk, the original purpose of this
program.

The following and more exist:

* BaBeSK: "Bargeldloses Bestellsystem für Schulkantinen"
* Fits: "Führerschein für IT-Systeme"
* Kuwasys: "Kurswahlsystem"
* Schbas: "Schulbuchausleihsystem"

Disclaimer
----------

This Program is still under development, which means that some things can
go unexpected or even wrong. It comes with absolute no warranty.
Bugreports are awesome.
Have fun!

General Installation
--------------------

* Copy the files in code/ to your designated webserver
* Create a file databaseValues.php from
  code/include/sql_access/databaseValues.php.orig and fill in the
  connection-details of your database
* Create a file config.php from dbv/config.php.sample and fill in the
  connection-details of your database.
* Put dbv on a webserver. Only the database-admin should have access to it.
* Open dbv in the webbrowser and execute the Queries, filling the database with
  data.
* Open the URL to administrator in your Webbrowser, Log in as admin and
  customize the system.

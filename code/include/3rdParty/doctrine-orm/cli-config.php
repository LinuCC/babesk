<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once dirname(__FILE__) . '/../../path.php';
require_once '../../sql_access/DBConnect.php';
$connect = new DBConnect();
$connect->initDatabaseFromXml();
$entityManager = $connect->getDoctrineEntityManager();
// replace with mechanism to retrieve EntityManager in your app
//$entityManager = GetEntityManager();

return ConsoleRunner::createHelperSet($entityManager);

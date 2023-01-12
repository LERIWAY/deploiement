<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
date_default_timezone_set('America/Lima');
require_once "vendor/autoload.php";
$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
$conn = array(
'host' => 'dpg-ceo3oimn6mpoovl02sb0-a.oregon-postgres.render.com',
'driver' => 'pdo_pgsql',
'user' => 'webcnamdb_user',
'password' => 'EGhU7MYux8w0qPpq5qYdQWbLR1Wwdu3I',
'dbname' => 'webcnamdb',
'port' => '5432'
);
$entityManager = EntityManager::create($conn, $config);

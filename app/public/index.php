<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = require_once __DIR__ . '/../src/settings.php';


$app = new \Slim\App($settings);
require_once __DIR__ . '/../src/dependencies.php';

require_once __DIR__ . '/../src/middleware.php';

require_once __DIR__ . '/../src/routes.php';


$app->run();

$app->db = null;

?>
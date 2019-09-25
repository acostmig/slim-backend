<?php
$container = $app->getContainer();


$container['renderer'] = function($c)
{
    $settings = $c ->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer('/templates');
};

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('Logger: ');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app'.date('Y-m-d').'.log');
    $logger->pushHandler($file_handler);

    return $logger;
};


$container['db'] = function ($c) {
    $settings = $c['settings']['db'];
  
    $pdo = new PDO('mysql:host=' . $settings['host'] . ';dbname=' . $settings['dbname'].";charset=utf8",
        $settings['user'], $settings['pass'], array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['LoginController'] = function ($c) {
   
    return new \app\src\controllers\LoginController($c);    
};

$container['UserController'] = function ($c) {
   
    return new \app\src\controllers\UserController($c);    
};

$container['MenuController'] = function ($c) {
   
    return new \app\src\controllers\MenuController($c);    
};

$container['FileController'] = function ($c) {
   
    return new \app\src\controllers\FileController($c);    
};

$container['User'] = function ($c) {
   
    return new \app\src\services\User($c);    
};

$container['Menu'] = function ($c) {
   
    return new \app\src\services\Menu($c);    
};

$container['File'] = function ($c) {
   
    return new \app\src\services\File($c);    
};
?>
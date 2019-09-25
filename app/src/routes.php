<?php


$app -> group('/login', function () use ($app)
{

    $app ->post('/authenticate', LoginController::class.':login');
    $app ->get('/renewsession', LoginController::class.':renewSession');
});

$app -> group('/user', function () use ($app)
{

    $app ->get('/props', UserController::class.':getUserbyToken');
});

$app -> group('/menu', function () use ($app)
{
    $app ->get('/item/{itemid}', MenuController::class.':getItem');
    $app ->get('/item', MenuController::class.':getItem');
    $app ->post('/item/{itemid}', MenuController::class.':updateItem');   
    $app ->post('/item/{itemid}/delete', MenuController::class.':deleteItem');
    $app ->put('/item', MenuController::class.':addItem');


    $app ->put('/image', FileController::class.':addMenuImage');
    $app ->get('/image', FileController::class.':getMenuImage');
    $app ->post('/image/{imageid}', FileController::class.':updateMenuImage');
    $app ->post('/image/{imageid}/delete', FileController::class.':deleteMenuImage');
    
   
});

?>
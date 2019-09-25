<?php



namespace app\src\controllers;


Class UserController
{

    public function __construct($app)
    {
        $this ->app =$app;
        $this ->User = $app['User'];
    }

    public function getUserbyToken()
    {
        return $this->app->response->withJson($this ->User->getUserObjectbyToken($this->app->request->getHeader('Authorization')[0]));
    }
    
}
?>
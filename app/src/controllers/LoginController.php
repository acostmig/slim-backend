<?php



namespace app\src\controllers;


Class LoginController
{

    public function __construct($app)
    {
        $this ->app =$app;
        $this ->User = $app['User'];
       
    }
    public function login($request){
    
        $RequestBody = $request->getParsedBody();
     
        if( empty($RequestBody['username']) || empty($RequestBody['password'])|| empty($RequestBody['ip_address']) )
        {
            return $this->app->response->withJson('Wrong Request Body',400 );      
        }
            $username   = trim($RequestBody['username']);
            $password   = trim($RequestBody['password']);

            //sanatize username to prevent hacking
            if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]{5,32}$/', $username) && !filter_var($username, FILTER_VALIDATE_EMAIL))
            {
                $this ->User->loginFailed("invalid username");
                return $this->app->response->withJson('Username or Password are incorrect',401 );

            }

        $results = null;  
       
        if(password_verify($this->app['settings']['secret']['password'].$password, $this->User->getHashedPassword($username)))
        {
            $results = $this ->User->authenticateUser($username, $password);
        }
        

       if ($results['username']==null)
       {
           
            if($this ->User->loginFailed($username) < 6)
            {
                return $this->app->response->withJson('Username or Password are incorrect',401);
            }
            else 
            {
                $this ->User->lockAccount($username);
                return $this->app->response->withJson('Due to too many login attempts, you account has been locked. Please Contact an administrator',401 );
            }
           
       }
       else if ($results['status'] == 2)
       {
            return $this->app->response->withJson('Your account was disabled. Please Contact an administrator',401 );
       }
       else if ($results['status'] == 3)
       {
            return $this->app->response->withJson('Due to too many login attempts, you account has been locked. Please Contact an administrator',401 );
       }
              
      
        $results += ['token' => $var=$this ->User->loginSuccess($username,  $results['user_id'], $request->getParsedBody()['ip_address']) ] ;
        
        header('Content-Type: application/json');

      
      
        return $this->app->response->withJson( $results);
       
       
      
    }


    public function renewSession()
    {
              
        //if no token was set to Authorization
        if( empty($this->app->request->getHeader('Authorization')) )
        {      
            return $this->app->response->withJson('Wrong Request header',400 );
        }
       
        //if no rows were affected
        if(strpos(hex2bin($this->app->request->getHeader('Authorization')[0]), $this->app['settings']['secret']['token'])!==0  || !$this ->User->renewSession($this->app->request->getHeader('Authorization')[0]) )
        {
            $this ->User->cleanSessions();
            return $this->app->response->withJson('Invalid Token', 401);
        }
        return $this->app->response;
    }

}
?>
<?php
use \Psr\Http\Message\ServerRequestInterface  as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app -> add(function (Request $request, Response $response, callable $next){
    $uri = $request->getUri();
    $path = $uri->getPath();
    
    if($path != '/' && substr($path,-1) == '/')
    {
        $uri = $uri -> withPath(substr($path, 0, -1));

        if($request ->getMethod() == 'GET'){
            
            
            return $response -> withRedirect((string)$uri, 301);

        }
        else{
            return $next ($request ->$withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

$app->add(new RKA\Middleware\IpAddress(true,['10.0.0.1', '10.0.0.2']));

$app->add(function (Request $request, Response $response, callable $next) {

    $route = $request->getAttribute('route'); 
    $this->logger->info($request->getMethod() . ' ' . $route->getPattern(), [$route->getArguments()]);

    return $next($request, $response);
});

$app->add(function (Request $request, Response $response, callable $next)
{
    $route = $request->getAttribute('route'); 
    if(null === $route){ return $response->withStatus(404);}
    
    $group = strstr(substr($request->getUri()->getPath(),1), '/', true);

   if($group != "login")
    {   
        $response = $this['LoginController']->renewSession();
       return ($response->getStatusCode() !=200 ) ? $response : $next($request, $response);
       
    }
   
    return $next($request, $response);
});


?>
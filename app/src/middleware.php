<?php
use \Psr\Http\Message\ServerRequestInterface  as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->add(new RKA\Middleware\IpAddress(true,['10.0.0.1', '10.0.0.2']))

->add(function (Request $request, Response $response, callable $next) {
    $route = $request->getAttribute('route'); 
    $this->logger->info($request->getMethod() . ' ' . $route->getPattern(), [$route->getArguments()]);

    return $next($request, $response);
})

->add(function (Request $request, Response $response, callable $next)
{
    $route = $request->getAttribute('route'); 
    if(null === $route){ return $response->withStatus(404);}

    $group = strstr(substr($request->getUri()->getPath(),5), '/', true);

   if($group != "login")
    {   
        $response = $this['LoginController']->renewSession();
       return ($response->getStatusCode() !=200 ) ? $response : $next($request, $response);
       
    }
   
    return $next($request, $response);
});


?>
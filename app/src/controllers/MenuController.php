<?php



namespace app\src\controllers;


Class MenuController
{

    public function __construct($app)
    {
        $this ->app =$app;
        $this ->Menu = $app['Menu'];

        
       
    }

    public function getItem($request, $response, $props)
    {
        $itemid = isset($props['itemid']) ? $props['itemid'] : "%";
        $results = $this->Menu->getItem($itemid);

        return $response->withJson($results);
    }

    public function addItem($request, $response)
    {
        $requestBody = $request->getParsedBody();

        if(!isset($requestBody['title']))
        {
            return $response->withJson('Wrong request boddy - title', 400);
        }

        $typeId = isset($requestBody['typeId']) ? $requestBody['typeId']: null;

        $imageId = isset($requestBody['imageid']) ? $requestBody['imageid']: null;

        $ingredientIds = isset($requestBody['ingredients_ids']) ? $requestBody['ingredients_ids']: null;

        $price = isset($requestBody['price']) ? $requestBody['price']: null;
       
            if($price != null && !isset($requestBody['currency']))
            {
                return $response->withJson('Wrong request boddy - currency', 400);
            }
            
        $currency = isset($requestBody['currency']) ? $requestBody['currency']: null;

    
        
    }



}
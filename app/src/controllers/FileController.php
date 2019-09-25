<?php



namespace app\src\controllers;


Class FileController
{

    public function __construct($app)
    {
        $this ->app =$app;
        $this ->File = $app['File'];

    }

    public function addMenuImage($request, $response)
    {
        $requestBody = $request->getParsedBody();

        $results = $this ->File->addMenuImage($requestBody['source']);
       
        if ($results == false)
        {
            return $response->withJson("Image source already exists in the Database", 409);
        }
        return $response->withJson($results);

      
    }
    public function getMenuImage($request, $response)
    {

        $id= $request->getQueryParam('id', $default = null);
        $source =$request->getQueryParam('source', $default = null);

        if($id == null && $source ==null)
        {
            $source ="%";
        }
        
        $results = $this->File->getMenuImage($id, $source);

      
        return $response->withJson($results);
    }

    public function deleteMenuImage($request, $response, $props)
    {
        $imageId = $props['imageid'];
        
        $results = $this->File->deleteMenuImage($imageId);

        return $results == 0 ? $response->withJson("Image ID does not exist", 401) : $response->withJson($results);

    }
   



}
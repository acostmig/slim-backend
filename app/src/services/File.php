<?php



namespace app\src\services;
use PDO;
use Exception;


Class File
{
    public function __construct($app)
    {
        $this->app = $app;
    }


    public function addMenuImage($source)
    {
        $query = "INSERT INTO Menu_Item_Image values(null, :source, :title, null);
        ";
        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':source', $source , PDO::PARAM_STR);
        $sth ->bindValue(':title', basename($source) , PDO::PARAM_STR);

        try {
            
            $sth ->execute();
            
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return false;

            } else {
                throw $e;
            }
         }
       

        return $this->app->db->lastInsertId();
    }

    public function getMenuImage($id, $source)
    {
        
        $query = "SELECT * FROM Menu_Item_Image WHERE id=:id OR source like :source;
        ";
        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':id', $id , PDO::PARAM_INT);
        $sth ->bindValue(':source', $source , PDO::PARAM_STR);
        $sth ->execute();

        return $sth->fetchAll();
       
    }
    public function deleteMenuImage($imageId)
    {
        $query = "DELETE FROM Menu_Item_Image WHERE id=:imageId";

        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':imageId', $imageId , PDO::PARAM_INT);
        $sth ->execute();

       
        return  ($sth ->rowCount() ==0) ? false : true;
    }
}
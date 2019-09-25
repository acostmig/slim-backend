<?php



namespace app\src\services;
use PDO;


Class Menu
{

    public function __construct($app)
    {
        $this ->app =$app;
    }

    public function getItem($itemid)
    {
        $query="SELECT 
        Menu_Item.id, 
        Menu_Item.title,
        Menu_Item_Type.`name` as type,
        Menu_Item_Image.`source` as imagesrc,
        Menu_Item_Image.title as image_title,
        GROUP_CONCAT(DISTINCT  Ingredient.`id`) as ingredients_ids,
        GROUP_CONCAT(DISTINCT  Ingredient.`name`) as ingredients_names,
        Menu_Item.price,
        Currency.acronym as currency
        FROM Menu_Item
        LEFT JOIN Menu_Item_Image ON Menu_Item_Image.id = Menu_Item.image_id
        LEFT JOIN Menu_Item_Ingredients ON Menu_Item_Ingredients.menu_item_id = Menu_Item.id
        LEFT JOIN Menu_Item_Type on Menu_Item_Type.id = Menu_Item.type_id
        LEFT JOIN Ingredient ON Ingredient.id = Menu_Item_Ingredients.ingredient_id
        LEFT JOIN Currency ON Currency.id = Menu_Item.price_currency_id
        where Menu_Item.id LIKE :itemid
        GROUP BY Menu_Item.id;";

       
        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':itemid', $itemid , PDO::PARAM_STR);
        $sth -> execute();

        
        return $sth->fetchAll();
    }

    public function getType($typeId, $typeName)
    {
        $query = "SELECT id FROM Menu_Item_Type WHERE id = :typeId OR `name` = :typeName;";

        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':typeId', $typeId , PDO::PARAM_INT);
        $sth ->bindValue(':typeName', $typeName , PDO::PARAM_STR);
        $sth -> execute();
        $result = $sth->fetchAll();

        if(!isset($result[0]['id']))
        {
            return false;
        }
        return $result[0];
    }

    public function addType($typeName)
    {
        $query = "INSERT INTO Menu_Item_Type (`name`)
        VALUE( :typeName);
        ";
        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':typeName', $typeName , PDO::PARAM_STR);
        $sth ->execute();
        
        return $this->app->db->lastInsertId();

    }

    public function getIngredient($ingredientId, $ingredientName)
    {
        $query = "SELECT * FROM Ingredient WHERE id = :ingredientid OR `name` = :ingredientName;";
        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':ingredientid', $ingredientId , PDO::PARAM_INT);
        $sth ->bindValue(':ingredientName', $ingredientName , PDO::PARAM_STR);
        $sth -> execute();
        $result = $sth->fetchAll();

        if( !isset($result[0]['id']) )
        {
            return false;
        }

        return $result[0];
    }
    public function addIngredient($ingredientName)
    {
        $query = "INSERT INTO Ingredient (`name`)
        VALUE( :ingredientName);
        ";
        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':ingredientName', $ingredientName , PDO::PARAM_STR);
        $sth ->execute();
        
        return $this->app->db->lastInsertId();

    }



    public function addItem($request, $response)
    {
        $type_query = "INSERT INTO Menu_Item_Type (`name`)
                        SELECT :type
                        FROM Menu_Item_Type
                        WHERE NOT EXISTS(
                            SELECT `name` 
                            FROM Menu_Item_Type
                            WHERE `name` = :type);
                        SELECT id from Menu_Item_Type where `name` =:type;";

        $sth = $this->app->db->prepare($type_query);
        $sth ->bindValue(':type', $RequestBody['type'] , PDO::PARAM_STR);
        $sth -> execute();
        $results = $sth->fetchAll();
        return $this->app->response->withJson($results);

        $RequestBody['type'];
        $RequestBody['imagesrc'];
        $RequestBody['image_title']; 
        $RequestBody['price'];
        $RequestBody['currency_id'];
        $RequestBody['ingredient_names'];
        $RequestBody['ingredient_ids'];
        
    }
}

?>
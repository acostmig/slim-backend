<?php



namespace app\src\services;
use PDO;



Class User
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function authenticateUser($username){
       
        //print_r("\n\n".password_hash($this->app['settings']['secret']['password'].$password, PASSWORD_DEFAULT));
       
        $query = "SELECT DISTINCT `User`.id AS user_id, 
        username,
        `status`,
        `User`.`name`,
        email,
        /*GROUP CONCAT because a user might have multiple roles*/ 
        GROUP_CONCAT(DISTINCT  `Role`.`id`) as role_id,
        GROUP_CONCAT(DISTINCT  `Role`.`name`) as role_name
        FROM `User`
        LEFT JOIN User_role on User_role.`user_id` = `User`.id 
        LEFT JOIN `Role`on `Role`.`id` = `User_role`.role_id
        LEFT JOIN `Session` on `Session`.`user_id` = `User`.id
        WHERE username=:username";

        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':username', $username, PDO::PARAM_STR);
        $sth->execute();
        $results = $sth->fetchAll();
        return $results[0];
   
    }
    public function getHashedPassword($username)
    {
        $query = "SELECT `password` from `User` WHERE `username` = :username";
        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':username', $username, PDO::PARAM_STR);
        $sth->execute();
        
        return $sth->fetchAll()[0]['password'];
    }
    

    
    public function renewSession($token)
    {
             
      
        $query = 'UPDATE `lbweb`.`Session` SET `time_stamp` =CURRENT_TIME WHERE token=:token AND time_stamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)';
        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':token', $token, PDO::PARAM_STR);
        $sth -> execute();
        return ($sth -> rowCount()==0) ? false: true;
    }

    
    public function getUserObjectbyToken($token)
    {
       

        $query = "SELECT DISTINCT 
        `User`.id AS user_id, 
        `User`.username,
        `User`.`status`,
        `User`.`name`,
        `User`.email, 
        GROUP_CONCAT(DISTINCT  `Role`.`id`) as role_id,
        GROUP_CONCAT(DISTINCT  `Role`.`name`) as role_name
        FROM `Session`
        LEFT JOIN User_role on User_role.`user_id` = `Session`.`user_id`
        LEFT JOIN `Role`on `Role`.`id` = `User_role`.role_id
        LEFT JOIN `User` on `User`.id = `Session`.`user_id` 
        WHERE token = :token
        GROUP BY `User`.id
        ;";
        

        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':token', $token, PDO::PARAM_STR);
        $sth -> execute();
       
    
        return $sth->fetchAll();
    }
    
    public function loginSuccess($username, $userID, $ip)
    {
      
        $query=
        "INSERT INTO `lbweb`.`User_log` (`user_id`, `action_id`, `time_stamp`, `new_value`) 
                                 VALUES (:userid,       '1',     CURRENT_TIME,  :username);
        ";

        $sth = $this->app->db->prepare($query);
        $sth ->bindValue(':userid',$userID, PDO::PARAM_INT);
        $sth ->bindValue(':username',$username, PDO::PARAM_STR);
        $sth -> execute();

        return $this->createSession($username, $userID, $ip);
    }
    public function loginFailed($username)
    {
        $query=
        "INSERT INTO `lbweb`.`User_log` (`user_id`, `action_id`, `time_stamp`, `new_value`) 
                                 VALUES ('0',       '2',     CURRENT_TIME,  :username);
        ";
        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':username', $username, PDO::PARAM_STR);
        $sth -> execute();

        $queryAttempts=
        "SELECT count(*) as failedLoginAttempts
        FROM `User_log`
        WHERE `action_id` = 2
        AND  `new_value` = :username
        /*added COALESCE because it was returning null if the user never logged in before*/
        AND id > (SELECT COALESCE(MAX(id),0)  FROM `User_log` where `action_id`=1 and new_value=:username)  
        ";
        $attmepts = $this->app->db->prepare($queryAttempts);
        $attmepts ->bindParam(':username', $username, PDO::PARAM_STR);
        $attmepts -> execute();
        
         $failedLoginAttempts = $attmepts->fetchAll()[0]['failedLoginAttempts'];


        return $failedLoginAttempts;
    }
    public function lockAccount($username)
    {
        $query=
        "UPDATE `lbweb`.`User` SET `status` = '3' WHERE (`username` = :username);";
        $sth = $this->app->db->prepare($query);
        $sth ->bindParam(':username', $username, PDO::PARAM_STR);
        $sth -> execute();
        
    }

    public function createSession($username, $userID, $ip)
    {
        //deleting old tokens
        User::cleanSessions();

        //create new token
        $bytes = openssl_random_pseudo_bytes(128);
        $token = bin2hex($this->app['settings']['secret']['token']).bin2hex($bytes);
        $query = 
        "INSERT INTO `lbweb`.`Session` (`user_id`,`ip`, `token`, `time_stamp`) 
                                VALUES( :userid,  :ip,  :token,   CURRENT_TIME)"; 
        $sth = $this->app->db->prepare($query);

        $sth ->bindParam(':userid', $userID, PDO::PARAM_INT);
        //user IP address from request body
        $sth ->bindParam(':ip',$ip, PDO::PARAM_STR);
        $sth ->bindParam(':token',$token, PDO::PARAM_STR);
        
        $sth->execute();
        
        return $token;
    }

    public function cleanSessions()
    {
        $query = "DELETE FROM lbweb.Session
        WHERE time_stamp < DATE_SUB(NOW(), INTERVAL 1 HOUR);";

        $sth = $this->app->db->prepare($query);
        $sth->execute();
    }

   

}
?>
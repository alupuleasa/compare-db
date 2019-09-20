<?php


namespace CompareDB;
use PDO;
use StdClass;

class DB extends PDO{
    function __construct($DB){
        parent::__construct("mysql:host=".$DB->host.";dbname=".$DB->dbname.";", $DB->user, $DB->passw,null);
    }
    public function query($query){ //secured query with prepare and execute
        $args = func_get_args();
        array_shift($args); //first element is not an argument but the query itself, should removed

        $response = parent::prepare($query);
        $response->execute($args);
        return $response->fetchAll(PDO::FETCH_OBJ);

    }
    public function noRespQuery($query){ //secured query with prepare and execute
        $data = parent::query($query);
        return;
    }
}
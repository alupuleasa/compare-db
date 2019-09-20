<?php
namespace CompareDB;

use CompareDB\DB;
use StdClass;

class CompareDB{
    private $diffsdB1;
    private $diffsdB2;
    private $DB1Struct;
    private $DB2Struct;
    
    function __construct($DB1,$DB2){
        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);
        
        $this->DB1Struct = $this->getDatabaseStructure($DB1);
        $this->DB2Struct = $this->getDatabaseStructure($DB2);
        
        $this->diffsdB1 = $this->compareDatabases($this->DB1Struct,$this->DB2Struct);
        $this->diffsdB2 = $this->compareDatabases($this->DB2Struct,$this->DB1Struct);
    }
    public function showDiferences(){
        var_dump($this->diffsdB1);
        var_dump($this->diffsdB2);
    }
    public function showStructures(){
        dd($this->DB1Struct);
        dd($this->DB2Struct);
    }
    public function getDB1Struct(){
        return $this->DB1Struct;
    }
    public function getDB2Struct(){
        return $this->DB2Struct;
    }
    public function getDiffsdB1(){
        return $this->diffsdB1;
    }
    public function getDiffsdB2(){
        return $this->diffsdB2;
    }
    private function compareDatabases($DB1Struct,$DB2Struct){
        $colProps = array("Field","Type","Collation","Null","Key","Default","Extra","Privileges","Comment"); 
        if($DB1Struct->host == $DB2Struct->host){
            $DB1 = $DB1Struct->dbname;
            $DB2 = $DB2Struct->dbname;
        }else{
            $DB1 = $DB1Struct->host;
            $DB2 = $DB2Struct->host;
        }
        $diffsdB = array();
        foreach($DB1Struct->tables as $tableKey=>$table){
            if(!array_key_exists($tableKey,$DB2Struct->tables)){
                $type = "Unknown";
                if($table->tableType == "BASE TABLE"){
                    $type = "Table";
                }
                if($table->tableType == "VIEW"){
                    $type = "View";
                }
                $diffsdB[$tableKey] = $type." missing on ".$DB2;
            }else{
//                $diffsdB[$tableKey] = array(); - or unset at the end 
                foreach($table->columns as $columnKey => $column){
                    if(!array_key_exists($columnKey,$DB2Struct->tables[$tableKey]->columns)){
                        $diffsdB[$tableKey][$columnKey] = "Column missing on ".$DB2;
                    }else{
                        foreach($colProps as $colProp){
                            if($column->columnProp->{$colProp} != $DB2Struct->tables[$tableKey]->columns[$columnKey]->columnProp->{$colProp}){
                                $msg = $DB2."(".$colProp."=>'".$DB2Struct->tables[$tableKey]->columns[$columnKey]->columnProp->{$colProp}."')";
                                $msg .= " => ";
                                $msg .= $DB1."(".$colProp."=>'".$column->columnProp->{$colProp}."')";
                                $diffsdB[$tableKey][$columnKey][] = $msg;
                            }
                        }
                    }
                }
                if($table->tableRows != $DB2Struct->tables[$tableKey]->tableRows){
                    $diffsdB[$tableKey]["rows"] = $DB1." (".$table->tableRows.") - ".$DB2." (".$DB2Struct->tables[$tableKey]->tableRows.")";
                }
            }
        }
        $dbDiffs = new stdClass();
        $dbDiffs->dbname = $DB2Struct->dbname;;
        $dbDiffs->host = $DB2Struct->host;
        $dbDiffs->diffs = $diffsdB;
        return $dbDiffs;
    }
    private function getDatabaseTables($DB){
        $db = new DB($DB);
        $tables = $db->query("SHOW FULL TABLES"); 
        $prep_tables = array();
        foreach($tables as $item){
            $table = new stdClass();
            $table->name = $item->{'Tables_in_'.$DB->dbname};
            $table->type = $item->Table_type;
            $prep_tables[$item->{'Tables_in_'.$DB->dbname}] = $table;
        }
        return $prep_tables;
    }
    private function describeTable($table,$DB){
        $db = new DB($DB);
        $desc = $db->query("SHOW FULL COLUMNS FROM {$table}"); 
        $columns = array();
        foreach($desc as $item){
            $column = new stdClass();
            $column->columnProp = $item;
            $columns[$item->Field] = $column;
        }
        return $columns;
    }
    private function getDatabaseStructure($DB){
        $tables = $this->getDatabaseTables($DB);
        $database = new stdClass();
        $database->dbname = $DB->dbname;
        $database->host = $DB->host;
        $database->tables = array();
        $db = new DB($DB);
        foreach($tables as $k=>$table) {
            $newTable = new stdClass();
            $newTable->tableName = $k;
            $newTable->tableType = $table->type;
            $rows = $db->query("SELECT COUNT(*) as tableRows FROM {$table->name}");
            $newTable->tableRows = $rows[0]->tableRows;
            $newTable->columns = $this->describeTable($table->name,$DB);
            $database->tables[$k] = $newTable;
        }
        return $database;
    }
}
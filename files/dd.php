<?php
/**
 * Created by PhpStorm.
 * User: P3701005
 * Date: 1/8/2019
 * Time: 12:09 PM
 */

function dd($var){
    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    var_dump($var);
    die();
}
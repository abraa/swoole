<?php
 /**
 * ====================================
 * thinkphp5
 * ====================================
 * Author: 1002571
 * Date: 2018/4/13 18:32
 * ====================================
 * File: Context.php
 * ====================================
 */

namespace abraa\swoole\data;


class Context {
    static protected $pool = [];

    static function put($pid,$key,$value){
        if(!isset(static::$pool[$pid])){
            static::$pool[$pid] = [];
        }
        static::$pool[$pid][$key] = $value;
    }

    static function get($pid,$key){
        if(isset(static::$pool[$pid])){
            return isset(static::$pool[$pid][$key]) ? static::$pool[$pid][$key] : null;
        }
        return null;
    }


    static function delete($pid,$key = null)
    {
        if($key){
            unset(self::$pool[$pid][$key]);
        }else{
            unset(self::$pool[$pid]);
        }

    }
}
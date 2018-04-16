<?php
 /**
 * ====================================
 * thinkphp5
 * ====================================
 * Author: 1002571
 * Date: 2018/4/11 15:22
 * ====================================
 * File: Config.php
 * ====================================
 */

namespace abraa\swoole;


class Config {
    static $setting = [];           //配置选项
    static $params = [];

    /**
     * 获取配置信息
     * @param string $name
     * @return array
     */
    public static function getSetting($name=""){
        if(empty($name)){
            return static::$setting;
        }
        return static::$setting[$name];
    }

    /**
     * 添加配置信息
     * @param $name
     * @param string $value
     */
    public static function setSetting($name,$value =""){
        if(is_array($name)){
            static::$setting = array_merge(static::$setting,$name);
        }else{
            static::$setting[$name] = $value;
        }
    }
}
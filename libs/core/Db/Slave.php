<?php

/**
 * DB 从类
 * 主类只读，按算法分配
 *
 * @category core
 * @package Db
 * @version $Id:$
 * @author xiaodong
 */
class Db_Slave extends Db_Model
{

    protected static $_instance = null;

    public static function getInstance ($config)
    {
        if (self::$_instance == null) {
            $dbId = array_rand(array_keys($config));
            self::$_instance = new Db_Slave($config[$dbId]);
        }
        return self::$_instance;
    }
}


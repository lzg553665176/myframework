<?php

/**
 * VIPSHOP DB 主类
 * 主类可读写
 *
 * @category core
 * @package Db
 * @version $Id:$
 * @author xiaodong
 */
class Db_Master3 extends Db_Model
{

    protected static $_instance = null;

    public static function getInstance ($config)
    {
        if (self::$_instance == null) {
            self::$_instance = new Db_Master3($config);
        }
        return self::$_instance;
    }
}
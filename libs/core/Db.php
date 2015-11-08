<?php
/**
 * Db
 *
 * @category core
 * @package Db
 * @author xiaodong<4634275@qq.com>
 */
class Db
{

    public static $connections = array();

    public static $config = array();

    /**
     * db工厂方法
     *
     * @param $adapter string           
     * @return core_Db_Abstract
     */
    static public function factory ($adapter = 'master')
    {
        $adapterClassName = "Db_".ucfirst($adapter);
        if (! empty($adapter)) {
//            if (class_exists($adapterClassName)) {
//                $config = self::$config;
//                return self::getInstance($adapter, $config[$adapter]);
                //return $adapterClassName::getInstance($config[$adapter]);
//            } else {
                $config = self::$config;
                return self::getInstance($adapter, $config[$adapter]);
//            }
        } else {
            return false;
        }
    }
    
    static private function getInstance($adapter, $config) 
    {
        switch ($adapter) {
            case 'slave':
                return Db_Slave::getInstance($config);
                break;
            case 'master':
                return Db_Master::getInstance($config);
                break;
            case 'master2':
                return Db_Master2::getInstance($config);
                break;
            case 'master3':
                return Db_Master3::getInstance($config);
                break;
            default:
                return Db_Model::getInstance($config);
                break;
        }
    }

}


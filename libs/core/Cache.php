<?php

/**
 * Cache
 *
 * @category 
 * @package Cache
 * @author xiaodong
 */
class Cache
{

    public static $connections = array();

    public static $config = array();

    /**
     * cache工厂方法
     *
     * @param $adapter string           
     * @return Cache
     */
    static public function factory ($adapter = 'memcache')
    {
        $adapterClassName = 'Cache_' . ucfirst($adapter);
        if (! empty($adapter)) {
            if (class_exists($adapterClassName)) {
                $config = self::$config;
                return self::getInstance($adapter, $config[$adapter]);
                //return $adapterClassName::getInstance($config[$adapter]);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    static private function getInstance($adapter, $config)
    {
        switch ($adapter) {
            case 'memcache':
                return Cache_Memcache::getInstance($config);;
                break;
            case 'redis':
                return Cache_Redis::getInstance($config);;
                break;
    
            default:
                return false;
                break;
        }
    }

}

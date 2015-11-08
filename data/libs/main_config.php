<?php
/**
 * 主配置文件
 */

$main_config= array(
    'common' => array(
        'core' => APP_PATH . '/libs/core',
        'model' => APP_PATH . '/model',
        'controllers' => APP_PATH . '/controller',
        'timezone' => "Asia/Shanghai"
    ),
    'cache' => array(
        'memcache' => array(
            '0' => array(
                'host' => '127.0.0.1',
                'port' => '11211',
                'timeout' => '86400',
                'weight' => '10',
                'persistent' => '1',
                'retry_interval' => '15',
            ),

        ),
    ),

    //控制器默认配置
    'defualt_controller'=>'Controller',
    //控制器默认配置
    'defualt_action'=>'Action',

    'defualt_views'=>'views',
);

//需要配置则在加一条包含代码，创建一个配置文件即可
$main_config += include(ROOT_PATH.'data/libs/db_config.php');

return $main_config;
?>
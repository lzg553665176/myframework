<?php
/**
 * 数据库配置文件
 * Created by PhpStorm.
 * User: 9008389
 * Date: 2015/11/6
 * Time: 15:51
 */
return array(
    'db' => array(
        'master' => array(
            'host' => '192.168.6.222',
            'username' => 'newxianya',
            'password' => '123abc',
            'dbname' => 'cijiskin',
        ),
        'slave' => array(
            array(
                'host' => '192.168.6.222',
                'username' => 'newxianya',
                'password' => '123abc',
                'dbname' => 'usercenter',
            )
        ),
    ),
);
?>
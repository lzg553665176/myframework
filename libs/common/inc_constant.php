<?php
/**
 * Created by long.
 * 定义全局常量文件
 * User: 9008389
 * Date: 2015/11/6
 * Time: 16:11
 */

//定义根路径
defined('ROOT_PATH') || define('ROOT_PATH', str_replace("\\", '/', dirname(dirname(dirname(__FILE__)))).'/');

//定义应用路径
defined('APP_PATH') || define('APP_PATH', str_replace("\\", '/', dirname(dirname(dirname(__FILE__)))).'/');

//定义是否启用缓存
defined('ENABLE_CACHE') || define('ENABLE_CACHE', false);

//定义加密字串的salt值
defined('ENCODE_STR') || define('ENCODE_STR', '*7^^7@@!~```,;!!*&&');

?>
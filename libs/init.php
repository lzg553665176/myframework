<?php if ( ! defined('MYFRAMEWORK')) exit('No direct script access allowed');

/****************************************
 * @author long
 * @explain 系统初始化文件
 ****************************************/

//设置报错机制
// error_reporting(0);

//设置页面输出格式为utf-8
header("Content-type:text/html;charset=UTF-8");

@ini_set('session.auto_start', 0);                    //关闭session自动启动
@ini_set('session.cookie_lifetime', 0);            //设置session在浏览器关闭时失效
@ini_set('session.gc_maxlifetime', 3600);  //session在浏览器未关闭时的持续存活时间

//开启session
session_start();

//加载常量文件
include_once('common/inc_constant.php');

//加载配置
global $_CONF;
$_CONF = include(ROOT_PATH . '/data/libs/main_config.php');

//加载timezone
if (!empty($_CONF['common']['timezone'])) {
	date_default_timezone_set($_CONF['common']['timezone']);
}

//自动加载文件Autoloder
require_once $_CONF['common']['core'].'/Loder.php';
Loder::setBasePath(
    array(
        $_CONF['common']['core'],
        $_CONF['common']['model'],
        $_CONF['common']['controllers']
    )
);

//加载360安全插件
require_once $_CONF['common']['core'].'/360_safe3.php';

//初始化Db配置
Db::$config = $_CONF['db'];

Cache::$config = $_CONF['cache'];

// session_start();
// define('SESS_ID', session_id());

@ini_set('display_errors', '1');

//xss过滤
if (!empty($_GET)) {
	$_GET  = Validator::addslashesDeep($_GET);
}
if (!empty($_POST)) {
	$_POST = Validator::addslashesDeep($_POST);
}
$_COOKIE   = Validator::addslashesDeep($_COOKIE);
$_REQUEST  = Validator::addslashesDeep($_REQUEST);
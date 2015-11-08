<?php

/*
 * @author long
 * 单一入口文件
 * @date 2014-07-08 13:11pm
 * @explain
*/
define('MYFRAMEWORK', TRUE);

include_once './libs/init.php';

$_GET['c'] = isset($_GET['c']) && !empty($_GET['c']) ? trim($_GET['c']) : 'index';
$_GET['a'] = isset($_GET['a']) && !empty($_GET['a']) ? trim($_GET['a']) : 'index';

$controller = ucfirst($_GET['c']).$_CONF['defualt_controller'];
$action = strtolower($_GET['a']).$_CONF['defualt_action'];
chmod(ROOT_PATH.'controller/',0777);
if(file_exists(ROOT_PATH.'controller/'.$controller.".php"))
{
    include_once(ROOT_PATH.'controller/'.$controller.".php");

}

require(ROOT_PATH."controller/IndexContoller.php");

$methods = get_class_methods($controller);
$sign = false;
if (!empty($methods) && is_array($methods)) {
	foreach ($methods as $val) {
		if (preg_match("/^\_/", $val) == 0 && $action == $val) {
			$sign = true;
			$relAction = $val;
			break;
		}
	}
}

if ($sign) {
	$do = new $controller();
	$do->$relAction();
}else{
	exit('The analytical path error');
}
unset($methods,$do,$sign);



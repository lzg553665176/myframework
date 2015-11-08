<?php
/*
 * @author Diven(QQ:702814242)
 * @date 2014-07-09
 * @explain The main controller
 */
Class Action{
	
	function __construct()
	{
		
	}
	
	function __destruct()
	{
		
	}
	
	function __get($key)
	{
		echo 'The object properties ['.$key.'] non-existent';
	}
	
	function __set($key,$value)
	{
		
	}
	
	function __call($key,$args)
	{
		echo 'The function ['.$key.'] non-existent';
	}
	
	function __toString()
	{
		
	}
	
	function __clone()
	{
		
	}
	
	//获取$_GET值@author Diven
	protected function _get($key, $default = NULL)
	{
		return isset($_GET[$key]) ? trim($_GET[$key]) : $default;	
	}
	
	//获取$_POST值@author Diven
	protected function _post($key, $default = NULL)
	{
		return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
	}
	
	//获取页面提交过来的值$_GET,$_POST,$_COOKIE @author Diven
	protected function _param($key, $default = NULL)
	{
		return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : $default;
	}
	
	//获取$_GET全集 @author Diven
	protected function _getAll($key = NULL)
	{
		$get = array();
		if (!empty($_GET) && $key === NULL) {
			foreach ($_GET as $k => $v) {
				$get[$k] = $v; 
			}
			return $get;
		}
		
		return !empty($key) ? (isset($_GET[$key]) ? $_GET[$key] : NULL) : NULL;
	}
	
	//获取$_POST全集 @author Diven
	protected function _postAll($key = NULL)
	{
		$post = array();
		if (!empty($_POST) && $key === NULL) {
			foreach ($_POST as $k => $v) {
				$post[$k] = $v;
			}
			return $post;
		}
		
		return !empty($key) ? (isset($_POST[$key]) ? $_POST[$key] : NULL) : NULL;
	}
	
	//获取全部表单数据$_GET,$_POST @author Diven
	protected function _form()
	{
		$arr = array();
		if (!empty($_GET)) {
			foreach ($_GET as $k => $v) {
				if ($k == 'c' || $k == 'a') continue;
				$arr[$k] = trim($v);
			}
		}
		if (!empty($_POST)) {
			foreach ($_POST as $k => $v) {
				$arr[$k] = trim($v);
			}
		}
		return $arr;
	}
	
	//判断是否为Ajax请求 @author Diven
	protected function _isAjax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
	
	//返回退出json数据 @author Diven
	protected function end($error,$message,$returnData = array())
	{
		$returnData = @json_encode($returnData);
		exit('{"error": "'.$error.'","message":"'.$message.'","data":'.$returnData.'}');
	}
}
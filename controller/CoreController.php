<?php
/**
 * Created by PhpStorm.
 * User: 9008389
 * Date: 2015/11/6
 * Time: 17:43
 */

class CoreController {
    protected $layout = '';
    public $_globals = array();
    //public $_CONF;

    function __constuction()
    {

    }
    //带smarty模板引擎的视图渲染方法 @author Diven
    public function display($data = NULL, $view = NULL)
    {
        echo $this->fetch($data, $view);
        exit();
    }

    //smarty获取页面信息@author Diven
    private function fetch($data = NULL, $view = NULL)
    {
        global $_CONF;
        $view = strtolower($view);
        //if (!empty($view) && (!is_string($view) || !strpos($view,'/'))) {
        if (!empty($view) && !is_string($view) ) {
            return false;
        }

        //如果包含文件夹，则需要处理 默认templates
        if (!empty($view) && strpos($view,'/')) {
            $file = ltrim(strrchr($view, '/'), '/');
            $dir = substr($view, 0, strrpos($view, '/'));
        }
        else
        {
            $dir=$this->get_controller();
            $file=$this->get_action();
        }

        //开启layout页面布局
        if (!empty($this->layout)) {
            $data['CONENTFILE'] = APP_PATH.$_CONF['defualt_views'].'/'.$dir.'/'.$file.'.html';
            $dir = 'layouts';
            $file = $this->layout;
        }
        //把controller和action首字母大写后传给模板文件
        //$data['CONTROLLER'] = ucfirst($this->get_controller());
       // $data['ACTION'] = ucfirst($this->get_action());

        //判断视图文件是否存在
        $viewFileType = $this->__viewExists($dir.'/'.$file);
        if (empty($viewFileType)) {
            if ($dir == 'layouts') {
                exit('Cannot find the layout');
            }else{
                exit('Cannot find the view');
            }
        }

        include_once(APP_PATH.'/libs/smarty/Smarty.class.php');
        $smarty = new Smarty();
        $smarty->caching = false;
        $smarty->template_dir = APP_PATH.$_CONF['defualt_views'].'/'.$dir; //模板存放目录
        $smarty->compile_dir = APP_PATH."/templates"; //编译目录
        $smarty->left_delimiter = "{"; //左定界符
        $smarty->right_delimiter = "}"; //右定界符
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $val) {
                $smarty->assign($key,$val);
            }
        }

        if (is_array($this->_globals) && !empty($this->_globals)) {
            $smarty->assign('GLOBALS',$this->_globals);
        }

        return $smarty->fetch($file.'.'.$viewFileType);
    }

    //判断视图文件是否存在  @author Diven
    private function __viewExists($str)
    {
        global $_CONF;
        if (!is_string($str) || !strpos($str,'/')) {
            return false;
        }
        if (file_exists(APP_PATH.$_CONF['defualt_views'].'/'.$str.'.html')){
            return 'html';
        }else if (file_exists(APP_PATH.$_CONF['defualt_views'].'/'.$str.'.tpl')){
            return 'tpl';
        }else if (file_exists(APP_PATH.$_CONF['defualt_views'].'/'.$str.'.php')){
            return 'php';
        }
        return false;
    }

    private function get_controller()
    {
        if(trim($_GET['c']))
            return trim($_GET['c']);
        else
            return 'index';
    }

    private function get_action()
    {
        if(trim($_GET['a']))
            return trim($_GET['a']);
        else
            return 'index';
    }

}
<?php
/**
 * 首页控制器文件
 */

class IndexController extends Controller
{

    function indexAction()
    {
        echo "首页控制器index方法";

        $this->display('',"lzg/index");
    }

    function lzgAction()
    {
        echo "首页控制器lzg方法";
        $this->display('','public/index');
    }
}
?>
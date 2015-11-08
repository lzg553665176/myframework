<?php
/**
 * Created by PhpStorm.
 * 定义核心加载方法文件
 * User: 9008389
 * Date: 2015/11/6
 * Time: 16:14
 */

/**
 * @param $path
 * 将路径下所有文件加载进来
 */
function include_all_files($path)
{
    if (is_dir($path)) {
        if ($dh = opendir($path)) {

            while (($file = readdir($dh)) !== false) {
                if ($file!="." && $file!=".." && end(explode('.', $file))=='.php' ) {
                    include_once($path."/".$file);
                }
            }
            closedir($dh);
        }
    }
    return;
}
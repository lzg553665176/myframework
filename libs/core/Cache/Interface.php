<?php
/**
 * 缓存接口
 * 
 * @category   core
 * @package    Cache
 * @author xiaodong
 */
interface Cache_Interface
{
    
    /**
     * 设置缓存
     * 
     * @param string $key 关键字
     * @param mix $val 内容
     * @param int $expire 有效期
     */
    public function set($key, $val, $expire);
    
    /**
     * 取得缓存内容
     * 
     * @param string $key
     */
    public function get($key);
    
    /**
     * 删除缓存
     * 
     * @param unknown_type $key
     */
    public function delete($key);



}
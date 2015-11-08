<?php
/**
 *  加密型 Cookie,会增加COOKIE传输开销,可存数组，适合大内容
 *
 * @category core
 * @package Cookie
 * @author xiaodong<4634275@qq.com>
 */
class Cookie
{

    private static $_config = array(
        'key' => COOKIEKEY, 
        'expire' => 0, 
        'lifetime' => 0, 
        'path' => "/", 
        'domain' => "", 
        'secure' => FALSE, 
        'httponly' => FALSE
    );

    /**
     * 取得cookie
     *
     * @param $name string
     *            of cookie
     * @param $config array
     *            settings
     * @return mixed
     */
    public static function get ($name, $config = array())
    {
        // Use default config settings if needed
        $config = $config + self::$_config;
        
        if (isset($_COOKIE[$name])) {
            // Decrypt cookie using cookie key
            if ($v = json_decode(Cookie_Crypt::decrypt(base64_decode($_COOKIE[$name]), $config['key']))) {
                // Has the cookie expired?
                if (empty($config['lifetime']) || $v[0] + intval($config['lifetime']) > time()) {
                    return is_scalar($v[1]) ? $v[1] : (array) $v[1];
                }
            }
        }
        
        return FALSE;
    }

    /**
     * 设置cookie
     *
     * @param $key string
     *            cookie name
     * @param $value mixed
     *            to save
     * @param $config array
     *            settings
     *            return boolean
     */
    public static function set ($name, $value, $config = NULL)
    {
        // Use default config settings if needed
        $config = $config + self::$_config;
        
        if (empty($config['expire']) && $config['lifetime'])
            $config['expire'] = time() + intval($config['lifetime']);
            
            // If the cookie is being removed we want it left blank
            $value = $value ? 
            base64_encode(Cookie_Crypt::encrypt(json_encode(array(time(), $value)), $config['key'])) : 
            '';
        
        // Save cookie to user agent
        return setcookie(
            $name, 
            $value, 
            $config['expire'], 
            $config['path'], 
            $config['domain'], 
            $config['secure'], 
            $config['httponly']
        );
    }
}

<?php
/**
 * 
 * 验证类
 *
 * @author xiaodong
 */
class Validator
{
    /**
     * 验证json回调函数名
     * @param $callback
     * @
     */
    public static function checkCallback($callback)
    {
        if(empty($callback))
        {
            return false;
        }
        if(preg_match("/^[a-zA-Z_][a-zA-Z0-9_\.]*$/", $callback))
        {
            return true;
        }
        return false;
    }
	
	private static function checkUriXSS($string) {
		$arr = array('<script','&lt;script','%3C/script','alert','<','>');
		if($string)
		{
			foreach($arr as $v)
			{
				if(stripos($string,$v))
					return true;
			}
			return false;
		}
		else
			return false;
	}
	
	private static function removeXSS($val) {
	    $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
	    $search = 'abcdefghijklmnopqrstuvwxyz';
	    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $search .= '1234567890!@#$%^&*()';
	    $search .= '~`";:?+/={}[]-_|\'\\';
	    for ($i = 0; $i < strlen($search); $i++) 
		{
	        $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	    }
	
		  // now the only remaining whitespace attacks are \t, \n, and \r
		  $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer'
		  ,'layer', 'bgsound', 'title', 'base','confirm','msgbox','function');
		  $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus'
		  , 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect'
		  , 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave'
		  , 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress'
		  , 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover'
		  , 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend'
		  , 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit'
		  , 'onunload','alert');
		  $ra = array_merge($ra1, $ra2);
	
		$found = true; // keep replacing as long as the previous round replaced something
		$ra_len = sizeof($ra);
		while ($found == true) 
		{
			$val_before = $val;
			for ($i = 0; $i < $ra_len; $i++) 
			{
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) 
				{
					if ($j > 0) 
					{
						$pattern .= '(';
						$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
						$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
						$pattern .= ')?';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) 
				{
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return addslashes($val); //addslashes add by diven 
	}
	
	public static function addslashesDeep($value) {
		if (empty($value)) {
			return $value;
		} else {
			if (is_array($value))
				return array_map('self::addslashesDeep', $value);
			else
				return (self::checkUriXSS($value)) ? self::removeXSS($value):addslashes($value);
	    }
	}
}
?>

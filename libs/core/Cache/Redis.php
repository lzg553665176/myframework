<?php
/**
 * Cache_Redis 
 * @category core
 * @package Cache_Redis
 * @author xiaodong
 */

class Cache_Redis implements Cache_Interface {
	
	private $_connections = false;
	
	private $_config = null;
	private static $_instance = null;
	
	public function __construct($config) {
		if (!is_array($config) || empty($config)) {
			exit("redis config not set properly");
		}
		
		$this->_config = $config;
	}
    
    public static function getInstance($config) {
		if (self::$_instance == null) {
			self::$_instance = new Cache_Redis($config);
		}
		return self::$_instance;
	}
	
	public function __destruct() {
		if (!empty($this->_connections)) {
			$this->_connections = false;
		}
	}
	
    /**
     * 链接redis
     */
	private function _connect() {
		if (empty($this->_connections)) {
			if (!class_exists('Redis')) {
				exit("redis is undefined");
			}
			
			$redis = new Redis();
			$result = $redis->connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
            if($result){
                if(!empty($this->_config['password'])){
                    $redis->auth($this->_config['password']);
                }
                $this->_connections = $redis;
            }else{
                $this->_connections = false;
            }
		}
		return $this->_connections;
	}
	
	/**
	 * 判断是否已连接
	 * @return boolean 已连接返回true，未连接返回false
	 */
	public function isConnected() {
		return (false === $this->_connections)? false : true;
	}
	
	/**
	 * 重新连接
	 * @return boolean 成功返回true，失败返回false
	 */
	public function reConnect() {
		return (false === $this->_connect())? false : true;
	}
	
	public function get($key) {
		return json_decode($this->_connect()->get($key), true);
	}
	/**
	 * 设置一个key对应的值
	 * 
	 * @param string $key 键名
	 * @param string $value 值
	 * @param int $expire 过期时间
	 * 
	 * @return boolean true成功,false失败
	 */
	public function set($key, $value, $expire = null) {
		$return = $this->_connect()->set($key, json_encode($value));
		if (false != $return) {
			$expire = (isset($expire))? intval($expire) : $this->_config['expire'];
			$this->_connections->setTimeout($key, $expire);
		}
		return $return;
	}
    
     /**
	 * 设置多个key对应的值
	 * @param array $data 键值对
	 * @return boolean true成功,false失败
	 */
	public function mSet($data = array()) {
		if (empty($data)||!is_array($data)) return false;
        $data = array_map('json_encode',$data);
		return $this->_connect()->mset($data);
	}
    
    /**
	 * 删除一个key
	 *
	 * @param string $key
	 * @return int 成功删除的数目
	 */
    public function delete($key){
        return $this->_connect()->delete($key);
    }
   
    /**
	 * 删除多个key
	 * @param array $keys
	 * @return int 成功删除的数目
	 */
	public function mDelete($keys) {
		return $this->_connect()->delete($keys);
	}
    
    /**
	 * 设置自增ID
	 *
	 * @param string $key
	 * @param int $increment 每次自增量，默认为每次自增1，大于1有效
	 *
	 * @return int 自增新值
	 */
	public function incrId($key, $increment = null) {
		if (true === empty($increment)) {
			return $this->_connect()->incr($key);
		} else {
			return $this->_connect()->incrBy($key, $increment);
		}
	}

	/**
	 * 设置自减ID
	 * @param string $key
	 * @param int $decrement 每次自减量，默认为每次自减1，大于1有效
	 * @return int 自减新值
	 */
	public function decrId( $key, $decrement = null) {
		if (true === empty($decrement)) {
			return $this->_connect()->decr($key);
		} else {
			return $this->_connect()->decrBy($key, $decrement);
		}
	}
	
    /**
	 * 检测给定的key是否存在
	 * @param string $keyword
	 * @return boolean true成功,false失败
	 */
	public function keyExists($key) {
		return $this->_connect()->exists($key);
	}
	
    /**
	 * 返回给定key的数据类型
	 * @param array $keys
	 * @return int none(key不存在) int(0)
                    string(字符串) int(1)
                    list(列表) int(3)
                    set(集合) int(2)
                    zset(有序集) int(4)
                    hash(哈希表) int(5)
	 */
	public function type($key) {
		return $this->_connect()->type($key);
	}
	
	/**
	 * 获取key名称，支持通配符?*与选择[char]
	 * @param array $keys
	 * @return array
	 */
	public function getKeys( $pattern = '*') {
		return $this->_connect()->getKeys($pattern);
	}
    
    /**
	 * 设定key过期时间
	 * 
	 * @param string $key
	 * @param int $expire
	 * 
	 * @return boolean true成功,false失败
	 */
	public function setTimeout( $key, $expire) {
		if (!is_int($expire)) return false;
		return $this->_connect()->setTimeout($key, $expire);
	}
    /////////////////////////// 队列部分  //////////////////////////////
	/**
	 * 队列头压栈
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return mixed 成功返回新队列长度，失败返回false
	 */
	public function lPush( $key, $value) {
		return $this->_connect()->lpush($key, json_encode($value));
	}
	
	/**
	 * 队列尾压栈
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return mixed 成功返回新队列长度，失败返回false
	 */
	public function rPush( $key, $value) {
		return $this->_connect()->rpush($key, json_encode($value));
	}
	
	/**
	 * 对已存在的key队列头压栈
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return mixed 成功返回新队列长度，失败返回false
	 */
	public function lPushX($key, $value) {
		return $this->_connect()->lPushx($key, json_encode($value));
	}
	
	/**
	 * 对已存在的key队列尾压栈
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return mixed 成功返回新队列长度，失败返回false
	 */
	public function rPushX($key, $value) {
		return $this->_connect()->rPushx($key, json_encode($value));
	}
	
	/**
	 * 队列头出栈
	 * 
	 * @param string $key
	 * 
	 * @return mixed 成功返回出栈元素，失败返回false
	 */
	public function lPop($key) {
		return $this->_connect()->lPop($key);
	}
	
	/**
	 * 队列尾出栈
	 * 
	 * @param string $key
	 * 
	 * @return mixed 成功返回出栈元素，失败返回false
	 */
	public function rPop( $key) {
		return $this->_connect()->rPop($key);
	}
	
	/**
	 * 获取队列长度
	 * 
	 * @param string $key
	 * 
	 * @return mixed 成功返回新队列长度，失败返回false
	 */
	public function lSize( $key) {
		return $this->_connect()->lSize($key);
	}
	
	/**
	 * 获取单个队列元素
	 * 
	 * @param string $key
	 * @param int $index
	 * 
	 * @return mixed 成功返回元素值，失败返回false
	 */
	public function lGet($key, $index) {
		return $this->_connect()->lGet($key, $index);
	}
	
	/**
	 * 更新单个队列元素
	 * 
	 * @param string $key
	 * @param int $index
	 * 
	 * @return mixed 成功返回元素值，失败返回false
	 */
	public function lSet($key, $index) {
		return $this->_connect()->lSet($key, $index);
	}
	
	/**
	 * 获取队列元素列表
	 * 
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 * 
	 * @return array 元素列表
	 */
	public function lGetRange($key, $start, $end) {
		return $this->_connect()->lGetRange($key, $start, $end);
	}
	
	/**
	 * 截取队列
	 * 
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 * 
	 * @return mixed 成功返回新元素列表，失败返回false
	 */
	public function lTrim( $key, $start, $end) {
		return $this->_connect()->lTrim($key, $start, $end);
	}
	/**
     *  将值value插入到列表key当中，位于值pivot之前或之后。
        当pivot不存在于列表key时，不执行任何操作。
        当key不存在时，key被视为空列表，不执行任何操作。
        如果key不是列表类型，返回一个错误。
     * @param $key 队列名
     * @param  $position  BEFORE|AFTER
     * @param  $pivot  位置
     * @param  $value  值
     * @return 如果命令执行成功，返回插入操作完成之后，列表的长度。
                如果没有找到pivot，返回-1。
                如果key不存在或为空列表，返回0。
     */
	public function lInsert($key, $position, $pivot, $value) {
		return $this->_connect()->lInsert($key, $position, $pivot, $value);
	}
    /**
     * 根据参数count的值，移除列表中与参数value相等的元素。
        count的值可以是以下几种：
        count > 0: 从表头开始向表尾搜索，移除与value相等的元素，数量为count。
        count < 0: 从表尾开始向表头搜索，移除与value相等的元素，数量为count的绝对值。
        count = 0: 移除表中所有与value相等的值。
     * @param $key 队列名
     * @param  $value  BEFORE|AFTER
     * @param  $count  位置
     * @return 被移除元素的数量。
                因为不存在的key被视作空表(empty list)，所以当key不存在时，LREM命令总是返回0。
     * 
     */
	public function lRem($key, $value, $count) {
		return $this->_connect()->lRem($key, $value, $count);
	}
}
<?php
/**
 * Cache_Memcache 
 *
 * @category core
 * @package Cache_Memcache
 * @author xiaodong
 */
class Cache_Memcache implements Cache_Interface {
	private static $_instance = null;
	
	private $_memCache = null;
	
	private $_compression = false;
	
	private $_compressMinSize = 20000;
	
	private $_compressLevel = 0.2;
	
	const persistent = true;
	
	const weight = 10;
	
	const timeout = 1;
	
	const retryInterval = 15;
	
	private function __construct($configServerArray) {
		if (!is_array($configServerArray) || empty($configServerArray)) {
//			throw new Exception_ResourceException(2000);
            exit("memcache config not set properly");
		}
		
		foreach ( $configServerArray as $val )  {
			$this->addServer($val);
		}
	}
	
	public static function getInstance($config) {
		if (self::$_instance == null) {
			self::$_instance = new Cache_Memcache($config);
		}
		return self::$_instance;
	}
	
	protected function _Connect() {
		if (!$this->_memCache) {
			$this->_memCache = new Memcache();
		}
		if ($this->_compression) {
			$this->_memCache->setCompressThreshold($this->_compressMinSize, $this->_compressLevel);
		}
	}
	
	public function addServer($server) {
		if (!is_array($server)) {
//			throw new Exception_ResourceException(2001);
            exit("memcache server not set properly");
		}
		$persistent = isset($server['persistent']) && $server['persistent'] ? $server['persistent'] : self::persistent;
		$timeout = isset($server['timeout']) && $server['timeout'] ? $server['timeout'] : self::timeout;
		$retryInterval = isset($server['retry_interval']) && $server['retry_interval'] ? $server['retry_interval'] : self::retryInterval;
		$this->_Connect();
		$this->_memCache->addServer($server['host'], $server['port'], $persistent, $server['weight'], $timeout, $retryInterval);
	}
	
	public function get($key) {
		$this->_Connect();
		return json_decode($this->_memCache->get($key), true);
	}
	
	public function set($key, $val, $expire) {
		$this->_Connect();
		return $this->_memCache->set($key, json_encode($val), false, intval($expire));
	}
	
	public function delete($key) {
		$this->_Connect();
		return $this->_memCache->delete($key);
	}
	
	public function Status() {
		$this->_Connect();
		return $this->_memCache->getExtendedStats();
	}
	
	public function __destruct() {
		if ($this->_memCache) {
			$this->_memCache->close();
		}
	}
}

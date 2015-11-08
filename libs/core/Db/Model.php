<?php
/**
 * MYSQL class
 * @author:  xiaodong
 * @version: v1.0
 * ---------------------------------------------
 * $Date: 2014-05-21
 * $Id: Db_Model.php
*/

class Db_Model
{
	protected $_db_id;
	protected $_transOff = 0;
	protected $_transCnt = 0;
	protected $_transOK = null;
	protected $_prefix = '';
    protected $_dbname='';
    protected $_insertId;
    protected $_queryTimes;
    protected $_config = array();
    protected $_charset = 'utf8';
    protected static $_instance = null;
	
	function Db_Model($config)
	{
        @$this->_db_id = mysql_connect($config['host'], $config['username'], $config['password'],TRUE);
        if(!empty($config['prefix'])){
            $this->_prefix = $config['prefix'];
        }
//         var_dump($this->_db_id);
		if ($this->_db_id)
		{
            if($config['dbname']){
                $this->_dbname = $config['dbname']; 
            }
            @mysql_set_charset($this->_charset, $this->_db_id);
		}
		else
		{
			$this->__dbMessage('不能连接服务器');
		}
	}
    /*
     * 获取 数据库资源
     */
    public static function getInstance ($config)
    {
       $key = md5(serialize($config));

       if (@self::$_instance[$key] == null) {
            self::$_instance[$key] = new Db_Model($config);
       }
        return self::$_instance[$key];
    }
	
	private function __dbMessage($msg)
	{
		die($msg);
	}
	/**
     * 普通查询
     * 不支持子查询，UNION查询，复杂查询使用方法complexQuery
     * update，insert 使用execute 方法
     */
	function query($sql)
	{
        $checkType = $this->__checkSql($sql);
        if($checkType !="select"){
            $this->__dbMessage("只执行普通查询！");
        }
		return $this->__doQuery($sql);
	}
    /**
     * 子查询
     * 支持子查询，普通查询使用方法query
     */
    function subQuery($sql)
    {
        $checkType = $this->__checkSql($sql);
        if($checkType !="subselect"){
            $this->__dbMessage("只执行子查询!");
        }
        return $this->__doQuery($sql);
    }
    
    /**
     *执行写操作
     * update insert 使用这个方法 
     **/
	function execute($sql)	
	{
        $checkType = $this->__checkSql($sql);
        if($checkType !="delete" && $checkType !="insert" &&$checkType !="insert_select" && $checkType !="update" && $checkType !='replace'){
            $this->__dbMessage("只执行写操作！");
        }
		return $this->__doQuery($sql);
	}
    
    /**
     * 检查sql注入信息
     */
    private function __sqlInjectionCheck($sql)
    {
    	$sql = strtolower($sql);    //转换小写
    	$key = '';
    	if(strpos($sql, 'load_file')){
    		$key = 'load_file';
    	}elseif(strpos($sql, 'into') && strpos($sql, 'outfile')){
    		$key = 'into|outfile';
    	}elseif(strpos($sql, 'union') && strpos($sql, 'database()')){
    		$key = 'union|database()';
    	}elseif(strpos($sql, 'union') && strpos($sql, 'select') !== false){
    		$key = 'union|select';
    	}elseif(strpos($sql, 'update ') === 0 && strpos($sql, 'select ') !== false){
    		$key = 'update|select';
    	}elseif(strpos($sql, 'delete ') !== false && strpos($sql, 'select ') !== false){
    		$key = 'delete|select';
    	}
    	if ($key !== '') {
    		$this->__dbMessage("检测到SQL注入（{$key}），你的行为已经被系统记录!");
    	}
    }
    /**
     * 检查sql语句类型，区分开curd
     */
    private  function __checkSql($sql)
    {
        $sql = strtolower($sql);    //转换小写
        $sql = trim($sql);
        $this->__sqlInjectionCheck($sql);
        if(strpos($sql, 'delete ') !== false){
            return 'delete';
        }elseif(strpos($sql, 'insert ') !== false){
            return 'insert';
        }elseif(strpos($sql, 'replace ') !== false){
            return 'replace';
        }elseif(strpos($sql, 'insert ') !== false && strpos($sql, 'select ') !== false){
            return 'insert_select';
        }elseif(strpos($sql, 'update ') === 0 && strpos($sql, 'set ') !== false){
            return 'update';
        }elseif(strpos($sql, 'select ') === 0 && strpos($sql, 'select ') != strrpos($sql,'select ')){ //出现多个select
            return 'subselect';
        }elseif(strpos($sql, 'select ')==strrpos($sql,'select ')){
            return 'select';
        }
    }
    
    /**
     * 执行sql 语句
     */
    private function __doQuery($sql)
    {
        $rs = mysql_query($sql, $this->_db_id);
		if ($rs)
			return new rs_mysql($rs);
		else{
			$this->__dbMessage(mysql_error($this->_db_id).";".mysql_errno($this->_db_id));
		}
			
		return false;
    }
	
	function selectLimit($sql, $num, $start = 0)
    {
        if ($start)
        {
            $sql .= ' limit ' . $start . ', ' . $num;
        }
        else
        {
            $sql .= ' limit ' . $num;
        }

        return $this->query($sql);
    }
	
	function getOne($sql)
	{
		$rs = $this->query($sql);
		if (!$rs)
			return false;
		$row = $rs->fetchNumRow();
		return $row[0];
	}
	
	function getRow($sql)
	{
		$rs = $this->query($sql);
		if (!$rs)
			return false;
		$row = $rs->fetchAssoc();
		return $row;
	}
	
	function getAll($sql)
	{
		$rs = $this->query($sql);
		if (!$rs)
			return false;
		$data = array();
		while ($row = $rs->fetchAssoc())
		{
			$data[] = $row;
		}
		return $data;
	}
    
    function getCol($sql)
	{
		$rs = $this->query($sql);
		if (!$rs)
			return false;
		$data = array();
		while ($row = $rs->fetchNumRow())
		{
			$data[] = $row[0];
		}
		return $data;
	}
    
    function getTable($table)
    {
        if(!strpos($table,'.')){ //未拼接库名
            if($this->_dbname){
                return $this->_dbname.".".$this->_prefix.$table;
            }else{
                $this->__dbMessage('未选择数据库，不能连接服务器');
            }
        }else{
            return $table;
        }
    }
	
	function insert($data, $table)	//execute the insert command
	{
        
		if (@!is_array($data))
			return false;
        $table = $this->getTable($table);
		foreach ($data as $k=>$v)
		{
			$arr_k[] = '`' . $k . '`';
			if (is_string($v))
				$arr_v[] = "'{$v}'";
			else
				$arr_v[] = $v;
		}
		$sql = "insert into $table (" . implode(",", $arr_k) . ") values (" . implode(",", $arr_v) . ")";
        $checkType = $this->__checkSql($sql);
        if($checkType =='insert'){
            return $this->__doQuery($sql);
        }else{
            $this->__dbMessage("只可以运行插入操作！");
        }
	}
	
    /**************************************************************
     *@explain 批量插入数据
     *@author Diven
     *@date 2014-07-17 10:55am
     *@param $data = array('field_1'=>array('value_1','value_2',...),'field_2'=>array('value_1','value_2',...)
     *@return int || boolbean
     ***************************************************************/
	function insertBatch($data, $table)
	{
		if (empty($data) || !is_array($data)) return false;
		$table = $this->getTable($table);
		$keyArr = array_keys($data);
		$valArr = array_values($data);
		if (empty($keyArr) || empty($valArr)) return false;
		 
		foreach ($keyArr as $val){
			$insertKey[] = '`'.$val.'`';
		}
		$insertKeySql = " (".implode(',', $insertKey).") ";
		$insertVal = array();
		foreach ($valArr as $key => $value){
			foreach ($value as $k => $v){
				$insertVal[$k][$key] = "'".$v."'";
			}
		}
		$insertArr = array();
		foreach ($insertVal as $val){
			if (!empty($val) && is_array($val)) {
				$insertArr[] = " (".implode(',', $val).") ";
			}
		}
		if (!empty($insertArr) && is_array($insertArr)) {
			$sql = "INSERT INTO {$table} {$insertKeySql} VALUES ".implode(',', $insertArr);
			return $this->__doQuery($sql);
		}
		
		return false;
	}
	
	/**
	 * 一个插入出现错误时返回mysql错误编码而不exit的方法（eg：订单号冲突时有用）
	 * @author Diven
	 * @param array $data 要插入是数据键值对
	 * @param string $table 表名
	 * @return boolean|number
	 */
	function insertReturnErrno($data, $table)
	{
		if (empty($data) || !is_array($data)) return false;
		
		$table = $this->getTable($table);
		$keyArr = $valArr = array();
		foreach ($data as $k => $v) {
			$keyArr[] = '`'.$k.'`';
			$valArr[] = "'".$v."'";
		}
		$sql = "INSERT INTO $table (" . implode(",", $keyArr) . ") 
				VALUES (" . implode(",", $valArr) . ")";
		
		$checkType = $this->__checkSql($sql);
		if($checkType != 'insert'){
			$this->__dbMessage("只可以运行插入操作！");
		}
		
		mysql_query($sql, $this->_db_id);
		return mysql_errno($this->_db_id);
	}
	
	/**
	 *@根据条件组装成sql 查询 返回带分页参数的数组 本函数设计较为宽松 适合做连表查询
	 *@author Diven
	 *@param $param->field string 查询字段
	 *		 $param->where string 查询条件（where条件字符串）
	 *		 $param->order string （order by 条件字符串）
	 *		 $param->from string （from条件字符串）
	 *		 $param->rows numeric 每次查询条数
	 *		 $param->page numeric 查询页数
	 *@param string $table 数据表名 在没有$param->from的时候才需要
	 *@return Array
	 */
	public function searchPage($param = NULL, $table = '')
	{
		
		$tableName = $this->getTable($table);
		$rows = isset($param->rows) && intval($param->rows)>0 ? intval($param->rows) : 20;
		$page = isset($param->page) && intval($param->page)>0 ? intval($param->page) : 1;
		$whereSql = isset($param->where) ? $param->where : " ";
		$orderSql = isset($param->order) ? $param->order : " ";
		$fromSql = isset($param->from) ? $param->from : " FROM ".$tableName;
		$countSql = "SELECT COUNT(*) AS num {$fromSql} {$whereSql} {$orderSql} ";
		$field = isset($param->field) ? $param->field : '*';
		
		$rowCount = $this->getOne($countSql);
		$pageCount = ceil($rowCount/$rows);
		$curPage = $page>$pageCount && $pageCount>0 ? $pageCount : ($pageCount == 0 ? 1 : $page);
		$offset = ($curPage - 1)*$rows;
		
		$limtSql = " LIMIT {$offset},{$rows} ";
		$sql = "SELECT {$field} {$fromSql} {$whereSql} {$orderSql} {$limtSql} ";
		
		$res = $this->getAll($sql);	
		 
		$returnData = array();
		$returnData['list'] = array();	//数据内容
		if (is_array($res) && !empty($res)) {
			foreach ($res as $v) {
				$returnData['list'][] = $v;
			}
		}
		 
		$returnData['pageObj'] = array(	//分页栏内容
				'page'=>$page,	//请求页码
				'curPage'=>$curPage, //真实查询页码
				'dataCount'=>$rowCount, //数据总条数
				'rows'=>$rows ,	//分页条数
				'pageCount'=>$pageCount //分页数
		);
		 
		return $returnData;
	}
	
	function update($data, $table, $wq='')
	{
		if (@!is_array($data))
			return false;
        $table = $this->getTable($table);
		foreach ($data as $k=>$v)
		{
			if (is_string($v))
				$v = "'{$v}'";
			$arr[] = '`' . $k . '`' . '=' . $v;
		}

		$sql = "update $table  set " . implode(",", $arr);

		if ($wq) {
			$sql .= " where $wq";
		}
		
        $checkType = $this->__checkSql($sql);
        if($checkType =='update'){
            return $this->__doQuery($sql);
        }else{
            $this->__dbMessage("只可以运行更新操作！");
        }
	}

	function StartTrans() {
		if ($this->_transOff > 0) {
			$this->_transOff += 1;
			return;
		}
		
		$this->_transOK = true;
		
		$this->_BeginTrans();
		$this->_transOff = 1;
	}

	function _BeginTrans() {
		if ($this->_transOff) return true;
		$this->_transCnt += 1;

		$this->execute('SET AUTOCOMMIT=0');
		$this->execute('BEGIN');
		return true;
	}

	function FailTrans()
	{

		$this->_transOK = false;
	}

	function _RollbackTrans() {
		if ($this->_transOff) return true;
		if ($this->_transCnt) $this->_transCnt -= 1;
		$this->execute('ROLLBACK');
		$this->execute('SET AUTOCOMMIT=1');
		return true;
	}

	function CompleteTrans($autoComplete = true)
	{
		if ($this->_transOff > 1) {
			$this->_transOff -= 1;
			return true;
		}
		
		$this->_transOff = 0;
		if ($this->_transOK && $autoComplete) {
			if (!$this->_CommitTrans()) {
				$this->_transOK = false;			}
		} else {
			$this->_transOK = false;
			$this->_RollbackTrans();
		}
		
		return $this->_transOK;
	}

	function _CommitTrans($ok=true) {
		if ($this->_transOff) return true; 
		if (!$ok) return $this->FailTrans();
		if ($this->_transCnt) $this->_transCnt -= 1;

		$this->execute('COMMIT');
		$this->execute('SET AUTOCOMMIT=1');
		return true;
	}


		
	function affectedRows()
	{
		return mysql_affected_rows($this->_db_id);
	}
	
	function insertId()
	{
		return mysql_insert_id($this->_db_id);
	}
	
	function errorMsg()
	{
		return mysql_error($this->_db_id);
	}
	
	function close()
	{
		return mysql_close($this->_db_id);
	}
}

class rs_mysql
{
	var $res;
	function rs_mysql($rs)
	{
		$this->res = $rs;
	}
	
	function fetchNumRow()
	{
		return mysql_fetch_row($this->res);
	}
	
	function fetchRow()
	{
		return mysql_fetch_assoc($this->res);
	}
	
	function fetchAssoc()
	{
		return mysql_fetch_assoc($this->res);
	}
	
	function fetchArray()
	{
		return mysql_fetch_array($this->res);
	}
	
	function numRows()
	{
		return mysql_num_rows($this->res);
	}
	
	function recordCount() {
		return mysql_num_rows($this->res);
	}
	
	function close()
	{
		return mysql_free_result($this->res);
	}
}


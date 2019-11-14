<?php
class DB {
	/**#@+
	 * @access private
	 */
	var $connection;
	var $queries = array();
	var $persistent;
	/**#@-*/
	/**
	 * Make a connection to the MySQL server
	 *
	 * @param array $config Database configuration
	 */
	function connect($config) {
		$this->persistent = (bool) $config['persistent'];
		//
		// Connect to server
		//
		if ( $this->persistent )
			$this->connection = @mysql_pconnect($config['server'], $config['username'], $config['passwd']) or trigger_error('SQL: '.mysql_error(), E_USER_ERROR);
		else
			$this->connection = @mysql_connect($config['server'], $config['username'], $config['passwd'], true) or die('<h1>数据库连接出错</h1>');
		@mysql_query("set names utf8");
		$this->selectDb($config['dbname']);
	}
	// 选择使用的数据库，通用性更大 2013-05-30 19:44:51
	function selectDb( $db ){
		@mysql_select_db($db, $this->connection) or trigger_error('SQL: '.mysql_error($this->connection), E_USER_ERROR);
	}
	/**
	 * Execute database queries
	 *
	 * @param string $query SQL query
	 * @param bool $return_error Return error instead of giving general error
	 * @returns mixed SQL result resource or SQL error (only when $return_error is true)
	 */
	function query($query, $return_error=false, $log=true) {
		if ( $log )
			$this->queries[] = preg_replace('#\s+#', ' ', $query);
		$result = @mysql_query($query, $this->connection) or $error = mysql_error($this->connection);
		if ( isset($error) ) {
			if ( $return_error ) 
				return $error;
			else
				trigger_error($query.'<br>SQL: '.$error, E_USER_ERROR);
		}
		return $result;
	}
	/**
	 * Fetch query results
	 *
	 * @param resource $result SQL query resource
	 * @returns array Array containing one result
	 */
	function fetch_array(&$result) {
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
	function r(&$result) {
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
	//取一行值
	function row($sql){
		$res = $this->query($sql);
		return $this->fetch_array($res);
	}
	//取单个值1
	function val($sql, $data_field = 0){
		if( stripos($sql, ' limit ')===false ) $sql .= ' limit 1';
		$res = $this->query($sql);
		if(@mysql_num_rows($res)>0)
			return mysql_result($res, 0, $data_field);
	}
	//取单个值2
	function result(&$res, $x = 0, $y = 0){
		return mysql_result($res, $x, $y);
	}
	//查询并组合起来，返回一个字符串
	function jo( $sql, $in=',' ){
		$res = $this->query($sql);
		$str = '';
		while( $r=$this->fetch_array($res) ){
			$str .= $in.reset($r);
		}
		return substr($str, 1);
	}

	//查询所有结果
	function selectAll( $sql ){
		$res = $this->query($sql);
		$arr = array();
		while( $r=$this->fetch_array($res) ){
			$arr[] = $r;
		}
		return $arr;
	}
	/**
	 * Count row number
	 *
	 * @param resource $result SQL query resource
	 * @returns int Number of result rows
	 */
	function rn($result) {
		if( stristr($result, 'select ')!==FALSE ){
			return mysql_num_rows($this->query($result));
		}
		return mysql_num_rows($result);
	}
	/**
	 * Last inserted ID
	 *
	 * @returns int Last inserted auto increment ID
	 */
	function insert_id() {
		return mysql_insert_id($this->connection);
	}
	/**
	 * Get used queries array
	 *
	 * @returns array Array containing executed queries
	 */
	function get_used_queries() {
		return $this->queries;
	}
	/**
	 * Get server version info
	 *
	 * @returns array Array containing database driver info and server version
	 */
	function get_server_info() {
		return array(
			'MySQL',
			mysql_get_server_info($this->connection)
		);
	}
	/**
	 * Disconnect the database connection
	 */
	function disconnect() {
		if ( !$this->persistent )
			@mysql_close($this->connection);
	}
}
?>

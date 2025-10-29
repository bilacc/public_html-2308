<?php
class Database
{
	protected $dbhost, $username, $password, $dbname, $mysqli, $result, $rows = Array(), $production = 1;
	
	public function __construct()
	{
		$this->dbhost = get_conf('database_host');
		$this->username = get_conf('database_username');
		$this->password = get_conf('database_password');
		$this->dbname = get_conf('database_name');
		
		try
		{
			$this->mysqli = new mysqli($this->dbhost, $this->username, $this->password, $this->dbname);
			
			if( $this->mysqli->connect_errno )
			{
				throw new Exception('Greška u pristupu bazi podataka: '.$this->mysqli->connect_error);
			}
		}
		catch( Exception $e )
		{
			echo $e->getMessage();
			exit();
		}
		
		$this->mysqli->query('SET NAMES utf8');
		
		$_SESSION['sql_log'] = '';
		$this->production = ( get_conf('production') === 1 ) ? 1 : 0 ;
	}
	
	public function __destruct()
	{
		$this->mysqli->close();
	}
	
	public function database_query($sql)
	{
		$this->rows = null;
		
		if( $this->production == 0 )
			$start = microtime(true);
		
		$this->result = $this->mysqli->query($sql);
		
		if( $this->production == 0 )
		{
			$end = microtime(true);
			$time = $end - $start;
		}
		
		if ( ! $this->result )
		{
			if( $this->production == 0 )
				$this->logger($sql, 'fail', 0, $time);
			
			return null;
		}
		else
		{
			if( is_bool( $this->result) )
			{
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->mysqli->affected_rows, $time);
				
				return true;
			}
			else
			{
				$c = 0;
				while( $row = $this->result->fetch_assoc() )
				{
					$this->rows[$c] = $row;
					$c++;
				}
				
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->result->num_rows, $time);
				
				$this->result->close();
				
				if( ! $this->rows )
				{
					return null;
				}
				else
				{
					return $this->rows;
				}
			}
		}
	}
	
	public function database_query_row($sql)
	{
		$this->rows = null;
		
		if( $this->production == 0 )
			$start = microtime(true);
		
		$this->result = $this->mysqli->query($sql);
		
		if( $this->production == 0 )
		{
			$end = microtime(true);
			$time = $end - $start;
		}
		
		if ( ! $this->result )
		{
			if( $this->production == 0 )
				$this->logger($sql, 'fail', 0, $time);
			
			return null;
		}
		else
		{
			if( is_bool( $this->result) )
			{
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->mysqli->affected_rows, $time);
				
				return true;
			}
			else
			{
				$this->rows = $this->result->fetch_assoc();
				
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->result->num_rows, $time);
				
				$this->result->close();
				
				if( ! $this->rows )
				{
					return null;
				}
				else
				{
					return $this->rows;
				}
			}
		}
	}
	
	public function database_query_one($sql)
	{
		$this->rows = null;
		
		if( $this->production == 0 )
			$start = microtime(true);
		
		$this->result = $this->mysqli->query($sql);
		
		if( $this->production == 0 )
		{
			$end = microtime(true);
			$time = $end - $start;
		}
		
		if ( ! $this->result )
		{
			if( $this->production == 0 )
				$this->logger($sql, 'fail', 0, $time);
			
			return null;
		}
		else
		{
			if( is_bool( $this->result) )
			{
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->mysqli->affected_rows, $time);
				
				return true;
			}
			else
			{
				$this->rows = $this->result->fetch_row();
				
				if( $this->production == 0 )
					$this->logger($sql, 'success', $this->result->num_rows, $time);
				
				$this->result->close();
				
				if( ! $this->rows )
				{
					return null;
				}
				else
				{
					return $this->rows[0];
				}
			}
		}
	}
	
	public function database_insert_id()
	{
		return $this->mysqli->insert_id;
	}
	
	public function real_escape_string($string)
	{
		return $this->mysqli->real_escape_string($string);
	}
	
	private function logger($sql, $status, $affected, $time)
	{
		$s['sql'] = $sql;
		$s['status'] = $status;
		$s['affected'] = $affected;
		$s['time'] = $time;
		
		$_SESSION['sql_log'][] = $s;
	}
}
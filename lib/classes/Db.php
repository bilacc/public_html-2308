<?php
class Db
{
	static function query($sql)
	{
		global $db;
		
		if( ! is_object($db) )
			return 'greška! nema definirane konekcije na bazu!';
		
		return $db->database_query($sql);
	}
	
	static function query_row($sql)
	{
		global $db;
		
		if( ! is_object($db) )
			return 'greška! nema definirane konekcije na bazu!';
		
		return $db->database_query_row($sql);
	}
	
	static function query_one($sql)
	{
		global $db;
		
		if( ! is_object($db) )
			return 'greška! nema definirane konekcije na bazu!';
		
		return $db->database_query_one($sql);
	}
	
	static function insert_id()
	{
		global $db;
		return $db->database_insert_id();
	}
	
	static function clean($string)
	{
		global $db;
		
		if( ! is_object($db) )
			return 'greška! nema definirane konekcije na bazu!';
		
		if ( get_magic_quotes_gpc() )
		{
			$string = stripslashes( $string );
		}
		
		return $db->real_escape_string($string);
	}
}
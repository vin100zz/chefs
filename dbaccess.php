<?php

function utf8_encode_array(&$array, $key)
{
    if(is_array($array))
	{
      array_walk ($array, 'utf8_encode_array');
    }
	else
	{
      $array = utf8_encode($array);
    }
}

class DBAccessor extends SQLite3
{
	function __construct()
    {
        $this->open("chefs.db3");
    }
}

class DBAccess
{  
    // singleton
    private static $_dbAccessor = null;
    
    private static function getDbAccessor()
    {
    	if(self::$_dbAccessor == null)
    		self::$_dbAccessor = new DBAccessor();
    		
    	return self::$_dbAccessor;
    }
    
//=========================================================================
	public static function exec($iQuery)
	{
		return self::getDbAccessor()->exec(utf8_decode($iQuery));
	}
//=========================================================================
	public static function query($iQuery)
	{
		$aDbResult = self::getDbAccessor()->query(utf8_decode($iQuery));
		
		if(!$aDbResult) return null;
		
		$aReturn = array();
		while($aDbRow = $aDbResult->fetchArray(SQLITE3_ASSOC))
		{
			$aReturn[] = $aDbRow;
		}
		
		array_walk($aReturn, 'utf8_encode_array');
		return $aReturn;
	}
//=========================================================================
	public static function singleRow($iQuery)
	{
		$aReturn = self::getDbAccessor()->querySingle(utf8_decode($iQuery), true);
		array_walk($aReturn, 'utf8_encode_array');
		return $aReturn;
	}
//=========================================================================
	public static function singleColumn($iQuery)
	{
		$aDbResult = self::getDbAccessor()->query(utf8_decode($iQuery));
		if(!$aDbResult) return null;
		
		$aReturn = array();
		while($aDbRow = $aDbResult->fetchArray(SQLITE3_NUM))
		{
			$aReturn[] = $aDbRow[0];
		}

		array_walk($aReturn, 'utf8_encode_array');
		return $aReturn;
	}
//=========================================================================
	public static function singleValue($iQuery)
	{
		$aReturn = self::getDbAccessor()->querySingle(utf8_decode($iQuery), false);
		array_walk($aReturn, 'utf8_encode_array');
		return $aReturn;
	}
//=========================================================================
	public static function keyVal($iQuery)
	{
		$aDbResult = self::getDbAccessor()->query(utf8_decode($iQuery));
		if(!$aDbResult) return null;
		
		$aReturn = array();
		while($aDbRow = $aDbResult->fetchArray(SQLITE3_NUM))
		{
			$aReturn[$aDbRow[0]] = $aDbRow[1];
		}
		
		array_walk($aReturn, 'utf8_encode_array');
		return $aReturn;
	}
}

?>
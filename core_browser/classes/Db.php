<?php
require_once(WWW_DIR.'classes/Cache.php');

class Db
{
    private static $instance = null;

    public function __construct()
    {
        if( !(self::$instance instanceof PDO ) )
        {
            try {            	
                self::$instance = new PDO('sqlite:'.WWW_DIR.'db/core.sqlite3');
                
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            	self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            } catch(PDOException $e) {
                die("Error: Could not connect to database! Check your config. ".$e);
            }
        }
    }

    public function getPDO()
    {
        return self::$instance;
    }

    public function __call($function, $args)
    {
        if( method_exists( self::$instance, $function ) )
        {
            return call_user_func_array( array( self::$instance, $function ), $args );
        }
        trigger_error( "Unknown PDO Method Called: $function()\n", E_USER_ERROR );
    }

    public function escapeString($str)
    {
        return self::$instance->quote($str);
    }

    public function nullInt($val, $threshold=1)
    {
        if (is_numeric($val) && $val >= $threshold)
            return $val;
        else
            return "NULL";
    }

    public function queryInsert($query, $returnlastid = true)
    {
        if($query=="")
            return false;

        $result = self::$instance->exec($query);
		return ($returnlastid) ? self::$instance->lastInsertId() : $result;
    }

    public function queryOneRow($query, $useCache = false, $cacheTTL = '')
    {
        if($query=="")
            return false;

        $rows = $this->query($query, $useCache, $cacheTTL);
        return ($rows ? $rows[0] : false);
    }

    public function query($query, $useCache = false, $cacheTTL = '')
    {
        if($query=="")
            return false;

        if ($useCache) {
            $cache = new Cache();
            if ($cache->enabled && $cache->exists($query)) {
                $ret = $cache->fetch($query);
                if ($ret !== false)
                    return $ret;
            }
        }

        $result = self::$instance->query($query)->fetchAll();

        if ($result === false || $result === true)
            return array();

        if ($useCache)
            if ($cache->enabled)
                $cache->store($query, $result, $cacheTTL);

        return $result;
    }

    public function queryDirect($query)
    {
        if($query=="")
            return false;

        return self::$instance->query($query);
    }

    public function getNumRows(PDOStatement $result)
    {
        return $result->rowCount();
    }

    public function getAssocArray(PDOStatement $result)
    {
        return $result->fetch();
    }

    public function getDataAsArray($data, $col)
    {
        $ret = array();
        foreach ($data as $item)
            $ret[] = $item[$col];

        return $ret;
    }

    public function getLookupAsArray($data, $keycol)
    {
        $ret = array();
        foreach ($data as $item) {
            $ret[$item[$keycol]][] = $item;
        }
        return $ret;
    }
}
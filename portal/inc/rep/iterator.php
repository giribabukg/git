<?php
class CInc_Rep_Iterator extends CCor_Qry implements IteratorAggregate {
  
  protected $mTbl;
  protected $mCnd;
  protected $mLimit;
  protected $mQry;
  protected $mFie;
  
  protected $mOrd = array();
  
  public function __construct($aTbl) {
    parent::__construct();
    $this -> mTbl = $aTbl;
    $this -> mCnd = array();
    $this -> mFie = array();

    $lReplication = CCor_Cfg::get('replication.host', '');
    if(!empty($lReplication)) {
      $lCfg = CCor_Cfg::getInstance();
      $lConfig = array('host' => $lReplication, 'pass' => $lCfg->get('db.pass'), 'name' => $lCfg->get('db.name'));
      	
      $lRepDb = new CCor_Anysql();
      $lRepDb->setConfig($lConfig);
      $this -> mDb = $lRepDb;
    }
  }

  public function addCnd($aCnd) {
    $this -> mCnd[] = $aCnd;
  }

  public function addCondition($aField, $aOp, $aValue) {
    $this -> addCnd('`'.$aField.'` '.$aOp.'"'.$aValue.'"');
  }
  
  public function addField($aAlias) {
    $this -> mFie[$aAlias] = $aAlias;
  }
  
  public function addDef($aDef) {
    $this -> addField($aDef['alias']);
  }
  
  public function getCondSql() {
    $this -> filterJobs();
    $lRet = 'WHERE 1 ';
    if (!empty($this -> mCnd)) {
      foreach ($this -> mCnd as $lCnd) {
        $lRet.= 'AND ('.$lCnd.') ';
      }
    }
    return $lRet;
  }
  
  public function getOrderSql() {
    if (empty($this -> mOrd)) {
      return '';
    }
    $lRet = 'ORDER BY ';
    foreach ($this -> mOrd as $lAli => $lDir) {
      $lRet.= '`'.$lAli.'` '.$lDir.',';
    }
    $lRet = strip($lRet).' ';
    return $lRet;
  }
  
  public function setLimit($aFrom, $aLpp = NULL) {
    $this -> mLimit = intval($aFrom);
    if (NULL !== $aLpp) {
      $this -> mLimit.= ','.intval($aLpp);
    }
  }
  
  public function addOrder($aField, $aDir = 'asc') {
    $this -> mOrd[$aField] = $aDir;
  }
  
  public function getCount() {
    $lSql = 'SELECT COUNT(*) FROM '.$this -> mTbl.' ';
    $lSql.= $this -> getCondSql();
    return CCor_Qry::getStr($lSql);
  }
  
  public function getIterator() {
    //Build up table iterator SQL
    $lSql = 'SELECT ';
    $lSql.= (empty($this -> mFie)) ? '* ' : implode(',', $this -> mFie).' ';
    $lSql.= 'FROM '.$this -> mTbl.' ';
    $lSql.= $this -> getCondSql();
    $lSql.= $this -> getOrderSql();
    if (!empty($this -> mLimit)) {
      $lSql.= ' LIMIT '.$this -> mLimit;
    }
    $this -> dbg('Getting Iterator with '.$lSql, mlInfo);
    $this -> query($lSql);
    
    return new CCor_QryIte($this -> mHandle, $this -> mDb);
  }
  
  public function getArray($aKeyField = NULL) {
    $lRet = array();
    $lIte = $this -> getIterator();
    
    if (NULL == $aKeyField) {
      foreach ($lIte as $lRow) {
        $lRet[] = $lRow -> toArray();
      }
    } else {
      foreach ($lIte as $lRow) {
        $lRet[$lRow[$aKeyField]] = $lRow -> toArray();
      }
    }
    return $lRet;
  }
  
  public function dump($aVar, $aPrefix = '') {
    $lArr = $this -> getArray();
    $lRet = '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    foreach ($lArr as $lRow) {
      if (!$lSec) {
        $lRet.= '<tr>'.LF;
        foreach ($lRow as $lKey => $lVal) {
          $lRet.= '<td class="th1">'.htm($lKey).'</td>';
        }
        $lRet.= '</tr>'.LF;
        $lSec = TRUE;
      }
      $lRet.= '<tr>'.LF;
      foreach ($lRow as $lKey => $lVal) {
        $lRet.= '<td class="th1">'.htm($lVal).'</td>';
      }
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '</table>';
    return $lRet;
  }
  
  protected function filterJobs() {
    // get search criteria
    $lUsr = CCor_Usr::getInstance();
    $this -> mSer = $lUsr -> getPref('rep.ser', '');
    if (is_string($this -> mSer)) {
      $this -> mSer = unserialize($this -> mSer);
    }
    if (!empty($this -> mSer)) {
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all');
        $lIte -> addField('jobid');
      } else {
        $lIte = new CApi_Alink_Query_Getjoblist();
        $lIte -> addField('jobid', 'jobid');
      }

      foreach($this -> mSer as $lAlias => $lValue){
        $lIte -> addCondition($lAlias, '=', $lValue);
      }
      $lJobIds = $lIte -> getArray('jobid');
      if(!empty($lJobIds)){
        $lAlias = ($this -> mTbl == 'al_job_his') ? 'src_id' : 'jobid';
        $lJobIds = '"' . implode('","', array_keys($lJobIds)) . '"';
        $this -> addCnd('`'.$lAlias.'` IN ('.$lJobIds.')');
      } else {
        $lAlias = ($this -> mTbl == 'al_job_his') ? 'src_id' : 'jobid';
        $this -> addCnd('`'.$lAlias.'` IN (-1)');
      }
    }
  }
  
}
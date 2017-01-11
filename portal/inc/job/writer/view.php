<?php
class CInc_Job_Writer_View extends CCor_Obj {

  public function __construct() {
    $this->mAva = $this->getFieldAvailability();
    $this->mFie = $this->getFields();
    $this->mJobTypes = $this->getJobTypes();
  }

  protected function getFieldAvailability() {
    $lSrc['art'] = fsArt;
    $lSrc['rep'] = fsRep;
    $lSrc['sec'] = fsSec;
    $lSrc['adm'] = fsAdm;
    $lSrc['mis'] = fsMis;
    $lSrc['com'] = fsCom;
    $lSrc['tra'] = fsTra;
    $this->mFsAll = fsArt | fsRep | fsSec | fsAdm | fsMis | fsCom | fsTra;
    return $lSrc;
  }

  protected function getFields() {
    $lRet = array();
    // make sure we have these fields, and we have them only once
    $lRet['jobid'] = $this->mFsAll;
    $lRet['flags'] = $this->mFsAll;
    $lRes = CCor_Res::extract('alias', 'avail', 'fie');
    $lRet = array_merge($lRet, $lRes);
    unset($lRet['src']);
    return $lRet;
  }

  protected function getJobTypes() {
    $lMnu = CCor_Cfg::get('menu-aktivejobs');
    $lJobTypes = array();
    foreach ($lMnu as $lKey => $lVal) {
      if ($lVal == 'job-all') continue;
      $lJobTypes[] = substr($lVal, strrpos($lVal, '-') + 1);
    }
    return $lJobTypes;
  }

  public function getViewSql() {
    $lSelect = array();
    foreach ($this->mJobTypes as $lSrc) {
      $lSelect[] = '('.$this->getJobSql($lSrc).')';
    }
    $lSql = 'CREATE OR REPLACE VIEW al_job_all_'.MID.' AS ';
    $lSql.= implode(' UNION ALL ', $lSelect); // union all is faster than union, does not strip duplicates and we don't
    return $lSql;
  }

  public static function recreateView() {
    $lObj = new self();
    $lSql = $lObj->getViewSql();
    return CCor_Qry::exec($lSql);
  }

  protected function getJobSql($aSrc) {
    $lSrcAva = $this->mAva[$aSrc];

    $lRet = 'SELECT '.esc($aSrc).' AS `src`,';
    $lFie = array();
    foreach ($this->mFie as $lAlias => $lAva) {
      $lHasField = bitSet($lAva, $lSrcAva);
      if ($lHasField) {
        $lFie[] = '`'.$lAlias.'`';
      } else {
        $lFie[] = 'NULL AS `'.$lAlias.'`';
      }
    }
    $lRet.= implode(', ', $lFie);
    $lRet.= ' FROM al_job_'.$aSrc.'_'.MID;
    return $lRet;
  }

}

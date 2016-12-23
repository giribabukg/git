<?php
class CInc_Arc_Dat extends CCor_Dat {

  protected $mSrc;
  protected $mJid;

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
  }

  public function getSrc() {
    return $this -> mSrc;
  }

  public function getId() {
    return $this -> mJid;
  }

  public function load($aJobId) {
    return $this -> doLoad($aJobId);
  }

  protected function doLoad($aJobId) {
    
    $this -> mIte = new CCor_TblIte('al_job_arc_'.MID);
    $this -> mIte -> addCnd('jobid='.esc($aJobId));
    $this -> mIte -> getIterator();
    $lRes = $this -> mIte -> getDat();
    $this -> assign($lRes);
    $this -> mJid = $aJobId;

/*
    $lCond = $this -> addUserConditions();
    $this -> dbg('User Condition: '. $lCond);
    
    $lQry = new CCor_Qry();
    $lSql = 'SELECT * FROM al_job_arc_'.MID;
    $lSql .= ' WHERE 1'.$lCond.' AND jobid='.esc($aJobId);
    # echo '<pre>---dat.php---';var_dump($lSql,'#############');echo '</pre>';
    $lRes = $lQry -> query($lSql);
    if (!$lRes) return FALSE;
    $this -> assign($lQry -> getDat());
    $this -> mJid = $aJobId;
*/
    return $aJobId;
  }
/*
  protected function addUserConditions() {
    $lRestr = '';
    $lRestrictions = CCor_Usr::getArrUserConditions($lTyp = 'arc');
    
    if (!empty($lRestrictions)) {
      foreach ($lRestrictions as $lRow) {
        $lRestr.= ' AND '.backtick($lRow['field']).' '.$lRow['op'].' '.$lRow['value'];
      }
      $lRestr.= ' ';
    }
    #echo '<pre>---AddUserConditions---'.get_class().'---';var_dump($lRestrictions,$lRestr,'#############');echo '</pre>';
    return $lRestr;
  }
*/

  public function getFlags() {
    return $this -> mVal['flags'];
  }

}
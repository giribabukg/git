<?php
class CInc_Mig_AddField extends CHtm_Form {

  public function __construct($aId, $aSql, &$aQry) {
		$this -> mId = $aId;
		$this -> mSql = $aSql;
		$this -> mQry = $aQry;

		$lCap = 'Migration: Die Anfrage an Networker';

    parent::__construct('mig.JobSelFie', $lCap, 'mig.JobSelFie');
    $this -> setAtt('style', 'width:100%');

 		$this -> getForm();
  }


  protected function getForm() {
    $lRet = '';
    $lRet.= $this -> getMemo();
   # $lRet.= parent::getForm();
    return $lRet;
  }

  protected function getMemo() {
      $lRet = '';

      $lRet.= '<div class="frm c p16">';
      $lRet.= $this -> mSql;
      $lRet.= '</div>';
      $lRet.= '<div class="th1">Die Inserts f&uuml;r Networker</div>';
      $lRet.= '<div class="frm c p16">';
      $lRet.= '<textarea cols="100" rows="20" class="inp w700">';

      foreach ($this -> mQry as $lRow) {
        # var_dump($lRow);
        $lJid = $lRow['jobid'];
        $lVal = $lRow['val'];
        $lSql = 'REPLACE INTO jobinfos SET ';
        $lSql.= 'jobid='.esc($lJid).',val='.esc($lVal).',infoid='.esc($this -> mId).';'.LF;
        # var_dump($lSql);
        $lRet.= $lSql;
      }
     # var_dump(strlen($lSql));

      $lRet.= '</textarea>';
      $lRet.= '</div>';
    return $lRet;
  }

    protected function getTitle() {
      $lRet = '';
      $lRet.= '<div class="tbl">';
      $lRet.= '<div class="th1">'.htm($this -> mCap).'</div>'.LF;
      $lRet.= '</div>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.back'), '', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}

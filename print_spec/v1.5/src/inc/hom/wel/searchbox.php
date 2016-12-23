<?php
class CInc_Hom_Wel_Searchbox extends CCor_Ren {

  public function __construct() {
    $this -> mSer = array();
    $lUsr = CCor_Usr::getInstance();
    $lUid = CCor_Usr::getAuthId();
    $lQry = new CCor_Qry('SELECT * FROM al_usr_search WHERE ref="job" AND src_id='.$lUid.' AND mand='.MID);
    foreach ($lQry as $lRow) {
      $this -> mSer[] = $lRow;
    }
    $this -> mPre = intval($lUsr -> getPref('job.ser_id'));
  }

  protected function getRow($aId, $aCap) {
    $lRet = '<tr>';
    $lRet.= '<td class="w16">';
    if ($aId == $this -> mPre) {
      $lRet.= img('img/ico/16/cancel.gif');
    } else {
      $lRet.= img('img/ico/16/check-lo.gif');
    }
    $lRet.= '</td>';
    $lRet.= '<td>';
    $lRet.= '<a href="index.php?act=hom-wel.serpre&amp;id='.$aId.'" class="nav">';
    $lRet.= htm($aCap);
    $lRet.= '</a>';
    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getCont() {
    if (empty($this -> mSer)) return '';
    $lRet = '';
    $lRet.= '<div class="tbl w800">';
    $lRet.= '<div class="cap">'.htm(lan('lib.opt.search_presets')).'</div>';
    $lRet.= '<div class="td1 p16">';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0">';
    $lRet.= $this -> getRow(0, lan('lib.search.clear'));
    foreach ($this -> mSer as $lRow) {
      $lRet.= $this -> getRow($lRow['id'], $lRow['name']);
    }
    $lRet.= '</table>';
    $lRet.= '</div>';
    $lRet.= '</div>';
    $lRet.= BR;
    return $lRet;
  }

}
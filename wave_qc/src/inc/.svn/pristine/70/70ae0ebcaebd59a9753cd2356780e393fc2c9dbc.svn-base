<?php
class CInc_Hlp_Item extends CCor_Ren {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
  }

  protected function getCont() {
    $lSql = 'SELECT * FROM al_sys_help WHERE act="'.addslashes($this -> mSrc).'"';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $lRet = '<div class="h1 hr">'.htm($lRow['subject']).'</div>';
      $lRet.= $lRow['txt'];
      return $lRet;
    } else {
      $lRet = '<div class="h1">Sorry!</div>';
      $lRet.= '<p>There is no entry in the help system for the requested item.</p>';
      $lRet.= '<p>An Administrator will be informed and might add some information here, so please check back in from time to time.</p>';
      return $lRet;
    }
  }

}
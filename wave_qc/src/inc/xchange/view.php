<?php
class CInc_Xchange_View extends CCor_Ren {

  public function __construct($aId, $aMid = null) {
    $this->mId = intval($aId);
    $this->mMid = (empty($aMid)) ? MID : intval($aMid);
    $this->loadItem();
  }

  protected function loadItem() {
    $lSql = 'SELECT * FROM al_xchange_jobs_'.$this->mMid.' WHERE id='.$this->mId;
    $lQry = new CCor_Qry($lSql);
    $this->mItem = $lQry->getDat();
  }

  protected function getCont() {
    $lRet = '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= $this->getTitle();
    $lRet.= $this->getRows();
    $lRet.= $this->getButtons();
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getTitle() {
    $lRet = '<tr>';
    $lRet.= '<td class="th1">Field</td>';
    $lRet.= '<td class="th1">Value</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getRows() {
    $lRet = '';
    foreach ($this->mItem as $lKey => $lVal) {
      $lRet.= '<tr>';
      $lRet.= '<td class="td2 b">'.htm($lKey).NB.'</td>';
      $lRet.= '<td class="td1">'.htm($lVal).NB.'</td>';
      $lRet.= '</tr>';
    }
    return $lRet;
  }

  protected function getButtons() {
      $lRet = '<tr>';
      $lRet.= '<td class="frm p16" colspan="2">';
      $lRet.= btn('Back', "go('index.php?act=xchange')", 'img/ico/16/cancel.gif', 'button', array('class'=>'btn w200'));
      $lRet.= '</td>';
      $lRet.= '</tr>';
      return $lRet;
  }

}
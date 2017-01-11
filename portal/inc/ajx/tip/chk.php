<?php
class CInc_Ajx_Tip_Chk extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
  }

  protected function getCont() {
    $lSql = 'SELECT
      chk_master.name_'.LAN.' AS master_name,
      (SELECT cond.name FROM al_cond AS cond WHERE chk_master.cnd_id=cond.id) AS master_cnd,
      chk_items.name_'.LAN.' AS items_name,
      (SELECT cond.name FROM al_cond AS cond WHERE chk_items.cnd_id=cond.id) AS items_cnd
      FROM al_chk_master AS chk_master, al_chk_items AS chk_items
      WHERE chk_master.domain=chk_items.domain AND chk_items.id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();

    $lMasterName = trim($lRow['master_name']);
    $lMasterCnd = trim($lRow['master_cnd']);
    $lMasterCnd = empty($lMasterCnd) ? '-' : $lMasterCnd;

    $lItemsName = trim($lRow['items_name']);
    $lItemsCnd = trim($lRow['items_cnd']);
    $lItemsCnd = empty($lItemsCnd) ? '-' : $lItemsCnd;

    $lRet = '<div class="th1">'.htm($lMasterName).' / '.htm($lMasterCnd).'</div>';
    $lRet.= '<div class="frm p8" style="height:100%">';
    $lRet.= '<div>';
    $lRet.= '<b>'.lan('lib.name').'</b>: '.nl2br(htm($lItemsName));
    $lRet.= '<br/>';
    $lRet.= '<br/>';
    $lRet.= '<b>'.lan('lib.condition').'</b>: '.nl2br(htm($lItemsCnd));
    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }
}
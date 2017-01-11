<?php
class CInc_Job_Sku_Header extends CJob_Header {

  public function __construct($aSKU) {
    $lArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrp = $lArr['sku'];
    parent::__construct('sku', $aSKU, $lCrp);
  }

  protected function getRelatedMenu() {
    $lArr = array();
    $lUsr = CCor_Usr::getInstance();
    $lMen = new CHtm_Menu(lan('job-rel.menu'));
    $lSQL = 'SELECT sku.pro_id, pro.project_name FROM al_job_sku_sur_'.intval(MID).' as sku, al_job_pro_'.intval(MID).' as pro WHERE sku.pro_id=pro.id AND sku.sku_id='.esc($_REQUEST['jobid']);

    $lQry = new CCor_Qry($lSQL);
    foreach ($lQry as $lRow) {
      array_push($lArr, array('pro_id' => $lRow['pro_id'], 'project_name' => $lRow['project_name']));
    }

    foreach ($lArr as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro.edt&amp;jobid='.$lVal['pro_id'], jid($lVal['pro_id'], TRUE).', '.lan('job-pro.item').': '.$lVal['project_name'], 'ico/16/'.LAN.'/job-'.$lKey.'.gif');
    }

    return $lMen -> getContent();
  }

  protected function getBookmarkMenu() {
    $lMen = new CJob_Bookmarks($this -> mSrc, $_REQUEST['jobid'], $this -> mJob['stichw']);
    return $lMen -> getContent();
  }

}
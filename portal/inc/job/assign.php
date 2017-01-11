<?php
class CInc_Job_Assign extends CCor_Ren {

  public function __construct($aSrc, $aJobId, $aSKU = false, $aPrjId = NULL) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mDestination = $aSKU ? 'sku' : 'pro';
    $this -> mPrjId = $aPrjId;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mDestination];
  }

  protected function getRestriction() {
    $lRestr = '';
    return $lRestr;
  }

  public function getCont() {
    $lRet = '';

    $lProKeywords = CCor_Cfg::get('job-'.$this -> mDestination.'.keyw');
    $lProKeywordsAmount = count($lProKeywords);
    $lKeywords = implode(',', $lProKeywords);

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w800">'.LF;
    $lRet.= '<tr><td class="th1" colspan="'.$lProKeywordsAmount.'">'.lan('lib.pro.sel').'</td></tr>'.LF;
    $lRet.= '<tr><td class="sub ar p8" colspan="'.$lProKeywordsAmount.'">';
    $lRet.= btn(lan('lib.cancel'), 'go("index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'")', 'img/ico/16/cancel.gif');
    $lRet.= '</td></tr>'.LF;

    $lCls = 'td1';

    // job to be assigned to sku
    if ($this -> mDestination == 'sku') {
      // is the job already assigned to a project?
      $lSql = 'SELECT pro_id FROM al_job_sub_'.intval(MID).' WHERE jobid_'.$this -> mSrc.'='.esc($this -> mJobId);
      $lProID = CCor_Qry::getInt($lSql);

      $lSql = '';
      if (0 < $lProID) {
        // show skus that are already assigned to this project
        $lSql = 'SELECT id,'.$lKeywords;
        $lSql.= ' FROM al_job_sku_'.intval(MID).' AS main, al_job_sku_sur_'.intval(MID).' AS sur';
        $lSql.= ' WHERE main.del="N" AND main.id=sur.sku_id AND sur.pro_id='.esc($lProID);
        $lSql.= ' UNION';
        // show skus that are not assigned to any project yet
        $lSql.= ' SELECT id,'.$lKeywords;
        $lSql.= ' FROM al_job_sku_'.intval(MID).' AS main';
        $lSql.= ' WHERE main.del="N" AND main.id NOT IN (SELECT sur.sku_id FROM al_job_sku_sur_'.intval(MID).' AS sur)';
        $lSql.= ' ORDER BY id;';
      } else {
        // show all skus
        $lSql = ' SELECT id,'.$lKeywords;
        $lSql.= ' FROM al_job_sku_'.intval(MID);
        $lSql.= ' WHERE del="N"';
        $lSql.= ' ORDER BY id;';
      }
    }

    // sku to be assigned to pro
    if ($this -> mSrc == 'sku' && $this -> mDestination == 'pro') {
      $lSql = ' SELECT id,'.$lKeywords;
      $lSql.= ' FROM al_job_pro_'.intval(MID);
      $lSql.= ' WHERE del="N"';
      $lSql.= ' ORDER BY projekt_name;';
    }

    // job to be assigned to project
    if ($this -> mSrc != 'sku' && $this -> mDestination == 'pro') {
      #$lSql = "SELECT DISTINCT `to_id` FROM `al_crp_step` WHERE `mand`=".MID;
      #$lSql.= " AND `trans` LIKE '".$this -> mDestination."2arc' AND `crp_id`=".$this -> mCrpId." LIMIT 0,1";
      $lSql = "SELECT sta.`status` FROM `al_crp_step` step, `al_crp_status` sta WHERE step.`mand`=".MID;
      $lSql.= " AND step.`trans` LIKE '".$this -> mDestination."2arc' AND step.`crp_id`=".$this -> mCrpId;
      $lSql.= " AND step.`to_id` = sta.`id` LIMIT 0 , 1";
      $lArcStatus = CCor_Qry::getInt($lSql);

      $lSql = 'SELECT id,'.$lKeywords.' FROM al_job_pro_'.intval(MID);
      $lSql.= ' WHERE 1 ';
      if (!empty($lArcStatus)) {
        $lSql.= ' AND webstatus < '.$lArcStatus;
      }
      $lSql.= ' AND del != "Y"'; // check if Project deleted.
      if ($this -> mPrjId){
        $lSql.= ' AND id != "'.$this -> mPrjId.'"';
      }
      $lSql.= $this -> getRestriction();
      $lSql.= ' ORDER BY projekt_name';
    }

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet.= '<tr>';

      foreach ($lProKeywords as $lKey) {
        $lRet.= '<td class="'.$lCls.' nw">';
        if ($this -> mDestination == 'pro') {
          $lRet.= '<a href="index.php?act=job-'.$this -> mSrc.'.sassignprj&amp;jobid='.$this -> mJobId.'&amp;pid='.$lRow['id'].'&amp;fromid='.$this->mPrjId.'" class="nav">';
        } else {
          $lRet.= '<a href="index.php?act=job-'.$this -> mSrc.'.sassignskusub&amp;jobid='.$this -> mJobId.'&amp;skuid='.$lRow['id'].'" class="nav">';
        }
        $lRet.= htm($lRow[$lKey]).NB;
        $lRet.= '</a>';
        $lRet.= '</td>';
      }

      $lRet.= '</tr>'.LF;
      $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
    }

    $lRet.= '</table>';

    return $lRet;
  }

}
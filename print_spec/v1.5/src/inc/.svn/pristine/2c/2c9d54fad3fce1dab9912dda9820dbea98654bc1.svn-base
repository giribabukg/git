<?php
class CInc_Job_Sku_Sub extends CCor_Ren {

  var $mJobs = array();

  public function __construct($aSKUID) {
    $this -> mSKUID = intval($aSKUID);

    $this -> mJobs['ALINK'] = array(); // jobs stored in the networker database
    $this -> mJobs['PDB'] = array(); // jobs stored in the portal database
  }

  protected function getList() {
    $lJobTypesStoredInAlink = CCor_Cfg::get('all-jobs_ALINK');

    $lSql = 'SELECT job_id,src FROM al_job_sku_sub_'.intval(MID);
    $lSql.= ' WHERE sku_id='.$this -> mSKUID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!empty($lRow['job_id'])) {
        if (in_array($lRow['src'], $lJobTypesStoredInAlink)) {
          $this -> mJobs['ALINK'][$lRow['job_id']] = $lRow['job_id']; // job type is obviously stored in the networker database
        } else {
          $this -> mJobs['PDB'][$lRow['job_id']] = $lRow['job_id']; // job type is obviously stored in the portal database
        }
      }
    }
  }

  protected function getCont() {
    $this -> getList();

    if (empty($this -> mJobs['ALINK']) && empty($this -> mJobs['PDB'])) {
      return lan('no.jobs.yet');
    }

    $lAllJobFields = CCor_Res::extract('alias', 'native', 'fie');
    $lSKUJobFields = CCor_Cfg::get('job-sku.subfields');

    $this -> mSub = array();

    // get jobs stored in the networker database and add to a temporary table
    if (!empty($this -> mJobs['ALINK'])) {
      $lJobsFromAlinkEscaped = array_map("esc", $this -> mJobs['ALINK']);
      $lJobsFromAlinkImploded = implode(',', $lJobsFromAlinkEscaped);

      $lIterator = new CApi_Alink_Query_Getjoblist();
      foreach ($lSKUJobFields as $lJobField) {
          $lIterator -> addField($lJobField, $lAllJobFields[$lJobField]);
      }
      $lIterator -> addField('src', $lAllJobFields['src']);
      $lIterator -> addField('webstatus', $lAllJobFields['webstatus']);
      $lIterator -> addCondition('jobid', 'IN', $lJobsFromAlinkImploded);

      foreach ($lIterator as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
    }

    // get jobs stored in the portal database and add to a temporary table
    if (!empty($this -> mJobs['PDB'])) {
      $lJobsFromPDBEscaped = array_map("esc", $this -> mJobs['PDB']);
      $lJobsFromPDBImploded = implode(',', $lJobsFromPDBEscaped);

      $lSql = 'SELECT jobid,src,webstatus,';
      $lSql.= implode(',', $lSKUJobFields);
      $lSql.= ' FROM al_job_pdb_'.intval(MID).' WHERE jobid IN ('.$lJobsFromPDBImploded.')';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
    }

    // create temporary table
    if (!empty($this -> mSub)) {
      $lRet = '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
      $lCls = 'td1';

      foreach ($this -> mSub as $lRow) {
        $lRet.= '<tr>';
        $lRet.= '<td class="'.$lCls.' w16">';
        $lRet.= img('img/ico/16/'.LAN.'/job-'.$lRow['src'].'.gif');
        $lRet.= '</td>';
        $lRet.= '<td class="'.$lCls.'">';
        $lRet.= '<a href="index.php?act=job-'.$lRow['src'].'.edt&amp;jobid='.$lRow['jobid'].'" class="nav">';
        $lRet.= jid($lRow['jobid']);
        foreach ($lSKUJobFields as $lJobField) {
          if ($lRow[$lJobField]){
            $lRet.= ', ['.$lRow[$lJobField].']';
          }
        }
        $lRet.= '</a>';
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }
      $lRet.= '</table>'.LF;
      return $lRet;
    } else {
      return lan('no.jobs.yet');
    }
  }

}
<?php
class CJob_Pro_Sub_Cnt extends CCust_Job_Pro_Sub_Cnt {


  protected function actEdt() {
      $lItmId  = $this -> getInt('id');
      $lSql = 'SELECT src,jobid FROM al_job_sub_'.MID.' WHERE id='.$lItmId;
      $lQry = new CCor_Qry($lSql);
      $lRow = $lQry->getAssoc();
      if (!empty($lRow['jobid'])) {
        $this->redirect('index.php?act=job-'.$lRow['src'].'.edt&jobid='.$lRow['jobid']);
      }
      $lProId  = $this -> getInt('id');
      $this->redirect('index.php?act=job-pro.edt&jobid='.$lProId);
      #parent::actEdt();

  }

}
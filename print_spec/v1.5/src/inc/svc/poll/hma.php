<?php
class CInc_Svc_Poll_Hma extends CSvc_Poll {

  protected function match() {
    $lSubject = $this->mMessage->subject;

    $lMatches = array();
    if (!preg_match('/^ArtNr: ([0-9]+)/', $lSubject, $lMatches)) {
      return false;
    }
    $lArtNr = $lMatches[1];

    $lSql = 'SELECT COUNT(*) FROM al_job_shadow_2 ';
    $lSql.= 'WHERE project_name='.esc($lArtNr);
    $lCount = CCor_Qry::getInt($lSql);

    return ($lCount > 0);
  }

  protected function processMail() {
    $lSubject = $this->mMessage->subject;
    $lBody    = $this->getFirstPlainPart();

    $lMatches = array();
    if (!preg_match('/^ArtNr: ([0-9]+)/', $lSubject, $lMatches)) {
      $this->mCanDelete = false;
      return;
    }
    $lFrom = $this->mMessage->from;
    $lArtNr = $lMatches[1];
    $lSql = 'SELECT jobid,src FROM al_job_shadow_2 ';
    $lSql.= 'WHERE project_name='.esc($lArtNr);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lHis = new CApp_His($lRow['src'], $lRow['jobid']);
      $lHis->setVal('mand', 2);
      $lHis->add(htMail, $lSubject, $lBody);
    }
  }

}
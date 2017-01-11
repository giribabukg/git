<?php
class CJob_Cnt extends CCust_Job_Cnt {

  protected function actSassignprj() {
    $lJobId = $this -> getReq('jobid');
    $lProId = $this -> getReqInt('pid');
    $lProItemId = $this -> getReqInt('prjitmid');

    $lPag = $this -> getReq('page', 'job');

    //if job already assigned to Projekt, get ProjektId.
    $lFromProId = $this -> getReqInt('fromid');
    $lColoumnName = 'jobid_'.$this -> mSrc;
    $lFromSubId = 0;
    /** If the Job is already assigned to Project.
     * Set Jobid in the old Project Items empty.
     */
    if ($lFromProId){
      //22651 Project Critical Path Functionality
      $lSql = 'SELECT `sub_id` FROM `al_job_pro_crp` WHERE `mand`='.MID;
      $lSql.= ' AND pro_id ='.esc($lFromProId).' AND jobid='.esc($lJobId);
      $lQry = new CCor_Qry();
      $lFromSubId = $lQry -> getInt($lSql);

      $lSql = 'UPDATE al_job_sub_'.MID;
      $lSql.= ' SET '.$lColoumnName.' = "",jobid="",src=""';
      $lSql.= ' WHERE pro_id ="'.$lFromProId.'"';
      $lSql.= ' AND  '.$lColoumnName.' = "'.$lJobId.'"';
      CCor_Qry::exec($lSql);
      $this ->dbg('Job Assigment from ProjektId #'.$lFromProId.' deleted.');
    }

    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lJob = $lFac -> getDat();

    $lArr = array('jobid');
    $lMod = new CJob_Pro_Sub_Mod();
    foreach ($lJob as $lKey => $lVal3) {
      if (in_array($lKey, $lArr)) continue;
      $lMod -> setVal($lKey, $lVal3);
    }
    $lMod -> setVal('pro_id', $lProId);
    $lMod -> setVal($lColoumnName, $lJobId);

    // Before INSERT to al_job_sub_x , werden die Daten aus Projekt uebernommen.
    // JobId #22942: Alle Deadlines aus Projekt wierden im Projekt Item uebernommen.

    $lJobUpd = Array();
    //Projektfelder, die im Job uebernommen werden soll,werden aus config gelesen
    $lCnfArr = CCor_Cfg::getFallback('job-pro.fields.onassign', 'job-pro.fields', array());

    $lJobUpd['pro_id'] = $lProId;

    // Get Projekt or if is set ProjektItem Daten to take on.
    if ($lProItemId){
      // Get Daten from ProjektItem
      $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id ='.$lProItemId);
    } else {
      // Get Daten from Projekt
      $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id ='.$lProId);
    }
    $lRow = $lQry-> getAssoc();

    //Felder werden im Job uebernommen.
    foreach ($lCnfArr as $lKey) {
      if (isset ($lRow[$lKey])) {
        // Feld ist im Projekt definiert. Inhalt wird uebernommen.
        $lJobUpd[$lKey] = $lRow[$lKey];
        // Feld wird im Projekt Item uebernommen.
        $lMod -> setVal($lKey, $lRow[$lKey]);
      } else {
        // Feld ist nicht im Projekt definiert. Leeres Inhalt wird uebernommen.
        $lJobUpd[$lKey] = '';
      }
    }

    $lGetDdlFromProject = CCor_Cfg::get('job-pro.fields.pro2item.ddl', true);
    if ($lGetDdlFromProject && (!empty($lRow))) {
      // Alle Deadlines werden in Projekt Item uebernommen.
      foreach ($lRow as $lKey => $lVal) {
        if (substr($lKey,0,4) == 'ddl_') {
          // Alle Deadlines im ProjektItems uebernommen.
          $lMod -> setVal($lKey, $lVal);
        }
      }
    }

    $lMod->setVal('jobid', $lJobId);
    $lMod->setVal('src', $this->mSrc);

    /*
     * Entweder wird ein neu ProjektItem angelegt oder
    * zu einer bestehende ProjektItem hinzugefuegt.
    *
    */
    if (!$lProItemId) {
      // Add new Project Item
      $lMod -> insert();
      $lSubId = $lMod -> getInsertId();
    } else {
      //Target job src id is free. It means, add in existin Project Item.
      $lMod -> insertInPrjItem($lJobId,$lProItemId, $this -> mSrc);
      $lSubId = $lProItemId;
      $lAddInProjectItem = TRUE;
    }

    // zugeordnete Projektdaten werden im Job ueberschrieben.
    $lMod = $lFac -> getMod($lJobId);
    $lMod -> forceUpdate($lJobUpd);

    //22651 Project Critical Path Functionality
    if ($lFromProId AND 0 < $lFromSubId) {
      $lMod -> updateProjectStatusInfo($lJobId, $lProId, $lSubId, $lFromProId, $lFromSubId);
    } else {
      $lWebstatus = $lJob['webstatus'];
      $lMod -> insertIntoProjectStatusInfo($lJobId, $lProId, $lSubId, $lWebstatus);
    }
    $this -> redirect('index.php?act=job-'.$this->mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
  }
}
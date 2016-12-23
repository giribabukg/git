<?php
class CCrp_Apl_List extends CHtm_List {

  protected $mSrcArr = array();
  
  public function __construct() {
    parent::__construct('crp');

    $this -> setAtt('width', '600px');
    $this -> mTitle = 'Apl';
    $this -> mStdLnk = 'index.php?act=crp-sta&amp;id=';

    $this -> mDefaultOrder = 'name_'.LAN;

    $this -> addCtr();
    $lUsr = CCor_Usr::getInstance();
    if (0 == MID AND $lUsr -> canInsert('crp')) {
      $this -> addCpy(TRUE);
    }
    #$this -> addColumn('code', lan('job.typ'), TRUE);
    $this -> addColumn('name_'.LAN, 'Name',    TRUE, array('width' => '100%'));
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> getPrefs();

      $this -> mIte = new CCor_TblIte('al_crp_apl c'); // Vorlagen für alle Mandanten: al_crp_mastertpl

    $this -> mIte -> addCnd('c.mand = '.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mMaxLines = $this -> mIte -> getCount();
    
 
    $this -> addBtn('New Apl', "go('index.php?act=crp.new')", 'img/ico/16/plus.gif');

    

}

  protected function preLoad() {
    $lAllJobs = CCor_Cfg::get('all-jobs'); // holt die Jobtypen für den Mandanten
    if (CCor_Licenses::get('job-pro')) { // arbeitet der Mandant mit Projekten
      array_unshift($lAllJobs, 'pro');
    }
    $lAllJobs = array_flip($lAllJobs);
    $lArr = array();
    $this -> mIte = $this -> mIte -> getArray('id'); // holt die Critical Path für den Mandanten

    foreach ($this -> mIte as $lKey => $lRow) {
      $lCode = $lRow['code'];
      unset($lAllJobs[$lCode]);
    }
    // $lAllJobs enthält nun alle Jobtypen, die keinen Critical Path haben
    if (!empty($lAllJobs)) {
      $lAllJobs = array_flip($lAllJobs);
      $lAll = array_map("esc", $lAllJobs); // jedes Element wird ".mysql_escaped."
      $lCodes = implode(',',$lAll);
      // hole jetzt alle Vorlagen, die es für die verbliebenen Jobtypen gibt.
      $lSql = 'SELECT id, code, name_'.LAN.' as name FROM al_crp_mastertpl WHERE mand=0 AND code IN ('.$lCodes.') ORDER BY name_'.LAN; // Vorlagen für alle Mandanten: al_crp_mastertpl
      $lQry = new CCor_Qry($lSql);
      foreach($lQry as $lRow) {
        $lArr[$lRow['id']] = $lRow;
      }
      $this -> mSrcArr = $lArr;
    }
    #echo '<pre>---list.php---';var_dump($lRow,$lArr,$lSql,$this -> mSrcArr,'#############');echo '</pre>';
  }

}
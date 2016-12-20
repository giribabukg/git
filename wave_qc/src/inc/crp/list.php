<?php
class CInc_Crp_List extends CHtm_List {

  protected $mSrcArr = array();

  public function __construct() {
    parent::__construct('crp');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('crp.menu');
    $this -> mStdLnk = 'index.php?act=crp-sta&amp;id=';

    $this -> mDefaultOrder = 'name_'.LAN;

    $this -> addCtr();
    $lUsr = CCor_Usr::getInstance();
    if (0 == MID AND $lUsr -> canInsert('crp')) {
      $this -> addCpy(TRUE);
    }
    $this -> addColumn('code', lan('job.typ'), TRUE);
    $this -> addColumn('name_'.LAN, 'Name',    TRUE, array('width' => '100%'));
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> getPrefs();
    if (0 == MID) {
      $this -> mIte = new CCor_TblIte('al_crp_mastertpl c'); // Vorlagen für alle Mandanten: al_crp_mastertpl
    } else {
      $this -> mIte = new CCor_TblIte('al_crp_master c'); // CRP pro Mandant: al_crp_master
    }
    $this -> mIte -> addCnd('c.mand = '.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mMaxLines = $this -> mIte -> getCount();

    // Für welche Jobtypen hat der Mandant noch keinen CriticalPath: Diese können kopiert werden
    $this -> preLoad();
    if ($this -> mCanInsert) {
      if (empty($this -> mSrcArr)) {
        $this -> addBtn(lan('crp.new'), "go('index.php?act=crp.new')", '<i class="ico-w16 ico-w16-plus"></i>');
      } else {
        $this -> addButton('cpy', $this -> getButtonMenu());
      }
    }

}

  public function getButtonMenu() {
  	$lMen = new CHtm_Menu('Button');
  	$lMen -> addItem('index.php?act=crp.new', lan('lib.create'), '<i class="ico-w16 ico-w16-plus"></i>');
  	$lMen -> addTh2(lan('crp.cpy'));
    foreach ($this -> mSrcArr as $lId => $lRow) {
       $lMen -> addItem('index.php?act=crp.cpy&amp;id='.$lId, $lRow['name'], '<i class="ico-w16 ico-w16-job-'.$lRow["code"].'-de"></i>');
    }
    $lLnk = "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')";
    $lBtn = btn(lan('crp.new'), $lLnk, '<i class="ico-w16 ico-w16-plus"></i>', 'button', array('class' => 'btn w130','id' => $lMen -> mLnkId ));
    $lBtn .= $lMen -> getMenuDiv();
    return $lBtn;
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
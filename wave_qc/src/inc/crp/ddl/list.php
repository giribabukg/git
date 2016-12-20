<?php
class CInc_Crp_Ddl_List extends CHtm_List {

  protected $mCrpId = 0;
  protected $mSrc;
  protected $mVal = array();
  protected $mIte = '';
  protected $mDateAli = '';
  public $mDdl = Array(); // Array mit Status_id und already saved Jobfields_id;

  public function __construct($aCrpId, $aVal = array()) {
    parent::__construct('crp-ddl', 'crp');

    $this -> mCrpId = intval($aCrpId);
    $this -> mVal = $aVal;
    $this -> setAtt('class', 'tbl w700');
    $this -> mTitle = lan('crp.menu.ddl');
    $this -> mOrd = "a.status" ;
	$this -> mSrc = CApp_Crpimage::getCriticalPathSrc($aCrpId);
	
    $this -> mDefaultOrder = 'display';

    $this -> addColumn('display', '', FALSE, array('width' => '16','valign'=>'middle','align'=>'center'));
    $this -> addColumn('name', lan('lib.name'), FALSE, array('width' => '50%'));
    $this -> addColumn('ddl', lan('lib.ddl'), FALSE, array('width' => '50%'));

    if (0 < $this -> mCrpId) {
      $this -> mIte = new CCor_TblIte('al_crp_status as a');
      $this -> mIte -> addCnd('a.mand='.MID);
      $this -> mIte -> addCnd('a.crp_id='.$this -> mCrpId);
      $this -> mIte -> addField('a.name_'.LAN.' as aname_'.LAN);
      $this -> mIte -> addField('a.display');
      $this -> mIte -> addField('a.status');
      $this -> mIte -> addField('a.id');
      $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    }

    $lSql = "SELECT id,mand,alias,name_".LAN.",typ FROM al_fie WHERE typ LIKE 'date%' AND mand='".MID."'";
    $this -> mDateAli = new CCor_Qry($lSql);

    $this -> setDdl();

     

  }

  public function getCrpList() {
    $this -> mDateFie = array();
    $lSql = "SELECT mand,crp_id,name_".LAN.",status FROM al_crp_status WHERE mand='".MID."' AND crp_id='". $this -> mCrpId ."' ORDER BY status ASC ";
    $lRes = $db -> query($lSql);
    while ($lRow = $db -> getAssoc($lRes)) {
      $this -> mDateFie[$lRow['id']] = $lRow['name'];
    }
  }

  protected function getTdDdl() {
    $lStaId = $this -> getInt('id');
    $form = '<select name="post_ddl_value[]">';
    $form.= '<option value="null"></option>';

    foreach ($this -> mDateAli as $row) {
      if (isset($this -> mDdl[$lStaId]) AND $row['id'] == $this -> mDdl[$lStaId]) $selected = ' selected="selected"'; else $selected = "";
      $form.='<option value="'.$row['id'].'"'.$selected.'>'.$row['name_'.LAN].'</option>';
    }

    $form.= '</select>';
    $form.= '<input type="hidden" name="post_ddl_status_id[]" value="'.$lStaId.'">';
    $lRet = $this -> td($form);
    return $lRet;
  }

  protected function getTdName() {
    $lVal = htm($this -> getVal('aname_'.LAN));
    $lRet = $this -> td($lVal);
    return $lRet;
  }

  protected function getTdDisplay() {
    $lDis = $this -> getInt('display');
    $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lDis.'b.gif');
    $lRet = $this -> tdc( $this -> a(img($lPath), false) );
    return $lRet;
  }

  protected function getCont() {
    $lRet = parent::getCont();
    if ($this -> mCanEdit) {
      $lRet = $this -> wrapForm($lRet);
    }
    return $lRet;
  }

  protected function wrapForm($aRet) {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="post_crp_id" value="'.$this -> mCrpId.'">';
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mCrpId.'">';
    $lRet.= '<input type="hidden" name="act" value="crp-ddl">';
    $lRet.= $aRet;
    $lRet.= '<div class="btnPnl">';
    $lRet.= btn('Ok', '', 'img/ico/16/ok.gif', 'submit');
    $lRet.= '</div>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  public function insertData()
  {
    if (!empty($this -> mVal['post_ddl_status_id'])) foreach ($this -> mVal['post_ddl_status_id'] as $key => $status_id)
    {
      if ($status_id != 'null')
      {
        $update=false;
        $lSql = new CCor_Qry("SELECT id from al_crp_ddl where crp_id = '".$this -> mCrpId."' and mand='".MID."' and status_id='".$status_id."'");
        foreach($lSql as $row)
        {
          $lSql2 = new CCor_Qry("UPDATE  al_crp_ddl SET crp_id = '".$this -> mCrpId."' , mand='".MID."' , status_id='".$status_id."' ,fie_id='".$this -> mVal['post_ddl_value'][$key]."' where id='". $row['id']."' ");
          $update=true;
        }
        if ($update!=true)
        {
          $lSql2 = new CCor_Qry("INSERT INTO al_crp_ddl SET crp_id = '".$this -> mCrpId."' , mand='".MID."' , status_id='".$status_id."' ,fie_id='".$this -> mVal['post_ddl_value'][$key]."'  ");
          $update=false;
        }
      }
    }
    $this ->setDdl();
  }

  public function setDdl(){
    $this -> mDdl = Array();
    $lSql = "SELECT id,fie_id,status_id FROM al_crp_ddl WHERE crp_id = '".$this -> mCrpId."' AND mand='".MID."'";
    $lCrpDdlAli = new CCor_Qry($lSql);
    foreach($lCrpDdlAli as $row) {
      $this -> mDdl[$row['status_id']] = $row['fie_id'];
    }
  }
}
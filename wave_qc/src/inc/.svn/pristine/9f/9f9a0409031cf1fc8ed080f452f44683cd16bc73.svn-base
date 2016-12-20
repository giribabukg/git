<?php
/**
 * @class CInc_Sys_Msg_List
 * @author pdohmen
 * @description Class for displaying the list with System Messages
 */
class CInc_Sys_Msg_List extends CHtm_List {
  public function __construct($aMod = NULL) {
    if(!isset($aMod))
      $this -> mMod = 'sys-msg';
    else
      $this -> mMod = $aMod;
    $this -> m2Act = $this -> mMod;

    parent::__construct($this -> mMod);

    //Setup List
    $this -> mTitle = lan('sys-msg.menu');
    $this -> setAtt('width', '1000');
    $this -> addCtr();

    $this->addColumn("msgText", lan("content.text.menu"), true);
    $this->addColumn("msgType", lan("tab_master.type")  , true);
    $this->addColumn("name_de",    lan("lib.mand")         , true);
    $this->addColumn("startDate", lan("lib.start_date"), true);
    $this->addColumn("endDate", lan("lib.end_date"), true);
    if($this->mCanDelete) {
      $this -> addColumn('del', '', FALSE, array('width' => '16', 'id' => 'del'));
    }

    //Control Buttons
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('sys-msg.caption'), "go('index.php?act=".$this -> m2Act.".new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }
    if ($this -> mCanDelete) {
      $this -> addBtn(lan('lib.del.all'), 'javascript:Flow.Std.cnfDel("index.php?act=sys-msg.truncate", "'.LAN.'")', '<i class="ico-w16 ico-w16-del"></i>');
    }

    //Get List Content
    $this -> mIte = new CCor_TblIte('al_sys_msg m, al_sys_mand ma');
    $this -> mIte -> addField('m.id');
    $this -> mIte -> addField('ma.name_de');
    $this -> mIte -> addField('m.msgText');
    $this -> mIte -> addField('m.msgType');
    $this -> mIte -> addField('m.startDate');
    $this -> mIte -> addField('m.endDate');
    $this -> mIte -> addCnd('m.mandName=ma.code');
  }
}
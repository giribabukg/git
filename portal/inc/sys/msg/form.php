<?php
/**
 * @class CInc_Sys_Msg_Form
 * @author pdohmen
 * @description Form for generating System Messages on the Portal
 */
class CInc_Sys_Msg_Form extends CHtm_Form {
  public function __construct($aAct, $aCaption, $aCancel = NULL) {
      parent::__construct($aAct, $aCaption , $aCancel);
      $this -> setAtt('class', 'tbl w700');
      $this->load();
  }
  /**
   * @method load
   * @author pdohmen
   * @description Builds and returns the Form in HTML
   * @return string Rendered Form in HTML
   */
  protected function load() {
     $this -> addDef(fie("msgType"   , lan("sys-msg.type")     , "select" , array('Info' => lan("lib.info") , 'Warning' => lan("lib.warn"), 'Critical' => lan("lib.crit"))));
     $this -> addDef(fie('msgText'   , lan("sys.message")      , 'memo'   , NULL  , array('class' => 'inp w500', "required" => true)));
     $this -> addDef(fie("startDate" , lan("lib.start_date")   , "date"   ,""     , array("required" => true)));
     $this -> addDef(fie("endDate"   , lan("lib.end_date")     , "date"   ,""     , array("required" => true)));

     //All Mands
     $lArr = CCor_Res::extract('code', 'name_'.LAN, 'mand');
     $lMandArr = array("cust" => "All Mandators");
     foreach ($lArr as $lKey => $lVal) {
       $lMandArr[$lKey] = $lVal;
     }
     $this -> addDef(fie("mandName"    , lan("lib.mand")      , "select", $lMandArr));
  }
}
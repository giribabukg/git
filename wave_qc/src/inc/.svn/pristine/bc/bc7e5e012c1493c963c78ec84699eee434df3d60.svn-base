<?php
/**
 * @class CInc_Sys_Msg_Cnt
 * @author pdohmen
 * @description Controller Class for System Messages
 */
class CInc_Sys_Msg_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
  }

  /**
   * @method actStd
   * @author pdohmen
   * @description Standard Action for displaying the list of current messages
   */
  protected function actStd() {
    $lList = new CSys_Msg_List();
    $this->render($lList);
  }

  /**
   * @method actNew
   * @author pdohmen
   * @description Edit Action which displays the form for adding new messages
   */
  protected function actNew() {
      $lForm = new CSys_Msg_Form_Base("sys-msg.sMsg",lan("sys-msg.caption"));
      $this->render($lForm);
  }

  /**
   * @method actsMsg
   * @author pdohmen
   * @description Action for Saving the given System Message
   */
  protected function actsMsg() {
      $lVal = $this->mReq->val;
      $startDate = date("Y-m-d", strtotime($lVal["startDate"]));
      $endDate = date("Y-m-d", strtotime($lVal["endDate"]));
      //Save
      $lQry = "INSERT INTO .`al_sys_msg` (`msgText`, `msgType`, `mandName`, `startDate`, `endDate`) VALUES ('".$lVal["msgText"]."', '".$lVal["msgType"]."', '".$lVal["mandName"]."', '".$startDate."', '".$endDate."');";
      CCor_Qry::exec($lQry);

      //Log
      $log = new CCor_Log();
      if($lVal["mandName"] === "cust") {
        $lMand = "All Mandators";
      }
      else {
        $lMand = $lVal["mandName"];
      }
      $log->log("[".$lMand."] Added System Message [".$lVal["msgType"]."] ".$lVal["msgText"],1,1);

      $this->redirect();
  }
  /**
   * @method actEdt
   * @author pdohmen
   * @description Edits one specific System Message
   */
  protected function actEdt() {
      $lUid = $this -> getReqInt('id');
      $lForm = new CSys_Msg_Form_Edit($lUid);
      $this->render($lForm);
  }
  /**
   * @method actsMsg
   * @author pdohmen
   * @description Action for Saving the given System Message
   */
  protected function actedtMsg() {
      $lVal = $this->mReq->val;
      $startDate = date("Y-m-d", strtotime($lVal["startDate"]));
      $endDate = date("Y-m-d", strtotime($lVal["endDate"]));
      //Save
      $lQry = "UPDATE `al_sys_msg` SET `msgText`='".$lVal["msgText"]."', `msgType`='".$lVal["msgType"]."', `mandName`='".$lVal["mandName"]."', `startDate`='".$startDate."', `endDate`='".$endDate."' WHERE  `ID`=".$lVal["id"].";";
      CCor_Qry::exec($lQry);

      //Log
      $log = new CCor_Log();
      if($lVal["mandName"] === "cust") {
        $lMand = "All Mandators";
      }
      else {
        $lMand = $lVal["mandName"];
      }
      $log->log("[".$lMand."] Updated System Message [".$lVal["msgType"]."] ".$lVal["msgText"],1,1);

      $this->redirect();
  }
  /**
   * @method actDel
   * @author pdohmen
   * @description Deletes one specific System Message
   */
  protected function actDel() {
    $lId  = $this -> getReqInt('id');
    CCor_Qry::exec('DELETE FROM al_sys_msg WHERE `ID`='.$lId);

    $this -> redirect();
  }
  /**
   * @method actTruncate
   * @author pdohmen
   * @desciption Truncates sys-msg Table
   */
  protected function actTruncate() {
    CCor_Qry::exec('TRUNCATE al_sys_msg');
    $this -> redirect();
  }

  public function getMessages() {
    //System Message
    $lSql = "SELECT * FROM al_sys_msg WHERE mandName IN ('cust', '".MAND."')";
    $lQry = new CCor_Qry($lSql);
    $lRet ="";
    foreach($lQry as $lRow) {
      $lDateNow = time();
      $lStartDate = strtotime($lRow["startDate"]);
      $lEndDate = strtotime($lRow["endDate"]);
      if($lDateNow >= $lStartDate && $lDateNow <= $lEndDate) {
        $lRet .= '<pre class="sm'.$lRow["msgType"].' w100p smBase">['.$lRow["msgType"].'] '.$lRow['msgText'].'</pre>';
      }
    }
    return $lRet;
  }

}
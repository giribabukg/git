<?php
class CInc_Pck_Itm_Cnt extends CCor_Cnt {

  public static $mFields= Array(); //Picklist Columns
  public $mFieldCaptions = Array(); // Column Captions
  public $mAdminView = FALSE;
  public $mDom = '';
  public $mIdx = '';
  public $mCpp = '';
  public $mUrl_Param = '';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('pck-itm.menu');
    $this -> mDom = $this -> getReq('dom', '');
    $this -> mIdx = $this -> getInt('idx', '');
    $this -> mCpp = $this -> getInt('cpp', '');
    $lUrl_Param = 'dom='.$this -> mDom;
#   if (!empty($this -> mIdx)) {
      $lUrl_Param.= '&idx='.$this -> mIdx;
      $lUrl_Param.= '&cpp='.$this -> mCpp;
#   }

    $lAdminView = $this -> getReq('xx','');
    if (!empty( $lAdminView )) {
      $this -> mAdminView = TRUE;
      $lUrl_Param .= '&xx=1';
    } else {
      $this -> mPag = new CUtl_Page();
    }

    $this -> mUrl_Param = $lUrl_Param;

     /**
     * @var CPck_Itm_Cnt
     * Get Picklist Column
     */
    $lQry = new CCor_Qry('SELECT * FROM al_pck_columns WHERE domain='.esc($this -> mDom).' AND mand IN ("0","'.MID.'") ORDER BY position');
    foreach ($lQry as $lRow) {
      $this-> mFields[] = $lRow;
   #     if ($lRow['hidden'] == 'N') $lFieldCount++;
    }

    if (!empty($this-> mFields)) {
      /**
       * @var CPck_Itm_Cnt
       * Get Picklist Column Caption
       */
      $lCaption = 'name_'.LAN;
      $lFie = CCor_Res::extract('alias', $lCaption, 'fie');
      foreach ($this -> mFields as $lCol) {
        $this -> mFieldCaptions[$lCol['alias']] = (isset($lFie[$lCol['alias']])) ? $lFie[$lCol['alias']] : $lCol['alias'] ;
      }
    } else {
      $this ->dbg(lan('pck.no.column'), mlWarn);
      $this -> redirect('index.php?act=pck');
    }
  }

  protected function getStdUrl() {
    $lUrl = parent::getStdUrl();
    $lUrl.= '&'.$this -> mUrl_Param;
    return $lUrl;
  }

  protected function actStd() {
    if ($this -> mAdminView) {
      $lMen = new CPck_Menu($this -> mDom, 'itm');
    }
    $lVie = new CPck_Itm_List($this -> mDom, $this -> mFields, $this -> mFieldCaptions, $this -> mIdx, $this -> mCpp, $this -> mAdminView);
    if ($this -> mAdminView) {
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    } else {
      # $lVie -> setAtt('width', '800px');
      $lRet = $lVie -> addJs();
      $this-> mPag -> setPat('pg.cont', toStr($lVie));
      $this-> mPag -> setPat('pg.title', $this -> mTitle);
      $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
      $this-> mPag -> setPat('pg.js', $lRet);
      $this-> mPag -> render();
    }
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');
    if ($this -> mAdminView) {
      $lMen = new CPck_Menu($this -> mDom, 'itm');
    }
    $lVie = new CPck_Itm_Form_Edit($lId, $this -> mDom, $this -> mFields, $this -> mFieldCaptions, $this -> mAdminView);
    $lVie -> setParam('idx', $this -> mIdx);
    $lVie -> setParam('cpp', $this -> mCpp);
   # $this -> render($lVie);
    if ($this -> mAdminView) {
      $lVie -> setParam('xx', 1);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    } else {
      # $lVie -> setAtt('width', '800px');
      $this-> mPag -> setPat('pg.cont', toStr($lVie));
      $this-> mPag -> setPat('pg.title', $this -> mTitle);
      $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
      $this-> mPag -> render();
    }

  }

  protected function actSedt() {
    $lMod = new CPck_Itm_Mod($this -> mFields);
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();
      CCor_Cache::clearStatic('cor_res_pck_'.$this -> mDom);
    }
    $this -> redirect();
  }

  protected function actCopy() {
    $lId = $this -> mReq -> getInt('id');
    if ($this -> mAdminView) {
      $lMen = new CPck_Menu($this -> mDom, 'itm');
    }
    $lVie = new CPck_Itm_Form_Edit($lId, $this -> mDom, $this -> mFields, $this -> mFieldCaptions, $this -> mAdminView);
    $lVie -> setParam('act', 'pck-itm.snew');
    $lVie -> setParam('val[id]', '');
    $lVie -> setParam('old[id]', '');
    $lVie -> setParam('idx', $this -> mIdx);
    $lVie -> setParam('cpp', $this -> mCpp);
    if ($this -> mAdminView) {
      $lVie -> setParam('xx', 1);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    } else {
      $this-> mPag -> setPat('pg.cont', toStr($lVie));
      $this-> mPag -> setPat('pg.title', $this -> mTitle);
      $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
      $this-> mPag -> render();
    }
  }

  protected function actBatchNew() {
    $lMen = new CPck_Menu($this -> mDom, 'itm');
    $lUrlParam = '&xx=1&idx='.$this -> mIdx.'&dom='.$this -> mDom;

    $lVie = new CPck_Itm_Batch_Form('pck-itm.cbatch', lan("lib.addMultiple"), $this->mFields,  'pck-itm'.$lUrlParam);
    $lVie -> setDom($this -> mDom);
    $this->render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actCbatch() {
    $lUrlParam = '&xx=1&idx='.$this -> mIdx.'&dom='.$this -> mDom;

    $lMen = new CPck_Menu($this -> mDom, 'itm');
    $lVie = new CPck_Itm_Batch_List('pck-itm.sbatch', 'pck-itm'.$lUrlParam, lan("fie.check"), $this->mFields, $this->mReq);

    $this->render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSbatch() {
    $lReq = $this->mReq;
    $lVal = $lReq->getVal("val");

    $lBatchData = array();
    $lCols = "";
    $lLines = explode("\n", $this->getVal("batch"));
    $set=1;

    //Create Batch Array
    foreach($lLines as $line) {
      $lineArr = explode(";", $line);
      foreach($lineArr as $key => $value) {
        $lBatchData[$set][$key] = $value;
      }
      $set++;
    }

    //Create Col String for DB
    for($i=2;$i<=count($lBatchData[1])+1;$i++) {
      $lCols .= "`col".$i."`, ";
    }
    $lCols = substr($lCols, 0,-2);

    foreach ($lBatchData as $key => $value) {
      if(isset($lVal[$key])) {
        //Create Value String for DB
        $lColValues = "";
        $i = 1;
        foreach($value as $row) {
          $lColValues .= "col".$i."='" . trim($row) . "', ";
          $i++;
        }
        $lColValues = substr($lColValues, 0,-2);
        //Add to Helptable
        $lQry = "INSERT INTO `al_pck_items` SET ";
        $lQry.= "pck_id='".$this->mFields[0]["pck_id"]."', domain='".$this->mDom."',mand='".$this->getReq('mand')."', ";
        $lQry.= $lColValues;
        //echo $lQry;
        
        CCor_Qry::exec($lQry);
      }
    }

    CCor_Cache::clearStatic('cor_res_picklist_'.$this -> mDom);
    $lUrlParam = '&xx=1&idx='.$this -> mIdx.'&dom='.$this -> mDom;
    //exit;
    $this -> redirect('?act=pck-itm'.$lUrlParam);
  }

  protected function actNew() {
    if ($this -> mAdminView) {
      $lMen = new CPck_Menu($this -> mDom, 'itm');
      $lUrl_Param = '&xx=1';
    } else {
      $lUrl_Param = '';
    }
    $lVie = new CPck_Itm_Form_Base('pck-itm.snew', lan('pck-itm.new'), $this -> mFields, $this -> mFieldCaptions, 'pck-itm'.$lUrl_Param.'&idx='.$this -> mIdx.'&dom='.$this -> mDom);
    $lVie -> setDom($this -> mDom);
    $lVie -> setParam('idx', $this -> mIdx);
    $lVie -> setParam('cpp', $this -> mCpp);
    if ($this -> mAdminView) {
      $lVie -> setParam('xx', 1);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    } else {
      $this-> mPag -> setPat('pg.cont', toStr($lVie));
      $this-> mPag -> setPat('pg.title', $this -> mTitle);
      $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
      $this-> mPag -> render();
    }
  }

  protected function actSnew() {
    $lMod = new CPck_Itm_Mod($this -> mFields);
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_pck_'.$this -> mDom);
    }
    $this -> redirect();

  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_pck_items WHERE id="'.$lId.'"';
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_pck_'.$this -> mDom);
    $this -> redirect();
  }


  /*
   * Get Print Machinery Information from Networker
   * by Alink Method 'PrintSpecs'.
   *
   */
  protected function actPrintSpecAbgleich(){
  	$lRes = Array();
    $lQry = new CApi_Alink_Query('getPrintSpecs');
    $lQry -> addParam('sid', MANDATOR); // 'har'
    $lRet = $lQry->query();
    $lItem = $lRet ->getVal('item');

    foreach ($lItem as $lRow){
      $lRes[] = (array)$lRow;
    }

    // If dont get List, redirect to back.
    if (empty($lRes)){
      $this ->dbg('No machinery found');
      $this -> redirect('index.php?act=pck-itm&dom='.$this->mDom.'&idx=&cpp=&xx=1');
    }

    $lMaschinenZahl = count($lRet);
    $this ->dbg($lMaschinenZahl.' machinery found');

    // Delete old Items
    $lSqlDel = "DELETE FROM al_pck_items WHERE domain ='".$this -> mDom."'";
    CCor_Qry::exec($lSqlDel);

    // Find Pick List Id from 'prnt'
    $lSqlFind = 'SELECT DISTINCT(id) from al_pck_master where domain ="'.$this->mDom.'"';
    $lPckId = CCor_Qry::getInt($lSqlFind);


    // Find out the ColId of Alias (knr) in the Picklist
    $lSqlColFind = 'SELECT alias,col from al_pck_columns where domain ="'.$this->mDom.'"';
    $lQry = new CCor_Qry($lSqlColFind);

    $lSqlColoumn = '';
    foreach ($lQry as $lRow){
      if(isset($lRes[0][$lRow['alias']])){
		$lColumnAlias[$lRow['col']] = $lRow['alias'];
		$lSqlColoumn.='col'.$lRow['col'].',';
      }
    }
    $lSqlColoumn = substr($lSqlColoumn,0,-1);

   foreach ($lRes as $lKey){
      $lSql ="INSERT INTO al_pck_items (pck_id,domain,mand,".$lSqlColoumn.") VALUES (".$lPckId.",'".$this -> mDom."',0,";
      foreach ($lColumnAlias as $lWert){
        $lSql .= "'".$lKey[$lWert]."',";
      }
      $lSql = substr($lSql,0,-1);
      $lSql .= ")";
      CCor_Qry::exec($lSql);
    }
    $this -> redirect('index.php?act=pck-itm&dom='.$this->mDom.'&idx=&cpp=&xx=1');
  }

  protected function actCsvExp() {
    if(CCor_Cfg::get("csv-exp.bymail", true)) {
      //Mail Export
    }
    else {
      //File Export
      $lFileName = $this->mDom;

      //File
      header("Content-type: text/csv");
      header("Content-Disposition: attachement; filename=".$lFileName.".csv");
      flush();

      //Content
      $lCont = new CCor_Qry("SELECT * FROM al_pck_items WHERE domain = '".$this->mDom . "' AND mand IN ('0','".MID."')");
      $lCont = $lCont->getAssocs();

      foreach($lCont as $row) {
        $lRetCsv .= implode(';', array_filter($row)) . "\n";
      }

      echo $lRetCsv;
    }
  }

}
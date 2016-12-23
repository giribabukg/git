<?php
class CInc_Pck_Mod_Cnt extends CCor_Cnt {
  
  public static $mFields= Array(); //Picklist Columns
  public $mFieldCaptions = Array(); // Column Captions
  public $mFieldCount; // Column Count
  
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.items');
    $this -> mFields = Array();
    $lUsr = CCor_Usr::getInstance();
    $this -> mId  = $this -> getReq('id');
    $this -> mPag = new CUtl_Page();
    $this -> mFieldCount = 0; # for counter;
    
     /**
     * @var CPck_Mod_Cnt
     * Get Picklist Column
     */
    $lQry = new CCor_Qry('SELECT * FROM al_pck_columns WHERE pck_id='.$this->mId.' ORDER BY position');
    foreach ($lQry as $lRow) {
      $this-> mFields[] = $lRow;
        if ($lRow['hidden'] == 'N') $this -> mFieldCount++;
    }
    
    /**
     * @var CPck_Mod_Cnt
     * Get Picklist Column Caption
     */
    $lCaption = 'name_'.LAN;
    $lSql = "SELECT alias,$lCaption FROM al_fie WHERE mand=".MID." AND alias IN(";
    foreach ($this -> mFields as $lCol) {
       $lSql.= '"'.$lCol['alias'].'",';
    }
    $lSql = substr($lSql, 0, -1);
    $lSql.= ')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
       $this -> mFieldCaptions[$lRow['alias']] = $lRow[$lCaption];
    }
        
  }
  
  protected function actStd() {
    $lId  = $this -> getReq('id');
    $lIdx  = $this -> getReq('idx');
    $lMen = new CPck_Menu($lId, 'mod');
    $lVie = new CPck_Mod_List($lId,$lIdx,$this->mFields,$this->mFieldCaptions,$this->mFieldCount);
    $lRet = $lVie->addJs();
    
    //$this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    
    
    //$lPag = new CUtl_Page();
    $this-> mPag -> setPat('pg.cont', toStr($lVie));
    $this-> mPag -> setPat('pg.title', $this -> mTitle);
    $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $this-> mPag -> setPat('pg.js', $lRet);
    $this-> mPag -> render();
    
  }
 
  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lSid = $this -> getReq('sid');
     
    $lVie = new CPck_Mod_Form('pck-mod.sedt', lan('pck-col.edt'), 'pck-mod&id='.$this->mId,$this -> mFields, $this -> mFieldCaptions);
    $lVie -> setParam('id', $lId);
    $lVie -> setParam('sid', $lSid);
    
    $lVie -> load($lSid);
    
    //$lMen = new CPck_Menu($this -> mId , 'dat');
    //$this -> render(CHtm_Wrap::wrap($lMen, $lVie));
    
    $this-> mPag -> setPat('pg.cont', toStr($lVie));
    $this-> mPag -> setPat('pg.title', $this -> mTitle);
    $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $this-> mPag -> render();
    
}
  
  protected function actSedt() {
    $lId = $this -> getReqInt('id');
    $lSid = $this -> getReqInt('sid');
    
    $lMod = new CPck_Mod_Mod($this -> mFields, $this -> mFieldCaptions);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=pck-mod&id='.$this -> mId);
    $this -> redirect();
  }
  
  protected function actNew() {
    $lId = $this -> getReqInt('id');
   
    $lVie = new CPck_Mod_Form('pck-mod.snew', lan('pck-mod.new'), 'pck-mod&id='.$lId,$this -> mFields, $this -> mFieldCaptions,$lId);
    $lVie -> setParam('id', $lId);
    $lVie -> setVal('pck_id', $lId);
    $lVie -> setParam('mand', $lId);
    
   
    
    $this-> mPag -> setPat('pg.cont', toStr($lVie));
    $this-> mPag -> setPat('pg.title', $this -> mTitle);
    $this-> mPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $this-> mPag -> render();
    
    
    
  }
  
  protected function actSnew() {
    $lMod = new CPck_Mod_Mod($this -> mFields , $this -> mFieldCaptions);
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=pck-mod&id='.$this -> mId);
  }
  
  protected function actDel() {
    $lId = $this -> getReqInt('id');
    $lSid = $this -> getReqInt('sid');
    $lSql = 'DELETE FROM al_pck_items WHERE id='.$lSid;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=pck-mod&id='.$lId);
    
  }
      
}
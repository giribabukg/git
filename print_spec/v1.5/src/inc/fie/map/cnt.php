<?php
class CInc_Fie_Map_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('fie-map.menu');
    $this -> mMmKey = 'opt';
  
    $lpn = 'fie-map';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      //$this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lVie = new CFie_Map_List();
    $this -> render($lVie);
  }
  
  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lFrm = new CFie_Map_Form('fie-map.sedt', 'Edit Map');
    $lFrm->load($lId);
    $this -> render($lFrm);
  }
  
  protected function actSedt() {
    $lMod = new CFie_Map_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }
  
  protected function actNew() {
    $lFrm = new CFie_Map_Form('fie-map.snew', 'New Map');
    $this -> render($lFrm);
  }
  
  protected function actSnew() {
    $lMod = new CFie_Map_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('mand', MID);
    $lMod -> insert();
    $this -> redirect();
  }
  
  protected function actDel() {
    $lId = $this -> getInt('id');
    $lMod = new CFie_Map_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }
  
  protected function actSub() {
    $lMapId = $this->getInt('id');
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }
  
  protected function actDelitem() {
    $lMapId = $this->getInt('mid');
    $lId = $this->getInt('fid');
    
    $lSql = 'DELETE FROM al_fie_map_items WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }
  
  protected function actAddJobFields() {
    $lRet = '';
    $lRet.= '<select size="20" multiple="multiple">';
    $lFie = CCor_Res::getByKey('alias', 'fie');
    ksort($lFie);
    foreach ($lFie as $lKey => $lRow) {
      $lRet.= '<option value="'.htm($lKey.','.$lRow['native']).'">';
      $lRet.= htm($lKey.' ('.$lRow['name_en'].')');
      $lRet.= '</option>';
    }
    $lRet.= '</select>';
    echo $lRet;
    exit;
  }
  
  protected function actSaddjobfields() {
    $lMapId = $this->getInt('mid');
    $lVal = $this->getReq('val');
    
    $lMod = new CFie_Map_Item_Mod();
    foreach ($lVal as $lItem) {
      list($lAlias, $lNative) = explode(',', $lItem);
      $lMod->setVal('map_id', $lMapId);
      $lMod->setVal('alias', $lAlias);
      $lMod->setVal('native', $lNative);
      $lMod->insert();
    }
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }
  
  protected function actNewitem() {
    $lForm = new CFie_Map_Item_Form();
    echo $lForm->getForm();
    exit;
  }
  
  protected function actSnewitem() {
    $lMapId = $this->getInt('mid');
    $lVal = $this->getReq('val');
    $lMod = new CFie_Map_Item_Mod();
    $lMod->setVal('map_id', $lMapId);
    //var_dump($lVal); exit;
    foreach ($lVal as $lKey => $lValue) {
      $lMod->setVal($lKey, $lValue);
    }
    $lMod->insert();
    
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }
  
  protected function actEdititem() {
    $lId = $this->getInt('fid');
    $lForm = new CFie_Map_Item_Form();
    $lForm->load($lId);
    echo $lForm->getForm();
    exit;
  }
  
  protected function actSedititem() {
    $lMapId = $this->getInt('mid');
    $lFieldId = $this->getInt('fid');
    $lVal = $this->getReq('val');
    $lMod = new CFie_Map_Item_Mod();
    $lMod->forceVal('id', $lFieldId);
    //var_dump($lVal); exit;
    foreach ($lVal as $lKey => $lValue) {
      $lMod->forceVal($lKey, $lValue);
    }
    $lMod->update();
  
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }
  
  
}

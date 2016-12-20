<?php
class CInc_Pck_Mod_List extends CHtm_List {
  
  public $mColCount; // Column Count
  public $mId; //Pickliste Source
  public $mArray2Show= Array(); // Columns to show (not hidden)
  public $mCol = Array(); //Columns Info
  public $mFieldCaption= Array(); //Field Captions
  public $lEintraege = Array(); //Picklist Entries
  public $lColoumnId = Array(); //Picklist used Columns
  
  
  public function __construct($aId,$aIdx='',$aFields,$aFieldCaptions,$aFieldCount) {
    
    parent::__construct('pck-col');
     $this -> mId = intval($aId);
     $this -> mIdx = intval($aIdx);
     $this -> mCol = $aFields;
     $this -> mCol_link = $aFields;
     
     $this -> mColCount = $aFieldCount;
     $this -> mFieldCaption = $aFieldCaptions;
     
     $this -> setAtt('width', '800px');
     $this -> mTitle = lan('lib.items');
     
     $this -> getPriv('pck');
      
     $this -> mDelLnk = 'index.php?act=pck-mod.del&amp;id='.$this -> mId.'&amp;sid=';
     $this -> mNewLnk = 'index.php?act=pck-mod.new&amp;id='.$this -> mId.'&amp;typ=';
     
     $this -> addCtr();
     
    
    foreach ($this->mCol as $lRow){
      if ($lRow['hidden']=='N'){
        $this-> mArray2Show = $lRow;
        $this -> addColumn('col'.$lRow['col'], $this->mFieldCaption[$lRow['alias']]);
      }
    }
    
    if ($this -> mCanEdit) {
       $this -> addColumn('edt', '', FALSE, array('width' => '16'));
    }
         
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    
    $this -> addJs();
       
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('pck-mod.new'), "go('index.php?act=pck-mod.new&id=".$this -> mId."')", 'img/ico/16/plus.gif');
    }
        
    # $this -> addPanel('ser', $this -> getSearchForm());
    $this -> getPrefs();
    
       
    $this -> mIte = new CCor_TblIte('al_pck_items');
    $this -> mIte -> addCnd('pck_id='.$this -> mId);
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    
      
    
    $this -> lEintraege = $this-> mIte ->getArray();
    $lTempArray = array();
    
    foreach ($this->lEintraege as $lRow){
      $lTempArray[$lRow[id]] = $lRow;
      }
    $this->lEintraege = $lTempArray;
    
    $lTempArray = array();
    foreach ($this->mCol as $lRow){
    $lTempArray[$lRow[col]] = $lRow[col];
    }
    $this -> lColoumnId = $lTempArray;
   
    }
  
  protected function getLink() {
    $lId = $this -> getVal($this -> mIdField);
    $lLnk = "javascript:setVal(";
     
      foreach($this -> lColoumnId as $lRow) {
            $lVar = 'col'.$lRow;
            $lVal = $this-> lEintraege[$lId][$lVar];
            $lVal = htmlentities($lVal);
            // Spez. Zeichenersetzung:
            // - Leerzeichen durch %20
            // - Linefeed durch doppelt Escaped LF (\n)
            // - Cariage Return durch nichts
            $lVal = strtr($this-> lEintraege[$lId][$lVar], array(' ' => '%20', "\n" => '\\n', "\r" => ''));
            $lLnk.= '\''.$lVal.'\',';
          }
          $lLnk = substr($lLnk, 0, -1);
          $lLnk.= ");";
          return $lLnk;
   }
  
  protected function getTdCol3() {
    if ($this->mArray2Show['color'] == 'Y' ){
        $lVal = $this -> getVal($this -> mColKey);
        $lRet= '<td  class="'.$this -> mCls.'" width="45">';
        $lRet.= "<div style=\"background-color:#".$lVal."\">";
        $lRet.= "<a href='#' style=\"display:block\">";
        $lRet.= "<img src=\"img/d.gif\" width=\"16\" border=0 />";
        $lRet.= "</a></div></td>".LF;
        
      } else {
        //$this -> addColumn('edt', '', FALSE, array('width' => '16'));
        $lRet = $this -> td($this -> a(htm('col3')));
      }
      return $lRet;
    }
  
  	/**
  	* @TODO : Search Function for Picklist
	*/
  
  /*
  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="pck-mod.ser" />'.LF;
    $lRet.= '<input type="hidden" name="act" value="pck-mod.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input id="lis_ser" type="text" name="val[msg]" class="inp w200" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=pck-mod.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;
    
    $lPag = CHtm_Page::getInstance();
    

    return $lRet;
   }
   */
  
  
  protected function getTdEdt() {
    $lSid = $this -> getInt('id');
    $lRet = '<a class="nav" href="index.php?act=pck-mod.edt&id='.$this->mId.'&amp;sid='.$lSid.'">';
    $lRet.= img('img/ico/16/edit.gif').'</a>';
    return $this -> td($lRet);
  }
  

  public function addJs(){
    
    $lRet = '<script type="text/javascript"><!--'.LF;
    $lRet.= "function getFormElement(aName) {\n";
    
    
    $lRet.= "obj = window.opener.frm.elements[aName];\n";
    if (!empty($this -> srcFieldSuffix)) {
        $lRet.= "if (obj) {\n";
    }
    $lRet.= "  return obj;\n";
    
    if (!empty($this -> srcFieldSuffix)) {
      $lRet.= "} else {\n";
      $lRet.= "return window.opener.frm.elements[aName.slice(0,-1) + '".$this -> srcFieldSuffix."' + aName.slice(-1)];\n";
      $lRet.= "}\n";
    }
    $lRet.= "}\n";
    //$lRet='';
    $lRet.= "function setVal(";
        
        #foreach ($this -> col as $_key => $_col) {
    foreach ($this ->mCol as $lKey => $lValue) {
      $lRet.= 'col'.$lKey.',';
    }
    $lRet = substr($lRet,0 , -1);
    $lRet.= ") {\n";
    
    $lRet.= "var obj;\n";
    #foreach($this -> col as $_key => $_col) {
    
   
    foreach($this -> mCol as $lKey => $lValue) {
      $lAlias = $lValue['alias'];
      $lRet.= "obj = getFormElement('val[$lAlias]'); ";
      $lRet.= "if ((obj) && (obj.value != undefined)) obj.value=col$lKey;\n";
      }
      
      // if Color Picklist set Back-Color
      if (!empty($this -> mIdx)) {
         if ($lValue['image'] == 'Y'){// not_used
           $lRet.= "window.opener.document.getElementById(\"$lAlias\").src= 'mand/mand_'.MID.'/files/$lValue->alias/'+col2;\n";
          
         }else {
           $lRet.= "window.opener.document.getElementById(\"cdiv$this->mIdx\").style.background='#'+col1;\n";
           $lRet.= "window.opener.colChanged(\"$this->mIdx\");\n";
         }
       }
        
        $lRet.= "window.close();\n";
        $lRet.= "}\n";
        $lRet.= '--></script>'.LF;
        
        //$lPag = CHtm_Page::getInstance();
        //$lPag = new CUtl_Page();
        
        //$lPag -> addJs($lRet);
        return $lRet;
  }
  
    
} //end_Class
<?php
class CInc_Pck_Itm_List extends CHtm_List {

  public $mIdx = ''; //Color-Pickliste: Farbnummer
  public $mCpp = 1; ////Color Printer Passes / Druckdurchgänge
  public $mDom = ''; //Pickliste Source
  public $mColumns = Array(); //Pickliste: Columns Info
  public $mFieldCaption= Array(); //Pickliste:Field Captions
  public $mItems = Array(); //Picklist Entries
  protected $mColoumnId = Array(); //Picklist used Columns
  protected $mColor = '';
  protected $mColorColumn = 1;
  public $mUrl_Param = '';
  public $mAdminView = '';

  public function __construct($aDom, $aFields, $aFieldCaptions, $aIdx = '', $aCpp = 1, $aAdminView = FALSE) {

    $this -> mAdminView = $aAdminView;
    $this -> mDom = $aDom;
    $this -> mIdx = $aIdx;
    $this -> mCpp = $aCpp;

    $lUrl_Param = 'dom='.$this -> mDom;
    if ($this -> mAdminView) {
      $lUrl_Param.= '&xx=1';
    }
    $lUrl_Param.= '&idx='.$this -> mIdx;
    $lUrl_Param.= '&cpp='.$this -> mCpp;

    $this -> mUrl_Param = $lUrl_Param;
    $this -> mStdLink = 'index.php?'.$this -> mUrl_Param.'&act=pck-itm';

    parent::__construct('pck-itm', 'pck'); // 'pck' == Recht zu Modifizieren etc.

    $this -> mColumns = $aFields;
    $this -> mFieldCaption = $aFieldCaptions;
    $lCountColumns = count($this -> mColumns) - 1;

    $lPckMst = CCor_Res::extract('domain', 'description_'.LAN, 'pckmaster');
    $this -> mName = $lPckMst[$this -> mDom];
    $this -> mTitle = lan('pck-itm.menu').' - '.htm($this -> mName);
    $this -> getPriv('pck');

    $this -> addCtr(); // automatische Nummerierung
    foreach ($this -> mColumns as $lKey => $lRow){
      if (1 == $lRow['position']) {
        // Definiere, daß dann das Alias = co1_nr_1 entspricht!
        $this -> mColorNumber = $lRow['alias'];
      }
      if ($lRow['hidden']=='N'){
        if ('Y' == $lRow['color']) {
          $this -> mColor = 'col'.$lRow['col'];
          $this -> mColorColumn = $lRow['position'] - 1; // Nummerierung beginnt bei 0
        }
        if ('Y' == $lRow['image']) {
          $this -> mImage = 'col'.$lRow['col'];
          $this -> mImageAli = $lRow['alias'];
          $this -> mImageColumn = $lRow['position'] - 1; // Nummerierung beginnt bei 0
        }

        if ($lKey == $lCountColumns) {
          $lHtmAtt = array('width' => '100%'); //'letzte' Spalte breiter machen-> vordere Spalten schmal
        } else $lHtmAtt = array();
        $this -> addColumn('col'.$lRow['col'], $this -> mFieldCaption[$lRow['alias']], TRUE, $lHtmAtt);
      }
    }
    #$this -> mDefaultOrder = 'value_'.LAN;

    if ($this -> mCanEdit) {
      $this -> addColumn('edt', '', FALSE, array('width' => '16'));
    }
    if ($this -> mCanInsert) {
      $this -> addColumn('cpy',  '', FALSE, array('width' => '16'));
    }
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    if ($this -> mCanInsert) {
      // wenn hier $this -> mStdLink genutzt wird -> LOGIN-Seite <- falsche Parameterreihenfolge!
      $this -> addBtn(lan('lib.new_item'), "go('index.php?act=pck-itm.new&".$this -> mUrl_Param."')", '<i class="ico-w16 ico-w16-plus"></i>');
      $this -> addBtn(lan("lib.addMultiple"), "go('index.php?act=pck-itm.batchNew&".$this -> mUrl_Param."')", '<i class="ico-w16 ico-w16-plus"></i>');
    }
    $this -> addBtn(lan("csv-exp"),"go('index.php?act=pck-itm.csvexp&".$this -> mUrl_Param."')",'<i class="ico-w16 ico-w16-txtfile"></i>');
    $lPrintingDataPicklistDomain = CCor_Cfg::get('printing.data.picklist.domain', 'prnt');
    if ($this -> mDom == $lPrintingDataPicklistDomain) {
      // wenn hier $this -> mStdLink genutzt wird -> LOGIN-Seite <- falsche Parameterreihenfolge!
      $this -> addBtn('Get Values', "go('index.php?act=pck-itm.printspecabgleich&dom=".$this -> mDom."')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    #$this -> addJs();

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_pck_items');
    $this -> mIte -> addCnd('domain='.esc($this -> mDom));
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lCndArr = array();
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        foreach ($this -> mColumns as $lRow){
          if ($lRow['hidden']=='N'){
            $lCndArr[]= 'col'.$lRow['col'].' LIKE '.$lVal;
          }
        }
        $lCnd = implode(' OR ',$lCndArr);
        $lCnd = '('.$lCnd.')';
        $this -> mIte -> addCnd($lCnd);
      }
    }
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('ser', $this -> getSearchForm());// Suchfeld muß nach $this -> mSer aufgerufen werden
    $this -> addPanel('vie', $this -> getViewMenu()); //Optionen
    $this -> addPanel('nav', $this -> getNavBar());
    ####
    #$this -> mIte = new CCor_TblIte('al_pck_items');
    #$this -> mIte -> addCnd('pck_id='.$this -> mId);
    #$this -> mIte -> addCnd('mand IN(0,'.MID.')');


    $this -> mItems = $this -> mIte -> getArray();
    $lTempArray = array();

    foreach ($this -> mItems as $lRow){
      $lTempArray[$lRow['id']] = $lRow;
    }
    $this -> mItems = $lTempArray;

    $lTempArray = array();
    foreach ($this -> mColumns as $lRow){
      $lTempArray[$lRow['col']] = $lRow['col'];
    }
    $this -> mColoumnId = $lTempArray;
  }

  protected function getLink() {
    if (!$this -> mAdminView) {
      $lId = $this -> getVal($this -> mIdField);
      $lLnk = "javascript:setValCol(";

      foreach($this -> mColoumnId as $lRow) {
        $lVar = 'col'.$lRow;
        $lVal = $this -> mItems[$lId][$lVar];
        $lVal = htmlentities($lVal);
        // Spez. Zeichenersetzung:
        // - Leerzeichen durch %20
        // - Linefeed durch doppelt Escaped LF (\n)
        // - Cariage Return durch nichts
        $lVal = strtr($this -> mItems[$lId][$lVar], array(' ' => '%20', "\n" => '\\n', "\r" => '',"'" => "\'"));
        $lLnk.= '\''.$lVal.'\',';
      }
      $lLnk = substr($lLnk, 0, -1);
      $lLnk.= ");";
    } else {
      $lId = $this -> getInt('id');
      $lLnk = $this -> mStdLnk.$lId;
    }
    return $lLnk;
  }

  protected function getTdColor() {
    $lVal = $this -> getVal($this -> mColKey);
    $lRet = '<td  class="'.$this -> mCls.'" width="45">';
    $lRet.= '<div style="background-color:#'.$lVal."\">";
    $lRet.= '<a href="'.$this -> getLink().'" style="display:block">';
    $lRet.= '<img src="img/d.gif" width="16" border="0" />';
    $lRet.= "</a></div></td>".LF;

    return $lRet;
  }

  protected function getTdImage() {
    $lVal = $this -> getVal($this -> mColKey);
    $lRet = '<td  class="'.$this -> mCls.'" width="45">';
    $lRet.= '<a href="'.$this -> getLink().'" style="display:block">';
    $lRet.= '<img src="mand/mand_'.MID.'/files/'.$this -> mImageAli.'/col'.$this -> mImageColumn.'/'.$lVal.'" width="16" border="0" />';
    $lRet.= "</a></td>".LF;
    return $lRet;
  }

  protected function getColTd() {
    if ($this -> mColor == $this -> mColKey) {
      return $this -> getTdColor();
    }
    if ($this -> mImage == $this -> mColKey) {
      return $this -> getTdImage();
    }
    $lFnc = 'getTd'.$this -> mColKey;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc();
    }
    $lCurCol = & $this -> getColumn($this -> mColKey);
    if (NULL !== $lCurCol) {
      $lTyp = $lCurCol -> getFieldAttr('typ');
      if (!empty($lTyp)) {
        $lFnc = 'getTdTyp'.$lTyp;
        if ($this -> hasMethod($lFnc)) {
          return $this -> $lFnc();
        }
      }
    }
    $lRet = $this -> td($this -> a(htm($this -> getVal($this -> mColKey))));
    return $lRet;
  }

  /**
  	* @TODO : Search Function for Picklist
  	*/
  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="pck-itm.ser" />'.LF;
    $lRet.= '<input type="hidden" name="dom" value="'.$this -> mDom.'" />'.LF;
    $lRet.= '<input type="hidden" name="idx" value="'.$this -> mIdx.'" />'.LF;
    $lRet.= '<input type="hidden" name="cpp" value="'.$this -> mCpp.'" />'.LF;
    if ($this -> mAdminView) {
    	$lRet.= '<input type="hidden" name="xx" value="1" />'.LF;
    }

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=pck-itm.clser&'.$this -> mUrl_Param.'")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }

    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp);
    $lNav -> setParam('dom', $this -> mDom);
    $lNav -> setParam('idx', $this -> mIdx);
    $lNav -> setParam('cpp', $this -> mCpp);
    if ($this->mAdminView) {
      $lNav -> setParam('xx', 1);
    }
    return $lNav -> getContent();
  }

  protected function getTdEdt() {
    if (empty($this -> mStdLnk)) {
      return '';
    } else {
      $lId = $this -> getInt('id');
      $lRet = '<a class="nav" href="'.$this -> mStdLnk.$lId.'">';
      $lRet.= '<i class="ico-w16 ico-w16-edit"></i></a>';
      return $this -> td($lRet);
    }
  }

  public function addJs(){

    $lRet = '<script type="text/javascript"><!--'.LF;

    $lRet.= "function getFormElement(aName) {\n";
    $lRet.= "  obj = window.opener.frm.elements[aName];\n";
    $lRet.= "  return obj;\n";
    $lRet.= "}\n";
    $lRet.= "\n";

    $lRet.= "function resetCol(aColPP,aIdx)";
    $lRet.= "{\n";
    $lRet.= "try {\n";

    $lAliasArr = array();
    $lColNumberExplode = '';
    foreach($this -> mColumns as $lKey => $lValue) {
      $lAlias = $lValue['alias'];
      $AliSubstr = substr($lAlias,0,-1);
      $AliExplode = explode(1, $AliSubstr, 2);
      if ($this -> mColorNumber == $lValue['alias']) {
        $lColNumberExplode = $AliExplode;
      }
      if (1 < $this -> mCpp) {
        $lAlias = preg_replace('/1/', $this -> mCpp, $lAlias, 1);
      }
      if (!empty($this -> mIdx)) {
        $lAlias = substr($lAlias,0,-1).$this -> mIdx; // Farbe_1, Farbe_2, ...
      }
      $lAliasArr[$lKey] = $lAlias;

      $lAliExplode_1 = isset($AliExplode[1]) ? $AliExplode[1] : '';
      $lRet.= "  setJobFrmVal('".$AliExplode[0]."' + aColPP + '".$lAliExplode_1."'  + aIdx, '');\n"; // reset von allen Feldern!
    }
    $lRet.= "  document.getElementById('c' + aColPP + 'div' + aIdx).style.background='#EEE';\n";
    $lRet.= "} catch(e) {}\n";
    $lRet.= "}\n";
    $lRet.= "\n";
    /* --> So'ne SCH...., funktioniert nur hardcodiert in std.js! :(
     $lRet.= "function colChanged(aColPP,aIdx) {\n";
    $lRet.= "try {\n";
    $lRet.= "  var val = getJobFrmVal('".$lColNumberExplode[0]."' + aColPP + '".$lColNumberExplode[1]."'  + aIdx);\n";
    $lRet.= "  if (val == '') {\n";
    $lRet.= "    resetCol(aColPP,aIdx);\n";
    $lRet.= "  }\n";
    $lRet.= "} catch(e) {}\n";
    $lRet.= "}\n";
    $lRet.= "\n";
    */
    $lRet.= "function setValCol(";
    foreach ($this -> mColumns as $lKey => $lValue) {
      //z.B. (col0,col1,col2)
      $lRet.= 'col'.$lKey.',';
    }
    $lRet = substr($lRet,0 , -1);
    $lRet.= ") {\n";

    $lRet.= "var obj;\n";

    foreach($this -> mColumns as $lKey => $lValue) {
      $lRet.= "  obj = getFormElement('val[".$lAliasArr[$lKey]."]'); ";
      $lRet.= "if ((obj) && (obj.value != undefined)) obj.value=col$lKey;\n";
    }

    // if Color Picklist set Back-Color
    if (!empty($this -> mIdx)) {
      if ('Y' == $lValue['image']){
        $lRet.= "window.opener.document.getElementById(\"".$this -> mImageAli."\").src= 'mand/mand_".MID."/files/".$this -> mImageAli."/col".$this -> mImageColumn.";\n";

      }else {
        $lRet.= 'window.opener.document.getElementById("c'.$this -> mCpp.'div'.$this -> mIdx."\").style.background='#'+col".$this -> mColorColumn.";\n"; //
        $lRet.= 'window.opener.colChanged("'.$this -> mCpp.'","'.$this -> mIdx.'");'."\n";
      }
    }

    $lRet.= "window.close();\n";
    $lRet.= "}\n";
    $lRet.= "\n";
    $lRet.= '--></script>'.LF;
    return $lRet;
  }


  protected function getTdCpy() {
    $lCod = $this -> getVal('domain');
    $lSid = $this -> getInt('id');
    $lRet = '<a href="index.php?act=pck-itm.copy&'.$this -> mUrl_Param.'&id='.$lSid.'" class="nav">';
    $lRet.= '<i class="ico-w16 ico-w16-copy"></i>';
    $lRet.= '</a>';
    return $this -> td($lRet);
  }


} //end_Class
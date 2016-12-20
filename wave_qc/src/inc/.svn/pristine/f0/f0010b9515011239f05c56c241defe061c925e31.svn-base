<?php
class CInc_Api_Pdf_Report extends CCor_Obj {
  
  public function __construct($aOpt, $aRep) {
    $this -> mOpt = $aOpt;
    $this -> mYear = (isset($aOpt['year'])) ? intval($aOpt['year']) : date('Y');
    switch ($aRep) {
      case 'pro':
        $this -> mIte = new CRep_Pro_Ite($this -> mYear);
        break;
      case 'pde':
        $this -> mIte = new CRep_Pde_Ite($this -> mYear);
        break;
      case 'mba':
        $this -> mIte = new CRep_Mba_Ite($this -> mYear);
        break;
      case 'tpl':
        $this -> mIte = new CRep_Tpl_Ite($this -> mYear);
        break;
      case 'pac':
        $this -> mIte = new CRep_Pac_Ite($this -> mYear);
        break;
    }
    unset($aOpt['year']);
    if (!empty($aOpt)) {
      $this -> addSearchCond($aOpt);
    }

    require_once 'Zend/Pdf.php';
    require_once 'extpdf.php';
    $this -> mDoc = new Zend_Pdf();
    
    $this -> mCapStyle = new Zend_Pdf_Style();
    $lFon = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD); 
    $this -> mCapStyle -> setFont($lFon, 24);

    $this -> mChaStyle = new Zend_Pdf_Style();
    $lFon = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA); 
    $this -> mChaStyle -> setFont($lFon, 10);
    
    $this -> mTxtStyle = new Zend_Pdf_Style();
    $lFon = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER); 
    $this -> mTxtStyle -> setFont($lFon, 10);

    $this -> mCol = array();
    $lUsr = CCor_Usr::getInstance();
    $lCol = $lUsr -> getPref('rep-'.$aRep.'.cols');
    $this -> dbg($lCol, mlWarn);
    switch ($aRep) {
      case 'pro':
        $lObj = new CRep_Pro_Fields();
        break;
      case 'pde':
        $lObj = new CRep_Pde_Fields();
        break;
      case 'mba':
        $lObj = new CRep_Mba_Fields();
        break;
      case 'tpl':
        $lObj = new CRep_Tpl_Fields();
        break;
      case 'pac':
        $lObj = new CRep_Pac_Fields();
        break;
    }
    $lArr = $lObj -> getFields();
    #$this -> dbg(var_export($lArr, TRUE), mlWarn);
    if (empty($lCol)) {
      $this -> addCol('count', 'Jobs');
    } else {
      $lCol = explode(',', $lCol);
      foreach ($lCol as $lAli) {
        if (isset($lArr[$lAli])) {
          $this -> addCol($lAli, $lArr[$lAli]);
        }
      } 
    }
  }
  
  protected function addCol($aAlias, $aCaption) {
    $this -> mCol[$aAlias] = $aCaption;
  }
    
  protected function addSearchCond($aOpt) {
    $this -> mFie = CCor_Res::get('fie', 'pro,tpl,pac');
    foreach ($aOpt as $lKey => $lVal) {
      if ('' == $lVal) {
        continue;
      } 
      if ('webstatus' == $lKey) {
        $lSta = intval($lVal);
        $this -> mIte -> addCnd($lKey.' BETWEEN '.$lSta.' AND '.($lSta +9));
        continue;
      } 
      foreach ($this -> mFie as $lDef) {
        $lAli = $lDef['alias'];
        if ($lAli == $lKey) {
          if ($lAli == 'col_1') {
            $lSql = '(';
            for ($i = 1; $i < 11; $i++) {
              $lSql.= 'col_'.$i.' LIKE "%'.addslashes($lVal).'%" OR ';
            } 
            $lSql = strip($lSql,4).')';
            $this -> mIte -> addCnd($lSql);
            continue 2;
          }
          $lTyp = $lDef['typ'];
          switch ($lTyp) {
            case 'uselect' :
            case 'gselect' :
            case 'resselect' :
            case 'valselect' :
            case 'tselect' :
              $this -> mIte -> addCnd($lKey.' = "'.addslashes($lVal).'"');
              BREAK;
            default:
              $this -> mIte -> addCnd($lKey.' LIKE "%'.addslashes($lVal).'%"');
              BREAK;
          }
          BREAK;
        }
      }
    }
  }
  
  protected function fmtVal($aAlias, $aValue) {
    $lRet = $aValue;

    $lIsCos = substr($aAlias, 0, 4);
    if ($lIsCos == 'cos_')
        $lRet = fmtCur($lRet).' €  ';

/*
    switch ($aAlias) {
      case 'cos_pac':
        $lRet = fmtCur($lRet).' €  ';
        BREAK;
      case 'cos_pde':
        $lRet = fmtCur($lRet).' €  ';
        BREAK;
    }
*/

    return $lRet;
  }  

  protected function calculateTextWidth($aStr, $aFnt, $aFntSiz) {
    $lFnt = $aFnt -> getFont();
    $lFntSiz = $aFntSiz -> getFontSize();
    $lStr = iconv('UTF-8', 'UTF-16BE//IGNORE', $aStr);
    $lChars = array();

    for ($lDum = 0; $lDum < strlen($lStr); $lDum++) {
      $lChars[] = (ord($lStr[$lDum++]) << 8) | ord($lStr[$lDum]);
    }

    $lGlyphs = $lFnt->glyphNumbersForCharacters($lChars);
    $lWidths = $lFnt->widthsForGlyphs($lGlyphs);
    $lRes = (array_sum($lWidths) / $lFnt->getUnitsPerEm()) * $lFntSiz;

    return $lRes;
  }

  protected function renderPage($aAlias, $aCaption) {
    $lPag = new Ext_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

    $lPagWid = $lPag->getWidth();
    $lPagHei = $lPag->getHeight();

    $lCapTxt = 'Packaging '.$aCaption.' '.$this -> mYear;
    $lCapWidth = $this->calculateTextWidth($lCapTxt, $this -> mCapStyle, $this -> mCapStyle);

    $this -> mDoc -> pages[] = $lPag;

    $lPag -> setStyle($this -> mCapStyle);
    $lPag -> drawText($lCapTxt, ($lPagWid / 2) - ($lCapWidth / 2), 800);
    
    $lDat = $this -> mIte -> getRows($aAlias);
    
    $lPag -> setStyle($this -> mChaStyle);
    $lChart = new CApi_Pdf_Chart($lPag, $aCaption);
    $lChart -> setData($lDat);
    $lChart -> draw(165, 745, 260, 200);

    $lTotal = 0;
    foreach ($lDat as $lKey => $lVal) {
      $lTotal += $lVal;
    }

    $table = new Ext_Pdf_Table($lPag, 45, 400);
    $row = new Ext_Pdf_Table_Row();

    $col = new Ext_Pdf_Table_Column();
    $col -> setWidth(45) -> setText('Period');
    $row -> addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col -> setWidth(145) -> setText($aCaption);
    $row -> addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col -> setWidth(145) -> setText('YTD');
    $row -> addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col -> setWidth(145) -> setText('Percent');
    $row -> addColumn($col);

    $table->addRow($row);

    $lSum = 0;
    $lArr = array();
    foreach ($lDat as $lKey => $lVal) {

      $lItm = array();
      $lItm['period'] = $lKey;
      $lDiv = $this -> fmtVal($aAlias, $lVal); // display value
      $lItm[$aAlias]  = $lDiv;
      $lSum+= $lVal;
      if ($lTotal > 0)  {
        $lPer = $lVal * 100 / $lTotal;
        $lPer = fmtCur($lPer).' %';
      } else {
        $lPer = '0,00 %';
      }
      $lItm['per'] = $lPer;
      $lDiv = $this -> fmtVal($aAlias, $lSum); // display value
      $lItm['ytd'] = $lDiv;
      $lArr[] = $lItm;

      $row = new Ext_Pdf_Table_Row();

      $col = new Ext_Pdf_Table_Column();
      $col->setWidth(45)->setText($lItm['period'])->setAlignment('right');
      $row->addColumn($col);

      $col = new Ext_Pdf_Table_Column();
      $col->setWidth(145)->setText($lItm[$aAlias])->setAlignment('right');
      $row->addColumn($col);

      $col = new Ext_Pdf_Table_Column();
      $col->setWidth(145)->setText($lItm['ytd'])->setAlignment('right');
      $row->addColumn($col);

      $col = new Ext_Pdf_Table_Column();
      $col->setWidth(145)->setText($lItm['per'])->setAlignment('right');
      $row->addColumn($col);

      $table->addRow($row);
    }

    $lItm = array();
    $lItm['period'] = 'Total';
    $lDiv = $this -> fmtVal($aAlias, $lSum); // display value
    $lItm[$aAlias]  = $lDiv;
    $lItm['per']    = '100,00 %';    
    $lItm['ytd']    = $lDiv;    

    $row = new Ext_Pdf_Table_Row();

    $col = new Ext_Pdf_Table_Column();
    $col->setWidth(45)->setText($lItm['period']);
    $row->addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col->setWidth(145)->setText($lItm[$aAlias])->setAlignment('right');
    $row->addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col->setWidth(145)->setText($lItm['ytd'])->setAlignment('right');
    $row->addColumn($col);

    $col = new Ext_Pdf_Table_Column();
    $col->setWidth(145)->setText($lItm['per'])->setAlignment('right');
    $row->addColumn($col);

    $table->addRow($row);

    $lPag = $table->render();
  }
    
  
  public function render() {
    foreach ($this -> mCol as $lAli => $lCaption) {
      $this -> renderPage($lAli, $lCaption);
    }  
    
    header('Content-type: application.pdf');
    header('Content-Disposition: attachment; filename="Report.pdf"');
    echo $this -> mDoc -> render();
  }
}
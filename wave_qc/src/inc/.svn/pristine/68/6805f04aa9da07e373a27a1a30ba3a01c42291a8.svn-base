<?php 
require_once 'Spreadsheet/Excel/Writer.php';

class CInc_Api_Excel_Writer extends CCor_Obj {
  
  protected $mWorkbook;
  protected $mSheet;
  protected $mSheets;
  protected $mCurFmt;
  protected $mCurFmtKey;
  protected $mCurCol = 0;
  protected $mCurRow = 0;
  
  public function __construct($aFilename, $aSheet = NULL) {
    $this -> mFilename = $aFilename;
    $this -> mWorkbook = & new Spreadsheet_Excel_Writer();
    $this -> mWorkbook -> setVersion(8);
    
    $this -> mSheets = array();
    $this -> mFormats = array();
    $this -> mCurFmt = $this -> addFmt('std');
    $this -> mCurFmtKey = 'std';
    
    $this -> addFmt('th1', 48, 'white', true); 
    $this -> addFmt('th2', 44, 'black', true); 
    $this -> addFmt('td1'); 
    $this -> addFmt('td2', 'silver'); 
    $this -> addFmt('td1r', NULL, NULL, NULL, 'right');
    $this -> addFmt('th2r', 44, 'black', true, 'right'); 
    
    if (NULL !== $aSheet) {
      $this -> addSheet($aSheet, 'tab1');
    }
  }
  
  public function & addSheet($aName, $aKey = NULL) {
    $lSheet = & $this -> mWorkbook -> addWorksheet($aName);
    $lKey = (NULL === $aKey) ? 'tab'.(count($this -> mSheets) + 1) : $aKey;
    $this -> mSheets[$lKey] = & $lSheet;
    $lSheet -> setLandscape();
    $this -> mSheet = & $lSheet;
    $this -> gotoCell(0,0);
    return $lSheet;
  }
  
  public function useSheet($aKey) {
    if (isset($this -> mSheets[$aKey])) {
      $this -> mSheet = & $this -> mSheets[$aKey];
      $this -> gotoCell(0,0);
    }
  }
  
  public function & addFmt($aKey, $aBgColor = NULL, $aFgColor = NULL, $aBold = FALSE, $aHAli = NULL, $aVAli = NULL) {
    $lRet = & $this -> mWorkbook -> addFormat();
    $lRet -> setFontFamily('Arial');
    $lRet -> setSize('8');
    if (NULL !== $aBgColor) {
      $lRet -> setFgColor($aBgColor);
    }
    if (NULL !== $aFgColor) {
      $lRet -> setColor($aFgColor);
    }
    if ($aBold) {
      $lRet -> setBold();
    }
    if (NULL !== $aHAli) {
      $lRet -> setHAlign($aHAli);
    }
    if (NULL !== $aVAli) {
      $lRet -> setVAlign($aVAli);
    }
    $this -> mFormats[$aKey] = & $lRet;
    return $lRet; 
  }

  public function getFmt($aKey) {
    return (isset($this -> mFormats[$aKey])) ? $this -> mFormats[$aKey] : NULL; 
  }
  
  public function useFmt($aKey) {
    $lKey = (isset($this -> mFormats[$aKey])) ? $aKey : 'std';
    $this -> mCurFmt = $this -> mFormats[$lKey];
    $this -> mCurFmtKey = $lKey;
  }
  
  public function getCurFmtKey() {
    return $this -> mCurFmtKey;
  }
  
  public function setCell($aColumn, $aRow, $aText, $aFmt = NULL) {
    $lFmt = (NULL == $aFmt) ? $this -> mCurFmt : $this -> getFmt($aFmt);
    $this -> mSheet -> write($aRow, $aColumn, $aText, $lFmt);    
  }
  
  public function gotoCell($aColumn, $aRow = NULL) {
    $this -> mCurCol = intval($aColumn);
    if (NULL !== $aRow) {
      $this -> mCurRow = intval($aRow);
    }
  }
  
  public function write($aText) {
    $this -> mSheet -> write($this -> mCurRow, $this -> mCurCol, $aText, $this -> mCurFmt);
    $this -> mCurCol++;    
  }
  
  public function newLine($aRows = 1) {
    $this -> mCurCol = 0;
    $this -> mCurRow+= $aRows;
  }
  
  public function send() {
    $this -> mWorkbook -> send($this -> mFilename);
    $this -> mWorkbook -> close();
  }
  
}
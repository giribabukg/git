<?php
class CInc_Api_Pdf_Chart extends CCor_Obj {
  
  public function __construct(& $aPag, $aCaption = 'Chart') {
    $this -> mPag = & $aPag;
    $this -> mCap = $aCaption;
  }
  
  public function setData($aArr) {
    $this -> mDat = $aArr;
  }
  
  public function setCaption($aCaption) {
    $this -> mCap = $aCaption;
  }

  public function ruleOfThree($aA, $aB, $aC) {
    if ($aA != 0)
    $result = ($aC * $aB) / $aA;
	else $result = 0;
    return $result;
  }

  public function calculateTextWidth($aStr, $aFnt, $aFntSiz) {
    $lFnt = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
    $lFntSiz = 12;

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
  
  public function draw($aX, $aY, $aSizeX, $aSizeY) {
    $lMax = 0;
    $lSum = 0;
    $lCnt = count($this -> mDat);
    if ($lCnt == 0) {
      return;
    }
    foreach ($this -> mDat as $lVal) {
      if ($lVal > $lMax) {
        $lMax = $lVal;
      }
      $lSum += $lVal;
    }
    $lSum = $lSum * 1.1;
    $lDx  = floor($aSizeX / $lCnt);
    $lDxh =  floor($lDx / 2);
    if ($lSum > 0) {
      $lDy  = ($aSizeY /($lMax * 1.1));
    } else $lDy = 0;
    $this -> mPag -> setFillColor(new Zend_PDF_Color_GrayScale(0.9));
    $this -> mPag -> setLineColor(new Zend_PDF_Color_GrayScale(0.7));
    $this -> mPag -> setLineWidth(.3);
    $this -> mPag -> drawRectangle($aX, $aY, $aSizeX + $aX, $aY - $aSizeY);
    $this -> mPag -> setFillColor(new Zend_PDF_Color_Rgb(0.5, 0.5, 0.7));
    $this -> mPag -> drawText($this -> mCap, $aX + 3, $aY + 8);
    $lX = $aX;
    $lY = $aY - $aSizeY;
    $lSum = 0;
    foreach ($this -> mDat as $lKey => $lVal) {
      $lDva = floor($lVal * $lDy);
      $this -> mPag -> drawRectangle($lX, $lY, $lX + $lDx, $lY + $lDva);
      $this -> mPag -> drawText($lKey, $lX + 5, $aY - $aSizeY - 16);
      $lX += $lDx;
    }

    $lMaxLen = strlen($lMax);
    $lPow = pow(10, $lMaxLen - 1);
    $lMaxStepY = ceil($lMax / $lPow) * $lPow;
    $count = $lMaxStepY / $lPow;
    $result = $this->ruleOfThree(($lMax * 1.1), 200, $lPow);


    for ($dummy = 1; $dummy < $count; $dummy++) {
      $lCapTxt = $lPow * $dummy;
      $lCapWidth = $this->calculateTextWidth($lCapTxt, $this -> mCap, $this -> mCap);

      $this -> mPag -> drawLine(160, 745 - 200 + ($dummy * $result), 430, 745 - 200 + ($dummy * $result));
      $this -> mPag -> drawText($lPow * $dummy, 160 - $lCapWidth, 745 - 200 + ($dummy * $result) - 3);
    }
  }
  
}
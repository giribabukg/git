<?php
class CInc_Utl_Date_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = htm(lan('date.menu'));
  }

  protected function actStd() {
    $lDay = $this -> mReq -> getVal('d');
    $lCal = new CUtl_Date_Calendar('utl-date', $lDay);

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lCal -> getContent());
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));

    echo $lPag -> getContent();
    exit;
  }

  protected function actDay() {
    $this -> actStd();
  }

  protected function actSetpage() {
    $lPag = $this -> getReq('page');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('sys.cal', $lPag);
    $this -> actStd();
  }

  protected function actSave() {
    $lMonths = $this -> getReq('months', 3);

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('utl.months', $lMonths);

    return TRUE;
  }

  protected function actLoad() {
    $lUsr = CCor_Usr::getInstance();
    $lMonths = $lUsr -> getPref('utl.months', 3);

    echo $lMonths;
    exit;
  }

  function multiexplode($aDelimiters, $aString) {
    $lStrReplace = str_replace($aDelimiters, $aDelimiters[0], $aString);
    $lResult = explode($aDelimiters[0], $lStrReplace);
    return  $lResult;
  }

  protected function actFormat() {
    $lDateFormat = lan('lib.date.long');

    $lToken = $this -> multiexplode(array('-', '.', '/'), $lDateFormat);
    foreach ($lToken as $lKey => $lValue) {
      if ($lValue == 'd') {
        $lToken[$lKey] = 'dd';
      } elseif ($lValue == 'm') {
        $lToken[$lKey] = 'mm';
      } elseif ($lValue == 'Y') {
        $lToken[$lKey] = 'yy';
      } else {
        $lToken[$lKey] = $lValue;
      }
    }

    if (strpos($lDateFormat, '-') != FALSE) {
      $lDelimiter = '-';
    } elseif (strpos($lDateFormat, '.') != FALSE) {
      $lDelimiter = '.';
    } elseif (strpos($lDateFormat, '/') != FALSE) {
      $lDelimiter = '/';
    }

    echo implode($lDelimiter, $lToken);
    exit;
  }
}
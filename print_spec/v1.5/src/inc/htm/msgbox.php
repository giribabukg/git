<?php
class CInc_Htm_MsgBox extends CCor_Ren {

  public function __construct($aIsLogin = FALSE) {
    if ($aIsLogin) {
      $this -> getDefaultPrefs();
    } else {
      $this -> getPrefs();
    }
    $this -> mIsLogin = $aIsLogin;
    $lUsr = CCor_Usr::getInstance();
    $this -> mCanDebug = $lUsr -> canRead('dbg');
  }

  protected function getDefaultPrefs() {
    $this -> mOrd = moTime;
    $this -> mLvl = array();
    $this -> mLvl[mtUser] = mlAll;
  }

  protected function getPrefs() {
    $lUsr = CCor_Usr::getInstance();
    $this -> mOrd = $lUsr -> getPref('sys.msg.ord', moTime);
    $this -> mLvl = array();
    $this -> mLvl[mtUser]  = $lUsr -> getPref('sys.msg.mt'.mtUser);
    $this -> mLvl[mtDebug] = $lUsr -> getPref('sys.msg.mt'.mtDebug);
    $this -> mLvl[mtPhp]   = $lUsr -> getPref('sys.msg.mt'.mtPhp);
    $this -> mLvl[mtSql]   = $lUsr -> getPref('sys.msg.mt'.mtSql);
    $this -> mLvl[mtApi]   = $lUsr -> getPref('sys.msg.mt'.mtApi);
    $this -> mLvl[mtAdmin] = $lUsr -> getPref('sys.msg.mt'.mtAdmin);
  }

  private function getMsg() {
    $lSys = CCor_Sys::getInstance();
    // Find out if user logged with admin pass
    $lAdminLog = $lSys-> get('usr.loggedasadmin', FALSE);

    $lMsg = CCor_Msg::getInstance();
    $lArr = $lMsg -> getMsg(mtAll, mlAll);
    #echo '<pre>--getMsg----'.get_class().'---';var_dump($this -> mLvl,'#############');echo '</pre>';

    $lRet = array();
    foreach ($lArr as $lRow) {
      $lTyp = $lRow['typ'];
      $lLvl = (isset($this -> mLvl[$lTyp])) ? $this -> mLvl[$lTyp] : mlNone;
      #echo '<pre>--getMsg----'.get_class().'---';var_dump($lRow,$lTyp,"bitset($lLvl, $lRow[lvl])=",bitset($lLvl, $lRow['lvl']),' OR ',$lAdminLog,'=$lAdminLog  = ',bitset($lLvl, $lRow['lvl']) OR $lAdminLog,'#############');echo '</pre>';
      if (bitset($lLvl, $lRow['lvl']) OR $lAdminLog) {
        // if user logged with admin pass, show all messages.
        $lRet[] = $lRow;
      }
    }
    return $lRet;
  }

  protected function countMessages() {
    $this->mCountType = array();
    $this->mCountLevel = array();

    foreach ($this -> mMsg as $lRow) {
      $this->mCountType[$lRow['typ']]  = $this->getCountByType($lRow['typ']) +1;
      $this->mCountLevel[$lRow['lvl']] = $this->getCountByLevel($lRow['lvl']) +1;
    }
    $this->mCountLevel['all'] = count($this->mMsg);
  }

  protected function getCountByType($aType) {
    return isset($this->mCountType[$aType]) ? $this->mCountType[$aType] : 0;
  }

  protected function getCountByLevel($aLevel) {
    return isset($this->mCountLevel[$aLevel]) ? $this->mCountLevel[$aLevel] : 0;
  }

  protected function getSearchJs() {
    $lRet = '';
    $lRet.= '<script type="text/javascript">';
    $lRet.= 'jQuery(function(){jQuery("#pag-msg-search").bind("change keyup keypress", Flow.MsgBox.search);});';
    $lRet.= '</script>';
    return $lRet;
  }

  protected function getToggleButtons() {
    $lRet = '';
    $lRet.= '<tr><td class="td1 p8" colspan="3">';

    $this->countMessages();

    $lRet.= '<table cellpadding="2" cellspacing="0"><tr>'.LF;

    $lRet.= $this -> getAllToggleButton('All');

    $lRet.= '<td class="w100">&nbsp;</td>';

    $lRet.= $this -> getTypeToggleButton('User', mtUser);
    $lRet.= $this -> getTypeToggleButton('Debug', mtDebug);
    #$lRet.= $this -> getTypeToggleButton('Php Msg', mtPhp);
    $lRet.= $this -> getTypeToggleButton('Sql', mtSql);
    $lRet.= $this -> getTypeToggleButton('Api', mtApi);

    $lRet.= '<td class="w100">&nbsp;</td>';

    $lRet.= $this -> getLevelToggleButton('Info', mlInfo);
    $lRet.= $this -> getLevelToggleButton('Warn', mlWarn);
    $lRet.= $this -> getLevelToggleButton('Error', mlError);
    $lRet.= $this -> getLevelToggleButton('Fatal', mlFatal);

    $lRet.= '<td class="w100">&nbsp;</td>';
    $lRet.= '<td>Search</td>';
    $lRet.= '<td><input type="text" class="inp w100" id="pag-msg-search" /></td>';


    $lRet.= '</table>'.LF;

    $lRet.= '</td></tr>';
    return $lRet;
  }

  protected function getTypeToggleButton($aCap, $aType) {
    $lNum = $this->getCountByType($aType);
    if (empty($lNum)) {
      $lRet = '<a class="box tg p8 w70">'.htm($aCap).'</a>'.NB;
    } else {
      $lRet = '<a class="box cp b p8 w70" onclick="Flow.MsgBox.tog(this,\'mt-'.$aType.'\');">'.htm($aCap).' '.$lNum.'</a>'.NB;
    }
    return '<td>'.$lRet.'</td>';
  }

  protected function getLevelToggleButton($aCap, $aLevel) {
    $lNum = $this->getCountByLevel($aLevel);
    if (empty($lNum)) {
      $lRet = '<a class="box tg p8 w70">'.htm($aCap).'</a>'.NB;
    } else {
      $lRet = '<a class="box cp b p8 w70" onclick="Flow.MsgBox.tog(this,\'ml-'.$aLevel.'\');">'.htm($aCap).' '.$lNum.'</a>'.NB;
    }
    return '<td>'.$lRet.'</td>';
  }

  protected function getAllToggleButton($aCap) {
    $lNum = $this->getCountByLevel('all');
    if (empty($lNum)) {
      $lRet = '<a class="box tg p8 w70">'.htm($aCap).'</a>'.NB;
    } else {
      $lRet = '<a class="box cp b p8 w70" onclick="if(jQuery(this).hasClass(\'b\')){jQuery(\'ml-all\').hide()}else{jQuery(\'ml-all\').show();};jQuery(this).toggleClass(\'b\')">'.htm($aCap).' '.$lNum.'</a>'.NB;
    }
    return '<td>'.$lRet.'</td>';
  }

  protected function getRow($aRow) {
    $lTyp = $aRow['typ'];
    $lLvl = $aRow['lvl'];
    $lRet = '<tr class="ml-all mt-'.$lTyp.' ml-'.$lLvl.'">';
    $lRet.= '<td class="td1 w16"><i class="ico-w16 ico-w16-ml-'.$aRow["lvl"].'"></i></td>';
    #if (!$this -> mIsLogin) {
    #  $lRet.= '<td class="td1 w16">'.img('img/ico/16/mt-'.$aRow['typ'].'.gif').'</td>';
    #}
    $lRet.= '<td class="td2">'.htm($aRow['txt']).'</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getRows($aRows) {
    $lRet = '';
    foreach ($aRows as $lRow) {
      $lRet.= $this -> getRow($lRow);
    }
    return $lRet;
  }

  protected function getMsgLevel($aLvl) {
    $lRet = array();
    foreach ($this -> mMsg as $lRow) {
      if ($lRow['lvl'] == $aLvl) {
        $lRet[] = $lRow;
      }
    }
    return $lRet;
  }

  protected function getMsgType($aTyp) {
    $lRet = array();
    foreach ($this -> mMsg as $lRow) {
      if ($lRow['typ'] == $aTyp) {
        $lRet[] = $lRow;
      }
    }
    return $lRet;
  }

  protected function getRowsByLevel() {
    $lRet = '';
    $lRet.= $this -> getRows($this -> getMsgLevel(mlInfo));
    $lRet.= $this -> getRows($this -> getMsgLevel(mlWarn));
    $lRet.= $this -> getRows($this -> getMsgLevel(mlError));
    $lRet.= $this -> getRows($this -> getMsgLevel(mlFatal));

    return $lRet;
  }

  protected function getRowsByType() {
    $lRet = '';
    $lRet.= $this -> getRows($this -> getMsgType(mtUser));
    $lRet.= $this -> getRows($this -> getMsgType(mtDebug));
    $lRet.= $this -> getRows($this -> getMsgType(mtPhp));
    $lRet.= $this -> getRows($this -> getMsgType(mtSql));
    $lRet.= $this -> getRows($this -> getMsgType(mtApi));
    return $lRet;
  }

  protected function getRowsByTime() {
    return $this -> getRows($this -> mMsg);
  }

  protected function getCont() {
    $this -> mMsg = $this -> getMsg();
    if (empty($this -> mMsg)) {
      return '';
    }
    $lCnt = count($this -> mMsg);
    $lRet = '';
    if ($this->mCanDebug) {
      $lRet.= $this->getSearchJs();
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" width="100%">'.LF;
    if ($this->mCanDebug) {
      $lRet.= $this->getToggleButtons();
    }
    $this -> mOld = 0;
    switch($this -> mOrd) {
      case moLevel :
        $lRet.= $this -> getRowsByLevel();
        break;
      case moType :
        $lRet.= $this -> getRowsByType();
        break;
      case moTime :
        $lRet.= $this -> getRowsByTime();
        break;
      default :
        $lRet.= $this -> getRowsByTime();
    }
    $lRet.= '</table>';

    if (!$this -> mIsLogin) {
      $lTmp = '';
      if (0 == $lCnt) {
        $lRet = '<div id="msg" class="dn"></div>';
      } else {
        $lTmp = '<div id="msg" class="db">';
        $lPnl = new CHtm_Panel($lCnt.' Messages', $lRet, 'pag.msg');
        $lTmp.= $lPnl -> getContent();
        $lTmp.= '</div>';
        $lRet = $lTmp;
      }
    }

    #CCor_Msg::getInstance() -> clear();

    return $lRet;
  }

}
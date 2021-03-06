<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 10605 $
 * @date $Date: 2015-09-29 13:03:40 +0200 (Tue, 29 Sep 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Ser_Wrap extends CCor_Ren {

  public function __construct($aShow = FALSE) {
    $this -> mAct = FALSE;
    $this -> mArc = FALSE;
    $this -> mAnf = FALSE;
    $this -> mFil = array();

    if ($aShow) {
      $lUsr = CCor_Usr::getInstance();
      $lSer = $lUsr -> getPref('job-ser.ser');
      if (empty($lSer)) {
        $this -> mAnf = TRUE;
        $this -> mAct = TRUE;
        $this -> mArc = TRUE;
      } else {
        if (isset($lSer['anf'])) $this -> mAnf = TRUE;
        if (isset($lSer['job'])) $this -> mAct = TRUE;
        if (isset($lSer['arc'])) $this -> mArc = TRUE;
        if (isset($lSer['flags'])) $this -> mFil['flags'] = $lSer['flags'];
      }
    }
  }

  protected function getCont() {
    $lDiv = getNum('t');

    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '        <td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">'.LF;
    $lRet.= htm(lan('lib.search'));
    $lRet.= '        </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr id="'.$lDiv.'" style="display:table-row">'.LF;
    $lRet.= '        <td class="td1 p16">'.LF;
    $lVie = new CJob_Ser_Form($this -> mFil);
    $lRet.= $lVie -> getContent();
    $lRet.= '        </td>'.LF;
    $lRet.= '    </tr>'.LF;

    if (($this -> mAnf) or ($this -> mAct)) {
      $lDiv = getNum('t');

      $lRet.= '    <tr>'.LF;
      $lRet.= '        <td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">'.LF;
      $lRet.= 'Active Jobs';
      $lRet.= '        </td>'.LF;
      $lRet.= '    </tr>'.LF;
      $lRet.= '    <tr id="'.$lDiv.'" style="display:table-row">'.LF;
      $lRet.= '        <td class="td1 p16">'.LF;
      $lVie = new CJob_Ser_List($this -> mAnf, $this -> mAct, $this -> mFil);
      $lRet.= $lVie -> getContent();
      $lRet.= '        </td>'.LF;
      $lRet.= '    </tr>'.LF;
    }

    if ($this -> mArc) {
      $lDiv = getNum('t');

      $lRet.= '    <tr>'.LF;
      $lRet.= '        <td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">'.LF;
      $lRet.= 'Archive';
      $lRet.= '        </td>'.LF;
      $lRet.= '    </tr>'.LF;
      $lRet.= '    <tr id="'.$lDiv.'" style="display:table-row">'.LF;
      $lRet.= '        <td class="td1 p16">'.LF;
      $lVie = new CJob_Ser_Archive($this -> mFil);
      $lRet.= $lVie -> getContent();
      $lRet.= '        </td>'.LF;
      $lRet.= '    </tr>'.LF;
    }

    $lRet.= '</table>'.LF;
    return $lRet;
  }
}
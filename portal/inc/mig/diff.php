<?php
/**
 * Title
 *
 * Compare two arbitrary jobs with checkboxes to copy values from one job to
 * the other
 *
 * @package    Migration
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Mig_Diff extends CHtm_Form {

  public function __construct($aSrcJid, $aDestJid) {
    parent::__construct('mig.cnfdiff', 'Compare Jobs', FALSE);
    $this -> setAtt('class', 'tbl w700');

    $this -> mSrcJid  = $aSrcJid;
    $this -> mDestJid = $aDestJid;

    $this -> mPlain = new CHtm_Fie_Plain();
    $this -> mFie   = CCor_Res::get('fie');

    $this -> mSrcJob = new CJob_Art_Dat();
    $this -> mSrcJob -> load($this -> mSrcJid);
    $this -> mDestJob = new CJob_Art_Dat();
    $this -> mDestJob -> load($this -> mDestJid);

    $this -> setParam('src', $this -> mDestJob['src']);
    $this -> setParam('jobid', $this -> mDestJid);
    $this -> setParam('srcjid', $this -> mSrcJid);
  }

  protected function getForm() {
    $lRet.= '<div>'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0" class="w100p">'.LF;

    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="th2">Field</td>'.LF;
    $lRet.= '<td class="th2 w50p">'.htm($this -> mSrcJid).'</td>'.LF;
    $lRet.= '<td class="th2 w50p">'.htm($this -> mDestJid).'</td>'.LF;
    $lRet.= '<td class="th2 w16">&nbsp;</td>'.LF;
    $lRet.= '</tr>'.LF;

    $lKeyArr = CCor_Cfg::get('job.keyw');
    $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lDst).'" />';

    // blacklist - do not compare these
    $lBlack = array('webstatus', 'jobnr', 'apl', 'stichw', 'flags');

    foreach ($this -> mFie as $lDef) {
      $lAli = $lDef['alias'];
      if (in_array($lAli, $lBlack)) {
        continue;
      }
      $lSrc = $this -> mSrcJob[$lAli];
      $lDst = $this -> mDestJob[$lAli];
      $lDif = !($lSrc == $lDst);

      if ($lDif) {
        $lRet.= '<tr>'.LF;
      } else {
        $lRet.= '<tr style="display:none">'.LF;
      }
      $lRet.= '<td class="td2">'.htm($lDef['name_'.LAN]).'</td>'.LF;
      $lRet.= '<td class="td1">';
      $lRet.= $this -> mPlain -> getPlain($lDef, $lSrc);
      $lRet.= '</td>'.LF;
      $lCls = ($lDif) ? 'td1 cr' : 'td1';
      $lRet.= '<td class="'.$lCls.'">';
      $lRet.= $this -> mPlain -> getPlain($lDef, $lDst);
      $lRet.= '</td>'.LF;
      $lRet.= '<td class="td1 ac">';
      if ($lDif) {
        $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lDst).'" />';
        $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lSrc).'" />';
      } else {
        $lRet.= img('img/ico/16/ok.gif');
        if (in_array($lAli, $lKeyArr)) {
          $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lDst).'" />';
          $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lDst).'" />';
        }
      }
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }

    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

}
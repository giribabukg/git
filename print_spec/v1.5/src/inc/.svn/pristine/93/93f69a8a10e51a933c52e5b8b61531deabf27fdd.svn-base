<?php
class CInc_Job_His_Single extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
    $this -> mPlain = new CHtm_Fie_Plain();
  }

  protected function load() {
    $lSql = 'SELECT * FROM al_job_his WHERE id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    $lRet = $lQry -> getDat();
    return $lRet;
  }

  protected function getMoreAmt($aVal) {
    $lRet = '';
    $lHtb = CCor_Res::get('htb', 'amt');
    if (isset($lHtb[$aVal])) {
      $lRet = '<b>'.htm(lan('job.amend.type')).'</b> : '.htm($lHtb[$aVal]).BR;
    }
    return $lRet;
  }

  protected function getMoreFil($aVal) {
    $lRet = '<b>'.htm(lan('lib.file.name')).'</b> : '.htm($aVal).BR;
    return $lRet;
  }

  protected function getMoreCause($aVal) {
    $lRet = '<b>'.htm(lan('job.amend.root')).'</b> : '.nl2br(htm($aVal)).BR;
    return $lRet;
  }

  protected function getMorePer($aVal) {
    $lRet = '<b>'.htm(lan('lib.initiator')).'</b> : '.nl2br(htm($aVal)).BR;
    return $lRet;
  }

  protected function getMoreAk($aVal) {
    $lRet = '<b>'.htm(lan('job.amend.job')).'</b> : '.substr($aVal,-3).BR;
    return $lRet;
  }

  protected function getMoreUpd($aVal) {
    if (empty($aVal)) return '';
    $lFie = CCor_Res::getByKey('alias', 'fie');
    $lFmt = lan('job.changes.format');

    $lRet = '<b>'.htm(lan('job.changes')).'</b> :'.BR;
    foreach ($aVal as $lKey => $lRow) {
      if (!isset($lFie[$lKey])) {
        $this -> dbg('Unknown Alias '.$lKey);
        continue;
      }
      $lDef = $lFie[$lKey];
      $lOld = $this -> mPlain -> getPlain($lDef, $lRow['old']);
      $lNew = $this -> mPlain -> getPlain($lDef, $lRow['new']);
      if ('' == $lOld) $lOld = '""';
      if ('' == $lNew) $lNew = '""';
      $lCap = $lDef['name_'.LAN];
      $lRet.= '- '.htm(sprintf($lFmt, $lCap, $lOld, $lNew)).BR;
    }
    return $lRet;
  }

  protected function getCont() {
    $lRow = $this -> load();
    $lDat = new CCor_Datetime($lRow['datum']);

    $lArr = CCor_Res::extract('id','fullname','usr');
    $lUid = $lRow['user_id'];
    $lUsr = (isset($lArr[$lUid])) ? $lArr[$lUid] : '???';

    $lRet = '';

    $lRet.= '<table cellpadding="4" cellspacing="0" class="tbl w100p">'.LF;

    $lRet.= '<tr><td class="th2 b">'.htm(lan('lib.user')).'</td>';
    $lRet.= '<td class="td2 w100p">'.htm($lUsr).'</td></tr>'.LF;

    $lRet.= '<tr><td class="th2 b">'.htm(lan('lib.date')).'</td>';
    $lRet.= '<td class="td2">'.$lDat -> getFmt(lan('lib.date.xxl')).'</td></tr>'.LF;

    $lRet.= '<tr><td class="th2 b">'.htm(lan('lib.sbj')).'</td>';
    $lRet.= '<td class="td2">';

    $lTpl = new CCor_Tpl();
    $lTpl -> setDoc($lRow['subject']);
    $lTpl -> setLang(LAN);
    $lRet.= htm($lTpl -> getContent());
    $lRet.= '</td></tr>'.LF;

    $lRet.= '<tr><td class="td1 p16" colspan="2">';

    $lVal = $lRow['add_data'];
    if (!empty($lVal)) {
      $lVal = unserialize($lVal);
      foreach ($lVal as $lKey => $lValue) {
        $lFnc = 'getMore'.$lKey;
        if ($this -> hasMethod($lFnc)) {
          $lRet.= $this -> $lFnc($lValue);
        }
      }
      $lRet.= BR;
    }
    $lRet.= nl2br($lRow['msg']).BR.BR;

    $lRet.= '</td></tr>'.LF;
    $lRet.= '</table>'.LF;
    return $lRet;
  }

}
<?php
class CInc_Job_Apl2_Loop extends CCor_Ren {
  
  public function __construct($aLoop, $aNum = 1) {
    $this->mLoop = $aLoop;
    $this->mLid = $aLoop['id'];
    $this->mNum = $aNum;
    
    $this->getPrefs();
    
    $this->mCtr =  CCor_Res::get('htb', array('ctr', 'value', 'value_en'));
    $this->mAplTypes = CCor_Res::getByKey('code', 'apltypes');
  }
  
  protected function getPrefs() {
    $lUsr = CCor_Usr::getInstance();
    $this->mShowAllSubloops = $lUsr->getPref('job-apl.showallsub');
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet = '<div class="bc-apl-loop" data-loop="'.$this->mLid.'">';
    $lRet.= $this->getHeader();
    $lRet.= $this->getSubloops();
    $lRet.= '</div>';
    return $lRet;
  }
  
  protected function getHeader() {
    $this->mDivId = getNum('bc-apl-loop');
    $lRet = '';
    $lRet.= '<div class="th2 cp bc-loop-row apl-head-'.$this->mLoop['typ'].'" onclick="Flow.Std.tog(\''.$this->mDivId.'\')">';
    $lRet.= $this->getHeaderContent();
    $lRet.= '</div>';
    
    return $lRet;
  }
  
  protected function getAplTypes() {
    $lSql = 'SELECT code, name FROM al_apl_types WHERE mand='.MID;
    $lQry = new CCor_Qry($lSql);
    $lAplTypes = array('all'=>'All APL Types');
    foreach ($lQry as $lKey => $lVal) {
      $lAplTypes[$lVal['code']] = $lVal['name'];
    }
    return $lAplTypes;
  }
  
  protected function getHeaderContent() {
    $lDate = $this->mLoop['start_date'];
    $lObj = new CCor_Date($lDate);
    $lFmt = $lObj->getFmt('d.m.Y');

    $lType = $this->mLoop['typ'];
    $lName = 'Workflow';
    if (isset($this->mAplTypes[$lType])) {
      $lName = $this->mAplTypes[$lType]['short'];
    }
    
    $lRet = '';
    $lRet.= '<span class="apl-num">'.$this->mNum.'</span>'.NB.NB.$lName;
    $lRet.= ', started '.$lFmt;
    
    $lObj->setSql($this->mLoop['close_date']);
    if (!$lObj->isEmpty()) {
      $lRet.= ', closed '.$lObj->getFmt('d.m.Y');
    }
    
    /*
    $lArr = $this->getAplTypes();
    $lName = $this->mLoop['typ'];
    if (isset($lArr[$lName])) {
      $lName = $lArr[$lName];
    }
    */
  	//$lRet = $this->mLid.' '.$lName;
  	return $lRet;
  }
  
  protected function getSubLoops() {
    $lRet = '';
    $lSub = $this->mLoop->getSubLoops();
    #if (empty($lSub)) {
    #  $lRet.= $this->getStatesContent();
    #} else {
      $lRet.= $this->getSubLoopsContent($lSub);
    #}
    return $lRet;
    
  }

  protected function getSubLoopsContent($aSubs) {
    if (empty($aSubs)) {
      return '';
    }
    #$lDis = 'none';
    $lDis = ($this->mLoop['status'] == 'closed') ? 'none' : 'block';
    $lRet = '<div class="indent bc-loop-cont" id="'.$this->mDivId.'" style="display:'.$lDis.'">';
    foreach ($aSubs as $lPrefix => $lRows) {
      foreach ($lRows as $lId => $lRow) {
        $lCurId = $lId;
        $lCurRow = $lRow;
        break;
      }
      $lNum = getNum('bc-apl-ctr');
      $lRet.= '<div class="th2 cp bc-prefix-row apl-prefix-'.$this->mLoop['typ'].'" onclick="Flow.Std.tog(\''.$lNum.'\')">';
      $lCtr = strtolower($lPrefix);
      $lRet.= img('flags/'.$lCtr.'.png');
      $lCountryName = '';
      if (isset($this->mCtr[$lPrefix])) {
        $lCountryName.= NB.$this->mCtr[$lPrefix];
      }
      $lRet.= NB.$lCountryName;
      $lRet.= NB.NB.'('.$lPrefix.')';
      
      $lRet.= '<span style="position:absolute; left:30em" class="my-apl-icon">';
      $lRet.= self::getSubIconSummary($lCurRow); 
      $lRet.= '</span>';
      
      //$lRet.= 'Country '.$lPrefix.
      $lRet.= '</div>';
      $lDis = ($lCurRow['subloop_state'] == 'closed') ? 'none' : 'block';
      #$lDis = 'none';
      $lRet.= '<div id="'.$lNum.'" class="bc-prefix-cont" style="display:'.$lDis.'">';
      
      if ($this->mShowAllSubloops) {
        foreach ($lRows as $lId => $lRow) {
          $lSub = new CJob_Apl2_Sub($lRow);
          $lRet.= $lSub->getContent();
        }
      } else {
        $lSub = new CJob_Apl2_Sub($lCurRow);
        $lRet.= $lSub->getContent();
      }
      $lRet.= '</div>';
    }
    $lRet.= '</div>';
    return $lRet;
  }
  
  public static function getSubIconSummary($aRow) {
    $lStates = array();
    $lDis = $aRow->loadDisplayStates();
    if (empty($lDis)) return '';
    foreach ($lDis as $lRow) {
      $lState = $lRow['status'];
      $lStates[] = $lState;
    }
    $lRet = '';
    sort($lStates);
    foreach ($lStates as $lState) {
      $lRet.= img('img/ico/16/flag-0'.$lState.'_5px.gif');
    }
    return $lRet;
  }
  
  protected function getStatesContent() {
    $lRe .= '<div class="indent">';

      $lNum = getNum('bc-apl-ctr');
      $lRet.= '<div class="th2 cp" onclick="Flow.Std.tog(\''.$lNum.'\')">';
      //$lCtr = strtolower($lPrefix);
      //$lRet.= img('flags/'.$lCtr.'.png');
      $lRet.= NB.'Without subloops</div>';
      $lRet.= '<div id="'.$lNum.'" style="display:block">';
      #foreach ($lRows as $lId => $lRow) {
        $lSub = new CJob_Apl2_States($this->mLoop);
        $lRet.= $lSub->getContent();
      #}
      $lRet.= '</div>';
    $lRet.= '</div>';
    return $lRet;
  }
  
}

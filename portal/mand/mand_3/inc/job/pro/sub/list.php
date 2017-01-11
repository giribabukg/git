<?php
class CJob_Pro_Sub_List extends CCust_Job_Pro_Sub_List {

  protected function createToolBar() {
    $lUsr = CCor_Usr::getInstance();
    if ($this -> mIsNoArc AND $this -> mCanInsert) {
      #$this -> addBtn(lan('job-pro-sub.new'), 'go(\'index.php?act=job-art.newsub&pid='.$this -> mJobId.'\')', 'img/ico/16/edit.gif');
      $lMen = new CHtm_Menu('New Item');
      $lMen -> addItem('index.php?act=job-rep.newsub&pid='.$this -> mJobId, 'New Press Specification');
      $this -> addPanel('new', $lMen -> getContent());
    }
    $this -> addPanel('sca', '|');
    if ($this -> mMasterVariantBundleActiv && $this -> mColumnIsMasterDefined && $this -> mIsNoArc) {
      #$this -> addBtn(lan('job-pro-sub.createMasterBundle'), 'createMasterVariant(\''.$this -> mJobId.'\',\''.LAN.'\')' , 'img/ico/16/plus.gif');
      $this -> addBtn('Assign selected items', 'createMasterVariant(\''.$this -> mJobId.'\',\''.LAN.'\')' , 'img/ico/16/plus.gif');
    }
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdMasterBundle() {
    $lSid = $this -> getInt('id');
    $lSrc = $this -> getVal('src');
    $lMasterId = $this -> getVal('master_id');
    $lIsMaster = $this -> getVal('is_master');
    if (empty($lMasterId)) {
      if ($lIsMaster != 'X' || $lSrc == 'art') {
        $lRet = '<input type="checkbox" class="variant" id="v'.$lSid.'" />';
      } else {
        $lRet = NB;
      }
    } else {
      return '';
    }
    return $this -> tdClass($lRet, 'ac w16');
  }

  protected function getTd_Insert() {
    $lId = $this -> getInt('id');
    $lSrc = $this -> getVal('src');
    $lIsMaster = $this -> getVal('is_master');
    if (empty($lIsMaster)) return $this -> td();
    $lNewSrc = 'art';
    if ($lSrc == 'rep') {
      $lNewSrc = 'art';
    
    $lRet = '<a href="index.php?act=job-'.$lNewSrc.'.newmastersub&pid='.$this -> mJobId.'&mid='.$lId.'">';
    $lRet.= img('ico/16/plus.gif');
    $lRet.= '</a>';
    }
    return $this -> tdc($lRet);
  }

}
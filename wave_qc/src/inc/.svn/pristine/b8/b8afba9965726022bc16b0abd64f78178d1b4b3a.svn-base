<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Adduserdialog extends CCor_Tpl {

  public function __construct($aMod, $aJid, $aPrefix, $aWithPos = true, $aWithDesc = true) {
    $this->openProjectFile('job/apl/add_apl_user_dialog.htm');

    $lDis = ($aWithPos) ? 'block' : 'none';
    $lInfo = ($aWithPos) ? 'none' : 'block';
    $this->setPat('display.pos', $lDis);
    $this->setPat('display.info', $lInfo);
    $this->mMod = $aMod;
    $this->mJid = $aJid;
    $this->mPrefix = $aPrefix;
    $this->mSrc = $aMod;

    $this->mBaseGroup = 0;
    $this->mAllUsers = CCor_Res::extract('id', 'fullname', 'usr', $this->mBaseGroup);

    $this->mActions = new CCust_Job_Apl_Preview($this->mJid);
    $this->mActions->loadFromSession($this->mPrefix);

    $this->mMem = array();
    $lSql = 'SELECT m.gid,m.uid FROM al_usr_mem m, al_usr u ';
    $lSql.= 'WHERE m.uid=u.id AND u.del="N"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lUid = $lRow['uid'];
      $this->mMem[$lRow['gid']][$lUid] = $lUid;
    }

    $this->mExclude = array();
    $lAct = $this->mActions->getActions();
    $lMax = 1;
    foreach ($lAct as $lRow) {
      $lTyp = $lRow['typ'];
      if ('email_usr' == $lTyp) {
        $lSid = $lRow['param']['sid'];
        $this->mExclude[$lSid] = 1;
      }
      if ('email_gru' == $lTyp) {
        $lPar = $lRow['param'];

        $lMem = $lPar['sid'];
        if (!empty($this->mMem[$lMem])) {
          foreach ($this->mMem[$lMem] as $lUid) {
            $this->mExclude[$lUid] = 1;
          }
        }
      }
      $lPos = $lRow['pos'];
      if (($lPos > $lMax) && ($lPos < EVENT_DEFER_POSITION)) $lMax = $lPos;
    }
    $this->mMaxPos = $lMax +1;

    $this->getUsers();
    $this->getStrucGroups();

    $this->setPat('users', $this->getUserOptions());
    $this->setPat('groups', $this->getGroupOptions());
    $this->setPat('pos.options', $this->getPositionOptions());
    $this->setPat('func.options', $this->getFunctionOptions());
    
    if (empty($this->mPrefix)) {
      $this->mPrefix = $this->getPrefix();
      if (count($this->mPrefix) > 1) {
        $this->setPat('prefix.display.pos', 'block');
        $this->setPat('prefix', $this->getAplPrefixOptions());
      }
      else $this->setPat('prefix.display.pos', 'none');
    }else $this->setPat('prefix.display.pos', 'none');
    
    $lDesc = ($aWithDesc) ? $this->getButtonsDesc() : '';
    $this->setPat('buttons.info', $lDesc);
  }
  
  protected function getUsers() {
    $this->mUsers = array();
    foreach ($this->mAllUsers as $lUid => $lName) {
      #if (!isset($this->mExclude[$lUid])) {
        $this->mUsers[$lUid] = $lName;
      #}
    }
    return asort($this->mUsers);
  }

  protected function getStrucGroups() {
    $lSql = 'SELECT g.id,g.name FROM al_gru g,al_usr_mem m ';
    $lSql.= 'WHERE m.gid=g.id ';
    $lSql.= 'AND g.parent_id='.esc(STRUC_PARENT). ' ';
    $lSql.= 'AND g.mand IN (0,'.MID.') ';
    $lSql.= 'GROUP BY g.id ';
    $lSql.= 'ORDER BY g.name';
    $this->mGroups = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mGroups[$lRow['id']] = $lRow['name'];
    }
    #$this->mGroups = CCor_Res::extract('id', 'name', 'gru', array('parent_id' => STRUC_PARENT));

  }

  protected function getUserOptions() {
    $lRet = '';
    $lSel = ' selected="selected"';

    #$lRet = '<optgroup id="apl_usr" label="Users">';
    foreach ($this->mUsers as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lName).'</option>'.LF;
      $lSel = '';
    }
    #$lRet.= '</optgroup>';
    return $lRet;
  }

  protected function getGroupOptions() {
    $lRet = '';
    $lSel = '';

    #$lRet = '<optgroup id="apl_grp" label="Groups">';
    foreach ($this->mGroups as $lKey => $lName) {
      $lRet.= '<option value="-'.$lKey.'"'.$lSel.'>'.htm($lName).'</option>'.LF;
      $lSel = '';
    }
    #$lRet.= '</optgroup>';
    return $lRet;

  }

  protected function getPositionOptions() {
    $lRet = '';
    for ($i=1; $i<=$this->mMaxPos; $i++) {
      $lRet.= '<option value="a'.$i.'">Add to position '.$i.'</option>'.LF;
    }
    for ($i=1; $i<=$this->mMaxPos; $i++) {
      $lRet.= '<option value="i'.$i.'">Insert before position '.$i.'</option>'.LF;
    }
    $lRet.= '<option value="i100" selected="selected">Insert at the end</option>'.LF;
    return $lRet;
  }

  protected function getFunctionOptions() {
    $lRet = '';
    $lTmp = CCor_Res::get('gru', array('parent_id'=>FUNC_PARENT));
    $lGru = array('' => ' ');
    foreach ($lTmp as $lRow) {
      $lGru[$lRow['id']] = $lRow['name'];
    }
    foreach ($lGru as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'">'.htm($lName).'</option>'.LF;
    }
    return $lRet;
  }
  
  protected function getAplPrefixOptions() {
    $lRet = '';
    foreach ($this->mPrefix as $lKey => $lPrefix) {
      $lRet.= '<option value="'.$lPrefix.'">'.$lPrefix.'</option>'.LF;
    }
    return $lRet;
  }
  
  protected function getPrefix() {
    $lUid = CCor_Usr::getAuthId();
    $lApl = new CInc_App_Apl_Loop($this->mSrc, $this->mJid);
    $lLoopId = $lApl->getLastOpenLoop();
    $lActiveStatesIds = $lApl -> getActiveStateIds($lLoopId);
    $lSql = 'SELECT id, prefix FROM al_job_apl_states WHERE user_id='.$lUid.' AND loop_id='.$lLoopId.' AND done="N"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      #$lPrefix[] = $lRow['prefix'];
      foreach ($lActiveStatesIds as $lKey => $lVal) {
        if ($lVal == $lRow['id']) {
          $lPrefix[$lRow['id']] = $lRow['prefix'];
        }
      }
    }
    return $lPrefix;
  }
  
  protected function getButtonsDesc() {
    $lRet = '';
    $lRet.= '<b>Add: </b>'.lan('revisor.add.desc').BR.BR;
    $lRet.= '<b>Forward: </b>'.lan('revisor.forward.desc').BR.BR;
    $lRet.= '<b>Expand: </b>'.lan('revisor.expand.desc');
    return $lRet;
  }

}
<?php
/**
 * Hilfstabellen unter Daten, k�nnen vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 23 $
 * @date $Date: 2012-03-12 21:16:57 +0100 (Mon, 12 Mar 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_List extends CHtm_List {

  public function __construct() {
    parent::__construct('htg');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('htb.menu');
    $this -> m2Act = 'htg';

    $this -> addCtr();
    $this -> addColumn('domain',      lan('lib.code'), TRUE, array('width' => '16'));
    $this -> addColumn('description', lan('lib.description'), TRUE, array('width' => '100%'));
    $this -> addColumn('items',       lan('lib.items'), TRUE, array('width' => '16'));
    $this -> mDefaultOrder = 'description';
/* mit dem neuen Recht htg �berfl�ssig !?
    $this -> getPriv('htb');// !!!

    if($this -> mCanEdit)
      $this -> mStdLnk = 'index.php?act='.$this -> m2Act.'.edt&amp;id=';
    else
      $this -> mStdLnk = '';
*/
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('htb.new'), "go('index.php?act=".$this -> m2Act.".new')", 'img/ico/16/plus.gif');
    }
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_htb_master');

    $this -> mIte -> addCnd($this -> getConstraint());
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mMaxLines = $this -> mIte -> getCount();
    if ($this -> mPage * $this -> mLpp > $this->mMaxLines) {
      $this->mPage  = 0;
      $this->mCtr   = 1;
      $this->mFirst = 0;
    }

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> addPanel('nav', $this -> getNavBar());
    #$this -> addPanel('vie', $this -> getViewMenu());

  }

  protected function getConstraint() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lMid = MID;
    $lRight = 'htg';

    $lRig = array();

    $lSql = 'SELECT code,level FROM al_usr_rig WHERE user_id='.$lUid.' ';
    $lSql.= 'AND `right` LIKE "'.$lRight.'" ';
    $lSql.= 'AND mand='.$lMid.' ';
    $lSql.= 'AND (level &1)';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRig[$lRow['code']] = '"'.$lRow['code'].'"';
    }

    $lGid = array();
    $lSql = 'SELECT m.gid,g.name FROM al_usr_mem m, al_gru g WHERE m.gid=g.id AND m.uid='.$lUid;
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $lGid[] = intval($lRow['gid']);
    }
    if (!empty($lGid)) {
      $lSql = 'SELECT group_id,code,level FROM al_gru_rig WHERE group_id IN ('.implode(',', $lGid).') ';
      $lSql.= 'AND `right` LIKE "'.$lRight.'" ';
      $lSql.= 'AND mand='.$lMid.' ';
      $lSql.= 'AND (level &1)';
      $lQry -> query($lSql);
      $this -> dbg($lSql);
      foreach ($lQry as $lRow) {
        $lRig[$lRow['code']] = '"'.$lRow['code'].'"';
      }
    }
    if (!empty($lRig)) {
    	$lWhere = 'domain IN ('.implode(',', $lRig).')';
    } else
      $lWhere = '';

    return $lWhere;
  }

  protected function getRow() {
    $lRet = '<tr class="hi">';
    foreach ($this -> mCols as $this -> mColKey => & $this -> mCol) {
      if ($this -> mCol -> isHidden()) {
        continue;
      }
      if ($this -> mCanEdit) {
        $this -> mCurLnk = $this -> getLink();
      } else {
        $lCod = $this -> getVal('domain');
        $this -> mCurLnk = "index.php?act=".$this -> m2Act."-itm&amp;dom=".$lCod;
      }
      $lRet.= $this -> getColTd();
    }
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getTdItems() {
    $lCod = $this -> getVal('domain');
    $lRet = '<a href="index.php?act='.$this -> m2Act.'-itm.reset&amp;dom='.$lCod.'">';
    $lCnt = CCor_Qry::getStr('SELECT COUNT(*) FROM al_htb_itm WHERE mand IN(0,'.MID.') AND domain="'.$lCod.'"');
    $lRet.= $lCnt;
    $lRet.= '</a>';
    return $this -> td($lRet);
  }


}
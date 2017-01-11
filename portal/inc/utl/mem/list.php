<?php
class CInc_Utl_Mem_List extends CCor_Obj {

  protected $mUsr;
  protected $mMem;

  protected function getMem() {
    if (isset($this -> mMem)) {
      return;
    }
    $lQry = new CCor_Qry('SELECT m.gid,m.uid,u.firstname,u.lastname FROM al_usr_mem m, al_gru g,al_usr u WHERE g.id=m.gid AND m.uid=u.id AND g.parent_id<>0');
    foreach ($lQry as $lRow) {
      $this -> mMem[$lRow['gid']]['u'.$lRow['uid']] = $lRow['lastname'].', '.$lRow['firstname'];
    }
  }

  public function getJs() {
    $this -> getMem();
    $lJson = Zend_Json::encode($this -> mMem);
    $lRet = 'var gMem = eval("'.$lJson.'");'.LF;
    return $lRet;
  }

}
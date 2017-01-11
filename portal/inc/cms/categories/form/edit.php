<?php
class CInc_Cms_Categories_Form_Edit extends CCms_Categories_Form_Base {

  public function __construct($aId) {
    parent::__construct('cms-categories.sedt', lan('cms-categories.edt'));

    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM `al_cms_categories` WHERE `id`='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $lTasks = new CCor_Qry('SELECT DISTINCT `task` FROM `al_cms_categorytasks` WHERE `category`='.esc($lRow['value']));
      $lRow['tasks'] = $lTasks -> getImplode('task');
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}
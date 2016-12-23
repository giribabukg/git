<?php
class CInc_Pck_Col_List extends CHtm_List {

  public function __construct($aDom) {
    parent::__construct('pck-col', 'pck'); // 'pck' == Recht zu Modifizieren etc.
     $this -> mDom = $aDom;
    $this -> setAtt('width', '800px');

    $this -> mTitle = lan('pck-col.menu');
    $this -> getPriv('pck');

    $this -> mStdLnk = 'index.php?act=pck-col.edt&amp;dom='.$this->mDom.'&amp;id=';

    $this -> mDelLnk = 'index.php?act=pck-col.del&amp;dom='.$this -> mDom.'&amp;id=';
    $this -> mNewLnk = 'index.php?act=pck-col.new&amp;dom='.$this -> mDom.'&amp;typ=';

   $this -> addCtr();
    $this -> addColumn('alias', lan('fie.alias'));
    $this -> addColumn('col', lan('pck.column'));
    $this -> addColumn('position', lan('lib.pos'));
    $this -> addColumn('hidden', lan('lib.hidden'));
    $this -> addColumn('image', lan('lib.img'));
    $this -> addColumn('color', lan('lib.color'));
    $this -> addColumn('ignoretype', lan('pck.ignoretype'));

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('pck-col.new'), "go('index.php?act=pck-col.new&dom=".$this -> mDom."')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_pck_columns');
    $this -> mIte -> addCnd('domain='.esc($this -> mDom));
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    $this -> mIte -> setOrder('position');
  }

}
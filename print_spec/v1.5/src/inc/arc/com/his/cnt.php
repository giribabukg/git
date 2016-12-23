<?php
class CInc_Arc_Com_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-his.menu');
  }

  protected function getStdUrl() {
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');

    $lJob = new CArc_Dat('com');
    $lJob -> load($lJid);

    $lRet = '';

    $lVie = new CJob_Com_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CArc_Com_Tabs($lJid, 'his');
    $lRet.= $lVie -> getContent();

    $lVie = new CArc_His_List('com', $lJid);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actNew() {
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CArc_Dat('com');
    $lJob -> load($lJid);

    $lVie = new CJob_Com_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form('com', $lJid, 'snew', 'arc');
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod('com', $lJid);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CArc_Dat('com');
    $lJob -> load($lJid);

    $lVie = new CJob_Com_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form('com', $lJid, 'sedt', 'arc');
    $lVie -> load($lId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod('com', $lJid);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actPrnitm() {
    $lId = $this -> getInt('id');
    $lJid = $this -> getReq('jobid');

    $lJob = new CArc_Dat('com');
    $lJob -> load($lJid);

    $lRet = '';
    $lHdr = new CJob_Com_Header($lJob);
    $lHdr -> hideMenu();
    $lRet.= $lHdr -> getContent().BR;

    $lHis = new CJob_His_Single($lId);
    $lRet.= $lHis -> getContent();

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-his.menu')));
    $lPag -> setPat('pg.js', '<script type="text/javascript">window.print()</script>');

    echo $lPag -> getContent();
    exit;
  }

}
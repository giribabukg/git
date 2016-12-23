<?php
/**
 * Jobs: Typ - Historie - Controller
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Typ
 * @subsubpackage 	Historie
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Typ_His_Cnt extends CJob_His_Cnt {

  protected $mSrc   = 'typ';

  public function __construct(ICor_Req $aReq, $aSrc, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $aSrc;
    $this -> mTitle = lan('job-his.menu');

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
  }

  protected function getStdUrl() {
    $lJobId = $this -> getReq('jobid');
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobId;
  }

  protected function actStd() {
    $lJobId = $this -> getReq('jobid');

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lRet = '';

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Typ_Tabs($this -> mSrc, $lJobId, 'his');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_List($this -> mSrc, $lJobId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actNew() {
    $lJobId = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form($this -> mSrc, $lJobId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJobId = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this -> mSrc, $lJobId);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act=job-typ-his&jobid='.$lJobId);
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lJobId = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form($this -> mSrc, $lJobId, 'sedt');
    $lVie -> load($lId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJobId = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this -> mSrc, $lJobId);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=job-typ-his&jobid='.$lJobId);
  }

  protected function actPrnitm() {
    $lId = $this -> getInt('id');
    $lJobId = $this -> getReq('jobid');

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lRet = '';
    $lHdr = new CJob_Typ_Header($this -> mSrc, $lJob);
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
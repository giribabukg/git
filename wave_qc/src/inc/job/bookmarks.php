<?php
class CInc_Job_Bookmarks extends CHtm_Menu {

  public function __construct($aSrc, $aJobId, $aKeyword) {
    if(THEME === 'default'){
    parent::__construct(lan('job-bm.menu'));
    } else {
      $lImg = img('img/ico/16/fav.gif');

      parent::__construct("<i class='ico-w16 ico-w16-fav'></i>", 'bm', false);
    }
    $this -> addJsItem(htm('Flow.Std.addBm("job_bm","'.$aSrc.'","'.$aJobId.'","'.urlencode($aKeyword).'")'), lan('job-bm.add'), '<i class="ico-w16 ico-w16-plus"></i>');
    $lUsr = CCor_Usr::getInstance();
    $lQry = $lUsr -> getBookmarks();
    foreach ($lQry as $lRow) {
      $lImg = (THEME === 'default' ? 'job-'.$lRow['src'] : CApp_Crpimage::getColourForSrc($lRow['src']));
      $this -> addItem(
        'index.php?act=job-'.$lRow['src'].'.edt&amp;jobid='.$lRow['jobid'],
        jid($lRow['jobid'],TRUE).': '.urldecode($lRow['keyword']),
        'ico/16/'.$lImg.'.gif',
        htm('javascript:Flow.Std.removeBm("job_bm","'.$lRow['src'].'","'.$lRow['jobid'].'","'.urlencode($lRow['keyword']).'");'),
        'img/ico/16/del.gif'
      );
    }
  }

}
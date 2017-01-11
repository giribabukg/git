<?php
class CJob_Cnt extends CCust_Job_Cnt {

  protected function actPrn() {
    $lJobId = $this -> getReq('jobid');
  
    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lJob = $lFac -> getDat();
  
    $lRet = '';
    $lHdr = $lFac -> getHeader();
    $lHdr -> hideMenu();
    $lRet.= $lHdr -> getContent().BR;
  
    $lVie = new CJob_Print($this -> mSrc, $lJob, 'job', $lJobId);
    $lRet.= $lVie -> getContent();
  
    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-art.menu')));
    $lPag -> setPat('pg.js', '
      <script type="text/javascript">
        jQuery(document).ready(function(){
          jQuery("#ui-datepicker-div").remove();

          jQuery("#iframe").load(function() {
            window.print();
          });
        })</script>');

    echo $lPag -> getContent();
    exit;
  }
}
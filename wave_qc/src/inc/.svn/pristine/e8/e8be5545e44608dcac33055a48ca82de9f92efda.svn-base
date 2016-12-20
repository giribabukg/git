<?php
class CInc_Mig_Menu extends CHtm_Vmenu {

  public function __construct($aKey = NULL) {
    parent::__construct(lan('mig.tls'));
    $this -> setKey($aKey);

    $this -> addItem('arcjob', 'index.php?act=mig.arcjob', lan('mig.arc.job.missing'));
    $this -> addItem('copyjobstoarchive', 'index.php?act=mig.copyjobstoarchive', lan('mig.cpy.jobs.archiv'));
    $this -> addItem('copyjobstowave', 'index.php?act=mig.copyjobstowave', 'Migrate Jobs from Alink into Wave');

    $lMenu = array('jobrep','manualcopyjobstoarchive','archivstatusrep');
    if (in_array($aKey, $lMenu)) {
      $this -> addItem('jobrep', 'index.php?act=mig.jobrep', lan('mig.rep.to.status'));
      $this -> addItem('manualcopyjobstoarchive', 'index.php?act=mig.manualcopyjobstoarchive', lan('lib.manual').': '.lan('mig.cpy.jobs.archiv'));
      $this -> addItem('copyjobstoarchive', 'index.php?act=mig.copyjobstoarchive', 'autom.: '.lan('mig.cpy.jobs.archiv'));
      $this -> addItem('archivstatusrep', 'index.php?act=mig.archivstatusrep', 'Archivstatusrep');
    }
  }
}
<?php
/**
 * Html-Bausteine: Page
 *
 * SINGLETON
 *
 * @package    HTM
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13941 $
 * @date $Date: 2016-05-18 16:40:05 +0200 (Wed, 18 May 2016) $
 * @author $Author: ahanslik $
 */
class CHtm_Page extends CCor_Tpl {

  private static $mInstance = NULL;

  private function __construct() {
    $this -> mTpl = $this -> getProjectFilename('page.htm');

    $this -> setPat('pg.date', date(lan('lib.date.xxl')));

    $lSys = CCor_Sys::getInstance();
    $lNam = cat($lSys['usr.firstname'], $lSys['usr.lastname'], ' ');

    $this -> setPat('pg.bootstrapcss',      'js'.DS.'Bootstrap'.DS.'css'.DS.'bootstrap.css');
    $this -> setPat('pg.bootstrapthemecss', 'js'.DS.'Bootstrap'.DS.'css'.DS.'bootstrap-theme.css');
    $this -> setPat('pg.bootstrapjs',       'js'.DS.'Bootstrap'.DS.'js'.DS.'bootstrap.js');

    $this -> setPat('pg.user',      htm($lNam));
    $this -> setPat('pg.cust.name', CUSTOMER_NAME);
    //CSS
    $this -> setPat('pg.utilcss',   $this -> getProjectFilename('css'.DS.'util.css'));
    $this -> setPat('pg.stylecss',  $this -> getProjectFilename('css'.DS.'style.css'));
    $this -> setPat('pg.jqueryuicss',  'js'.DS.'jquery'.DS.'jquery-ui.css');
    $this -> setPat('pg.jqueryuithemecss',  'js'.DS.'jquery'.DS.'jquery.ui.theme.css');
    //JS Cache
    if($lUsr = CCor_Usr::getInstance()) {
      $this -> setPat('pg.langjson', 'tmp'.DS.'lang_'.$lUsr -> getPref('sys.lang').'.js');
    }
    else {
      $this -> setPat('pg.langjson', 'tmp'.DS.'lang_'.$this -> mVal['default.lang'].'.js');
    }
    //JS
    $this -> setPat('pg.jqueryjs', 'js'.DS.'jquery'.DS.'jquery.min.js');
    $this -> setPat('pg.jqueryuijs', 'js'.DS.'jquery'.DS.'jquery-ui.min.js');
    $this -> setPat('pg.prototypejs', 'js'.DS.'prototype.js');
    $this -> setPat('pg.crpstepsjs', 'cust'.DS.'js'.DS.'crpsteps.js');
    $this -> setPat('pg.phrasejs', 'js'.DS.'phrase.js');
    $this -> setPat('pg.highstockjs', 'js'.DS.'highstock.js');
    $this -> setPat('pg.flinkjs', 'js'.DS.'flink.js');
    $this -> setPat('pg.flowjs', 'js'.DS.'flow.js');
    $this -> setPat('pg.stdjs', 'js'.DS.'std.js');
    $this -> setPat('pg.json2js', 'js'.DS.'json2.js');
    $this -> setPat('pg.tinymcejs', 'js'.DS.'mce'.DS.'tiny_mce.js');
    $this -> setPat('pg.jqueryiframetransportjs', 'js'.DS.'jQuery File Upload'.DS.'js'.DS.'jquery.iframe-transport.js');
    $this -> setPat('pg.jqueryfileuploadjs', 'js'.DS.'jQuery File Upload'.DS.'js'.DS.'jquery.fileupload.js');
    //IMG
    $this -> setPat('pg.favicon',   $this -> getProjectFilename('pag'.DS.'favicon.ico'));
    $this -> setPat('pg.mand.logo', $this -> getProjectFilename('pag'.DS.'logo.png'));
    $this -> setPat('pg.mand.headerback', $this -> getProjectFilename('pag'.DS.'header-back.png'));
    $this -> setPat('pg.mand.cust', $this -> getProjectFilename('pag'.DS.'cust.png'));
    $this -> setPat('pg.lib.wait',  htm(lan('lib.wait')));

    $this -> mJs = '';
    $this -> mJsSrc = '';
    $this -> mHideMenu = false;
  }

  private final function __clone() {}

  /**
   * Singleton getInstance method
   * @return CHtm_Page
   */
  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  public function setMmKey($aKey) {
    $this -> mMmKey = $aKey;
  }

  public function hideMenu() {
    $this -> mHideMenu = True;
  }

  private function getMsgBox() {
    $lVie = new CHtm_MsgBox();
    return $lVie -> getContent();
  }

  protected function getBackButton() {
    $lRet = '';
    $lSys = CCor_Sys::getInstance();
    $lHis = $lSys -> get('his', array());
    if (!empty($lHis)) {
      $lCnt = count($lHis);
      if ($lCnt > 1) { // do not count current url
        $lRet = '<td><a href="index.php?act=hom-act.back" class="pgBtn">&nbsp;&nbsp;Back&nbsp;&nbsp;</a></td>';
      }
    }
    return $lRet;
  }

  protected function getMenu() {
    if ($this -> mHideMenu) return '';
    $lMen = new CHtm_Mainmenu($this -> mMmKey);
    return $lMen -> getContent();
  }

  public function addJs($aCont) {
    $this -> mJs.= $aCont.LF;
  }

  public function addJsSrc($aFilename) {
    $this -> mJsSrc.= '<script type="text/javascript" src="'.$aFilename.'"></script>'.LF;
  }

  protected function getJs() {
    $lRet = '';
    if (!empty($this -> mJs)) {
      $lRet.= '<script type="text/javascript"><!--'.LF;
      $lRet.= $this -> mJs;
      $lRet.= '--></script>'.LF;
    }
    $lRet.= $this -> mJsSrc;
    return $lRet;
  }

  protected function getCont() {
    if (empty($this -> mDoc)) {
      $this -> open($this -> mTpl);
    }
    $lSql = CCor_Sql::getInstance();

    $this -> setPat('pg.menu',  $this -> getMenu());
    if (extension_loaded('xdebug')) {
      $this -> setPat('pg.bench', substr(xdebug_time_index(), 0, 4));
      $this -> setPat('pg.mem',   round(xdebug_peak_memory_usage() / MEM_MB, 2));
    } else {
      $this -> setPat('pg.bench', lan('pg.bench'));
      $this -> setPat('pg.mem',   lan('pg.mem'));
    }

    if (0 < MID) {
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    } else {
      $lQry = new CCor_Qry('SELECT id, name_'.LAN.' as name FROM al_sys_mand');
      $lArr = array();
      foreach($lQry as $lRow) {
        $lArr[$lRow -> id] = $lRow -> name;
      }
    }
    $lNam = (isset($lArr[MID])) ? $lArr[MID] : lan('lib.unknown');
    $this -> setPat('pg.mand.name',   htm($lNam));
    $this -> setPat('pg.mand',        MAND);
    $this -> setPat('pg.qry',         $lSql -> getQueryCount());
    $this -> setPat('pg.rch',         getCtr('rch')); // Ressource cache hits
    $this -> setPat('pg.zch',         getCtr('zch')); // Zend cache hits
    $this -> setPat('pg.versioninfo',   CCor_Cfg::get('versioninfo')); // Version info LIVE, STAGE or LOCAL, ...
    $this -> setPat('pg.revision_nr',   lan('pg.revision_nr').' '.VERSION);  // Version number created with new TAG
    $this -> setPat('pg.revision_date', lan('pg.revision_date').' '.VERSIONDATE);// Version date created with new TAG
    $this -> setPat('pg.lib.wait',    htm(lan('lib.wait')));

    $this -> setPat('pg.js',          $this -> getJs());
    $this -> setPat('pg.msg',         $this -> getMsgBox());
    return parent::getCont();
  }

}
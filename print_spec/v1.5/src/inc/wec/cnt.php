<?php
class CInc_Wec_Cnt extends CCor_Cnt {

  protected $mNumbers = array(5 => 5, 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100, 150 => 150, 200 => 200, 250 => 250);

  protected $mFetchKeys = array(
    'wec-pi.fetch_subfolder',
    'wec-pi.fetch_number',
    'wec-pi.fetch_number_of_check',
    'wec-pi.fetch_check',
    'wec-pi.fetch_log'
  );

  protected $mSyncKeys = array(
    'wec-pi.sync_subfolder',
    'wec-pi.sync_number',
    'wec-pi.sync_log'
  );

  protected $mImageKeys = array(
    'wec-pi.image_path',
    'wec-pi.image_subfolder',
    'wec-pi.image_name',
    'wec-pi.image_notfound',
    'wec-pi.image_usefirst'
  );

  protected $mThumbnailKeys = array(
    'wec-pi.thumbnail_path',
    'wec-pi.thumbnail_subfolder',
    'wec-pi.thumbnail_name',
    'wec-pi.thumbnail_notfound',
    'wec-pi.thumbnail_usefirst'
  );

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('wec-pi.menu');
    $this -> mMmKey = 'opt';

    $lUserRight = 'wec-pi';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lUserRight)) {
      $this -> setProtection('*', $lUserRight, rdNone);
    }
  }

  protected function actStd() {
    $this -> redirect('index.php?act=wec.image');
  }

  protected function actFetch() {
    $lFrm = new CHtm_Form('wec.sfetch', lan('wec-pi.fetch_menu'), false);
    $lFrm -> setAtt('style', 'width: 100%');

    $lFetchKeys = implode('","', $this -> mFetchKeys);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lFetchKeys.'")');
    foreach ($lQry as $lKey => $lValue) {
      $lFrm -> setVal($lValue['code'], $lValue['val']);
    }

    $lFrm -> addDef(fie('wec-pi.fetch_subfolder',       lan('wec-pi.fetch_subfolder'),       'edit',   '',                array('style' => 'width: 400px', 'onblur' => "Flow.Std.checkForSubfolder('val[wec-pi.fetch_subfolder]');")));
    $lFrm -> addDef(fie('wec-pi.fetch_number',          lan('wec-pi.fetch_number'),          'select', $this -> mNumbers, array('style' => 'width: 400px')));
    $lFrm -> addDef(fie('wec-pi.fetch_log',             lan('wec-pi.fetch_log'),             'boolean'));
    $lFrm -> addDef(fie('wec-pi.fetch_check',           lan('wec-pi.fetch_check'),           'boolean'));
    $lFrm -> addDef(fie('wec-pi.fetch_number_of_check', lan('wec-pi.fetch_number_of_check'), 'select', $this -> mNumbers, array('style' => 'width: 400px')));

    $lMen = new CWec_Menu('fetch');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSfetch() {
    $lValues = $this -> getReq('val');

    foreach ($this -> mFetchKeys as $lKey) {
      if (!isset($lValues[$lKey])) {
        CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="'.$lKey.'";');
        if ('wec-pi.fetch_subfolder' == $lKey) {
          CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="wec-pi.sync_subfolder";');
        }
      } else {
        $lSqlVal = '';
        $lSqlValSub = '';
        $lSqlNam = '';
        foreach ($this -> mAvailLang as $lLang => $lN) {
          $lSqlVal.= ', '.esc(CCor_Lang::getStatic($lKey, $lLang));
          $lSqlValSub.= ', '.esc(CCor_Lang::getStatic('wec-pi.sync_subfolder', $lLang));
          $lSqlNam.= ','.backtick('name_'.$lLang);
        }
        CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("'.$lKey.'", "0", "adm"'.$lSqlVal.', "'.addslashes($lValues[$lKey]).'");');
        if ('wec-pi.fetch_subfolder' == $lKey) {
          CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("wec-pi.sync_subfolder", "0", "adm"'.$lSqlValSub.', "'.addslashes($lValues[$lKey]).'");');
        }
      }
    }

    $this -> redirect('index.php?act=wec.fetch');
  }

  protected function actSync() {
    $lFrm = new CHtm_Form('wec.ssync', lan('wec-pi.sync_menu'), false);
    $lFrm -> setAtt('style', 'width: 600px');

    $lSyncKeys = implode('","', $this -> mSyncKeys);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lSyncKeys.'")');
    foreach ($lQry as $lKey => $lValue) {
      $lFrm -> setVal($lValue['code'], $lValue['val']);
    }

    $lFrm -> addDef(fie('wec-pi.sync_subfolder', lan('wec-pi.sync_subfolder'), 'edit',   '',                array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForSubfolder('val[wec-pi.sync_subfolder]');")));
    $lFrm -> addDef(fie('wec-pi.sync_number',    lan('wec-pi.sync_number'),    'select', $this -> mNumbers, array('style' => 'width: 360px')));
    $lFrm -> addDef(fie('wec-pi.sync_log',       lan('wec-pi.sync_log'),       'boolean'));

    $lMen = new CWec_Menu('sync');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSsync() {
    $lValues = $this -> getReq('val');

    foreach ($this -> mSyncKeys as $lKey) {
      if (!isset($lValues[$lKey])) {
        CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="'.$lKey.'";');
        if ('wec-pi.sync_subfolder' == $lKey) {
          CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="wec-pi.fetch_subfolder";');
        }
      } else {
        $lSqlVal = '';
        $lSqlValSub = '';
        $lSqlNam = '';
        foreach ($this -> mAvailLang as $lLang => $lN) {
          $lSqlVal.= ', '.esc(CCor_Lang::getStatic($lKey, $lLang));
          $lSqlValSub.= ', '.esc(CCor_Lang::getStatic('wec-pi.sync_subfolder', $lLang));
          $lSqlNam.= ','.backtick('name_'.$lLang);
        }
        CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("'.$lKey.'", "0", "adm"'.$lSqlVal.', "'.addslashes($lValues[$lKey]).'");');
        if ('wec-pi.sync_subfolder' == $lKey) {
          CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("wec-pi.fetch_subfolder", "0", "adm"'.$lSqlValSub.', "'.addslashes($lValues[$lKey]).'");');
        }
      }
    }

    $this -> redirect('index.php?act=wec.sync');
  }

  protected function actImage() {
    $lFrm = new CHtm_Form('wec.simage', lan('wec-pi.image_menu'), false);
    $lFrm -> setAtt('style', 'width: 600px');

    $lImageKeys = implode('","', $this -> mImageKeys);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lImageKeys.'")');
    foreach ($lQry as $lKey => $lValue) {
      $lFrm -> setVal($lValue['code'], $lValue['val']);
    }

    $lFrm -> addDef(fie('wec-pi.image_path',      lan('wec-pi.image_path'),      'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForPath('val[wec-pi.image_path]');")));
    $lFrm -> addDef(fie('wec-pi.image_subfolder', lan('wec-pi.image_subfolder'), 'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForSubfolder('val[wec-pi.image_subfolder]');")));
    $lFrm -> addDef(fie('wec-pi.image_name',      lan('wec-pi.image_name'),      'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForName('val[wec-pi.image_name]');")));
    $lFrm -> addDef(fie('wec-pi.image_notfound',  lan('wec-pi.image_notfound'),  'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForNoImg('val[wec-pi.image_notfound]');")));
    $lFrm -> addDef(fie('wec-pi.image_usefirst',  lan('wec-pi.image_usefirst'),  'boolean'));

    $lMen = new CWec_Menu('image');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSimage() {
    $lValues = $this -> getReq('val');

    foreach ($this -> mImageKeys as $lKey) {
      if (!isset($lValues[$lKey])) {
        CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="'.$lKey.'";');
      } else {
        $lSqlVal = '';
        $lSqlNam = '';
        foreach ($this -> mAvailLang as $lLang => $lN) {
          $lSqlVal.= ', '.esc(CCor_Lang::getStatic($lKey, $lLang));
          $lSqlNam.= ','.backtick('name_'.$lLang);
        }
        CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("'.$lKey.'", "0", "adm"'.$lSqlVal.', "'.addslashes($lValues[$lKey]).'");');
      }
    }

    $this -> redirect('index.php?act=wec.image');
  }

  protected function actThumbnail() {
    $lFrm = new CHtm_Form('wec.sthumbnail', lan('wec-pi.thumbnail_menu'), false);
    $lFrm -> setAtt('style', 'width: 600px');

    $lThumbnailKeys = implode('","', $this -> mThumbnailKeys);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lThumbnailKeys.'")');
    foreach ($lQry as $lKey => $lValue) {
      $lFrm -> setVal($lValue['code'], $lValue['val']);
    }

    $lFrm -> addDef(fie('wec-pi.thumbnail_path',      lan('wec-pi.thumbnail_path'),      'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForPath('val[wec-pi.thumbnail_path]');")));
    $lFrm -> addDef(fie('wec-pi.thumbnail_subfolder', lan('wec-pi.thumbnail_subfolder'), 'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForSubfolder('val[wec-pi.thumbnail_subfolder]');")));
    $lFrm -> addDef(fie('wec-pi.thumbnail_name',      lan('wec-pi.thumbnail_name'),      'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForName('val[wec-pi.thumbnail_name]');")));
    $lFrm -> addDef(fie('wec-pi.thumbnail_notfound',  lan('wec-pi.thumbnail_notfound'),  'edit', '', array('style' => 'width: 360px', 'onblur' => "Flow.Std.checkForNoImg('val[wec-pi.thumbnail_notfound]');")));
    $lFrm -> addDef(fie('wec-pi.thumbnail_usefirst',  lan('wec-pi.thumbnail_usefirst'),  'boolean'));

    $lMen = new CWec_Menu('thumbnail');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSthumbnail() {
    $lValues = $this -> getReq('val');

    foreach ($this -> mThumbnailKeys as $lKey) {
      if (!isset($lValues[$lKey])) {
        CCor_Qry::exec('DELETE FROM al_sys_pref WHERE code="'.$lKey.'";');
      } else {
        $lSqlVal = '';
        $lSqlNam = '';
        foreach ($this -> mAvailLang as $lLang => $lN) {
          $lSqlVal.= ', '.esc(CCor_Lang::getStatic($lKey, $lLang));
          $lSqlNam.= ','.backtick('name_'.$lLang);
        }
        CCor_Qry::exec('REPLACE INTO al_sys_pref (code,mand,grp'.$lSqlNam.',val) VALUES ("'.$lKey.'", "0", "adm"'.$lSqlVal.', "'.addslashes($lValues[$lKey]).'");');
      }
    }

    $this -> redirect('index.php?act=wec.thumbnail');
  }

}
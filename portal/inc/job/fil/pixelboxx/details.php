<?php
class CInc_Job_Fil_Pixelboxx_Details extends CCor_Tpl {
  
  public function __construct($aDoi, $aDiv, $aSrc, $aJid, $aSub, $aAge) {
    $this->mDoi = $aDoi;
    
    $this->mDiv = $aDiv;
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;
    $this->mSub = $aSub;
    $this->mAge = $aAge;
    
    $this->setPat('doi', $aDoi);
    $this->setPat('div', $aDiv);
    $this->setPat('src', $aSrc);
    $this->setPat('jid', $aJid);
    $this->setPat('sub', $aSub);
    $this->setPat('age', $aAge);
    
    $this->mMap = CPixelboxx_Utils::getMetaMap();
    $this->mFie = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    
    $this->loadTemplate();
    $this->loadMetadata();
    $this->fillMetadata();
    $this->fillMenu();
  }
  
  protected function loadTemplate() {
    $lName = $this->getProjectFilename('pixelboxx/pboxdetails.htm');
    $this->open($lName);
  }
  
  protected function loadMetadata() {
    $lQry = new CApi_Pixelboxx_Query_Getobject();
    $this->mMeta = $lQry->getMetadata($this->mDoi);
    if (isset($this->mMeta['importdate'])) {
      $this->mMeta['importdate'] = strtotime($this->mMeta['importdate']);
    }
    if (isset($this->mMeta['objmodlast'])) {
      $this->mMeta['objmodlast'] = strtotime($this->mMeta['objmodlast']);
    }
  }
  
  protected function fillMetadata() {
    $lFpa = $this->findPatterns('val.');
    foreach ($lFpa as $lKey) {
      $this->setPat('val.'.$lKey, $this->mMeta[$lKey]);
    }
    $this->setPat('val.filesize', $this->fmtSize($this->mMeta['filesize']));
    
    $lFmt = lan('lib.datetime.short');
    $this->setPat('val.importdate', date($lFmt, $this->mMeta['importdate']));
    $this->setPat('val.objmodlast', date($lFmt, $this->mMeta['objmodlast']));

    if (!in_array('all', $lFpa)) return; 
    
    $lRet = '';
    foreach ($this->mMeta as $lKey => $lVal) {
      $lRet.= '<tr style="display:none" class="pbox-hide-details"><td class="td2">'.$lKey.'</td>';
      $lRet.= '<td class="td1">'.htm($lVal).'</td></tr>';
    }
    $this->setPat('val.all', $lRet);

    $lLine = '';
    foreach ($this->mMap as $lAlias => $lNative) {
      $lLine.= '<tr><td class="td2">';
      $lFieldName = isset($this->mFie[$lAlias]) ? $this->mFie[$lAlias] : $lAlias;
      $lLine.= htm($lFieldName);
      $lLine.= '</td><td class="td1">';
      $lFieldValue = isset($this->mMeta[$lNative]) ? $this->mMeta[$lNative] : '';
      $lLine.= htm($lFieldValue);
      $lLine.= '</td></tr>';
    }
    $this->setPat('val.meta', $lLine);
  }
  
  protected function fmtSize($aBytes) {
    $lVal = $aBytes;
    $lRet = $lVal.' Bytes';
    if ($lVal > 1024) {
      $lRet = number_format($lVal / 1024, 1).' kB';
    }
    $lMb = 1024 * 1024;
    if ($lVal > $lMb) {
      $lRet = number_format($lVal / $lMb, 1).' MB';
    }
    return $lRet;
  }
  
  protected function fillMenu() {
    $lMen = new CHtm_Menu('Actions', true);
    $lPar = "'".$this->mDoi."',";
    $lPar.= "'".$this->mDiv."',";
    $lPar.= "'".$this->mSrc."',";
    $lPar.= "'".$this->mJid."',";
    $lPar.= "'".$this->mSub."',";
    $lPar.= "'".$this->mAge."'";

    
    $lCart = new CApi_Pixelboxx_Cart();
    if ($lCart->isInCart($this->mDoi)) {
      $lMen->addJsItem('Flow.Pbox.removeFromCart('.$lPar.')', 'Remove from Cart', 'ico/16/check-lo.gif');
    } else {
      $lArr = array();
      $lArr['doi'] = $this->mDoi;
      $lArr['name'] = $this->mMeta['filename'];
      $lArr['size'] = $this->mMeta['filesize'];
      $lArr['date'] = $this->mMeta['importdate'];
      $lEnc = Zend_Json::encode($lArr);
      $lMen->addJsItem('Flow.Pbox.addToCart(\''.htm($lEnc).'\','.$lPar.')', 'Add to Cart', 'ico/16/check-hi.gif');
    }
    $lMen->addItem('index.php?act=utl-pixelboxx.download&doi='.$this->mDoi, 'Download', 'ico/16/flag-04.gif');
    #$lMen->addJsItem('Flow.Pbox.uploadNewVersion('.$lPar.')', 'Upload New Version', 'ico/16/folder.png');
    $lMen->addJsItem('Flow.Pbox.deleteFile('.$lPar.')', 'Delete File', 'ico/16/del.gif');
    
    $this->setPat('menu', $lMen->getContent());
  }

}
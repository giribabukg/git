<?php
/**
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package job
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4064 $
 * @date $Date: 2014-04-03 15:46:30 +0800 (Thu, 03 Apr 2014) $
 * @author $Author: ahajali $
 */
class CInc_Job_Part extends CCor_Tpl {

  /**
   *
   * @var CHtm_Fie_Fac
   */
  protected $mFac;
  protected $mHiddenUpload;
  

  public function __construct($aSrc, $aKey, & $aFac, & $aJob, $aOld = TRUE) {
    $this -> mSrc = $aSrc;
    $this -> mKey = $aKey;
    $this -> mFac = & $aFac;
    $this -> mJob = & $aJob;
    $this -> mOld = $aOld;
    $this -> openProjectFile('job/'.$aSrc.'/'.$aKey.'.htm');

    $this -> setPat('src', $this -> mSrc);
    $this -> mUsr = CCor_Usr::getInstance();
    $lPrf = $this -> mUsr -> getPref('cpl.'.$aSrc.'.'.$aKey, 0);
    $lDis = (empty($lPrf)) ? 'block' : 'none';
    $this -> setPat('display', $lDis);

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lNam = (isset($lArr[MID])) ? $lArr[MID] : lan('lib.unknown');
    $this -> setPat('pg.mand.name',   $lNam);

    $this -> mDisabled = FALSE;
    $this -> mState = fsStandard;
    $this -> mHasPat = FALSE;

    $this -> mPlain = new CHtm_Fie_Plain();
    $this -> mTextContent = new CInc_Content_Text();
	$this -> mHiddenUpload = FALSE;
  }

  public function setDisabled($aFlag = TRUE) {
    if ($aFlag) {
      $this -> setState(fsDisabled);
    } else {
      $this -> setState(fsStandard);
    }
  }

  public function setState($aState = fsStandard) {
    $this -> mState = $aState;
  }

  protected function getVal($aAlias) {
    $lRet = (isset($this -> mJob[$aAlias])) ? $this -> mJob[$aAlias] : '';
    return $lRet;
  }


  public function setFieldState($aAlias, $aState) {
    $this -> mSta[$aAlias] = $aState;
  }

  protected function onBeforeContent() {
    $lFie  = CCor_Res::get('fie');
    $lFpa  = $this -> findPatterns('val.');
    $lFpaPval  = $this -> findPatterns('plain.');
    $lFtxt = $this -> findPatterns('txt.');
    $lContent = $this->findPatterns('content.');
    #echo '<pre>---part.php---'.get_class().'---';var_dump($lFpa,'#############');echo '</pre>';
    foreach ($lFie as $lFid => $lDef) {
      $lAli = $lDef['alias'];
      $lFlags = $lDef['flags'];
      $lVal = $this -> getVal($lAli);

      // If Jobfield has Edit Flag active, ask for User Edit-RIGHT (combination of 'fie_' and Alias).
      // If User has no EDIT-RIGHT, set the attribute DISABLED.
      if (bitset($lFlags,ffEdit)){
        $lFieRight = 'fie_'.$lAli;
        if (!$this -> mUsr ->canEdit($lFieRight)){
          $this->setFieldState($lAli,fsDisabled);
        }
      }

      $this -> setPat('plain.'.$lAli, $this->mPlain->getPlain($lDef, $lVal));
      $lDef2 = $lDef;
      $lDef2['typ'] = 'hidden';
      $this -> setPat('hidden.'.$lAli, $this -> mFac -> getInput($lDef2, $lVal));

      // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
      // If User has no READ-RIGHT, set the attribute HIDDEN.
      if (bitset($lFlags,ffRead)){
        $lFieRight = 'fie_'.$lAli;
        if (!$this -> mUsr ->canRead($lFieRight)){
          $lDef['typ'] = 'hidden';
        }
      }

      #$lFeature = toArr($lDef['feature']); //wird jetzt in cor/res/fie erledigt!
      // Ist dieses Feld ein Colorfield
      if(isset($lDef['IsColor']) AND $lDef['IsColor']) {
        $lIsColor = TRUE;
      } else {
        $lIsColor = FALSE;
      }

      if(isset($lDef['IsImage']) AND $lDef['IsImage']) {
        $lIsImage = TRUE;
      } else {
        $lIsImage = FALSE;
      }

      if(isset($lDef['SteeredBy']) AND !empty($lDef['SteeredBy'])) {
        $lIsSteered = TRUE;
        $lValSteerBy = $this -> getVal($lDef['SteeredBy']);
        #echo '<pre>---onBeforeContent---'.get_class().'---';var_dump($lFid ,$lDef['alias'],$lDef['SteeredBy'],$lValSteerBy,$lIsSteered,'#############');echo '</pre>';
      } else {
        $lIsSteered = FALSE;
      }
      $lText = false;
      // in der gl. Maske ist entweder die Ausgabe in val oder txt,
      // die restlichen Felder können übersprungen werden
      // In der Maske dürfen keine auskommentierten val-Felder zusätzlich enthalten sein
      if (!in_array($lAli, $lFpa) and !in_array($lAli, $lFpaPval)) {
        if (in_array($lAli, $lFtxt)){
          $lText = true;
        } else {
          continue;
        }
      }

      $lVal = $this -> getVal($lAli);
  #echo '<pre>---part.php---'.get_class().'---';var_dump($lDef['alias'],$lVal,$lFid ,$lSta,$lIsSteered,'#############');echo '</pre>';

      if ($this -> mHiddenUpload == true && $lDef['typ'] == 'file') {
        if ($this -> mHiddenUpload[$lAli]) $lDef['typ'] = 'hidden';
      }

      $lSta = $this -> mState;
      if (isset($this -> mSta[$lAli])) {
        $lSta = $this -> mSta[$lAli];
      }

      if ($lIsColor) {
        $this -> setPat('txt.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta, TRUE, TRUE));
        $this -> setPat('val.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta));
      } elseif ($lText) {
        $this -> setPat('txt.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta, $lText));
      } elseif ($lIsSteered) {
        $this -> setPat('val.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta, FALSE, FALSE, $lValSteerBy));
      } elseif ($lIsImage) {
        $this -> setPat('txt.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta));
        $this -> setPat('val.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta, FALSE, FALSE, '', TRUE));
      } else {
        $this -> setPat('val.'.$lAli, $this -> mFac -> getInput($lDef, $lVal, $lSta));
      }
    }
	  
      $lDef2 = $lDef;
      $lDef2['typ'] = 'hidden';
      $this -> setPat('hidden.'.$lAli, $this -> mFac -> getInput($lDef2, $lVal));
      $this -> setPat('plain.'.$lAli, $this->mPlain->getPlain($lDef, $lVal));
	  
      if($lContent) {
      	foreach ($lContent as $lContAli) {
      		$this -> setPat('contentLabel.'.$lContAli, $this -> mTextContent ->getTextLabel($lContAli));
      		$this -> setPat('content.'.$lContAli, $this -> mTextContent -> getTextContent($lContAli));      		
      	}
      }
    }
   public function setHidden($aHidden) {
    $this -> mHiddenUpload = $aHidden;
  }
}
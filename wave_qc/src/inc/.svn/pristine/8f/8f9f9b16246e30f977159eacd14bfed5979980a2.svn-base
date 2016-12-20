<?php

class CInc_Fie_Validate_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);

    $this->setAtt('class', 'tbl w600');

    $this->addDef(fie('id', '', 'hidden'));
    $this->addDef(fie('name', lan('lib.name')));
    $this->addDef(fie('alias', lan('fie.alias')));

    $this->mIsGlobal = CFie_Validate_Mod::areWeOnGlobal();

    $lArr[0] = '['.lan('lib.all').']';
    $lArr[MID] = MANDATOR_NAME;
    if ($this->mIsGlobal) {
      $lArr[CFie_Validate_Mod::WAVE_GLOBAL] = '[WAVE GLOBAL]';
    }
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));

    $lArr = CApp_Validate::getTypes();
    asort($lArr);
    $lPar['lis'] = $lArr;
    $this->addDef(fie('validate_type', lan('lib.type'), 'valselect', $lPar));

    $this->addDef(fie('param', lan('lib.params')));

    $lJs = $this->getJavaScript();
    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lJs);
    $lUsr = CCor_Usr::getInstance();
    $this->canEdit = $lUsr->canEdit('fie-validate');
  }

  protected function getJavaScript() {
    $lRet = '';
    $lRet .= 'jQuery(function(){' . LF;
    $lRet .= '  function setValidationType(){' . LF;
    $lRet .= '    jQuery("div.par").hide();' . LF;
    $lRet .= '    jQuery("div.typ-" + jQuery(".field_validate_type").val()).show();' . LF;
    $lRet .= '  }';
    $lRet .= '  jQuery(".field_validate_type").on("change",setValidationType);' . LF;
    $lRet .= '  setValidationType();' . LF;
    $lRet .= '});';
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this->mFie)) {
      foreach ($this->mFie as $lAlias => $lDef) {
        $lFnc = 'getAlias' . $lAlias;
        if ($this->hasMethod($lFnc)) {
          $lRet .= $this->$lFnc($lDef);
          continue;
        }
        $lRet .= '<tr>' . LF;
        $lRet .= '<td class="nw">' . htm($lDef['name_' . LAN]) . NB . '</td>' . LF;
        $lRet .= '<td>' . LF;
        $lRet .= $this->mFac->getInput($lDef, $this->getVal($lAlias));
        $lRet .= '</td>' . LF;
        $lRet .= '</tr>' . LF;
      }
    }
    return $lRet;
  }

  protected function getAliasParam() {
    $lFac = new CHtm_Fie_Fac();
    $lArr = CApp_Validate::getAllTypeOptions();

    $lCur = $this->getVal('typ', 'int');
    $lPar = $this->getVal('options');
    if (!empty($lPar)) {
      $lPar = toArr($lPar);
    }

    $lRet = '<tr>' . LF;
    $lRet .= '<td colspan="2">' . LF;
    foreach ($lArr as $lTyp => $lOpt) {
      if (!empty($lOpt)) {
        $lDis = ($lTyp == $lCur) ? 'block' : 'none';
        $lRet .= '<div class="box par typ-' . $lTyp . '" style="display:' . $lDis . '">' . LF;
        $lRet .= '<div class="th2">' . ucfirst($lTyp) . '</div>' . LF;
        $lRet .= '<div class="p8">' . LF;
        $lFac->mValPrefix = 'par_val[' . $lTyp . ']';
        $lFac->mOldPrefix = 'par_old[' . $lTyp . ']';
        $lRet .= '<table cellpadding="2" cellspacing="0" border="0">' . LF;
        foreach ($lOpt as $lFie) {
          $lKey = $lFie['alias'];
          $lVal = (isset($lPar[$lKey])) ? $lPar[$lKey] : NULL;
          $lRet .= '<tr>' . LF;
          $lRet .= '<td class="nw">' . htm($lFie['name_' . LAN]) . '</td>' . LF;
          $lRet .= '<td>' . LF;
          $lRet .= $lFac->getInput($lFie, $lVal);
          $lRet .= '</td>' . LF;
          $lRet .= '</tr>' . LF;
        }
        $lRet .= '</table>' . LF;
        $lRet .= '</div>' . LF;
        $lRet .= '</div>' . LF;
      }
    }
    $lRet .= '</td>' . LF;
    $lRet .= '</tr>' . LF;
    return $lRet;
  }


}

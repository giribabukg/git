<?php
class CInc_Fie_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);

    $this -> setAtt('class', 'tbl w600');

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    $this -> addDef(fie('alias',   lan('fie.alias')));

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    $this -> addDef(fie('native',  lan('fie.native'), "string", "", array("data-change" => "autocomplete", "data-source" => "ajx.native", "data-autocomplete-body" => "'<b>'+item.label+'</b>: '+item.value")));
    if ('mop' == $lWriter) {
      $this -> addDef(fie('native_wdc',  'WaveDataCenter'));
    }

    $lArr = array();
    $lArr['pro'] = lan('job-pro.item');
    $this -> addDef(fie('src', lan('lib.src'), 'select', $lArr));

    $lArr = array();
    $lReg = new CHtm_Fie_Reg();
    $lArr = $lReg -> getTypes();
    $lArr = collectVal($lArr, 'typ', 'cap');
    asort($lArr);
    $lPar = array('onchange' => 'setFieTyp(this)');
    $this -> addDef(fie('typ', lan('lib.type'), 'select', $lArr, $lPar));

    $lJs = 'function setFieTyp(aEl) {';
    $lJs.= '$$("div.par").each(function(aDiv){aDiv.hide()});';
    $lJs.= 'var lTyp = aEl.value; if ($(lTyp)) {$(lTyp).show()};';
    $lJs.= 'if (!!~(jQuery.inArray(lTyp, ["boolean", "ccomplete", "cselect", "file", "image"]))) {jQuery("input[data-key=\"4096\"]").prop("disabled", true);} else {jQuery("input[data-key=\"4096\"]").prop("disabled", false);}';
    $lJs.= '};
    jQuery(function(){
        if (jQuery(\'select[name="par_val[file][dest]"]\')[0].value == "doc") {
          jQuery(\'input[name="par_val[file][folder]"]\').val("");
          jQuery(\'input[name="par_val[file][url]"]\').val("");

          jQuery(\'input[name="par_val[file][folder]"]\').prop("disabled", true);
          jQuery(\'input[name="par_val[file][url]"]\').prop("disabled", true);
        } else {
          jQuery(\'input[name="par_val[file][folder]"]\').prop("disabled", false);
          jQuery(\'input[name="par_val[file][url]"]\').prop("disabled", false);
        }

        jQuery(\'select[name="par_val[file][dest]"]\').change(function () {
        if (jQuery(this)[0].value == "doc") {
          jQuery(\'input[name="par_val[file][folder]"]\').val("");
          jQuery(\'input[name="par_val[file][url]"]\').val("");

          jQuery(\'input[name="par_val[file][folder]"]\').prop("disabled", true);
          jQuery(\'input[name="par_val[file][url]"]\').prop("disabled", true);
        } else {
          jQuery(\'input[name="par_val[file][folder]"]\').prop("disabled", false);
          jQuery(\'input[name="par_val[file][url]"]\').prop("disabled", false);
        } 
      });
    });';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    $lArr = array('' => '', '_self' => '['.lan('fie.own_list').']');
    $lQry = new CCor_Qry('SELECT DISTINCT(learn) AS alias FROM al_fie WHERE `mand`='.MID.' AND learn<>""');
    foreach ($lQry as $lRow) {
      $lArr[$lRow['alias']] = $lRow['alias'];
    }
    $this -> addDef(fie('learn', lan('fie-learn.list'), 'select', $lArr));
    $this -> addDef(fie('maxlen', lan('fie-maxlen')));


    $this -> addDef(fie('param', lan('lib.param'), 'params'));
    $this -> addDef(fie('attr', lan('lib.attributes'), 'params'));
    $this -> addDef(fie('feature', lan('lib.feature'), 'params'));

    if (CCor_Cfg::get('validate.available')) {
      $lArr = CCor_Res::extract('id', 'name', 'validate');
      $lArr = array(0 => '') + $lArr;
      $lPar['lis'] = $lArr;
      $this->addDef(fie('validate_rule', lan('validate.rule'), 'select', $lArr));
    }

    $lArr = array('dom' => 'ffl');
    $this -> addDef(fie('flags', lan('lib.flags'), 'bitset', $lArr));

    $lArr = array('dom' => 'ava');
    $this -> addDef(fie('avail', lan('fie.avail'), 'bitset', $lArr));

    $this -> addDef(fie('desc_de', lan('lib.description').' (DE)', 'memo'));
    $this -> addDef(fie('desc_en', lan('lib.description').' (EN)', 'memo'));
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this -> mFie)) {
      foreach ($this -> mFie as $lAlias => $lDef) {
        $lFnc = 'getAlias'.$lAlias;
        if ($this -> hasMethod($lFnc)) {
          $lRet.= $this -> $lFnc($lDef);
          continue;
        }
        $lRet.= '<tr>'.LF;
        $lRet.= '<td class="nw">'.htm($lDef['name_'.LAN]).NB.'</td>'.LF;
        $lRet.= '<td>'.LF;
        $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAlias));
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
      }
    }
    return $lRet;
  }

  protected function getAliasParam($aDef) {
    $lReg = new CHtm_Fie_Reg();
    $lFac = new CHtm_Fie_Fac();
    $lArr = $lReg -> getTypes();

    $lCur = $this -> getVal('typ');
    $lPar = $this -> getVal('param');
    if (!empty($lPar)) {
      $lPar = toArr($lPar);
    }

    $lRet = '<tr>'.LF;
    $lRet.= '<td colspan="2">'.LF;
    foreach ($lArr as $lTyp => $lDef) {
      $lOpt = $lReg -> getParamDef($lTyp);
      if (!empty($lOpt)) {
        $lDis = ($lTyp == $lCur) ? 'block' : 'none';
        $lRet.= '<div class="box par" id="'.$lTyp.'" style="display:'.$lDis.'">'.LF;
        $lRet.= '<div class="th2">'.$lDef['cap'].'</div>'.LF;
        $lRet.= '<div class="p8">'.LF;
        $lFac -> mValPrefix = 'par_val['.$lTyp.']';
        $lFac -> mOldPrefix = 'par_old['.$lTyp.']';
        $lRet.= '<table cellpadding="2" cellspacing="0" border="0">'.LF;
        foreach ($lOpt as $lFie) {
          $lKey = $lFie['alias'];
          $lVal = (isset($lPar[$lKey])) ? $lPar[$lKey] : NULL;
          $lRet.= '<tr>'.LF;
          $lRet.= '<td class="nw">'.htm($lFie['name_'.LAN]).'</td>'.LF;
          $lRet.= '<td>'.LF;
          $lRet.= $lFac -> getInput($lFie, $lVal);
          $lRet.= '</td>'.LF;
          $lRet.= '</tr>'.LF;
        }
        $lRet.= '</table>'.LF;
        $lRet.= '</div>'.LF;
        $lRet.= '</div>'.LF;
      }
    }
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
}

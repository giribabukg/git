<?php
class CInc_Job_Fil_Tree extends CHtm_Tree_List {

  public function __construct($aContentId = NULL) {
    parent::__construct();

    $this -> mDiv = (empty($aContentId)) ? getNum('d') : $aContentId;
  }

  public function getContentId() {
    return $this -> mDiv;
  }

  protected function getNodeSpan($aNode) {
    $lTag = new CHtm_Tag('span');
    $lTag -> setAtt('class', 'nav nw '.$aNode -> getId().'s');
    $lTag -> setAtt('id', $aNode -> getId().'s');
    $lTag -> setAtt('name', $aNode -> getVal('sub'));

    $lJs = '';
    if ($aNode -> hasChildren()) {
      $lJs .= '$(this).up(\'li\').down(\'ul\').toggle();';
    }

    $lSrc = $aNode -> getVal('src');
    if (!empty($lSrc)) {
      $lJid = $aNode -> getVal('jid');
      $lSub = $aNode -> getVal('sub');
      $lAge = $aNode -> getVal('age');

      $lParams = array(
        'act' => 'job-'.$lSrc.'-fil.get',
        'src' => $lSrc,
        'jid' => $lJid,
        'sub' => $lSub,
        'div' => $this -> mDiv,
        'age' => $lAge,
        'loading_screen' => TRUE
      );
      $lParamsJSONEnc = json_encode($lParams);
      $lJs.= 'Flow.Std.ajxUpd('.$lParamsJSONEnc.');';
    }

    if (!empty($lJs)) {
      $lTag -> setAtt('onclick', $lJs);
    }

    $lCap = $aNode -> getVal('caption');
    $lTip = $aNode -> getVal('tip');

    if (!empty($lTip)) {
      $lTag -> setAtt('data-toggle', 'tooltip');
      $lTag -> setAtt('data-tooltip-head', $lCap);
      $lTag -> setAtt('data-tooltip-body', $lTip);
    }

    $lRet = $lTag -> getTag();
    $lRet .= htm($aNode -> getVal('caption'));
    $lRet .= "</span>";
    return $lRet;
  }
}
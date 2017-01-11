<?php
class CInc_Rep_Tree extends CHtm_Tree_List {

  public function __construct($aSrc) {
    parent::__construct();

    $lNod = $this -> add('General');
    $lSql = 'SELECT id, code ,name FROM al_reports WHERE mand=0 ORDER BY name ASC;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lActive = ($lRow ['code'] == $aSrc) ? TRUE : FALSE;
      $lNod -> add($lRow ['name'], array(
        'src' => $lRow ['code'],
        'active' => $lActive 
      ));
    }
    $lNod -> setExpanded();
    $lNod = $this -> add('Client Specific');

    $lSql = 'SELECT id, code, name FROM al_reports WHERE mand=' . MID . ' ORDER BY name ASC;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lActive = ($lRow ['code'] == $aSrc) ? TRUE : FALSE;
      $lNod -> add($lRow ['name'], array(
        'src' => $lRow ['code'],
        'active' => $lActive 
      ));
    }
    $lNod -> setExpanded();
    $this -> mDiv = 'rep_form';
  }

  protected function getNode($aNode, $aDepth = 0) {
    try {
      if (0 == $aDepth) {
        $lRet = '<li id="' . $aNode -> getId() . '">';
      } else {
        $lRet = '<li class="doc" id="' . $aNode -> getId() . '">';
      }
      $lRet .= $this -> getNodeSpan($aNode);
      if ($aNode -> hasChildren()) {
        $lDis = ($aNode -> isExpanded()) ? '' : ' style="display:none"';
        $lRet .= '<ul' . $lDis . '>';
        $lChi = $aNode -> getChildren();
        foreach ($lChi as $lChild) {
          $lRet .= $this -> getNode($lChild, $aDepth + 1);
        }
        $lRet .= '</ul>';
      }
      $lRet .= '</li>' . LF;
    }
    catch (Exception $e) {
      $this -> dbg($e -> getMessage());
    }
    return $lRet;
  }

  protected function getNodeSpan($aNode) {
    $lSrc = $aNode -> getVal('src');
    $lActive = $aNode -> getVal('active');
    $lClass = ($lActive) ? 'nav nw b' : 'nav nw';

    $lTag = new CHtm_Tag('span');
    $lTag -> setAtt('class', $lClass." ". $aNode -> getId()."s");
    $lTag -> setAtt('id', $aNode -> getId().'s');

    $lJs = '';
    if ($aNode -> hasChildren()) {
      $lJs .= 'jQuery(this).closest(\'li\').find(\'ul\').first().toggle();';
    }

    if (!empty($lSrc)) {
      $lJs .= 'Flow.Std.ajxImg("'.$this -> mDiv.'", "Form"); loadReport(this.id, "#'.$this -> mDiv.'", "index.php?act=rep.chart", {src:"'.$lSrc.'"});';
    }
    if (!empty($lJs)) {
      $lTag -> setAtt('onclick', $lJs);
    }

    $lCap = $aNode -> getVal('caption');
    $lTip = $aNode -> getVal('tip');

    if (!empty($lTip)) {
      $lTag -> addAtt('data-toggle', 'tooltip');
      $lTag -> setAtt('data-tooltip-head', $lCap);
      $lTag -> addAtt('data-tooltip-body', $lTip);
    }

    $lRet = $lTag -> getTag();
    $lRet .= htm($aNode -> getVal('caption'));
    $lRet .= '</span>';
    return $lRet;
  }
}

<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 7346 $
 * @date $Date: 2015-01-20 11:56:15 +0100 (Tue, 20 Jan 2015) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Pro_Wrap extends CCor_Ren {

  public function __construct($aJobId, $aSubJobList = '', $aSubArcList = '', $aSubSkuList = '') {
    $this -> mJobId = $aJobId;
    $this -> mSubJobList = $aSubJobList;
    $this -> mSubArcList = $aSubArcList;
    $this -> mSubSkuList = $aSubSkuList;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;

      if ($this -> mSubJobList != ''){
        $lDiv = 'g'.uniqid();
        $lRet.= '<tr>';
        $lRet.= '<td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">';
        $lRet.= lan('job.menu');
        $lRet.= '</td>';
        $lRet.= '</tr>';
        $lRet.= '<tr id="'.$lDiv.'" style="display:table-row">';
        $lRet.= '<td class="td1">'.LF;
        $lVie = new CJob_Pro_Actsublist($this -> mJobId, $this -> mSubJobList);
        $lRet.= $lVie -> getContent();
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }

      if ($this -> mSubArcList != ''){
        $lDiv = 'g'.uniqid();
        $lRet.= '<tr>';
        $lRet.= '<td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">';
        $lRet.= lan('arc.menu');
        $lRet.= '</td>';
        $lRet.= '</tr>';
        $lRet.= '<tr id="'.$lDiv.'" style="display:table-row">';
        $lRet.= '<td class="td1">'.LF;
        $lVie = new CJob_Pro_Arcsublist($this -> mJobId, $this ->  mSubArcList);
        $lRet.= $lVie -> getContent();
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }

      if ($this -> mSubSkuList != ''){
        $lDiv = 'g'.uniqid();
        $lRet.= '<tr>';
        $lRet.= '<td class="cap cp" onclick="Flow.Std.togTr(\''.$lDiv.'\')">';
        $lRet.= lan('job-sku-sub.menu');
        $lRet.= '</td>';
        $lRet.= '</tr>';
        $lRet.= '<tr id="'.$lDiv.'" style="display:table-row">';
        $lRet.= '<td class="td1">'.LF;
        $lVie = new CJob_Pro_Skusublist($this -> mJobId, $this ->  mSubSkuList);
        $lRet.= $lVie -> getContent();
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }

    $lRet.= '</table>'.LF;
    return $lRet;
  }

}